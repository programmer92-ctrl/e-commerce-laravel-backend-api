<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    //

    public function store(StoreCategoryRequest $request, CategoryService $category): JsonResponse {

        $category = $category->store($request->validated());

        return response()->json([
            'message' => 'Category added successfully',
            'category' => $category,
        ]);
    }

    public function show(CategoryService $category, string $id): JsonResponse {

        $category = $category->show($id);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function index(CategoryService $category): JsonResponse {

        $category = $category->index();

        return response()->json([
            'message' => 'Categories',
            'category' => $category,
        ]);

    }

    public function delete(CategoryService $category, string $id){

        $category = $category->delete($id);

        return response()->json([
            'message' => 'Category deleted successfully',
            'category' => $category,
        ]);

    }

}
