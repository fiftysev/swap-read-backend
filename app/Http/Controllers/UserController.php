<?php

namespace App\Http\Controllers;

use App\Models\Book;

class UserController extends Controller
{
    public function profile()
    {
        $user_id = auth()->user()->id;

        $books = Book::where('user_id', $user_id)->get();

        return response()->json([
            'user' => auth()->user(),
            'books' => $books
        ]);
    }
}
