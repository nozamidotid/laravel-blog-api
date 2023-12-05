<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private function getCategoryById(int $id) : Category 
    {
        $category = Category::query()->find($id);
        if (!$category) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "not found!"
                    ]
                ]
            ], 404));
        }

        return $category;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $category = Category::query()->get();

        return (CategoryResource::collection($category))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $category = new Category($data);
        $category->slug = str($data["name"])->slug(); // laravel blog API => laravel-blog-api
        $category->save();

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): CategoryResource
    {
        $category = $this->getCategoryById($id);

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): CategoryResource
    {
        $category = $this->getCategoryById($id);
        $name = $request->input('name');

        $category->name = str($name)->title();
        $category->slug = str($name)->slug();
        $category->save();

        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = $this->getCategoryById($id);
        
        $category->delete();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
