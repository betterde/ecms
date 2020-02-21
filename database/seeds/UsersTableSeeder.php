<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 用户数据填充器
 *
 * Date: 2019/12/7
 * @author George
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'name' => 'George',
                'email' => 'george@betterde.com',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => bcrypt('George@1994'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ], [
                'id' => 2,
                'name' => 'Echo',
                'email' => 'echo@betterde.com',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => bcrypt('Echo@1997'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        DB::table('users')->insert($users);
    }
}
