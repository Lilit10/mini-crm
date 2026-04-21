<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateTicketAction;
use App\Actions\GetTicketStatisticsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketStatisticsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketController extends Controller
{
    public function store(StoreTicketRequest $request, CreateTicketAction $action): JsonResponse
    {
        $ticket = $action->execute($request->validated());

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    public function statistics(GetTicketStatisticsAction $action): JsonResource
    {
        return new TicketStatisticsResource($action->execute());
    }
}
