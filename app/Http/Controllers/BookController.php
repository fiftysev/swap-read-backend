<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index()
    {
        return Book::all();
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'published_at' => 'numeric',
            'description' => 'required|string'
        ]);

        $book = Book::query()->create($request->all());

        $book->user_id = Auth::id();

        $book->save();

        return response()->json([
            'status' => 'created',
            'book' => $book,
        ], 201);
    }

    public function show($id)
    {
        return Book::query()->findOrFail($id);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->findOrFail($id);

        if ($book->user_id !== Auth::id()) {
            return error_response('You\'re not author of post with id'.$id, 403);
        }

        $request->validate([
            'title' => 'string',
            'description' => 'string'
        ]);

        $book->update($request->all());

        return response()->json([
            'status' => 'success',
            'book' => $book
        ]);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->find($id);

        if (!$book) {
            return error_response('Book not found!', 404);
        }

        $book->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function reviews($id) {
        return Book::findOrFail($id)->reviews;
    }
}
