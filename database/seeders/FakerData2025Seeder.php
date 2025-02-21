<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Order_Product;
use Faker\Factory as Faker;

class FakerData2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Insert a single product
        $productCode = 'P' . $faker->unique()->numberBetween(1000, 9999);
        Product::insert([
            'productCode' => $productCode,
            'productName' => $faker->word,
            'productPrice' => 200.12
        ]);

        // Seed Orders Table (Only tracking "out" orders)
        $orders = [];
        for ($i = 0; $i < 50; $i++) {
            $orders[] = [
                'nomor_surat_jalan' => 'SJ' . $faker->unique()->numberBetween(1000, 9999),
                'storageCode' => 'NON', // No need for conditional check
                'no_LPB' => $faker->optional()->numerify('LPB#####'),
                'no_truk' => $faker->numerify('TRUCK#####'),
                'vendorCode' => 'NON',
                'customerCode' => 'TOM',
                'orderDate' => $faker->dateTimeBetween('2025-01-01', '2025-12-31')->format('Y-m-d'),
                'purchase_order' => $faker->numerify('PO#####'),
                'status_mode' => 2, // Always "out"
            ];
        }
        Order::insert($orders);

        // Fetch inserted order numbers
        $orderNumbers = Order::pluck('nomor_surat_jalan')->toArray();

        // Seed OrderProducts Table (One product, multiple orders, always "out")
        $orderProducts = [];
        foreach ($orderNumbers as $orderNumber) {
            $orderProducts[] = [
                'nomor_surat_jalan' => $orderNumber,
                'repack_no_repack' => "-",
                'moving_no_moving' => "-",
                'productCode' => $productCode,
                'qty' => $faker->numberBetween(1, 100),
                'UOM' => $faker->randomElement(['kg', 'pcs', 'box']),
                'price_per_UOM' => $faker->randomFloat(2, 1, 100),
                'note' => $faker->optional()->sentence,
                'product_status' => 'out', // Always "out"
            ];
        }
        Order_Product::insert($orderProducts);
    }
}