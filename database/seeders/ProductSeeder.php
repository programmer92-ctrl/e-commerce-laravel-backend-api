<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('products')->insert([
            'title' => 'Jordans',
            'desc' => 'Maroon 6s',
            'price' => 100.00,
            'photo' => 'images/jordans6.jpg',
        ]);

        DB::table('products')->insert([
            'title' => 'Rolex',
            'desc' => 'Gold 16k watch',
            'price' => 1200.00,
            'photo' => 'images/rolex.jpg',
        ]);

    }
}
