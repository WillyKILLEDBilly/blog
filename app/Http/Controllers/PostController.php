<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Post;
use Carbon\Carbon;

class PostController extends Controller
{
	protected $user;

	public function __construct()
	{
		$this->user = JWTAuth::parseToken()->authenticate();
	}

	public function store(Request $request)
	{
		$this->validate($request, $this->rules());

		$post = new Post;
		$post->text 	= $request->text;
		$post->header 	= $request->header;
		$post->user_id	= $this->user->id;
		$post->save();

		return response()->json(['post_id'=>$post->id], 200);
	}

	public function update(Request $request,int $id)
	{
		$post = Post::findOrFail($id);

		if ($this->user->id!=$post->user_id)
			return response('no access', 404);

		if (Carbon::now()->diffInMinutes($post->created_at)>5)
			return response('time out', 404);

		$this->validate($request, $this->rules());

		$post->text = $request->text;
		$post->header = $request->header;
		$post->save();

		return response()->json(['success'=>'changes saved', 'post_id' => $post->id], 200);
	}

	public function destroy(int $id)
	{
		$post = Post::findOrFail($id);

		if ($this->user->id==$post->user_id)
		{
			$post->delete();
			return response()->json(['success'=>'post deleted', 'post_id' => $post->id], 200);
		}

	}

	protected function rules()
	{
		return [
			'text' 	 => 'required|string|between:50,10000',
			'header' => 'required|string|between:4,255'
		];
	}
}