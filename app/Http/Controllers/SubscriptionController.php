<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CustomJsonResponses;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function followers(): array
    {
        return Subscription::with('follower')
            ->where('follows', Auth::id())
            ->get('follower')
            ->toArray();
    }

    public function follows(): array
    {
        return Subscription::with('follows')
            ->where('follower', Auth::id())
            ->get('follows')
            ->toArray();
    }

    public function store($id)
    {
       User::query()->findOrFail($id);

       $exists_subscription = Subscription::notDouble(Auth::id(), $id);

       if (!$exists_subscription) {
           return CustomJsonResponses::error_response("You're already follows user with id $id");
       }

       Subscription::query()->create([
           'follower' => Auth::id(),
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
           ->where('follower', Auth::id())
           ->where('follows', $id)
           ->first();

       if (!$exists_subscription) {
           return CustomJsonResponses::error_response("You're not follow on user with id $id");
       }

       return response()->json([
           'status' => 'success'
       ]);
    }
}
