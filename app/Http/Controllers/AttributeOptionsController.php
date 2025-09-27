<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttributeOptionService;
use Illuminate\Http\JsonResponse;

class AttributeOptionsController extends Controller
{
    //create attribute first id 1,2,3
    //then create attribute options linking to the attribute id 1 or 2 or 3
    //then create the Product sku service

    public function store(Request $request, AttributeOptionService $attributeOptionService) {

        $validatedData = $request->validate([
            'attribute_id' => 'required|numeric',
            'value' => 'required|string|max:255',
        ]);

        $attributeOption = $attributeOptionService->store($validatedData);

        return response()->json([
            'message' => 'Attribute Option added successfully!',
            'attribute' => $attributeOption,
        ]);

    }

    public function show(AttributeOptionService $attributeOptionService, string $id) {

        $attributeOption = $attributeOptionService->show($id);

        return response()->json([
            'message' => 'Attribute Option retrieved!',
            'attribute' => $attributeOption,
        ]);

    }
}