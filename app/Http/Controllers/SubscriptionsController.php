<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerCollection;
use App\Http\Resources\FollowsCollection;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function followers(): array
    {
        return Subscription::with('follower')
            ->where('follows', auth()->id())
            ->get('follower')
            ->toArray();
    }

    public function follows(): array
    {
        return Subscription::with('follows')
            ->where('follower', auth()->id())
            ->get('follows')
            ->toArray();
    }

    public function store($id)
    {
       User::query()->findOrFail($id);

       $exists_subscription = Subscription::notDouble(auth()->id(), $id);

       if (!$exists_subscription) {
           return response()->json([
               'status' => 'error',
               'message' => "You're already follows user with id $id"
           ], 400);
       }

       Subscription::query()->create([
           'follower' => auth()->id(),
           'follows' => $id
       ]);

       return response()->json([
           'status' => 'success'
       ]);
    }

    public function destroy($id)
    {
       User::query()->findOrFail($id);

       $exists_subscription = Subscription::query()
           ->where('follower', auth()->id())
           ->where('follows', $id)
           ->first();

       if (!$exists_subscription) {
           return response()->json([
               'status' => 'error',
               'message' => "You're not follow on user with id $id"
           ], 400);
       }

       return response()->json([
           'status' => 'success'
       ]);
    }
}
