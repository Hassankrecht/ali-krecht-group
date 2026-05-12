<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone_number,
            'phone_number' => $this->phone_number,
            'avatar' => $this->avatar,
            'country' => $this->country,
            'town' => $this->town,
            'city' => $this->town,
            'zipcode' => $this->zipcode,
            'postal_code' => $this->zipcode,
            'address' => $this->address,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


