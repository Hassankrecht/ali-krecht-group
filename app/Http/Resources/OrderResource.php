<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Disable resource wrapping for single resource responses.
     */
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'items' => $this->items ? $this->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $item->total_price,
                ];
            })->all() : [],
        ];
    }
}
