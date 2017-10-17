<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTFactory;
use JWTAuth;

class LoginController extends Controller
{
	/**
	 * Handle a admin login request for the application
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function login(Request $request)
	{
		$this->validate($request, [
			'email' 	=> 'required|email',
			'password' 	=> 'required'
		]);
		//checking if exists admin with current credentials
		if (!$this->verify($request->only('email', 'password')))
			//respond error
			return response()->json(['error' => 'user not found'], 404);
		//respond token
		return response()->json(['token' => $this->getToken()], 200);
	}

	/**
	 * Verifying admin login and pass
	 * @param  array $credentials
	 * @return bool
	 */
	protected function verify(array $credentials)
	{
		$email 	  = env('ADMIN_EMAIL');
		$password = env('ADMIN_PASSWORD');

		return  ($credentials ['email'] == $email && 
				$credentials ['password'] == $password);
	}

	/**
	 * Creating a new token for admin
	 * @return string
	 */
	protected function getToken()
	{
		$payload = JWTFactory::sub('ADMIN')->make();
    	return JWTAuth::encode($payload)->get();
	}
}