<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
            'rating' => $this->rating,
            'ISBN' => $this->ISBN,
            'published_at' => $this->published_at,
            'updated_at' => $this->updated_at,
            'thumbnail' =>
               $this->thumbnail ? asset('storage/'.$this->thumbnail) : $this->thumbnail
        ];
    }
}
