<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'published_at',
        'description',
        'rating',
        'user_id',
        'count_of_rates',
        'ISBN',
        'thumbnail'
    ];

    protected $casts = [
      'rating' => 'double'
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'book_id');
    }
}
