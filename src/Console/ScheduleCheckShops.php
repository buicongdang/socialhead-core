<?php


namespace Socialhead\Core\Console;


use Illuminate\Console\Command;

class ScheduleCheckShops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socialhead:schedule-check-shops';

    protected $description = 'Install the BlogPackage';


    public function handle()
    {
        print('schedule ');
        return false;
    }
}
