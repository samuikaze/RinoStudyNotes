<?php

namespace Tests\Unit\Repositories\v1;

use App\Models\Character;
use App\Models\CV;
use App\Models\Guild;
use App\Models\Nickname;
use App\Models\Race;
use App\Models\Skill;
use App\Models\SkillType;
use App\Models\SpecialWeapon;
use App\Repositories\v1\CharacterRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CharacterRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * CharacterRepository
     *
     * @var \App\Repositories\v1\CharacterRepository
     */
    protected $repository;

    /**
     * 注入 Repository
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(CharacterRepository::class);
    }

    /**
     * 測試取得全部角色清單
     *
     * @return void
     */
    public function testCharacterListTest()
    {
        try {
            $charaList = $this->repository->characterList();
            $this->assertNotNull($charaList);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試以角色 ID 取得角色資料
     *
     * @return void
     */
    public function testCharacterInfoTest()
    {
        // 新增測試資料
        $test = [
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
        ];
        $result = Character::create($test);
        $id = $result->id;

        // 測試以 ID 取得資料
        try {
            $info = $this->repository->characterInfo($id, 'id');
            $this->assertNotNull($info);
            $this->assertEquals($test['tw_name'], $info->tw_name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }

        // 測試以名稱取得資料
        try {
            $info = $this->repository->characterInfo($test['tw_name'], 'name');
            $this->assertNotNull($info);
            $this->assertEquals($test['tw_name'], $info->tw_name);
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
        try {
            $skilltypes = $this->repository->skillTypeList();
            $this->assertNotNull($skilltypes);
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
        try {
            $list = $this->repository->guildList();
            $this->assertNotNull($list);
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
        try {
            $list = $this->repository->CVList();
            $this->assertNotNull($list);
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
        try {
            $list = $this->repository->raceList();
            $this->assertNotNull($list);
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
        try {
            $list = $this->repository->specialWeaponList();
            $this->assertNotNull($list);
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
        $data = [
            'tw_name' => '測試角色',
            'jp_name' => 'テストキャラ',
            'cv_of' => 1,
            'race_of' => 1,
            'description' => '測試描述',
            'ages' => 18,
            'height' => 160,
            'weight' => 50,
            'likes' => json_encode([
                '測試喜好',
            ], JSON_UNESCAPED_UNICODE),
            'birthday' => Carbon::parse('2021-01-01T00:00:00Z'),
            'guild_of' => 1,
            'blood_type' => 'A',
            's_image_url' => null,
            'f_image_url' => null,
            't_image_url' => null,
        ];

        try {
            $new = $this->repository->addCharacter($data);
            $this->assertNotNull($new);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試新增角色暱稱資料
     *
     * @return void
     */
    public function testAddNicknameTest()
    {
        try {
            $this->repository->addNickname([
                [
                    'character_of' => 1,
                    'nickname' => 'testnickname',
                ],
            ]);
            $data = Nickname::orderBy('id', 'desc')->first();
            $this->assertNotNull($data);
            $this->assertEquals('testnickname', $data->nickname);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試新增技能資料
     *
     * @return void
     */
    public function testAddSkillTest()
    {
        try {
            $this->repository->addSkill([
                [
                    'character_of' => 1,
                    'skill_name' => 'test',
                    'skill_type_of' => 1,
                    'description' => 'testDesc',
                    'effect' => 'testEff',
                ],
            ]);
            $data = Skill::orderBy('id', 'desc')->first();
            $this->assertNotNull($data);
            $this->assertEquals('test', $data->skill_name);
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
        // 新增測試資料
        $test = [
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
        ];
        $result = Character::create($test);
        $id = $result->id;

        try {
            $this->repository->editCharacter($id, [
                'tw_name' => 'nameTest',
            ]);
            $chara = Character::where('id', $id)->first();
            $this->assertNotNull($chara);
            $this->assertEquals('nameTest', $chara->tw_name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試以角色 ID 取得角色的暱稱
     *
     * @return void
     */
    public function testGetNicknameByCharacterId()
    {
        // 新增測試資料
        $this->repository->addNickname([
            [
                'character_of' => 1,
                'nickname' => 'testnickname',
            ],
        ]);
        $data = Nickname::orderBy('id', 'desc')->first();

        try {
            $nickname = $this->repository->getNicknameByCharacterId($data->character_of);
            $this->assertNotNull($nickname);
            $nickname = $nickname->last();
            $this->assertEquals('testnickname', $nickname->nickname);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試移除不需要的角色暱稱資料
     *
     * @return void
     */
    public function testRemoveUnnecessaryNickname()
    {
        // 新增測試資料
        $this->repository->addNickname([
            [
                'character_of' => 1,
                'nickname' => '妹弓1',
            ],
            [
                'character_of' => 1,
                'nickname' => 'testnickname',
            ],
        ]);
        $data = Nickname::orderBy('id', 'desc')->first();

        try {
            $this->repository->removeUnnecessaryNickname(
                $data->character_of,
                ['妹弓1'],
            );
            $nickname = Nickname::where('character_of', 1)->count();
            $this->assertNotNull($nickname);
            $this->assertEquals(1, $nickname);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試更新已存在的技能
     *
     * @return void
     */
    public function testUpdateExistsSkillsTest()
    {
        $test = [
            'character_of' => 1,
            'skill_name' => 'testSkill',
            'skill_type_of' => 1,
            'description' => 'testDesc',
            'effect' => 'testEff',
        ];
        // 新增測試資料
        $skill = Skill::create($test);

        $target = collect([1]);
        $skills = collect([[
            'skill_type_of' => 1,
            'skill_name' => 'testSkill1',
            'description' => '描述',
            'effect' => '測試效果',
        ]]);

        try {
            $this->repository->updateExistsSkills($skill->character_of, $target, $skills);
            $afterSkill = Skill::where('id', $skill->id)->first();
            $this->assertNotNull($afterSkill);
            $this->assertEquals($skills->first()['skill_name'], $afterSkill->skill_name);
            $this->assertEquals($skills->first()['description'], $afterSkill->description);
            $this->assertEquals($skills->first()['effect'], $afterSkill->effect);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試移除不需要的角色技能資料
     *
     * @return void
     */
    public function testRemoveUnnecessarySkillTest()
    {
        $test = [
            'character_of' => 1,
            'skill_name' => 'testSkill',
            'skill_type_of' => 3,
            'description' => 'testDesc',
            'effect' => 'testEff',
        ];
        // 新增測試資料
        $skill = Skill::create($test);

        try {
            $this->repository->removeUnnecessarySkill($skill->character_of, [1]);
            $row = Skill::where('id', $skill->id)->count();
            $this->assertEquals(0, $row);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試以角色 ID 取得角色更詳細的資料
     *
     * @return void
     */
    public function testCharacterDetailInfoTest()
    {
        // 新增測試資料
        $test = [
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
        ];
        $character = Character::create($test);

        try {
            $t = $this->repository->characterDetailInfo($character->id);
            $this->assertNotNull($t);
            $this->assertEquals($test['tw_name'], $t->tw_name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試更新聲優、公會、種族、技能種類資料
     *
     * @return void
     */
    public function testAddRelatedDataTest()
    {
        $test = 'test';

        // 測試聲優
        try {
            $id = $this->repository->addRelatedData('cv', $test);
            $cv = CV::where('id', $id)->first();
            $this->assertNotNull($cv);
            $this->assertEquals($test, $cv->name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }

        // 測試公會
        try {
            $id = $this->repository->addRelatedData('guild', $test);
            $guild = Guild::where('id', $id)->first();
            $this->assertNotNull($guild);
            $this->assertEquals($test, $guild->name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }

        // 測試種族
        try {
            $id = $this->repository->addRelatedData('race', $test);
            $race = Race::where('id', $id)->first();
            $this->assertNotNull($race);
            $this->assertEquals($test, $race->name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }

        // 測試技能種類
        try {
            $id = $this->repository->addRelatedData('skilltype', $test);
            $skilltype = SkillType::where('id', $id)->first();
            $this->assertNotNull($skilltype);
            $this->assertEquals($test, $skilltype->name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試編輯聲優、公會、種族、技能種類資料
     *
     * @return void
     */
    public function testEditRelatedData()
    {
        $test = 'test';
        $newTest = 'newName';

        // 測試聲優
        try {
            $id = $this->repository->addRelatedData('cv', $test);
            $this->repository->editRelatedData('cv', [
                'id' => $id,
                'name' => $newTest,
            ]);
            $cv = CV::where('id', $id)->first();
            $this->assertNotNull($cv);
            $this->assertEquals($newTest, $cv->name);
        }  catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }

        // 測試公會
        try {
            $id = $this->repository->addRelatedData('guild', $test);
            $this->repository->editRelatedData('guild', [
                'id' => $id,
                'name' => $newTest,
            ]);
            $guild = Guild::where('id', $id)->first();
            $this->assertNotNull($guild);
            $this->assertEquals($newTest, $guild->name);
        }  catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }

        // 測試種族
        try {
            $id = $this->repository->addRelatedData('race', $test);
            $this->repository->editRelatedData('race', [
                'id' => $id,
                'name' => $newTest,
            ]);
            $race = Race::where('id', $id)->first();
            $this->assertNotNull($race);
            $this->assertEquals($newTest, $race->name);
        }  catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }

        // 測試技能種類
        try {
            $id = $this->repository->addRelatedData('skilltype', $test);
            $this->repository->editRelatedData('skilltype', [
                'id' => $id,
                'name' => $newTest,
            ]);
            $skilltype = SkillType::where('id', $id)->first();
            $this->assertNotNull($skilltype);
            $this->assertEquals($newTest, $skilltype->name);
        }  catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得指定的專用武器資料
     *
     * @return void
     */
    public function testGetSpecialWeaponInfo()
    {
        // 新增測試資料
        $sw = SpecialWeapon::create([
            'name' => 'test',
            'description' => 'testDesc',
            'ability' => '{"30":{"pAtk":"15 (239)","tpRise":"9 (15)"},"50":{"pAtk":"15 (325)","tpRise":"9 (18)"}}',
            'apply_time' => Carbon::now(),
        ]);

        try {
            $swInfo = $this->repository->getSpecialWeaponInfo($sw->id);
            $this->assertNotNull($swInfo);
            $this->assertEquals($sw->name, $swInfo->name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試新增專用武器資料
     *
     * @return void
     */
    public function testAddSpecialWeapon()
    {
        $data = [
            'name' => 'test',
            'description' => 'testDesc',
            'ability' => '{"30":{"pAtk":"15 (239)","tpRise":"9 (15)"},"50":{"pAtk":"15 (325)","tpRise":"9 (18)"}}',
            'apply_time' => Carbon::now(),
        ];

        try {
            $this->repository->addSpecialWeapon($data);
            $sw = SpecialWeapon::where('name', $data['name'])->first();
            $this->assertNotNull($sw);
            $this->assertEquals($data['ability'], $sw->ability);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試更新專用武器資料
     *
     * @return void
     */
    public function testEditSpecialWeapon()
    {
        // 新增測試資料
        $sw = SpecialWeapon::create([
            'name' => 'test',
            'description' => 'testDesc',
            'ability' => '{"30":{"pAtk":"15 (239)","tpRise":"9 (15)"},"50":{"pAtk":"15 (325)","tpRise":"9 (18)"}}',
            'apply_time' => Carbon::now(),
        ]);

        try {
            $new = ['name' => 'newName'];
            $this->repository->editSpecialWeapon($sw->id, $new);
            $afterSw = SpecialWeapon::where('id', $sw->id)->first();
            $this->assertNotNull($afterSw);
            $this->assertEquals($new['name'], $afterSw->name);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }
}
