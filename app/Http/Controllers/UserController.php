<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        $user = User::query()
            ->with(['books', 'reviews', 'followers', 'follows'])
            ->find(Auth::id());

        return response()->json([
            'user' => $user,
        ]);
    }
}
