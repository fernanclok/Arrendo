<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zones = [
            ['name' => 'Centro'],
            ['name' => 'Otay'],
            ['name' => 'Playas de Tijuana'],
            ['name' => 'Cerro Colorado'],
            ['name' => 'La Presa'],
            ['name' => 'La Mesa'],
            ['name' => 'San Antonio de las Buenas'],
            ['name' => 'Sanchéz Taboada'],
            ['name' => 'La Presa Rural'],
            ['name' => 'Sierra Tijuanense'],
        ];

        DB::table('zones')->insert($zones);
    }
}