<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return AddressResource::collection($request->user()->addresses()->latest()->get());
    }

    public function store(AddressRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['country'] ??= 'India';
            if (($data['is_default'] ?? false) || ! $request->user()->addresses()->exists()) {
                $request->user()->addresses()->update(['is_default' => false]);
            }
            $data['is_default'] = $data['is_default'] ?? ! $request->user()->addresses()->exists();

            return (new AddressResource($request->user()->addresses()->create($data)))->response()->setStatusCode(201);
        });
    }

    public function update(AddressRequest $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        return DB::transaction(function () use ($request, $address) {
            $data = $request->validated();
            if ($data['is_default'] ?? false) {
                $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }
            $address->update($data);

            return new AddressResource($address->refresh());
        });
    }

    public function destroy(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $address->delete();

        return response()->json(['message' => 'Address deleted.']);
    }
}
