<?php

namespace Tests\Unit\Controllers\v1\Api;

use App\Http\Controllers\v1\Api\CharacterController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use stdClass;
use Tests\TestCase;

class CharacterControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * CharacterController
     *
     * @var \App\Http\Controllers\Api\CharacterController
     */
    protected $characterController;

    /**
     * 注入 Controller
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->characterController = $this->app->make(CharacterController::class);
    }

    /**
     * 測試陣列內部的鍵名
     *
     * @param array $keys 鍵名陣列
     * @param array $data 待測資料
     * @return void
     */
    protected function testArrayDataKeys(array $keys, array $data)
    {
        if (count($data) > 0) {
            foreach ($data as $d) {
                $d = array_keys($d);
                foreach ($keys as $key) {
                    $this->assertContains($key, $d, '資料未包含 '.$key.' 鍵');
                }
            }
        }
    }

    /**
     * 測試角色清單的取得
     *
     * 測試目標：
     * 此方法正常一定返回 200 OK，有資料就是 JSON 格式的物件，無資料時是 null
     *
     * @return void
     */
    public function testCharacterListTest()
    {
        $response = $this->characterController->characterList();

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue(
            (is_null($response->getContent()) || is_array($data)),
            '返回值不為 null 或不為標準 JSON 格式陣列'
        );

        $this->testArrayDataKeys(['id', 'tw_name', 'jp_name'], $data);
    }

    /**
     * 測試角色資料
     *
     * 測試目標：
     * 此方法正常一定返回 200 OK，有資料就是 JSON 格式的物件，無資料時是 null
     *
     * @return void
     */
    public function testCharacterInfoTest()
    {
        // 測試以 Body 傳入
        // ID
        $request = new Request();
        $request->merge(['id' => 1]);
        $response = $this->characterController->characterInfo($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getContent() == null || is_array(json_decode($response->getContent(), true)));

        // 暱稱或名稱
        $request = new Request();
        $request->merge(['nickname' => '妹弓']);
        $response = $this->characterController->characterInfo($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getContent() == null || is_array(json_decode($response->getContent(), true)));

        // 測試以 URL 傳入
        // ID
        $request = new Request();
        $response = $this->characterController->characterInfo($request, 1);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(
            (is_null($response->getContent()) || json_decode($response->getContent()) instanceof stdClass),
            '返回的資料不為 null 或不為標準 JSON 格式'
        );

        // 暱稱或名稱
        $response = $this->characterController->characterInfo($request, '妹弓');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(
            (is_null($response->getContent()) || json_decode($response->getContent()) instanceof stdClass),
            '返回的資料不為 null 或不為標準 JSON 格式'
        );
    }

    /**
     * 測試公會清單的取得
     *
     * 測試目標：
     * 此方法正常一定返回 200 OK，不論有無資料都是返回標準 JSON 格式陣列。
     * 如果有資料需額外驗證是不是包含有 id 和 name 的鍵名
     *
     * @return void
     */
    public function testGuildListTest()
    {
        $response = $this->characterController->guildList();
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->testArrayDataKeys(['id', 'name'], $data);
    }

    /**
     * 測試所有技能種類的取得
     *
     * 測試目標：
     * 此方法正常一定返回 200 OK，不論有無資料都是返回標準 JSON 格式陣列。
     * 如果有資料需額外驗證是不是包含有 id 和 name 的鍵名
     *
     * @return void
     */
    public function testSkillTypeListTest()
    {
        $response = $this->characterController->skillTypeList();
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->testArrayDataKeys(['id', 'name'], $data);
    }

    /**
     * 測試所有聲優資料的取得
     *
     * 測試目標：
     * 此方法正常一定返回 200 OK，不論有無資料都是返回標準 JSON 格式陣列。
     * 如果有資料需額外驗證是不是包含有 id 和 name 的鍵名
     *
     * @return void
     */
    public function testCvListTest()
    {
        $response = $this->characterController->CVList();
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->testArrayDataKeys(['id', 'name'], $data);
    }

    /**
     * 測試所有種族資料的取得
     *
     * 測試目標：
     * 此方法正常一定返回 200 OK，不論有無資料都是返回標準 JSON 格式陣列。
     * 如果有資料需額外驗證是不是包含有 id 和 name 的鍵名
     *
     * @return void
     */
    public function testRaceListTest()
    {
        $response = $this->characterController->raceList();
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->testArrayDataKeys(['id', 'name'], $data);
    }
}
