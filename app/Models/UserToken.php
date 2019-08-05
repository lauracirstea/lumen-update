<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserToken
 *
 * @package App\Models
 */
class UserToken extends Model
{
    /** @var int */
    const TYPE_REMEMBER = 0;

    /** @var bool */
    public $timestamps = true;

    /** @var string */
    protected $table = 'user_tokens';

    /** @var array */
    protected $fillable = [
        'user_id',
        'token',
        'type',
        'expire_at'
    ];

    /** @var array */
    protected $visible = [

    ];

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
