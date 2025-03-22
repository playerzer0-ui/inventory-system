<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(NeutralValues::class);
        $this->call(TestSeed::class);
        $this->call(FakerData2025Seeder::class);
        $this->call(FakerData::class);
    }
}
