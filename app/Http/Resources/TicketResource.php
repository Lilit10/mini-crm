<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ticket
 */
class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'customer' => [
                'id' => $this->resource->customer?->id,
                'name' => $this->resource->customer?->name,
                'phone_e164' => $this->resource->customer?->phone_e164,
                'email' => $this->resource->customer?->email,
            ],
            'subject' => $this->resource->subject,
            'message' => $this->resource->message,
            'status' => $this->resource->status?->value ?? $this->resource->status,
            'manager_replied_at' => $this->resource->manager_replied_at?->toISOString(),
            'created_at' => $this->resource->created_at?->toISOString(),
            'attachments' => $this->resource->getMedia('attachments')->map(fn ($media) => [
                'file_name' => $media->file_name,
                'url' => $media->getUrl(),
            ])->values()->all(),
        ];
    }
}
