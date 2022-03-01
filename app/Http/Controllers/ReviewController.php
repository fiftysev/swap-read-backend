<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\CustomJsonResponses;

class ReviewController extends Controller
{
    public function index($id)
    {
        return Review::query()->find($id);
    }

    public function store(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->findOrFail($id);

        if ($book->user_id === Auth::id()) {
            return CustomJsonResponses::error_response('You can\'t rate your own book review!', 403);
        }

        $request->validate([
            'rating' => 'required|numeric|min:1|max:10',
            'title' => 'string|required',
            'preview' => 'string|nullable',
            'text' => 'string|nullable'
        ]);


        $exists_feedback = Review::notDouble(Auth::id(), $id);

        if (!$exists_feedback) {
            return CustomJsonResponses::error_response('You\'re already rate this book review!');
        }

        $feedback = Review::query()->create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            ...$request->all()
        ]);

        Book::query()->update([
            'rating' => Review::query()->where('book_id', $id)->average('rating')
        ]);

        return response()->json([
            'status' => 'success',
            'rate_object' => $feedback,
        ], 201);
    }

    public function destroy($id)
    {
        $rate_obj = Review::query()
            ->where('user_id', Auth::id())
            ->where('book_id', $id)
            ->firstOrFail();

        $rate_obj->delete();

        return response()->json([
            'status' => 'success'
        ], 201);
    }
}
