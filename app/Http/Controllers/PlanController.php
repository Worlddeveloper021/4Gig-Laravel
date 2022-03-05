<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\Api\V1\PlanResource;

class PlanController extends Controller
{

    public function index(Request $request)
    {
        $plans = Plan::when($request->input('get_actives', 0) == 1, function($query) {
            $query->where('status', Plan::STATUS_ACTIVE);
        })->paginate();

        return response()->json(PlanResource::collection($plans)->response()->getData(true));
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            'status' => ['required', 'integer', Rule::in(Plan::STATUSES)],
        ]);

        $plan = Plan::create($validated_data);

        return response()->json(PlanResource::make($plan), 200);
    }

    public function update(Request $request, Plan $plan)
    {
        $validated_data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'duration' => 'sometimes|numeric',
            'status' => ['sometimes', 'integer', Rule::in(Plan::STATUSES)],
        ]);

        $plan->update($validated_data);

        return response()->json(PlanResource::make($plan), 200);
    }
}
