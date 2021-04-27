<?php

namespace Tests\Unit\Services\v1;

use App\Models\Character;
use App\Models\CV;
use App\Models\Guild;
use App\Models\Race;
use App\Models\SkillType;
use App\Models\SpecialWeapon;
use App\Services\v1\CharacterService;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class CharacterServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * CharacterService
     *
     * @var \App\Services\CharacterService
     */
    protected $service;

    /**
     * 注入 Service
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CharacterService::class);
    }

    /**
     * 測試取得全部角色清單
     *
     * @return void
     */
    public function testCharacterListTest()
    {
        Character::create([
            'guild_of' => 1,
            'cv_of' => 1,
            'race_of' => 1,
            'tw_name' => 'testTWName',
            'jp_name' => 'testJPName',
            'description' => 'testDesc',
            'ages' => 15,
            'height' => 151,
            'weight' => 48,
            'likes' => json_encode(['test'], JSON_UNESCAPED_UNICODE),
            'birthday' => Carbon::now(),
        ]);

        $characters = $this->service->characterList();
        $this->assertTrue($characters->count() > 0);
    }

    /**
     * 測試以角色 ID 取得角色資料
     *
     * @return void
     */
    public function testCharacterInfoTest()
    {
        $id = Character::create([
            'guild_of' => 1,
            'cv_of' => 1,
            'race_of' => 1,
            'tw_name' => 'testTWName',
            'jp_name' => 'testJPName',
            'description' => 'testDesc',
            'ages' => 15,
            'height' => 151,
            'weight' => 48,
            'likes' => json_encode(['test'], JSON_UNESCAPED_UNICODE),
            'birthday' => Carbon::now(),
        ]);

        try {
            $info = $this->service->characterInfo($id->id);
            $this->assertNotNull($info);
            $this->assertEquals('testTWName', $info['tw_name']);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得公會清單
     *
     * @return void
     */
    public function testGuildListTest()
    {
        Guild::create([
            'name' => '測試公會',
        ]);

        try {
            $response = $this->service->guildList();
            $row = Guild::count();
            $this->assertEquals($row, $response->count());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得所有技能種類
     *
     * @return void
     */
    public function testSkillTypeListTest()
    {
        SkillType::create([
            'name' => 'test',
        ]);

        try {
            $response = $this->service->skillTypeList();
            $row = SkillType::count();
            $this->assertEquals($row, $response->count());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得所有聲優清單
     *
     * @return void
     */
    public function testCVListTest()
    {
        CV::create([
            'name' => 'test',
        ]);

        try {
            $response = $this->service->CVList();
            $row = CV::count();
            $this->assertEquals($row, $response->count());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得所有種族清單
     *
     * @return void
     */
    public function testRaceListTest()
    {
        Race::create([
            'name' => 'test',
        ]);

        try {
            $response = $this->service->raceList();
            $row = Race::count();
            $this->assertEquals($row, $response->count());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得專用武器清單
     *
     * @return void
     */
    public function testSpecialWeaponListTest()
    {
        SpecialWeapon::create([
            'name' => 'test',
            'description' => 'testDesc',
            'ability' => '{"30":{"pAtk":"15 (239)","tpRise":"9 (15)"},"50":{"pAtk":"15 (325)","tpRise":"9 (18)"}}',
        ]);

        try {
            $response = $this->service->specialWeaponList();
            $row = SpecialWeapon::count();
            $this->assertEquals($row, $response->count());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試新增角色資料
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

        try {
            $id = $this->service->addCharacter($request);
            $trueId = Character::where('tw_name', '測試角色')->first();
            $this->assertNotNull($trueId);
            $this->assertEquals($trueId->id, $id);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試編輯角色資料
     *
     * @return void
     */
    public function testEditCharacterTest()
    {
        $original = [
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
        ];
        $request = new Request();
        $request->merge($original);
        $id = $this->service->addCharacter($request);

        $original['id'] = $id;
        $original['tw_name'] = '測試編輯中文名稱';
        $request = new Request();
        $request->merge($original);

        try {
            $this->service->editCharacter($request);
            $chara = Character::where('id', $id)->first();
            $this->assertEquals('測試編輯中文名稱', $chara->tw_name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試以角色 ID 取得角色較詳細的資料
     *
     * @return void
     */
    public function testCharacterDetailInfoTest()
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
        $id = $this->service->addCharacter($request);

        $chara = $this->service->characterDetailInfo($id);

        $this->assertEquals($id, $chara['id']);
        $this->assertEquals('測試角色', $chara['tw_name']);
    }

    /**
     * 測試更新聲優、公會、種族、技能種類資料
     *
     * @return void
     */
    public function testAddRelatedDataTest()
    {
        $name = '測試名稱';

        // 測試聲優
        $type = 'cv';
        $response = $this->service->addRelatedData($type, $name);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response));
        $real = CV::where('id', $response)->first();
        $this->assertEquals($name, $real->name);

        // 測試公會
        unset($response, $real);
        $type = 'guild';
        $response = $this->service->addRelatedData($type, $name);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response));
        $real = Guild::where('id', $response)->first();
        $this->assertEquals($name, $real->name);

        // 測試種族
        unset($response, $real);
        $type = 'race';
        $response = $this->service->addRelatedData($type, $name);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response));
        $real = Race::where('id', $response)->first();
        $this->assertEquals($name, $real->name);

        // 測試技能種類
        unset($response, $real);
        $type = 'skilltype';
        $response = $this->service->addRelatedData($type, $name);
        $this->assertNotNull($response);
        $this->assertTrue(is_int($response));
        $real = SkillType::where('id', $response)->first();
        $this->assertEquals($name, $real->name);
    }

    /**
     * 測試編輯聲優、公會、種族、技能種類資料
     *
     * @return void
     */
    public function testEditRelatedDataTest()
    {
        // 測試聲優
        // 先新增測試資料
        $name = '測試名稱';
        $type = 'cv';
        $id = $this->service->addRelatedData($type, $name);
        $request = [
            'id' => $id,
            'name' => '測試聲優'
        ];
        try {
            $this->service->editRelatedData($type, $request);
            $row = CV::where('id', $id)->where('name', $request['name'])->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
        }

        unset($id, $request, $type);

        // 測試公會
        // 先新增測試資料
        $type = 'guild';
        $id = $this->service->addRelatedData($type, $name);
        $request = [
            'id' => $id,
            'name' => '測試公會'
        ];
        try {
            $this->service->editRelatedData($type, $request);
            $row = Guild::where('id', $id)->where('name', $request['name'])->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
        }

        unset($id, $request, $type);

        // 測試種族
        // 先新增測試資料
        $type = 'race';
        $id = $this->service->addRelatedData($type, $name);
        $request = [
            'id' => $id,
            'name' => '測試種族'
        ];
        try {
            $this->service->editRelatedData($type, $request);
            $row = Race::where('id', $id)->where('name', $request['name'])->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
        }

        unset($id, $request, $type);

        // 測試技能種類
        // 先新增測試資料
        $type = 'skilltype';
        $id = $this->service->addRelatedData($type, $name);
        $request = [
            'id' => $id,
            'name' => '測試技能種類'
        ];
        try {
            $this->service->editRelatedData($type, $request);
            $row = SkillType::where('id', $id)->where('name', $request['name'])->count();
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
        // 先新增測試資料
        $data = [
            'name' => '測試專武',
            'description' => '測試描述',
            'ability' => '{"30":{"pAtk":"15 (239)","tpRise":"9 (15)"},"50":{"pAtk":"15 (325)","tpRise":"9 (18)"}}',
            'apply_time' => Carbon::now(),
        ];
        $id = SpecialWeapon::create($data);
        $id = $id->id;

        $sp = $this->service->getSpecialWeaponInfo($id);
        $this->assertNotNull($sp);
        $this->assertEquals($data['name'], $sp['name']);
    }

    /**
     * 測試新增專用武器資料
     *
     * @return void
     */
    public function testAddSpecialWeaponTest()
    {
        $request = [
            'name' => '測試專武',
            'description' => '測試說明',
            'ability' => '30=pAtk:15>239|tpRise:9>15,50=pAtk:15>325|tpRise:9>18',
            'apply' => '2021-04-17T00:00:00Z',
        ];

        $this->service->addSpecialWeapon($request);
        $sp = SpecialWeapon::where('name', $request['name'])->first();
        $this->assertNotNull($sp);
        $this->assertEquals($request['name'], $sp->name);
    }

    /**
     * 測試編輯專用武器資料
     *
     * @return void
     */
    public function testEditSpecialWeaponTest()
    {
        // 先新增一筆測試資料
        $request = [
            'name' => '測試專武',
            'description' => '測試說明',
            'ability' => '30=pAtk:15>239|tpRise:9>15,50=pAtk:15>325|tpRise:9>18',
            'apply' => '2021-04-17T00:00:00Z',
        ];

        $response = $this->service->addSpecialWeapon($request);
        $id = SpecialWeapon::where('name', $request['name'])->first()->id;

        $request['id'] = $id;
        $request['name'] = 'test';
        $this->service->editSpecialWeapon($request);
        $sp = SpecialWeapon::where('id', $id)->first();
        $this->assertEquals($request['name'], $sp->name);
    }
}
