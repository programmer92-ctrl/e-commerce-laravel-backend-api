<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttributeService;
use Illuminate\Http\JsonResponse;

class AttributeController extends Controller
{

    public function store(Request $request, AttributeService $attributeService) {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        $attribute = $attributeService->store($validatedData);

        return response()->json([
            'message' => 'Attribute added successfully!',
            'attribute' => $attribute,
        ]);

    }

    public function show(AttributeService $attributeService, string $id) {

        $attribute = $attributeService->show($id);

        return response()->json([
            'message' => 'Attribute retrieved!',
            'attribute' => $attribute,
        ]);

    }
}
