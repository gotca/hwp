<?php

namespace App\Listeners;

use App\Events\ArticleImported as ArticleImportedEvent;
use App\Models\Article;
use App\Models\Site;
use App\Notifications\ArticleImported as ArticleImportedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArticleImportedNotifier implements ShouldQueue
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
     * @param  ArticleImportedEvent $event
     * @return void
     */
    public function handle(ArticleImportedEvent $event)
    {
        $site = $event->site;
        $article = Article::findOrFail($event->articleId);
        $notification = new ArticleImportedNotification($article);
        $site->notify($notification);
    }
}
