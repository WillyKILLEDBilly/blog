<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\MailDispatcher;
use App\Mails\AccountConfirmation;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use JWTFactory;
use DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal;
use App\Transformers\UserTransformer;
use League\Fractal\Serializer\DataArraySerializer;

class UsersController extends Controller
{
	/**
	 * Instance of user
	 * @var User
	 */
	protected $user;

	/**
	 * Instances of user
	 * @var UserCollection
	 */
	protected $users;

	/**
	 * Adding a new user
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request)
	{
		$this->validate($request, $this->rules());
		
		// creating a new user
		DB::beginTransaction();
		$this->createUser($request->only('email', 'name'));

		// send a list to email with account confirmation
       	try {
       		$this->sendConfirmation();
       	}
		catch (\GuzzleHttp\Exception\ClientException $e)
		{
			DB::rollBack();
			return response()->json(['error' => 'emails can only be mailgun registerd users'], 404);
		}

		DB::commit();
		return $this->createdUserReponse();
	}

	/**
	 * Reall all user, all activated
	 * and all not activated
	 * @param  Request $request contains users filter
	 * @return JsonResponse
	 */
	public function readAll(Request $request)
	{
		// paginator parameters
		$currentCursor = $request->input('cursor', null);
		$limit = $request->input('limit', 20);
		// activated or not user parameter
		$activated = $request->input('activated', null);
		// constain paginated users
		$this->users = User::pagination($currentCursor, $limit, $activated);

		return $this->readedUsersResponse();
	}

	/**
	 * Creating a new user
	 * @param  array $credentials
	 * @return User
	 */
	protected function createUser(array $credentials)
	{
		$this->user = new User;
		$this->user->email = $credentials['email'];
		$this->user->name  = $credentials['name'];
		$this->user->email_confirmation_token = hash_hmac('sha256', str_random(40), env('APP_KEY'));
		$this->user->save();
		// cuz we need updated timestamps
		$this->user = User::findOrFail($this->user->id);
	}

	/**
	 * Sends an email list with account confirmation
	 * @return void
	 */
	protected function sendConfirmation()
	{
		$mail = new AccountConfirmation($this->user);
		app('queue')->push(new MailDispatcher($mail));
	}

	/**
	 * User validation rules
	 * @return array
	 */
	protected function rules()
	{
		return [
			'email' => 'required|unique:users|email|max:32',
			'name'	=> 'required|string|between:3,20',
		];
	}

	/**
	 * Response with last created user in JSON
	 * @return JsonResponse
	 */
	protected function createdUserReponse()
	{
		$resource = new Fractal\Resource\Item($this->user, new UserTransformer);
		$manager = new Manager();
		$manager->setSerializer(new DataArraySerializer());
		return response()->json($manager->createData($resource)->toArray(), 201);
	}

	/**
	 * Response with readed users in JSON
	 * @return JsonResponse
	 */
	protected function readedUsersResponse()
	{
		$resource = new Fractal\Resource\Collection($this->users, new UserTransformer);
		$manager = new Manager();
		$manager->setSerializer(new DataArraySerializer());
		return response()->json($manager->createData($resource)->toArray(), 201);
	}
}