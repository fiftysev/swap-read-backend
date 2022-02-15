<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function index()
    {
        return new BookCollection(Book::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'published_at' => 'numeric',
            'preview' => 'required|string',
            'description' => 'required|string'
        ]);

        Book::query()->create($request->all());

        return response()->json(['data' => [
            'status' => 'created'
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
