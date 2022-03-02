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
        'ISBN',
        'thumbnail'
    ];

    protected $casts = [
      'rating' => 'double',
      'published_at' => 'integer'
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'book_id');
    }
}
