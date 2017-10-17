<?php

namespace App\Mails;

use Illuminate\Mail\Mailable;
use App\User;

class AccountConfirmation extends Mailable
{
	/**
	 * User instance
	 * @var User
	 */
	protected $user;

	/**
	 * Constructor
	 * @param User $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Building email sctructure before send
	 * @return Mailable
	 */
	public function build()
	{
    	return $this->to($this->user->email)
    			->from('willykilledbilly@gmail.com')
                ->view('email.register_confirmation')
                ->with([
                    'token' => $this->user->email_confirmation_token,
                    'email' => $this->user->email
                ]);
	}
}