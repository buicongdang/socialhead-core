<?php
namespace Socialhead\Core\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
class WebhooksEvent
{
    use Dispatchable, SerializesModels;

    public $myshopify_domain, $topic, $payload;
    public function __construct($myshopify_domain, $topic, $payload)
    {
        $this->myshopify_domain = $myshopify_domain;
        $this->topic = $topic;
        $this->payload = $payload;
    }
}
