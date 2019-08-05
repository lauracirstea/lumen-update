<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'Produs1',
            'description' => "first",
            'category_id' => "1",
            'full_price' => "25",
            'photo' => "asdf",
            'quantity' => "2",
            'sale_price' => "23"

        ]);
    }
}
