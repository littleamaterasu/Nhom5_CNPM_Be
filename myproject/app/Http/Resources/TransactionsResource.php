<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionsResource extends JsonResource
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
            'user_name' => optional($this->user)->username,
            'game' => optional($this->game)->name,
            'transaction_amount' => $this->transaction_amount,
            'transaction_bank' => $this->transaction_bank
        ];
    }
}
