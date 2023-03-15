<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'venue' => $this->venue,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'hosts' => $this->eventHosts->map(function ($host) {
                return $host->user->name;
            }),
        ];
    }
}
