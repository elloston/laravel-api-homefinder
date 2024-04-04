<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeRequest;
use App\Http\Resources\HomeResource;
use App\Models\Home;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|nullable|string',
            'bedrooms' => 'sometimes|array',
            'bedrooms.*' => 'integer|min:1|max:10',
            'bathrooms' => 'sometimes|array',
            'bathrooms.*' => 'integer|min:1|max:10',
            'storeys' => 'sometimes|array',
            'storeys.*' => 'integer|min:1|max:10',
            'garages' => 'sometimes|array',
            'garages.*' => 'integer|min:1|max:10',
            'price.from' => 'sometimes|nullable|numeric',
            'price.to' => 'sometimes|nullable|numeric',
        ]);

        $homes = Home::query()
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            })
            ->when($request->filled('bedrooms'), function ($query) use ($request) {
                $query->whereIn('bedrooms', $request->input('bedrooms'));
            })
            ->when($request->filled('bathrooms'), function ($query) use ($request) {
                $query->whereIn('bathrooms', $request->input('bathrooms'));
            })
            ->when($request->filled('storeys'), function ($query) use ($request) {
                $query->whereIn('storeys', $request->input('storeys'));
            })
            ->when($request->filled('garages'), function ($query) use ($request) {
                $query->whereIn('garages', $request->input('garages'));
            })
            ->when($request->filled('price.from'), function ($query) use ($request) {
                $query->where('price', '>=', $request->input('price.from'));
            })
            ->when($request->filled('price.to'), function ($query) use ($request) {
                $query->where('price', '<=', $request->input('price.to'));
            })
            ->paginate(50);

        return HomeResource::collection($homes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HomeRequest $request)
    {
        $validatedData = $request->validated();

        $home = Home::create($validatedData);

        return response()->json(new HomeResource($home), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Home $home)
    {
        return response()->json(new HomeResource($home), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Home $home)
    {
        $validatedData = $request->validated();

        $home->update($validatedData);

        return response()->json(new HomeResource($home), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Home $home)
    {
        $home->delete();

        return response()->json(null, 204);
    }
}
