<?php

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    /**
     * Date: 2019/12/8
     * @throws Exception
     * @author George
     */
    public function run()
    {
        $customers = [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'George',
                'email' => 'george@betterde.com',
                'mobile' => 18616882860,
                'balance' => 0,
                'province' => '上海市',
                'municipality' => '上海市',
                'password' => bcrypt('George@1994'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        DB::table('customers')->insert($customers);
    }
}
