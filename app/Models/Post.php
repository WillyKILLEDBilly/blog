<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Like;
use DB;

class Post extends Model
{
	protected $table = 'posts';

	protected $dates = ['created_at', 'updated_at'];

	public function likes()
	{
		return $this->hasMany('App\Models\Like');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function activeLikes()
	{
		return $this->likes()->where('state',true);
	}

	public static function mostRated(int $limit, int $currentCursor = null)
	{
		if ($currentCursor)
			$subQuery = DB::table('post_likes')->selectRaw('count(*) as count, post_id')
							->groupBy('post_id')
							->orderBy('count','DESC')
							->offset($currentCursor)
							->limit($limit);
		else
			$subQuery = DB::table('post_likes')->selectRaw('count(*) as count, post_id')
							->groupBy('post_id')
							->orderBy('count','DESC')
							->limit($limit);
		return self::fromLikes($subQuery);
	}

	public static function randomRated()
	{
		$subQuery = DB::table('post_likes')->selectRaw('count(*) as count, post_id')
							->groupBy('post_id')
							->havingRaw('`count` > 20')
							->inRandomOrder()
							->limit(20);
		return self::fromLikes($subQuery);
	}

	protected static function fromLikes($subQuery)
	{
		return Post::from(DB::raw("`posts`, ({$subQuery->toSql()}) as `likes_count`"))
						->whereRaw('posts.id = likes_count.post_id')
						->get();
	}
}