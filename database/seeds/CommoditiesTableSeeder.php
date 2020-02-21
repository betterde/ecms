<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommoditiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $commodities = [
            [
                'id' => 1,
                'brand' => 'Chanel',
                'name' => '蔚蓝男士香水',
                'unit' => '瓶',
                'specification' => '50ml',
                'category' => '香水',
                'remark' => null,
                'image' => null,
                'barcode' => null,
                'amount' => 0,
                'description' => '蔚蓝男士香水系列迷人的木质芳香调，代表了对男性自由的礼赞。永恒经典的气息，蕴藏于深邃神秘的蓝色瓶身之中。',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ], [
                'id' => 2,
                'brand' => 'Chanel',
                'name' => '蔚蓝男士香水',
                'unit' => '瓶',
                'specification' => '100ml',
                'category' => '香水',
                'remark' => null,
                'image' => null,
                'barcode' => null,
                'amount' => 0,
                'description' => '蔚蓝男士香水系列迷人的木质芳香调，代表了对男性自由的礼赞。永恒经典的气息，蕴藏于深邃神秘的蓝色瓶身之中。',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        DB::table('commodities')->insert($commodities);
    }
}
