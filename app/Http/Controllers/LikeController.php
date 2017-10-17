<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Post;
use App\Models\Like;
use App\User;

class LikeController extends Controller
{
	/**
	 * Liking post
	 * @var Post
	 */
	protected $post;

	/**
	 * Current like instance
	 * @var Like
	 */
	protected $like;

	/**
	 * Authenticated user
	 * @var User
	 */
	protected $user;

	/**
	 * Setting current user
	 */
	public function __construct()
	{
		$this->user = JWTAuth::parseToken()->authenticate();
	}

	/**
	 * Liking post
	 * @param  Request $request
	 * @param  int     $id      post id
	 * @return JsonResponse
	 */
	public function store(Request $request,int $id)
	{
		if (!$this->findPost($id))
			return response()->json(['error' => 'not found'], 404);

		// check if like exists
		if ($this->findLike()) 
			// update current
			$this->updateLike();
		else
			// create new
			$this->createLike();

		return $this->storedLikeResponse();
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
	 * Trying to find like to a current post
	 * made by authenticated user
	 * @return bool
	 */
	protected function findLike()
	{
		$this->like = Like::where('user_id', $this->user->id)
						->where('post_id', $this->post->id)
						->first();
		return ($this->like!=null);
	}

	/**
	 * Creating new Like
	 * @return void
	 */
	protected function createLike()
	{
		$this->like = new Like;
		$this->like->user_id = $this->user->id;
		$this->like->post_id = $this->post->id;
		$this->like->save();
	}

	/**
	 * Updating current like,
	 * sets the inverse value
	 * @return void
	 */
	protected function updateLike()
	{
		$this->like->state = !($this->like->state);
		$this->like->save();
	}

	/**
	 * Success stored like response
	 * @return JsonResponse
	 */
	protected function storedLikeResponse()
	{
		$result = [
			'like_state' => (bool)$this->like->state,
			'active_likes_count' => $this->post->activeLikes()->count()
		];
		return response()->json($result, 201);
	}
}