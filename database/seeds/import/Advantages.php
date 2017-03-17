<?php

namespace Import;

use Illuminate\Database\Seeder;
use App\Database\Seeds\Import\ImportBase;
use Illuminate\Support\Facades\DB;

class Advantages extends ImportBase
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statDump = DB::connection('mysql.new')
            ->table('game_stat_dumps')
            ->select('site_id', 'game_id', 'json')
            ->get() ;

        foreach ($statDump as $dump) {
            $json = json_decode($dump->json);

            foreach($json->advantage_conversion as $key => $advantage) {
                $new = new \StdClass();

                $new->site_id = $dump->site_id;
                $new->game_id = $dump->game_id;
                $new->team = $key == 0 ? 'US' : 'THEM';
                $new->drawn = $advantage->drawn;
                $new->converted = $advantage->converted;

                DB::connection('mysql.new')
                    ->table('advantages')
                    ->insert(get_object_vars($new));
            }
        }
    }
}
