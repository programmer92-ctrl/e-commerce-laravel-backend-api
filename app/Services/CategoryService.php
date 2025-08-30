<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService {

    public function store(array $data){

        $category = new Category($data);

        return $category;

    }

    public function show(string $id){

        $category = Category::findOrFail($id);

        return $category;

    }

    public function index(): LengthAwarePaginator {

        $category = Category::paginate(15);

        if ($category->isEmpty()) {
            throw (new ModelNotFoundException)->setModel(Category::class);
        }

        return $category;

    }

    public function delete(string $id){

        $category = Category::findOrFail($id);
        $category->delete();

        return $category;

    }

}