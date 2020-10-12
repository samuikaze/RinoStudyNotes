<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'table' => 'users',
                'data' => [
                    [
                        'role_of' => 1,
                        'username' => 'administrator',
                        'password' => Hash::make('123'),
                        'nickname' => '超級管理員',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ],
            ], [
                'table' => 'versions',
                'data' => [
                    [
                        'version_id' => '0.0.1a',
                        'content' => json_encode(['專案建置中'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ],
            ],
        ];

        /**
         * 開始執行 Seed
         * 
         * @link https://stackoverflow.com/questions/34034730/how-to-enable-color-for-php-cli PHP-CLI-Color
         */
        print("\033[33mSeeding: \033[39m開始執行 Seed。\n");

        $times = 0;
        $rows = 0;
        foreach ($data as $d) {
            DB::table($d['table'])->insert($d['data']);
            $rows += count($d['data']);
            $times += 1;
        }

        print("\033[32mSeeded: \033[39mSeed 執行完畢。\n");
    }
}
