<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorApplicationRequest;
use App\Http\Resources\VendorApplicationResource;
use App\Models\VendorApplication;
use App\Services\VendorApplicationService;
use Illuminate\Http\Request;

class VendorApplicationController extends Controller
{
    public function store(VendorApplicationRequest $request, VendorApplicationService $service)
    {
        if ($request->user()->role === 'admin' || $request->user()->role === 'vendor') {
            return response()->json(['message' => 'This account cannot submit another seller application.'], 409);
        }
        $application = $service->submit($request->user(), $request->validated());

        return (new VendorApplicationResource($application))->response()->setStatusCode(201);
    }

    public function mine(Request $request)
    {
        $application = VendorApplication::where('user_id', $request->user()->id)->firstOrFail();

        return new VendorApplicationResource($application);
    }
}
