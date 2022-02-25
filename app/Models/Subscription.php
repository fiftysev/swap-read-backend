<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static notDouble(int|string|null $id, $id1)
 */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    public $timestamps = false;

    protected $fillable = [
        'follower',
        'follows'
    ];

    public function follower(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'follower');
    }

    public function follows(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'follows');
    }


    /**
     * Scope a query to check not double follow
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param integer $follower_id
     * @param integer $follows_id
     * @return bool
     */
    public function scopeNotDouble(\Illuminate\Database\Eloquent\Builder $query, int $follower_id, int $follows_id) {
        return $query->where('follower', $follower_id)->where('follows', $follows_id)->doesntExist();
    }
}
