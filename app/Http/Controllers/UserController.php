<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;

class UserController extends Controller
{
    public function profile()
    {
        $user = User::query()
            ->with(['books', 'rates', 'followers', 'follows'])
            ->find(auth()->id());

        return response()->json([
            'user' => $user,
        ]);
    }
}
