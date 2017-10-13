<?php

namespace App\Fractal;

use App\User;
use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
	public function transform(User $user)
	{
	    return [
		    'id'  	   	 => (int) $user->id,
	        'name'  	 => $user->name,
	        'email'  	 => $user->email,
	        'registered' => $user->created_at,
	        'activated'  => $user->activated
	    ];
	}
}