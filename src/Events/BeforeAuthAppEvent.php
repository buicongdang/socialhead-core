<?php
namespace Socialhead\Core\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class BeforeAuthAppEvent
{
    use Dispatchable, SerializesModels;

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
}
