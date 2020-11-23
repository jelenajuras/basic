<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\AbsenceCronMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\EmailingController;

class Employee_absence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:absence_day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Izostanci';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(AbsenceCronMail $abs)
    {
		$send_to = EmailingController::sendTo('absences','cron');
		        
        foreach($send_to as $send_to_mail) {
			if( $send_to_mail != null & $send_to_mail != '' ) {
                Mail::to($send_to_mail)->send(new AbsenceCronMail()); // mailovi upisani u mailing 
            }
		}

    }
}