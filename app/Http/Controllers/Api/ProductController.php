<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('jwt.verify');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::mine()->simplePaginate($request->input('perPage', 15));
        return response()->json([
            'data' => $products
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        $request->validated();
        $product = Product::create(array_merge($request->all(), ['user_id' => auth()->user()->id]));
        return response()->json([
            'data' => $product
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, mixed $id): JsonResponse
    {
        $data = Product::mine()->find($id);
        if (!$data) {
            return response()->json(['message' => 'Data not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        return response()->json(['data' => $data]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, mixed $id): JsonResponse
    {
        $data = Product::mine()->find($id);

        if (!$data) {
            return response()->json(['message' => 'Data not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $filtered = Arr::except($request->all(), ['created_at', 'updated_at']);
        $data->update($filtered);
        $data->refresh();

        return response()->json(['data' => $data]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $id): JsonResponse
    {
        $data = Product::mine()->find($id);
        if (!$data) {
            return response()->json(['message' => 'Data not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data->delete();
        return response()->json(['message' => 'Data deleted']);
    }
}
