<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = [
        'name'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }
}
