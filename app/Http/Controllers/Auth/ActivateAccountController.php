<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use JWTFactory;
use App\User;

class ActivateAccountController extends Controller
{
	/**
	 * User instance
	 * @var User
	 */
	protected $user;

	/**
	 * Displaying reset password form
	 * @param  Request $request
	 * @return View/404
	 */
	public function show(Request $request)
	{	
		if (!($request->has('email') && $request->has('token')))
			return response('404 not found', 404);

		$this->userFromEmailToken($request->only('email','token'));

		if (!$this->user)
			return response('404 not found', 404);

		$this->updateUserTokens();		

		return view('auth.reset_password', ['email'=>$this->user->email, 'token' => $this->user->reset_password_token]);
	}

	/**
	 * Setting new password
	 * @param  Request $request
	 * @return View/404
	 */
	public function updatePassword(Request $request)
	{
		$this->validate($request, [
			'password' 			 	=> 'required|between:6,26|confirmed',
			'password_confirmation' => 'required',
			'email'					=> 'required',
			'token'					=> 'required'
		]);

		$this->userFromPasswordToken($request->only('email','token'));

		if (!$this->user)
			return response('404 not found', 404);

		$this->updateUserPassword($request->only('password'));

		return view('auth.reset_password_success');
	}

	/**
	 * Getting user from email and email token
	 * @param  array $credentials
	 * @return void
	 */
	protected function userFromEmailToken($credentials)
	{
		$this->user = User::where('email', $credentials['email'])
			->where('email_confirmation_token', $credentials['token'])
			->first();
	}

	/**
	 * Getting user from email and reset password token
	 * @param  array $credentials
	 * @return void
	 */
	protected function userFromPasswordToken($credentials)
	{
		$this->user = User::where('email', $credentials['email'])
			->where('reset_password_token', $credentials['token'])
			->first();
	}

	/**
	 * Switching token
	 * @return void
	 */
	protected function updateUserTokens()
	{
		$this->user->email_confirmation_token = null;
		$this->user->reset_password_token 	  = hash_hmac('sha256', str_random(40), env('APP_KEY'));
		$this->user->save();
	}

	/**
	 * Update user password, activated state
	 * @param  array  $params
	 * @return void
	 */
	protected function updateUserPassword(array $params)
	{
		$this->user->reset_password_token = null;
		$this->user->password 			  = app('hash')->make($params['password']);
		$this->user->activated			  = true;
		$this->user->save();
	}
}