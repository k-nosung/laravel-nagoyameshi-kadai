<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable; 

class Restaurant extends Model
{
    use HasFactory;
    use Sortable;  // Sortableトレイトを使用
    protected $fillable = [
        'name',
        'description',
        'lowest_price',
        'highest_price',
        'postal_code',
        'address',
        'opening_time',
        'closing_time',
        'seating_capacity',
        // 他の必要なフィールドも追加
    ];
     // 定義可能なカスタムソート
    public $sortable = [
        'rating', 'popular'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }
    public function regular_holidays() {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
    public function popularSortable($query, $direction) {
        return $query->withCount('reservations')->orderBy('reservations_count', $direction);
    }
}
