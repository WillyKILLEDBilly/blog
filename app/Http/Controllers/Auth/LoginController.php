<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;

class LoginController extends Controller
{
    /**
     * User login
     * @param  Request $request
     * @return JsonReponse
     */
	public function login(Request $request)
	{
		$this->validate($request, [ 
			'email' 	=> 'required|exists:users',
			'password'	=> 'required'
		]);

        if(!$this->accountActivated($request->email))
            return response()->json(['error' => 'account not activated'], 404);

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
	}

    /**
     * Check if account with current email is activated
     * @param  string $email
     * @return bool
     */
    protected function accountActivated(string $email)
    {
        $user = User::where('email', $email)->first();
        return $user->activated;
    }
}