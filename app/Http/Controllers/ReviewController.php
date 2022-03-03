<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewCollection;
use App\Http\Resources\ReviewResource;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\CustomJsonResponses;

class ReviewController extends Controller
{
    public function index()
    {
        return new ReviewCollection(Review::all());
    }

    public function show($id)
    {
        return new ReviewResource(Review::query()->findOrFail($id));
    }


    public function store(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $book = Book::query()->findOrFail($id);

        $request->validate([
            'rating' => 'required|numeric|min:1|max:10',
            'title' => 'string|required',
            'preview' => 'string|nullable',
            'text' => 'string|nullable',
            'thumbnail' => 'mimes:jpeg,png|max:2048'
        ]);

        if (!Review::notDouble(Auth::id(), $id)) {
            return CustomJsonResponses::error_response('You\'re created review for this book !');
        }

        $review = Review::query()->create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            ...$request->all()
        ]);

        if ($file = $request->file('thumbnail')) {
            $path = $file->store('images/rw_thumbnails', 'public');

            $review->thumbnail = $path;

            $review->save();
        }

        Book::query()->update([
            'rating' => Review::query()->where('book_id', $id)->average('rating')
        ]);

        return response()->json([
            'status' => 'success',
            'review' => new ReviewResource($review),
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
        ]);
    }
}
