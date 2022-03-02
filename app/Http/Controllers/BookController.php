<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CustomJsonResponses;
use App\Http\Resources\BookCollection;
use App\Models\Book;
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

        // TODO: Create env vars using for filepaths

        if ($file = $request->file('thumbnail')) {
            $filename = $book->title.'#'.$book->id.'cover.'.$file->extension();
            $filename = preg_replace('/\s+/', '_', $filename);

            $destination_path = 'public/files/thumbnails/covers';

            $book->thumbnail = $filename;

            Storage::putFileAs($destination_path, $file, $filename);
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

        // TODO: Create env vars using for filepaths

        if ($file = $request->file('thumbnail')) {
            Storage::delete('public/files/thumbnails/covers'.$book->thumbnail);

            $filename = $book->title.'#'.$book->id.'cover.'.$file->extension();
            $destination_path = 'public/files/thumbnails/covers';

            $book->thumbnail = $destination_path;

            Storage::putFileAs($destination_path, $file, $filename);
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
