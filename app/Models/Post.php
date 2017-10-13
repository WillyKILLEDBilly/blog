<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Like;

class Post extends Model
{
	protected $table = 'posts';

	protected $dates = ['created_at', 'updated_at'];

	public function likes()
	{
		return $this->hasMany('App\Models\Like');
	}

	public function activeLikes()
	{
		return $this->likes()->where('state',true);
	}
}