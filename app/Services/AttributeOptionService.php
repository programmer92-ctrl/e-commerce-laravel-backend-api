<?php

namespace App\Services;

use App\Models\AttributeOption;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class AttributeOptionService {

    public function store(array $data): AttributeOption {

        $attributeOption = AttributeOption::create($data);
        
        if($attributeOption) {

            return $attributeOption;

        } else {

            throw new Exception('Cannot leave attribute empty!');

        }
    }

    public function show(string $id): AttributeOption {

        return AttributeOption::findOrFail($id);

    }

}