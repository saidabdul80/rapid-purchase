<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* `ussd`.`products` */
        $products = array(
            array('id' => '1','name' => 'Malt','price' => '4200.00','user_id' => '15','description' => NULL,'quantity' => '1','active' => '1','created_at' => '2023-05-16 05:19:26','updated_at' => '2023-05-16 05:19:31'),
            array('id' => '2','name' => 'Fanta','price' => '4000.00','user_id' => '15','description' => NULL,'quantity' => '1','active' => '1','created_at' => '2023-05-16 05:19:26','updated_at' => '2023-05-16 05:19:31'),
            array('id' => '3','name' => 'CocaCola','price' => '4000.00','user_id' => '15','description' => NULL,'quantity' => '1','active' => '1','created_at' => '2023-05-16 05:19:26','updated_at' => '2023-05-16 05:19:31')
        );
        Product::insert($products);
    }
}
