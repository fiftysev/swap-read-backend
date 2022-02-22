<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @mixin \App\Models\Book */
class BookResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'rating' => $this->rating,
            'published_at' => $this->published_at,
            'preview' => $this->preview,
            'description' => $this->description,
            'updated_at' => $this->updated_at
        ];
    }
}
