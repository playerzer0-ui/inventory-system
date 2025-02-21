<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NeutralValues extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert('insert into storages values (?, ?, ?, ?)', ["NON", "none", "none", "001.111.111.111-111"]);
        DB::insert('insert into vendors values (?, ?, ?, ?)', ["NON", "none", "none", "001.111.111.111-111"]);
        DB::insert('insert into customers values (?, ?, ?, ?, ?, ?)', ["NON", "none", "none", "none", "none", "001.111.111.111-111"]);
        DB::insert('insert into purchase_orders values (?, ?, ?)', ["-", null, "NON"]);

        DB::insert('insert into orders values (?, ?, ?, ?, ?, ?, ?, ?, ?)', ["-", "NON", "-", "-", "NON", "NON", null, "-", "0"]);
        DB::insert('insert into repacks values (?, ?, ?)', ["-", null, "NON"]);
        DB::insert('insert into movings values (?, ?, ?, ?)', ["-", null, "NON", "NON"]);
        
        DB::table('users')->insert([
            [
                'userID' => '37d72912-5ad0-11ef-b5d1-5cbaef99b658',
                'email' => 'playerzero745@gmail.com',
                'password' => '$2y$10$I6HDp20xfQ.eyexX6Xu0XOmiCwmPmVGf7WuNTF6LApGFg0kxVcbIG',
                'userType' => 1,
            ],
            [
                'userID' => '3de53767-5ad0-11ef-b5d1-5cbaef99b658',
                'email' => 'cydacnote@gmail.com',
                'password' => '$2y$10$5Ymv4R2Qn3Fw/8FKRJxHmu5XAmO2G0mfXTK2naenRcsssZuvPBLVa',
                'userType' => 0,
            ],
        ]);
    }
}
