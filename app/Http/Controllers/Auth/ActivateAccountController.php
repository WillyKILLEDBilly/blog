<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use JWTFactory;
use App\User;

class ActivateAccountController extends Controller
{
	protected $user;

	public function show(Request $request)
	{	
		if (!($request->has('email') && $request->has('token')))
			response()->json(['user no found'], 404);

		$this->userFromEmailToken($request->only('email','token'));

		if (!$this->user)
			return response()->json(['user no found'], 404);

		$this->user->email_confirmation_token = null;
		$this->user->reset_password_token 	= hash_hmac('sha256', str_random(40), env('APP_KEY'));
		$this->user->save();

		return view('auth.resetpassword', ['email'=>$this->user->email, 'token' => $this->user->reset_password_token]);
	}

	public function edit(Request $request)
	{
		$this->validate($request, [
			'password' 			 	=> 'required|between:6,26|confirmed',
			'password_confirmation' => 'required',
			'email'					=> 'required',
			'token'					=> 'required'
		]);

		$this->userFromPasswordToken($request->only('email','token'));

		if (!$this->user)
			return response()->json(['user no found'], 404);

		$this->user->reset_password_token = null;
		$this->user->password 			= app('hash')->make($request->password);
		$this->user->activated			= true;
		$this->user->save();

		return '<h1 align="center">password created</h1>';
	}

	protected function userFromEmailToken($credentials)
	{
		$this->user = User::where('email', $credentials['email'])
			->where('email_confirmation_token', $credentials['token'])
			->first();
	}

	protected function userFromPasswordToken($credentials)
	{
		$this->user = User::where('email', $credentials['email'])
			->where('reset_password_token', $credentials['token'])
			->first();
	}

}