<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
	protected $table = 'post_likes';

	public $timestamps = false;
}