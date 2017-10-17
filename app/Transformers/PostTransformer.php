<?php

namespace App\Transformers;

use App\Models\Post;
use League\Fractal;
use App\Transformers\UserTransformer;

class PostTransformer extends Fractal\TransformerAbstract
{
	/**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'user'
    ];

	public function transform(Post $post)
	{
	    return [
		    'id'  	   	 => (int) $post->id,
	        'text'  	 => $post->text,
	        'header'  	 => $post->header,
	        'created_at' => $post->created_at,
	        'likes'		 => $post->activeLikes->count()
	    ];
	}

	/**
     * Include Author
     *
     * @return League\Fractal\ItemResource
     */
    public function includeUser(Post $post)
    {
        $user = $post->user;

        return $this->item($user, new UserTransformer);
    }
}