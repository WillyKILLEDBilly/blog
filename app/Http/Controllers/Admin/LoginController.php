<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTFactory;
use JWTAuth;

class LoginController extends Controller
{
	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'email' 	=> 'required|email',
			'password' 	=> 'required'
		]);

		if (!$this->verify($request->only('email', 'password')))
			return response('', 404);

		return response($this->getToken(), 200);
	}

	protected function verify(array $credentials )
	{
		$email 	  = env('ADMIN_EMAIL');
		$password = env('ADMIN_PASSWORD');

		return  ($credentials ['email'] == $email && 
				$credentials ['password'] == $password);
	}

	protected function getToken()
	{
		$payload = JWTFactory::sub('ADMIN')->make();
    	return JWTAuth::encode($payload);
	}
}