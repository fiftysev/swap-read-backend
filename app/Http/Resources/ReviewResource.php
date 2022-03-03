<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Review */
class ReviewResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'book_id' => $this->book_id,
            'title' => $this->title,
            'rating' => $this->rating,
            'preview' => $this->preview,
            'text' => $this->text,
            'thumbnail' =>
                $this->thumbnail ? asset('storage/'.$this->thumbnail) : $this->thumbnail
        ];
    }
}
