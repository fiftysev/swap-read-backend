<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    // TODO: Change resource usage to Eloquent serialization
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

        $book->user_id = $user_id;

        $book->save();

        return response()->json(['data' => [
            'status' => 'created',
            'book' => new BookResource($book),
        ]], 201);
    }

    public function show($id)
    {
        return new BookResource(Book::query()->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $book = Book::query()->find($id);

        if (!$book) {
            return response()->json(['data' =>
                ['status' => 'Book not found!']
            ], 404);
        }

        $book->delete();
        return response()->json(['data' =>
            ['status' => 'success'],
        ]);
    }
}
