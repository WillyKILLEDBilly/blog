<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Post;
use App\Models\Like;
use App\User;

class LikeController extends Controller
{
	public function update(Request $request,int $id)
	{
		$post = Post::findOrFail($id);
		$user = JWTAuth::parseToken()->authenticate();

		$like = Like::where('user_id', $user->id)
					->where('post_id', $post->id)
					->first();

		if (!$like) {
			$like = new Like;
			$like->user_id = $user->id;
			$like->post_id = $post->id;
		}
		else
			$like->state = !($like->state);

		$like->save();

		return response()->json([(int)$like->state,  Post::findOrFail($id)->activeLikes()->count()] ,200);
	}	
}