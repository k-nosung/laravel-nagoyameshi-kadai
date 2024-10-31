<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name']; // カテゴリIDを追加

    public function category()
    {
        return $this->belongsToMany(Restaurant::class, 'category_restaurant');
    }
}
