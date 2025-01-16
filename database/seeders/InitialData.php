<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO 
        `storages` (`storageCode`, `storageName`, `storageAddress`, `storageNPWP`) 
        VALUES
        ('APA', 'Agraprana Paramitha Amartya', 'jalan agraprana', '003.111.111.111-111'),
        ('BBB', 'Berkah Berbagi Berkat', 'jalan Berkah Berbagi', '005.111.111.111-111'),
        ('CBA', 'Catur Berkat Amartya', 'jalan catur berkat', '002.111.111.111-111')");

        DB::insert("INSERT INTO `vendors` (`vendorCode`, `vendorName`, `vendorAddress`, `vendorNPWP`) VALUES
        ('ASTRA', 'As astra', 'jln astralll', '100.90.111.111-111'),
        ('COC', 'Coca', 'jln coca', '100.111.111.199-111')");

        DB::insert("INSERT INTO `products` (`productCode`, `productName`) VALUES
        ('MK-100-BB', 'Mild Kapsul 100 Blueberry'),
        ('MM-100-A', 'Mild Mono 100 Acetatow'),
        ('MM-120-A', 'Mild Mono 120 Acetatow')");

        DB::insert("INSERT INTO `customers` (`customerCode`, `customerName`, `customerAddress`, `customerNPWP`) VALUES
        ('DED', 'dedi', 'jln', '123.111.111.111-111'),
        ('TOM', 'tomi', 'jln seen', '123.112.111.111-111'),
        ('ZEN', 'zeno', 'jln asdf', '123.001.111.111-111')");
    }
}
