<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketStatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'day' => $this->resource['day'] ?? null,
            'week' => $this->resource['week'] ?? null,
            'month' => $this->resource['month'] ?? null,
        ];
    }
}

