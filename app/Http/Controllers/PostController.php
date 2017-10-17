<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Post;
use Carbon\Carbon;
use App\User;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal;
use App\Transformers\PostTransformer;
use League\Fractal\Serializer\DataArraySerializer;
use App\Transformers\UserTransformer;

class PostController extends Controller
{
	/**
	 * Current authenticated user
	 * @var User
	 */
	protected $user;

	/**
	 * Current user post
	 * @var Post
	 */
	protected $post;

	/**
	 * Looking for posts
	 * @var PostCollection
	 */
	protected $posts;


	/**
	 * Trying to get a user from token
	 */
	public function __construct()
	{
		$this->user = JWTAuth::parseToken()->authenticate();
	}

	/**
	 * Adding a new post
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request)
	{
		$this->validate($request, $this->rules());

		$this->createPost($request->only('text', 'header'));

		return response()->json(['post_id' => $this->post->id], 201);
	}

	/**
	 * Updating post
	 * @param  Request $request
	 * @param  int     $id
	 * @return JsonResponse
	 */
	public function update(Request $request,int $id)
	{
		// trying to get a post
		if (!$this->findPost($id))
			return response()->json(['error' => 'not found'], 404);

		// check if post is own
		if (!$this->checkPermissions())
			return response()->json(['error' => 'no access'], 403);

		// check if not passed 5 mins from time of created
		if (!$this->enableToUpdate())
			return response(['error' => 'time out'], 403);

		// and recently validation
		$this->validate($request, $this->rules());

		$this->updatePost($request->only('text', 'header'));

		return response()->json(['post_id' => $this->post->id], 200);
	}

	/**
	 * Deleting post
	 * @param  int    $id
	 * @return JsonResponse
	 */
	public function destroy(int $id)
	{
		// trying to get a post
		if (!$this->findPost($id))
			return response()->json(['error' => 'not found'], 404);
		
		// check if post is own
		if (!$this->checkPermissions())
			return response()->json(['error' => 'no access'], 403);

		$this->post->delete();

		return response()->json(['post_id' => $this->post->id], 200);
	}

	/**
	 * Reading posts with most value of likes
	 * @param  Request 	$request
	 * @return JsonResponse
	 */
	public function readMostRated(Request $request)
	{
		// paginator parameters
		$currentCursor = $request->input('cursor', null);
		$limit = $request->input('limit', 20);

		$this->posts = Post::mostRated($limit, $currentCursor);

		return $this->readedPostsResponse();
	}

	/**
	 * Reading 20 random posts with likes>20
	 * @return JsonResponse
	 */
	public function readRandomRated()
	{
		$this->posts = Post::randomRated();

		return $this->readedPostsResponse();
	}

	/**
	 * Rulse for post
	 * @return array
	 */
	protected function rules()
	{
		return [
			'text' 	 => 'required|string|between:50,10000',
			'header' => 'required|string|between:4,255'
		];
	}

	/**
	 * Adding new Post
	 * @param  array $params
	 * @return Post
	 */
	protected function createPost(array $params)
	{
		$this->post = new Post;
		$this->post->text 	 = $params['text'];
		$this->post->header  = $params['header'];
		$this->post->user_id = $this->user->id;
		$this->post->save();
	}

	/**
	 * Trying to find post
	 * @param  int    $id
	 * @return bool
	 */
	protected function findPost(int $id)
	{
		$this->post = Post::find($id);
		return ($this->post!=null);
	}

	/**
	 * Cheking if post is own to current user
	 * @return bool
	 */
	protected function checkPermissions()
	{
		return ($this->user->id==$this->post->user_id);
	}

	/**
	 * Checking if update time does not out
	 * @return bool
	 */
	protected function enableToUpdate()
	{
		return (Carbon::now()->diffInMinutes($this->post->created_at)<5);
	}

	/**
	 * Updating post
	 * @param  array  $params
	 * @return void
	 */
	protected function updatePost(array $params)
	{
		$this->post->text = $params['text'];
		$this->post->header = $params['header'];
		$this->post->save();
	}

	protected function readedPostsResponse()
	{
		$resource = new Fractal\Resource\Collection($this->posts, new PostTransformer);
		$manager = new Manager();
		$manager->parseIncludes('user');
		$manager->setSerializer(new DataArraySerializer());

		return response()->json($manager->createData($resource)->toArray(), 201);
	}
}