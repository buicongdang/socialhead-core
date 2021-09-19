<?php


namespace Socialhead\Core;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Socialhead\Core\Events\AfterAuthAppEvent;
use Socialhead\Core\Events\BeforeAuthAppEvent;
use Socialhead\Core\Events\WebhooksEvent;
use Socialhead\Core\Listeners\AddWebhookListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AfterAuthAppEvent::class => [
            AddWebhookListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

}
