<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "user_id" => $this->user_id,
            "conversation_id" => $this->conversation_id,
            "message" => $this->content,
            "data_insert" => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            "data_edit" => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
