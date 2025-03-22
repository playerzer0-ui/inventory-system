<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FakerData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Fetch existing data
        $productCodes = DB::table('products')->pluck('productCode')->toArray();
        $storageCodes = DB::table('storages')->pluck('storageCode')->toArray();
        $truckNumbers = DB::table('trucks')->pluck('no_truk')->toArray();
        $vendorCodes = DB::table('vendors')->pluck('vendorCode')->toArray();
        $customerCodes = DB::table('customers')->pluck('customerCode')->toArray();

        // Orders
        $orderNumbers = [];
        foreach (range(1, 100) as $index) {
            $nomor_surat_jalan = $faker->unique()->bothify('SJ####');
            $orderNumbers[] = $nomor_surat_jalan;

            DB::table('orders')->insert([
                'nomor_surat_jalan' => $nomor_surat_jalan,
                'storageCode' => "APA",
                'no_LPB' => $faker->optional()->bothify('LPB####'),
                'no_truk_in' => $faker->optional()->bothify('TRUCK-IN####'),
                'no_truk_out' => $faker->optional()->randomElement($truckNumbers),
                'vendorCode' => $faker->randomElement($vendorCodes),
                'customerCode' => $faker->randomElement($customerCodes),
                'orderDate' => $faker->dateTimeBetween('2025-03-01', '2025-03-31')->format('Y-m-d'),
                'purchase_order' => $faker->bothify('PO####'),
                'status_mode' => $faker->numberBetween(1, 2),
                'delivered' => $faker->optional()->numberBetween(0, 1),
            ]);

            // Ensure each order has at least one product
            DB::table('order_products')->insert([
                'nomor_surat_jalan' => $nomor_surat_jalan,
                'repack_no_repack' => "-",
                'moving_no_moving' => "-",
                'PO_no_PO' => "-",
                'productCode' => $faker->randomElement($productCodes),
                'qty' => $faker->numberBetween(1, 100),
                'UOM' => $faker->randomElement(['kg', 'pcs', 'box', 'tray']),
                'price_per_UOM' => $faker->randomFloat(2, 10, 1000),
                'note' => $faker->optional()->sentence,
                'product_status' => 'in', // Assuming 'in' for order-related products
            ]);
        }

        // Movings
        $movingNumbers = [];
        foreach (range(1, 10) as $index) {
            $no_moving = $faker->unique()->bothify('MOVING####');
            $movingNumbers[] = $no_moving;

            DB::table('movings')->insert([
                'no_moving' => $no_moving,
                'moving_date' => $faker->dateTimeBetween('2025-03-01', '2025-03-31')->format('Y-m-d'),
                'storageCodeSender' => $faker->randomElement($storageCodes),
                'storageCodeReceiver' => $faker->randomElement($storageCodes),
            ]);

            // Ensure each moving has at least one product
            DB::table('order_products')->insert([
                'nomor_surat_jalan' => "-",
                'moving_no_moving' => $no_moving,
                'repack_no_repack' => "-",
                'PO_no_PO' => "-",
                'productCode' => $faker->randomElement($productCodes),
                'qty' => $faker->numberBetween(1, 100),
                'UOM' => $faker->randomElement(['kg', 'pcs', 'box', 'tray']),
                'price_per_UOM' => $faker->randomFloat(2, 10, 1000),
                'note' => $faker->optional()->sentence,
                'product_status' => 'moving',
            ]);
        }

        // Repacks
        $repackNumbers = [];
        foreach (range(1, 10) as $index) {
            $no_repack = $faker->unique()->bothify('REPACK####');
            $repackNumbers[] = $no_repack;

            DB::table('repacks')->insert([
                'no_repack' => $no_repack,
                'repack_date' => $faker->dateTimeBetween('2025-03-01', '2025-03-31')->format('Y-m-d'),
                'storageCode' => $faker->randomElement($storageCodes),
            ]);

            // Ensure each repack has at least one product
            DB::table('order_products')->insert([
                'nomor_surat_jalan' => "-",
                'moving_no_moving' => "-",
                'repack_no_repack' => $no_repack,
                'PO_no_PO' => "-",
                'productCode' => $faker->randomElement($productCodes),
                'qty' => $faker->numberBetween(1, 100),
                'UOM' => $faker->randomElement(['kg', 'pcs', 'box', 'tray']),
                'price_per_UOM' => $faker->randomFloat(2, 10, 1000),
                'note' => $faker->optional()->sentence,
                'product_status' => 'repack',
            ]);
        }

        // Invoices
        $invoiceNumbers = [];
        foreach (range(1, 10) as $index) {
            $choice = $faker->numberBetween(1, 2);

            $nomor_surat_jalan = '-';
            $no_moving = '-';

            if ($choice === 1) {
                $nomor_surat_jalan = $faker->randomElement($orderNumbers);
            } else {
                $no_moving = $faker->optional()->randomElement($movingNumbers);
            }

            $no_invoice = $faker->unique()->bothify('INV####');
            $invoiceNumbers[] = $no_invoice;

            DB::table('invoices')->insert([
                'nomor_surat_jalan' => $nomor_surat_jalan,
                'no_moving' => $no_moving,
                'invoice_date' => $faker->dateTimeBetween('2025-03-01', '2025-03-31')->format('Y-m-d'),
                'no_invoice' => $no_invoice,
                'no_faktur' => $faker->unique()->bothify('FAK####'),
                'tax' => $faker->randomFloat(2, 0, 100),
            ]);
        }

        // Payments
        $invoices = DB::table('invoices')->get();

        foreach (range(1, 10) as $index) {
            $invoice = $faker->randomElement($invoices);

            DB::table('payments')->insert([
                'nomor_surat_jalan' => $invoice->nomor_surat_jalan,
                'no_moving' => $invoice->no_moving,
                'payment_date' => $faker->dateTimeBetween('2025-03-01', '2025-03-31')->format('Y-m-d'),
                'payment_amount' => $faker->randomFloat(2, 100, 10000),
                'payment_id' => $faker->unique()->bothify('PAY####'),
            ]);
        }

        // Saldos
        foreach (range(1, 10) as $index) {
            DB::table('saldos')->insert([
                'productCode' => $faker->randomElement($productCodes),
                'storageCode' => $faker->randomElement($storageCodes),
                'totalPrice' => $faker->randomFloat(2, 100, 10000),
                'totalQty' => $faker->numberBetween(1, 100),
                'saldoMonth' => $faker->numberBetween(1, 12),
                'saldoYear' => $faker->numberBetween(2020, 2023),
            ]);
        }

        // Purchase Orders
        $purchaseOrderNumbers = [];
        foreach (range(1, 10) as $index) {
            $no_PO = $faker->unique()->bothify('PO####');
            $purchaseOrderNumbers[] = $no_PO;

            DB::table('purchase_orders')->insert([
                'no_PO' => $no_PO,
                'purchaseDate' => $faker->dateTimeBetween('2025-03-01', '2025-03-31')->format('Y-m-d'),
                'customerCode' => $faker->randomElement($customerCodes),
                'status_mode' => $faker->numberBetween(1, 2),
                'payIntent' => $faker->optional()->bothify('PAYINTENT####'),
            ]);

            // Ensure each purchase order has at least one product
            DB::table('order_products')->insert([
                'nomor_surat_jalan' => "-",
                'moving_no_moving' => "-",
                'repack_no_repack' => "-",
                'PO_no_PO' => $no_PO,
                'productCode' => $faker->randomElement($productCodes),
                'qty' => $faker->numberBetween(1, 100),
                'UOM' => $faker->randomElement(['kg', 'pcs', 'box', 'tray']),
                'price_per_UOM' => $faker->randomFloat(2, 10, 1000),
                'note' => $faker->optional()->sentence,
                'product_status' => $faker->randomElement(['purchase_order', 'purchase_approved']),
            ]);
        }

        // Additional random products for orders, movings, repacks, and purchase orders
        // foreach (range(1, 50) as $index) {
        //     $choice = $faker->numberBetween(1, 4);

        //     $nomor_surat_jalan = '-';
        //     $repack_no_repack = '-';
        //     $moving_no_moving = '-';
        //     $PO_no_PO = '-';
        //     $product_status = '';

        //     switch ($choice) {
        //         case 1: // Order-related
        //             $nomor_surat_jalan = $faker->randomElement($orderNumbers);
        //             $product_status = $faker->randomElement(['in', 'out']);
        //             break;
        //         case 2: // Repack-related
        //             $repack_no_repack = $faker->randomElement($repackNumbers);
        //             $product_status = 'repack';
        //             break;
        //         case 3: // Moving-related
        //             $moving_no_moving = $faker->randomElement($movingNumbers);
        //             $product_status = 'moving';
        //             break;
        //         case 4: // Purchase Order-related
        //             $PO_no_PO = $faker->randomElement($purchaseOrderNumbers);
        //             $product_status = $faker->randomElement(['purchase_order', 'purchase_approved']);
        //             break;
        //     }

        //     DB::table('order_products')->insert([
        //         'nomor_surat_jalan' => $nomor_surat_jalan,
        //         'repack_no_repack' => $repack_no_repack,
        //         'moving_no_moving' => $moving_no_moving,
        //         'PO_no_PO' => $PO_no_PO,
        //         'productCode' => $faker->randomElement($productCodes),
        //         'qty' => $faker->numberBetween(1, 100),
        //         'UOM' => $faker->randomElement(['kg', 'pcs', 'box', 'tray']),
        //         'price_per_UOM' => $faker->randomFloat(2, 10, 1000),
        //         'note' => $faker->optional()->sentence,
        //         'product_status' => $product_status,
        //     ]);
        // }
    }
}
