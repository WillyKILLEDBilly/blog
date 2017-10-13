<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use JWTFactory;
use DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal;
use App\Fractal\UserTransformer;
use League\Fractal\Serializer\DataArraySerializer;

class UsersController extends Controller
{
	public function store(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|unique:users|email|max:32',
			'name'	=> 'required|string|between:3,20',
		]);

		DB::beginTransaction();

		$user = new User;
		$user->email = $request->email;
		$user->name  = $request->name;
		$user->email_confirmation_token = hash_hmac('sha256', str_random(40), env('APP_KEY'));
		$user->save();

		try{
	        Mail::send('email.confirmation', 
	        	['token' => $user->email_confirmation_token, 'email' => $user->email], 
	        	function ($m) use ($user) {
				$m->to($user->email, $user->name)->subject('Account activation!');
			});
		}
		catch(Exeption $e){
			DB::rollBack();
			JWTAuth::invalid($token);
			return response('', 404);
		}

		DB::commit();
		return response('Created', 201);	
	}

	public function getAll(Request $request)
	{

		$currentCursor = $request->input('cursor', null);
		$previousCursor = $request->input('previous', null);
		$limit = $request->input('limit', 10);

		$activated = $request->input('activated', null);

		if ($currentCursor) {
		    $users = User::where(function ($query) use ($activated){
						if($activated!=null)
							$query->where('activated', (bool)$activated)
							->where('id', '>', $currentCursor);
						else
							$query->where('id', '>', $currentCursor);
					})
		    		->take($limit)
		    		->get();
		} else {
		    $users = User::where(function ($query) use ($activated){
						if($activated!=null)
							$query->where('activated', (bool)$activated);
					})
		    		->take($limit)
		    		->get();
		}

		$resource = new Fractal\Resource\Collection($users, new UserTransformer);
		$manager = new Manager();
		$manager->setSerializer(new DataArraySerializer());
		
		return $manager->createData($resource)->toArray();
	}
}