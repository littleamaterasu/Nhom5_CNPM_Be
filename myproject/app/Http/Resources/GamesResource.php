<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GamesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'name' => $this->name,
            'seller' => optional($this->user)->username,
            'price' => $this->price,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
//            'game_folder' => $this->game_folder,
            'upload_at' => $this->created_at,
        ];
    }
}
