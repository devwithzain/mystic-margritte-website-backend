<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'category' => $this->category,
            'image' => $this->image ? 'storage/' . $this->image : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'comments' => CommentResource::collection($this->comments)
        ];
    }
}