<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\Auth;

/**
 * Class User
 *
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /** @var int */
    const TYPE_NORMAL = 0;

    /** @var int */
    const TYPE_ADMIN = 1;

    /** @var bool */
    public $timestamps = true;

    /** @var string */
    protected $table = 'users';

    /** @var array */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'picture',
        'forgot_code',
        'forgot_generated'
    ];

    /** @var array */
    protected $hidden = [
        'password'
    ];

    /** @var array */
    protected $visible = [
        'id',
        'name',
        'email',
        'type',
        'picture',
        'created_at',
        'updated_at'
    ];

    /**
     * User boot
     */
    protected static function boot()
    {
        parent::boot();

        /** Delete all user associations */
        static::deleting(function ($user) {
            if ($user->userTokens) {
                foreach ($user->userTokens as $userToken) {
                    $userToken->delete();
                }
            }
        });
    }

    /**
     * User tokens
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userTokens()
    {
        return $this->hasMany('App\Models\UserToken', 'user_id', 'id');
    }

    public static function isAdmin()
    {
        $user = Auth::user();

        return $user->type;
    }
}
