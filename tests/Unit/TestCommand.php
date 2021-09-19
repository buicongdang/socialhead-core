<?php


class TestCommand extends \Tests\TestCase
{
    function callCheckShops()
    {
        \Illuminate\Support\Facades\Artisan::call();
        $this->assertTrue(\Illuminate\Support\Facades\File::exists(config_path('blogpackage.php')));
    }
}
