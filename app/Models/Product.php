<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * @package App\Models
 */
class Product extends Model
{
    protected $table = 'products';

    public $timestamps = true;

    public $fillable = [
        'name',
        'description',
        'category_id',
        'full_price',
        'photo',
        'quantity'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }
}
