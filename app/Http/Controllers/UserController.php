<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use League\Fractal\ParamBag;
use Illuminate\Http\Request;
use App\User;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal;
use League\Fractal\Serializer\DataArraySerializer;
use App\Transformers\UserTransformer;

class UserController extends Controller
{
	/**
	 * Reading user instance
	 * @var User
	 */
	protected $user;

	/**
	 * Read user by id
	 * @param  Request $request
	 * @param  int     $id
	 * @return JsonResponse
	 */
	public function read(Request $request, int $id)
	{
		if (!$this->findUser($id))
			return response()->json(['error' => 'not found'], 404);

		// load resource
		$resource = new Fractal\Resource\Item($this->user, new UserTransformer);
		$manager = new Manager();

		// checkong if need to include something to user
		if ($request->has('include'))
			$manager->parseIncludes($request->include);

		// setting serializer type
		$manager->setSerializer(new DataArraySerializer());

		// response
		return response()->json($manager->createData($resource)->toArray(), 200);
	}

	/**
	 * Trying to find user by id
	 * @param  int    $id
	 * @return bool
	 */
	protected function findUser(int $id)
	{
		$this->user = User::find($id);
		return ($this->user!=null);
	}
}