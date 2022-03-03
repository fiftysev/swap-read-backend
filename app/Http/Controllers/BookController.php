<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CustomJsonResponses;
use App\Http\Resources\BookCollection;
use App\Models\Book;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        return new BookCollection(Book::all());
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'published_at' => 'numeric',
            'description' => 'required|string',
            'ISBN' => 'string',
            'thumbnail' => 'nullable|mimes:jpeg,png|max:2048'
        ]);

        $book = Book::query()->create($request->all());

        if ($file = $request->file('thumbnail')) {
            $path = $file->store('images/covers', 'public');

            $book->thumbnail = $path;
        }

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
            return CustomJsonResponses::error_response('You\'re not author of post with id'.$id, 403);
        }

        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'published_at' => 'numeric',
            'description' => 'required|string',
            'ISBN' => 'string',
            'thumbnail' => 'mimes:jpeg,png|max:2048'
        ]);

        if ($file = $request->file('thumbnail')) {
            Storage::disk('public')->delete($book->thumbnail);

            $path = $file->store('images/covers', 'public');

            $book->thumbnail = $path;
        }

        $book->update($request->all());

        return response()->json([
            'status' => 'success',
            'book' => $book
        ]);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->findOrFail($id);

        // TODO: Add admin rights check

        $book->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function reviews($id) {
        return Book::query()->findOrFail($id)->reviews;
    }
}
