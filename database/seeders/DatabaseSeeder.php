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
                'table' => 'roles',
                'data' => [
                    [
                        'accessibles' => json_encode(['sysop', 'viewdata', 'editdata'], JSON_UNESCAPED_UNICODE),
                        'name' => '超級管理員',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ], [
                        'accessibles' => json_encode(['viewdata'], JSON_UNESCAPED_UNICODE),
                        'name' => '見習共同編輯者',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ], [
                        'accessibles' => json_encode(['viewdata', 'editdata'], JSON_UNESCAPED_UNICODE),
                        'name' => '共同編輯者',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
            ], [
                'table' => 'skill_types',
                'data' => [
                    ['name' => '必殺技', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                    ['name' => '技能 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                    ['name' => '技能 2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                    ['name' => 'EX 技能', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                    ['name' => '必殺技 +', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                    ['name' => '專武強化技能 1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                    ['name' => 'EX 技能 +', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ],
            ], [
                'table' => 'users',
                'data' => [
                    [
                        'username' => 'administrator',
                        'password' => Hash::make('123'),
                        'nickname' => '超級管理員',
                        'role_of' => 1,
                        'status' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ],
            ], [
                'table' => 'versions',
                'data' => [
                    [
                        'version_id' => '0.0.1a',
                        'content' => json_encode(['建立專案'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-12 02:40:00'),
                        'updated_at' => Carbon::parse('2020-10-12 02:40:00'),
                    ], [
                        'version_id' => '0.0.2',
                        'content' => json_encode(['完成登入及註冊功能', '完成後台登入驗證'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-15 01:12:00'),
                        'updated_at' => Carbon::parse('2020-10-15 01:12:00'),
                    ], [
                        'version_id' => '0.0.3',
                        'content' => json_encode(['完成權限驗證', '審核頁面完成，功能尚未完成'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-16 02:35:00'),
                        'updated_at' => Carbon::parse('2020-10-16 02:35:00'),
                    ], [
                        'version_id' => '0.0.4',
                        'content' => json_encode(['完成審核頁面全部功能'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-17 03:15:00'),
                        'updated_at' => Carbon::parse('2020-10-17 03:15:00'),
                    ], [
                        'version_id' => '0.0.5',
                        'content' => json_encode(['完成新增角色資料功能', '修正部分登入系統行為', '修正驗證登入失敗時重新導向位址不正確問題'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-19 02:30:00'),
                        'updated_at' => Carbon::parse('2020-10-19 02:30:00'),
                    ], [
                        'version_id' => '0.0.6',
                        'content' => json_encode(['完成新增角色相關資料功能', '修正部分登出重新導向問題', 'API 清單試作'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-20 03:19:00'),
                        'updated_at' => Carbon::parse('2020-10-20 03:19:00'),
                    ], [
                        'version_id' => '0.0.7',
                        'content' => json_encode(['完成編輯角色相關資料功能', '修正頁腳 CSS 問題', '登入頁面密碼欄位按下 Enter 也會觸發登入事件', '調整登出連結在滑鼠指著時不會顯示底線'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-21 02:30:00'),
                        'updated_at' => Carbon::parse('2020-10-21 02:30:00'),
                    ], [
                        'version_id' => '0.0.8',
                        'content' => json_encode(['完成後台角色資料取用', '將部分 JS 函式搬移至全域 JS 中'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-23 03:34:00'),
                        'updated_at' => Carbon::parse('2020-10-23 03:34:00'),
                    ], [
                        'version_id' => '0.0.9',
                        'content' => json_encode(['新增伺服器狀態檢查功能', '完成角色資料編輯', '完成版本資料增刪修', '網頁用 API 與公共 API 路由前綴拆分'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-25 03:03:00'),
                        'updated_at' => Carbon::parse('2020-10-25 03:03:00'),
                    ], [
                        'version_id' => '0.0.10',
                        'content' => json_encode(['調整登入與註冊方式為傳統 Form 方式傳送資料', '新增 env 設定可以調整是否顯示後台管理連結', '後台新增返回前台連結'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-25 23:16:00'),
                        'updated_at' => Carbon::parse('2020-10-25 23:16:00'),
                    ], [
                        'version_id' => '0.0.11',
                        'content' => json_encode(['網頁與公共 API 完全拆分', 'API 清單頁面排版', '調整空資料時的返回資料型態'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2020-10-28 01:36:00'),
                        'updated_at' => Carbon::parse('2020-10-28 01:36:00'),
                    ], [
                        'version_id' => '0.0.12',
                        'content' => json_encode(['加入專武功能（尚未處理角色資料編輯）'], JSON_UNESCAPED_UNICODE),
                        'created_at' => Carbon::parse('2021-01-20 01:57:00'),
                        'updated_at' => Carbon::parse('2021-01-20 01:57:00'),
                    ]
                ],
            ],
        ];

        /**
         * 開始執行 Seed
         *
         * @link https://stackoverflow.com/questions/34034730/how-to-enable-color-for-php-cli PHP-CLI-Color
         */
        print("\033[33mSeeding: \033[39m開始執行 Seed。\n");

        $start = microtime(true);
        $times = 0;
        $rows = 0;
        foreach ($data as $d) {
            DB::table($d['table'])->insert($d['data']);
            $rows += count($d['data']);
            $times += 1;
        }

        $duration = round(microtime(true) - $start, 3);

        print("\033[32mSeeded: \033[39mSeed 執行完畢，總共耗時 $duration 秒，查詢 $times 次，影響 $rows 行。\n");
    }
}
