<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressApiController extends Controller
{
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return AddressResource::collection($addresses);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAddress($request);

        $address = DB::transaction(function () use ($request, $validated) {
            if ($request->boolean('is_default')) {
                Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
            }

            $shouldBeDefault = $request->boolean('is_default')
                || !Address::where('user_id', $request->user()->id)->exists();

            return Address::create([
                ...$validated,
                'user_id' => $request->user()->id,
                'is_default' => $shouldBeDefault,
            ]);
        });

        return (new AddressResource($address))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->find($id);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $validated = $this->validateAddress($request, false);

        DB::transaction(function () use ($request, $address, $validated) {
            if ($request->boolean('is_default')) {
                Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
                $validated['is_default'] = true;
            }

            $address->update($validated);
        });

        return new AddressResource($address->refresh());
    }

    public function destroy(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->find($id);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $nextAddress = Address::where('user_id', $request->user()->id)->latest()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return response()->json(['message' => 'Address deleted successfully']);
    }

    public function setDefault(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->find($id);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        DB::transaction(function () use ($request, $address) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return new AddressResource($address->refresh());
    }

    private function validateAddress(Request $request, bool $creating = true): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'phone' => [$required, 'string', 'max:50'],
            'address' => [$required, 'string', 'max:500'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'country' => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_default' => ['sometimes', 'boolean'],
        ]);
    }
}
