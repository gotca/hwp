<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;

use App\Http\Requests;

class ScheduleController extends Controller
{


    /**
     * Gets the data for the schedule page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $upcoming = Schedule::with('location')
            ->upcoming()
            ->take(20)
            ->get()
            ->groupByDate('start', 'Y-m-d')
            ->slice(0, 4);

        $full = Schedule::with(['location', 'scheduled'])
            ->withCount(['album', 'updates', 'boxStats'])
            ->orderBy('start', 'asc')
            ->get();

        return view('schedule', compact('upcoming', 'full'));
    }

    /**
     * Output the iCal file for the schedule
     *
     * @return mixed
     */
    public function subscribe()
    {
        $schedule = Schedule::with(['location', 'scheduled'])
            ->withCount(['album', 'updates', 'boxStats'])
            ->orderBy('start', 'asc')
            ->get();

        $vCal = new Calendar('HudsonvilleWaterPolo.com');
        $vCal
            ->setMethod('PUBLISH')
            ->setName(trans('vcal.name'))
            ->setDescription(trans('vcal.description'))
            ->setCalId('HudsonvilleWaterPolo.com')
            ->setTimezone('US/Eastern');

        foreach ($schedule as $item) {
            $vEvent = new Event();
            $vEvent
                ->setDtStamp($item->start)
                ->setDtEnd($item->end)
                ->setSummary($item->opponent)
                ->setCategories([$item->team, $item->type])
                ->setLocation($item->location->title . "\n" . $item->location->full_address, $item->location->title);

            // tournaments don't have start times
            if ($item->type === Schedule::TOURNAMENT) {
                $vEvent->setNoTime(true);
            }

            // descriptions
            $desc = [];

            if (strlen($item->scheduled->result)) {
                $desc[] = trans('vcal.result') . ' ' . $item->scheduled->result;
            }
            if (isset($item->score_us)) {
                $desc[] = trans('vcal.score') . ' ' . $item->score_us . ' - ' . $item->score_them;
            }
            if ($item->type === Schedule::GAME) {
                if ($item->box_stats_count) {
                    $desc[] = trans('vcal.stats') . ' ' . route('game.stats', ['id' => $item->scheduled->id]);
                }
                if ($item->album_count) {
                    $desc[] = trans('vcal.photos') . ' ' . route('game.photos', ['id' => $item->scheduled->id]);
                }
                if ($item->updates_count) {
                    $desc[] = trans('vcal.recap') . ' ' . route('game.recap', ['id' => $item->scheduled->id]);
                }
            }

            if (count($desc)) {
                $vEvent->setDescription(implode("\n", $desc));
                $vEvent->setDescriptionHTML('<p>'.implode('<br>', $desc));
            }

            $vCal->addComponent($vEvent);
        }

        return response($vCal->render())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="ical.ics"');
    }
}
