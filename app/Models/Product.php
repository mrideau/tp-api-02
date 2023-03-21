<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    protected $guarded = [
        'slug',
        'thumbnail_filename',
        'category_id',
    ];

    protected $hidden = [
        'category_id',
        'pivot'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
