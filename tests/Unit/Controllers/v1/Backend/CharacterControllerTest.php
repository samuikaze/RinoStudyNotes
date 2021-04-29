<?php

namespace Tests\Unit\Controllers\v1\Backend;

use App\Http\Controllers\v1\Backend\CharacterController;
use App\Models\CV;
use App\Models\Guild;
use App\Models\Race;
use App\Models\SkillType;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class CharacterControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * CharacterController
     *
     * @var \App\Http\Controllers\Backend\CharacterController
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
     * 測試新增角色
     *
     * @return void
     */
    public function testAddCharacterTest()
    {
        $request = new Request();
        $request->merge([
            'tw_name' => '測試角色',
            'jp_name' => 'テストキャラ',
            'cv_of' => 1,
            'race_of' => 1,
            'description' => '測試描述',
            'ages' => 18,
            'height' => 160,
            'weight' => 50,
            'nicknames' => [
                '暱稱1',
            ],
            'likes' => [
                '測試喜好',
            ],
            'birthday' => '2021-01-01T00:00:00Z',
            'guild_of' => 1,
            'blood_type' => 'A',
            's_image_url' => null,
            'f_image_url' => null,
            't_image_url' => null,
            'skills' => [
                [
                    'skill_type_of' => 1,
                    'skill_name' => 'test',
                    'description' => 'testDesc',
                    'effect' => '',
                ],
            ],
        ]);

        $response = $this->characterController->addCharacter($request);
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getOriginalContent() > 0);
    }

    /**
     * 測試編輯角色
     *
     * @return void
     */
    public function testEditCharacterTest()
    {
        // 先新增測試資料
        $originalData = [
            'tw_name' => '測試角色',
            'jp_name' => 'テストキャラ',
            'cv_of' => 1,
            'race_of' => 1,
            'description' => '測試描述',
            'ages' => 18,
            'height' => 160,
            'weight' => 50,
            'nicknames' => [
                '暱稱1',
            ],
            'likes' => [
                '測試喜好',
            ],
            'guild_of' => 1,
            'blood_type' => 'A',
            'birthday' => '2021-01-01T00:00:00Z',
            's_image_url' => null,
            'f_image_url' => null,
            't_image_url' => null,
            'skills' => [
                [
                    'skill_type_of' => 1,
                    'skill_name' => 'test',
                    'description' => 'testDesc',
                    'effect' => '',
                ],
            ],
        ];
        $request = new Request();
        $request->merge($originalData);

        $id = $this->characterController->addCharacter($request);

        $originalData['id'] = $id->getOriginalContent();
        $originalData['tw_name'] = '測試編輯中文名稱';
        $request = new Request();
        $request->merge($originalData);
        $response = $this->characterController->editCharacter($request);
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * 測試以角色 id 取得角色資料
     *
     * @return void
     */
    public function testCharacterInfoTest()
    {
        // 先新增測試資料
        $request = new Request();
        $request->merge([
            'tw_name' => '測試角色',
            'jp_name' => 'テストキャラ',
            'cv_of' => 1,
            'race_of' => 1,
            'description' => '測試描述',
            'ages' => 18,
            'height' => 160,
            'weight' => 50,
            'nicknames' => [
                '暱稱1',
            ],
            'likes' => [
                '測試喜好',
            ],
            'birthday' => '2021-01-01T00:00:00Z',
            'guild_of' => 1,
            'blood_type' => 'A',
            's_image_url' => null,
            'f_image_url' => null,
            't_image_url' => null,
            'skills' => [
                [
                    'skill_type_of' => 1,
                    'skill_name' => 'test',
                    'description' => 'testDesc',
                    'effect' => '',
                ],
            ],
        ]);

        $response = $this->characterController->addCharacter($request);
        $id = $response->getOriginalContent();

        // 開始測試
        unset($request, $response);
        $response = $this->characterController->characterInfo($id);
        $this->assertNotNull($response);
        $this->assertEquals($id, $response->getOriginalContent()['id']);
    }

    /**
     * 測試取得公會清單
     *
     * @return void
     */
    public function testGuildListTest()
    {
        $response = $this->characterController->guildList();
        $this->assertNotNull($response);
        $this->assertNotEmpty($response->getOriginalContent());
    }

    /**
     * 測試取得所有技能清單
     *
     * @return void
     */
    public function testSkillTypeListTest()
    {
        $response = $this->characterController->skillTypeList();
        $this->assertNotNull($response);
        $this->assertNotEmpty($response->getOriginalContent());
    }

    /**
     * 測試取得所有聲優清單
     *
     * @return void
     */
    public function testCVListTest()
    {
        $response = $this->characterController->CVList();
        $this->assertNotNull($response);
        $this->assertNotEmpty($response->getOriginalContent());
    }

    /**
     * 測試取得所有種族清單
     *
     * @return void
     */
    public function testRaceListTest()
    {
        $response = $this->characterController->raceList();
        $this->assertNotNull($response);
        $this->assertNotEmpty($response->getOriginalContent());
    }

    /**
     * 測試新增聲優、公會、種族、技能種類資料
     *
     * @return void
     */
    public function testAddRelatedData()
    {
        $request = new Request();
        $request->merge(['name' => '測試名稱']);

        // 測試聲優
        $data = 'cv';
        $response = $this->characterController->addRelatedData($request, $data);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response->getOriginalContent()));

        // 測試公會
        unset($response);
        $data = 'guild';
        $response = $this->characterController->addRelatedData($request, $data);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response->getOriginalContent()));

        // 測試種族
        unset($response);
        $data = 'race';
        $response = $this->characterController->addRelatedData($request, $data);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response->getOriginalContent()));

        // 測試技能種類
        unset($response);
        $data = 'skilltype';
        $response = $this->characterController->addRelatedData($request, $data);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response->getOriginalContent()));
    }

    /**
     * 測試編輯聲優、公會、種族、技能種類資料
     *
     * @return void
     */
    public function testEditRelatedData()
    {
        // 測試聲優
        // 先新增測試資料
        $request = new Request();
        $request->merge(['name' => '測試名稱']);
        $data = 'cv';
        $response = $this->characterController->addRelatedData($request, $data);
        $id = $response->getOriginalContent();
        unset($request, $response);
        $request = new Request();
        $request->merge([
            'id' => $id,
            'name' => '測試聲優'
        ]);
        try {
            $this->characterController->editRelatedData($request, $data);
            $row = CV::where('id', $id)->where('name', $request->input('name'))->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
        }

        unset($id, $request, $data);

        // 測試公會
        // 先新增測試資料
        $request = new Request();
        $request->merge(['name' => '測試名稱']);
        $data = 'guild';
        $response = $this->characterController->addRelatedData($request, $data);
        $id = $response->getOriginalContent();
        unset($request, $response);
        $request = new Request();
        $request->merge([
            'id' => $id,
            'name' => '測試公會'
        ]);
        try {
            $this->characterController->editRelatedData($request, $data);
            $row = Guild::where('id', $id)->where('name', $request->input('name'))->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
        }

        unset($id, $request, $data);

        // 測試種族
        // 先新增測試資料
        $request = new Request();
        $request->merge(['name' => '測試名稱']);
        $data = 'race';
        $response = $this->characterController->addRelatedData($request, $data);
        $id = $response->getOriginalContent();
        unset($request, $response);
        $request = new Request();
        $request->merge([
            'id' => $id,
            'name' => '測試種族'
        ]);
        try {
            $this->characterController->editRelatedData($request, $data);
            $row = Race::where('id', $id)->where('name', $request->input('name'))->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
        }

        unset($id, $request, $data);

        // 測試技能種類
        // 先新增測試資料
        $request = new Request();
        $request->merge(['name' => '測試名稱']);
        $data = 'skilltype';
        $response = $this->characterController->addRelatedData($request, $data);
        $id = $response->getOriginalContent();
        unset($request, $response);
        $request = new Request();
        $request->merge([
            'id' => $id,
            'name' => '測試技能種類'
        ]);
        try {
            $this->characterController->editRelatedData($request, $data);
            $row = SkillType::where('id', $id)->where('name', $request->input('name'))->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得指定的專用武器資料
     *
     * @return void
     */
    public function testGetSpecialWeaponInfoTest()
    {
        $request = new Request();
        $request->merge(['id' => 1]);
        $response = $this->characterController->getSpecialWeaponInfo($request);
        $this->assertNotNull($response);
        $this->assertTrue(
            in_array($response->getStatusCode(), [200, 204])
        );
    }

    /**
     * 測試所有專武資料的取得
     *
     * 測試目標：
     * 此方法正常一定返回 200 OK，不論有無資料都是返回標準 JSON 格式陣列。
     * 如果有資料需額外驗證是不是包含有 id 和 name 的鍵名
     *
     * @return void
     */
    public function testSpecialWeaponListTest()
    {
        $response = $this->characterController->specialWeaponList();
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
    }

    /**
     * 測試新增專用武器資料
     *
     * @return void
     */
    public function testAddSpecialWeaponTest()
    {
        $request = new Request();
        $request->merge([
            'name' => '測試專武',
            'description' => '測試說明',
            'ability' => '30=pAtk:15>239|tpRise:9>15,50=pAtk:15>325|tpRise:9>18',
            'apply' => '2021-04-17T00:00:00Z',
        ]);

        $response = $this->characterController->addSpecialWeapon($request);
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $names = collect($response->getOriginalContent())->pluck('name');
        $this->assertContains($request->input('name'), $names);
    }

    /**
     * 測試編輯專用武器資料
     *
     * @return void
     */
    public function testEditSpecialWeaponTest()
    {
        // 先新增一筆測試資料
        $request = new Request();
        $request->merge([
            'name' => '測試專武',
            'description' => '測試說明',
            'ability' => '30=pAtk:15>239|tpRise:9>15,50=pAtk:15>325|tpRise:9>18',
            'apply' => '2021-04-17T00:00:00Z',
        ]);

        $response = $this->characterController->addSpecialWeapon($request);
        $id = collect($response->getOriginalContent())->pluck('id')->last();

        $request->merge(['id' => $id, 'name' => 'test']);
        $response = $this->characterController->editSpecialWeapon($request);
        $this->assertNotNull($response);
        $names = collect($response->getOriginalContent())->pluck('name');
        $this->assertContains($request->input('name'), $names);
    }
}
