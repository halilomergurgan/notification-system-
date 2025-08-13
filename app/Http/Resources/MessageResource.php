<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'phone_number' => $this->recipient->full_phone_number ?? null,
            'content' => $this->personalized_content ?? $this->message->content ?? null,
            'status' => $this->status,
            'message_id' => $this->message_id,
            'sent_at' => $this->sent_at ? $this->sent_at->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
