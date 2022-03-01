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
        'ISBN'
    ];

    protected $casts = [
      'rating' => 'double'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'book_id');
    }
}
