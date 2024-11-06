<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'description',
    ];
    public function categories() 
    {
        return $this->belongsToMany(Category::class, 'category_restaurant','restaurant_id', 'category_id');
    }
}
