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

        $request->validate([
            'rating' => 'required|numeric|min:1|max:10',
            'title' => 'string|required',
            'preview' => 'string|nullable',
            'text' => 'string|nullable'
        ]);

        if (!Review::notDouble(Auth::id(), $id)) {
            return CustomJsonResponses::error_response('You\'re created review for this book !');
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
        $review = Review::query()->findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            return CustomJsonResponses::error_response('You are not author of this review', 401);
        }

        $review->delete();

        return response()->json([
            'status' => 'success'
        ] );
    }
}
