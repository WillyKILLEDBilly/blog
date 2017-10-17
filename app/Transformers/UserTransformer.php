<?php

namespace App\Transformers;

use App\User;
use League\Fractal;
use League\Fractal\ParamBag;
use App\Transformers\PostTransformer;

class UserTransformer extends Fractal\TransformerAbstract
{
	protected $availableIncludes = [
        'posts'
    ];

	public function transform(User $user)
	{
	    return [
		    'id'  	   	 => (int) $user->id,
	        'name'  	 => $user->name,
	        'email'  	 => $user->email,
	        'created_at' => $user->created_at,
	        'activated'  => (bool)$user->activated
	    ];
	}

	public function includePosts(User $user)
	{
		$posts = $user->posts;

		return $this->collection($posts, new PostTransformer);
	}
}