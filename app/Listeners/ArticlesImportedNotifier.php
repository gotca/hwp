<?php

namespace App\Listeners;

use App\Events\ArticlesImported as ArticlesImportedEvent;
use App\Models\Article;
use App\Models\Site;
use App\Notifications\ArticlesImported as ArticlesImportedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArticlesImportedNotifier implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ArticlesImportedEvent $event
     * @return void
     */
    public function handle(ArticlesImportedEvent $event)
    {
        $site = $event->site;
        $articles = Article::whereIn($event->articleIds)->get();
        $articles->each(function($article) use ($site) {
            $notification = new ArticlesImportedNotification($article);
            $site->notify($notification);
        });
    }
}
