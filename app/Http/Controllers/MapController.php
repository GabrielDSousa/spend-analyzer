<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteMapRequest;
use App\Http\Requests\ReadMapRequest;
use App\Http\Requests\StoreMapRequest;
use App\Models\Map;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ReadMapRequest $request
     * @return void
     */
    public function index(ReadMapRequest $request)
    {
        return Map::latest()
            ->filter(request(['bank', 'type']))
            ->paginate(18)
            ->withQueryString();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMapRequest $request
     * @return JsonResponse
     */
    public function store(StoreMapRequest $request)
    {
        return response()->json(["map" => (new Map)->create([
            'bank' => $request->get('bank'),
            'type' => $request->get('type'),
            'date' => $request->get('date'),
            'date_format' => $request->get('date_format'),
            'amount' => $request->get('amount'),
            'description' => $request->get('description'),
            'user_id' => auth()->user()->id ?? null
        ])], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param ReadMapRequest $request
     * @param Map $map
     * @return JsonResponse
     */
    public function show(ReadMapRequest $request, Map $map)
    {
        return response()->json(["map" => $map], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteMapRequest $request
     * @param Map $map
     * @return JsonResponse
     */
    public function destroy(DeleteMapRequest $request, Map $map)
    {
        $map->delete();

        return response()->json(["message" => "Success."], 200);
    }
}
