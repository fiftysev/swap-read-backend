<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Rate;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function index()
    {
        return new BookCollection(Book::all());
    }

    public function store(Request $request)
    {
        $user_id = auth()->user()->id;

        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'published_at' => 'numeric',
            'preview' => 'required|string',
            'description' => 'required|string'
        ]);

        $book = Book::query()->create($request->all());

        $book->rating = 0.0;
        $book->user_id = $user_id;

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

    public function update(Request $request, $id)
    {
        $book = Book::query()->findOrFail($id);

        if ($book->user_id !== auth()->user()->id) {
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

    public function destroy($id)
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

    public function rate(Request $request, $id)
    {
        $book = Book::query()->findOrFail($id);

        if ($book->user_id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can\'t rate your own book review!'
            ], 403);
        }

        $request->validate([
            'value' => 'required|numeric|min:1|max:10',
            'comment' => 'string|nullable'
        ]);


        $exists_feedback = Rate::notDouble(auth()->id(), $id);

        if (!$exists_feedback) {
            return response()->json([
                'status' => 'error',
                'message' => 'You\'re already rate this book review!'
            ], 400);
        }

        $feedback = Rate::query()->create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'value' => $request->value,
            'comment' => $request->comment
        ]);

        Book::query()->update([
            'rating' => Rate::query()->where('book_id', $id)->average('value')
        ]);

        return response()->json([
            'status' => 'success',
            'rate_object' => $feedback,
        ], 201);
    }
}
