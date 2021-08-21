<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('produk')->insert([
            [
                'nama' => 'Baju',
                'image' => 'noimage',
                'deskripsi' => 'baju aja',
                'jumlah' => 10,
                'harga' => 30000,
                'created_at' => Date('Y-m-d H:i:s'),
                'updated_at' => Date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'mainan',
                'image' => 'noimage',
                'deskripsi' => 'mainan aja',
                'jumlah' => 1,
                'harga' => 100000,
                'created_at' => Date('Y-m-d H:i:s'),
                'updated_at' => Date('Y-m-d H:i:s'),
            ],

        ]);
    }
}
