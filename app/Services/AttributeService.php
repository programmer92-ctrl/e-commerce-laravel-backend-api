<?php

namespace App\Services;

use App\Models\Attribute;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class AttributeService {

    public function store(array $data): Attribute {

        $attribute = Attribute::create($data);
        
        if($attribute) {

            return $attribute;

        } else {

            throw new Exception('Cannot leave attribute empty!');

        }
    }

    public function show(string $id): Attribute {

        return Attribute::findOrFail($id);

    }

}