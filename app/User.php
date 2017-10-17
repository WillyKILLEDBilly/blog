<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'activated', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }

    /* public function posts(int $currentCursor = null,int $limit)
    {
        if ($currentCursor)
            return $this->hasMany('App\Models\Post')
                        ->offset($currentCursor)
                        ->limit($limit);
        else
            return $this->hasMany('App\Models\Post')
                        ->limit($limit);
    }*/

    public static function pagination($currentCursor, $limit, $activated)
    {
        if ($currentCursor) {
            $users = User::where(function ($query) use ($activated, $currentCursor){
                        if($activated!=null)
                            $query->where('activated', (bool)$activated);
                    })
                    ->offset($currentCursor)
                    ->take($limit)
                    ->get();
        } else {
            $users = User::where(function ($query) use ($activated){
                        if($activated!=null)
                            $query->where('activated', (bool)$activated);
                    })
                    ->take($limit)
                    ->get();
        }
        return $users;
    }
}
