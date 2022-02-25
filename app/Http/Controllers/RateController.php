<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    public function store(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->findOrFail($id);

        if ($book->user_id === Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can\'t rate your own book review!'
            ], 403);
        }

        $request->validate([
            'value' => 'required|numeric|min:1|max:10',
            'comment' => 'string|nullable'
        ]);


        $exists_feedback = Rate::notDouble(Auth::id(), $id);

        if (!$exists_feedback) {
            return response()->json([
                'status' => 'error',
                'message' => 'You\'re already rate this book review!'
            ], 400);
        }

        $feedback = Rate::query()->create([
            'user_id' => Auth::id(),
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

    public function destroy($id)
    {
        $rate_obj = Rate::query()
            ->where('user_id', Auth::id())
            ->where('book_id', $id)
            ->firstOrFail();

        $rate_obj->delete();

        return response()->json([
            'status' => 'success'
        ], 201);
    }
}
