<?php
namespace Socialhead\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Socialhead\Core\Events\AfterAuthAppEvent;
use Socialhead\Core\Shopify\RestSDK;

class AddWebhookListener
{
    use Queueable;
    public function handle(AfterAuthAppEvent $event)
    {
        $shop = $event->data;
        $webhooks = config('socialhead.services.shopify.webhooks');
        $shopify = RestSDK::config(
            [
                'myshopify_domain' => $shop['myshopify_domain'],
                'access_token' => $shop['access_token']
            ]
        );
        foreach ($webhooks as $webhook)
        {
            $result = $shopify->Webhooks()->post(
                [
                    'webhook' => [
                        'topic' => $webhook,
                        'address' => config('socialhead.app.url_webhook').'/shopify/webhook',
                        'format' => 'json'
                    ]
                ]
            );

            if( ! $result['status'])
                Log::info('fail');
        }
    }
}
