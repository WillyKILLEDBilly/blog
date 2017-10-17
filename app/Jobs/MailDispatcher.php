<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class MailDispatcher extends Job
{
	/**
	 * Abstraction of sending instance
	 * @var Mailable
	 */
	protected $mail;

	/**
     * Create a new job instance.
     * @param Mailable $mail
     * @return void
     */
    public function __construct(Mailable $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	Mail::send($this->mail);
    }
}