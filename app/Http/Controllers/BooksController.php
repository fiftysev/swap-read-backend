<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BooksController extends Controller
{
    public function index(): BookCollection
    {
        return new BookCollection(Book::all());
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'published_at' => 'numeric',
            'preview' => 'required|string',
            'description' => 'required|string'
        ]);

        $book = Book::query()->create($request->all());

        $book->rating = 0.0;
        $book->user_id = Auth::id();

        $book->save();

        return response()->json([
            'status' => 'created',
            'book' => new BookResource($book),
        ], 201);
    }

    public function show($id)
    {
        return new BookResource(Book::query()->findOrFail($id));
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->findOrFail($id);

        if ($book->user_id !== Auth::id()) {
            abort(403, "You're not author of this book review!");
        }

        $request->validate([
            'title' => 'string',
            'preview' => 'string',
            'description' => 'string'
        ]);

        $book->update($request->all());

        return response()->json([
            'status' => 'success',
            'book' => new BookResource($book)
        ]);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found!'
            ], 404);
        }

        $book->delete();
        return response()->json([
            'status' => 'success'
        ]);
    }


}
