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
            'user_id' => $this->user_id,
            'customer_name' => $this->name,
            'customer' => $this->name,
            'customer_phone' => $this->phone_number,
            'phone' => $this->phone_number,
            'delivery_address' => $this->fullAddress(),
            'address' => $this->address,
            'street_address' => $this->address,
            'city' => $this->town,
            'country' => $this->country,
            'full_address' => $this->fullAddress(),
            'subtotal' => $this->total_before_discount ?? $this->total_price,
            'delivery_fee' => $this->delivery_fee ?? 0,
            'delivery' => $this->delivery_fee ?? 0,
            'discount' => $this->discount_amount ?? 0,
            'coupon_id' => $this->coupon_id,
            'coupon_code' => $this->coupon?->code,
            'coupon' => $this->coupon ? [
                'id' => $this->coupon->id,
                'code' => $this->coupon->code,
                'type' => $this->coupon->type,
                'value' => $this->coupon->value,
                'generated_for' => $this->coupon->generated_for,
            ] : null,
            'total' => $this->total_price,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment' => $this->payment_method,
            'order_note' => $this->order_note,
            'source_platform' => $this->source_platform ?? 'web',
            'items' => $this->items ? $this->items->map(function ($item) {
                $product = $item->relationLoaded('product') ? $item->product : null;
                $englishTranslation = $product && $product->relationLoaded('translations')
                    ? $product->translations->firstWhere('locale', 'en')
                    : null;
                $arabicTranslation = $product && $product->relationLoaded('translations')
                    ? $product->translations->firstWhere('locale', 'ar')
                    : null;
                $name = $englishTranslation?->title ?? $product?->title ?? $item->name;
                $nameAr = $arabicTranslation?->title ?? $name;
                $description = $englishTranslation?->description ?? $product?->description;
                $descriptionAr = $arabicTranslation?->description ?? $description;
                $image = $product?->image ?? $item->image;

                return [
                    'id' => $item->id,
                    'order_id' => $item->checkout_id,
                    'checkout_id' => $item->checkout_id,
                    'product_id' => $item->product_id,
                    'name' => $name,
                    'name_ar' => $nameAr,
                    'description' => $description,
                    'description_ar' => $descriptionAr,
                    'image' => $image,
                    'image_url' => $this->assetUrl($image),
                    'quantity' => $item->quantity,
                    'qty' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total_price,
                    'total_price' => $item->total_price,
                    'product' => $product ? [
                        'id' => $product->id,
                        'name' => $name,
                        'name_ar' => $nameAr,
                        'title' => $name,
                        'title_ar' => $nameAr,
                        'description' => $description,
                        'description_ar' => $descriptionAr,
                        'price' => $product->price,
                        'image' => $this->assetUrl($image),
                    ] : null,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->all() : [],
            'date' => optional($this->created_at)->toDateTimeString(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    private function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'storage/')) {
            return url('api/media/' . $path);
        }

        return url('api/media/storage/' . $path);
    }

    private function fullAddress(): string
    {
        return collect([$this->address, $this->town, $this->country])
            ->filter(fn ($part) => filled($part))
            ->implode(', ');
    }
}






