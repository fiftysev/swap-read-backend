<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'value',
        'comment'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book() {
        return $this->belongsTo(Book::class,'book_id');
    }

    /**
     * Scope a query to check not double feedback
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param integer $user_id
     * @param integer $book_id
     * @return bool
     */
    public function scopeNotDouble($query, $user_id, $book_id) {
        return $query->where('user_id', $user_id)->where('book_id', $book_id)->doesntExist();
    }
}
