<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image,
            'role' => $this->customRole->name,
            'regNo' => $this->memberDetails == null ? 'none' : $this->memberDetails->reg_no,
            'isProjectOwner' => $this->isProjectOwner,
            'areaOfInterest' => $this->memberDetails == null ? 'none' : $this->memberDetails->area_of_interest,
            'university' => $this->memberDetails == null ? 'none' : $this->memberDetails->university->name,
            'college' => $this->memberDetails == null ? 'none' : $this->memberDetails->college->name,
            'department' => $this->memberDetails == null ? 'none' : $this->memberDetails->department->name,
            'degreeProgramme' => $this->memberDetails == null ? 'none' : $this->memberDetails->degreeProgramme->name,
        ];
    }
}
