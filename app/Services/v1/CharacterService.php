<?php

namespace App\Services\v1;

use App\Exceptions\IllegalArgumentsException;
use App\Repositories\v1\CharacterRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class CharacterService
{
    /**
     * CharacterRepository
     *
     * @var \App\Repositories\v1\CharacterRepository
     */
    protected $character;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(CharacterRepository $character)
    {
        $this->character = $character;
    }

    /**
     * 取得全部角色清單
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function characterList()
    {
        return $this->character->characterList();
    }

    /**
     * 以角色 ID 取得角色資料
     *
     * @param string $condition [id|name] 搜尋條件
     * @return array 角色資料
     */
    public function characterInfo(string $condition)
    {
        // 判斷是 ID 還是暱稱/名稱當條件
        if (is_numeric($condition)) {
            $type = 'id';
        } else {
            $type = 'name';
        }

        try {
            $character = $this->character->characterInfo($condition, $type);
        } catch (Exception $e) {
            return [];
        }

        // 找不到就返回空資料
        if (empty($character)) {
            return [];
        }

        // 處理技能資料
        $skillTypes = $this->skillTypeList();
        $diff = $skillTypes->pluck('id')->diff($character->skills->pluck('skill_type_of')->toArray());
        $skills = $character->skills->map(function ($skill) {
            return collect($skill->toArray())->except(['created_at', 'updated_at'])->toArray();
        })->toArray();

        // 如果技能種類 ID 陣列和技能資料中的種類 ID 陣列對不起來就要把空資料推進集合裡
        if (count($diff) > 0) {
            foreach ($diff as $lack) {
                $skills[] = [
                    'skill_name' => null,
                    'skill_type_of' => $skillTypes->where('id', $lack)->first()->name,
                    'description' => null,
                    'effect' => null,
                ];
            }
        }

        // 整理資料
        $otherInfo = [
            'likes' => json_decode($character->likes),
            'birthday' => Carbon::parse($character->birthday)->toISOString(true),
            'guild' => $character->guild->name,
            'cv' => $character->cv->name,
            'race' => $character->race->name,
            'skills' => $skills,
            'nicknames' => $character->nicknames->pluck('nickname'),
        ];
        $character = collect($character)
            ->except(['guild_of', 'cv_of', 'race_of', 'created_at', 'updated_at'])
            ->merge($otherInfo)
            ->toArray();

        return $character;
    }

    /**
     * 取得公會清單
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有公會清單
     */
    public function guildList(bool $withTrashed = false)
    {
        return $this->character->guildList($withTrashed);
    }

    /**
     * 取得所有技能種類
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有技能種類清單
     */
    public function skillTypeList(bool $withTrashed = false)
    {
        return $this->character->skillTypeList($withTrashed);
    }

    /**
     * 取得所有聲優清單
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有聲優清單
     */
    public function CVList(bool $withTrashed = false)
    {
        return $this->character->CVList($withTrashed);
    }

    /**
     * 取得所有種族清單
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有種族清單
     */
    public function raceList(bool $withTrashed = false)
    {
        return $this->character->raceList($withTrashed);
    }

    /**
     * 取得專用武器清單
     *
     * @return \Illuminate\Database\Eloquent\Collection 所有專用武器清單
     */
    public function specialWeaponList()
    {
        return $this->character->specialWeaponList();
    }

    /**
     * 新增角色資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return int $id 新增後的角色 ID
     */
    public function addCharacter(Request $request)
    {
        $request->merge([
            'birthday' => Carbon::parse($request->input('birthday')),
            'likes' => json_encode($request->input('likes'), JSON_UNESCAPED_UNICODE)
        ]);
        $character = $request->except(['nicknames', 'skills']);
        $nicknames = $request->input('nicknames');
        $skills = $request->input('skills');

        $id = $this->character->addCharacter($character);
        $created_at = $id->created_at;
        $updated_at = $id->updated_at;
        $id = $id->id;

        $nicknames = collect($nicknames)->map(function ($item) use ($id, $created_at, $updated_at) {
            $newItem = [
                'nickname' => $item,
                'character_of' => $id,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            ];
            return $newItem;
        })->toArray();

        $this->character->addNickname($nicknames);

        $skills = collect($skills)->map(function ($item) use ($id, $created_at, $updated_at) {
            if (empty($item['description']) || empty($skill_name)) {
                return null;
            }
            $item['character_of'] = $id;
            $item['created_at'] = $created_at;
            $item['updated_at'] = $updated_at;
            return $item;
        })->filter()->toArray();

        $this->character->addSkill($skills);

        return $id;
    }

    /**
     * 編輯角色資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return void
     */
    public function editCharacter(Request $request)
    {
        $request->merge([
            'birthday' => Carbon::parse($request->input('birthday')),
            'likes' => json_encode($request->input('likes'), JSON_UNESCAPED_UNICODE)
        ]);

        $updateTime = Carbon::now();

        // 開始更新資料
        $character = $request->only([
            'id', 'guild_of', 'cv_of', 'race_of', 'tw_name', 'jp_name',
            's_image_url', 'f_image_url', 't_image_url', 'description',
            'ages', 'height', 'weight', 'blood_type', 'likes', 'birthday'
        ]);

        $this->character->editCharacter($request->input('id'), $character);
        unset($character);

        // 暱稱要先把多出來的加進去，再刪除這次傳入的資料中沒有的暱稱
        $nicknames = $request->input('nicknames');

        $exists = $this->character
                       ->getNicknameByCharacterId(
                           $request->input('id'),
                           ['id', 'nickname']
                       )
                       ->pluck('nickname')
                       ->toArray();
        $adds = collect(array_diff($nicknames, $exists))->map(function ($nickname) use ($request, $updateTime) {
            $nickname = [
                'character_of' => $request->input('id'),
                'nickname' => $nickname,
                'created_at' => $updateTime,
                'updated_at' => $updateTime,
            ];
            return $nickname;
        })->toArray();

        $this->character->addNickname($adds);
        $this->character->removeUnnecessaryNickname($request->input('id'), $nicknames);
        unset($nicknames, $exists, $adds);

        // 技能
        $skills = collect($request->input('skills'))->filter(function ($skill) {
            if (is_null($skill['skill_name']) || is_null($skill['description'])) {
                return false;
            }
            return true;
        });

        if ($skills->count() > 0) {
            $exists = $this->character->getNicknameByCharacterId($request->input('id'));
            // 找出已存在的技能且資料有變動的
            $diff = collect($skills)->whereIn('skill_type_of', $exists->pluck('skill_type_of'))->filter(function ($skill, $key) use ($exists) {
                $exist = $exists[$key];
                if ($skill['skill_name'] != $exist['skill_name'] || $skill['description'] != $exist['description'] || $skill['effect'] != $exist['effiect']) {
                    return true;
                }
                return false;
            });
            // 更新已存在技能
            $target = $exists->whereIn('skill_type_of', $diff->pluck('skill_type_of'))->pluck('skill_type_of');
            $this->character->updateExistsSkills($request->input('id'), $target, $skills);
            // 找出資料庫中原本沒有存的技能
            $adds = collect($skills)->whereNotIn('skill_type_of', $exists->pluck('skill_type_of'))->map(function ($add) use ($request, $updateTime) {
                $add['character_of'] = $request->input('id');
                $add['created_at'] = $add['updated_at'] = $updateTime;
                return $add;
            })->toArray();
            $this->character->addSkill($adds);
            // 刪除這次留空的項目
            $this->character->removeUnnecessarySkill($request->input('id'), $skills->pluck('skill_type_of'));
        }
    }

    /**
     * 以角色 ID 取得角色較詳細的資料
     *
     * @param int $id 角色 ID
     * @return array 角色資料
     */
    public function characterDetailInfo(int $id)
    {
        $character = $this->character->characterDetailInfo(
            $id,
            [
                'id', 'guild_of', 'cv_of', 'race_of', 'tw_name', 'jp_name',
                'description', 'ages', 'height', 'weight', 'blood_type', 'likes',
                'birthday'
            ]
        );

        if (!is_null($character)) {
            // 處理技能資料
            $diff = $this->character
                         ->skillTypeList()
                         ->pluck('id')
                         ->diff(
                             $character->skills
                                       ->pluck('skill_type_of')
                                       ->toArray()
                         );
            $skills = $character->skills->map(function ($skill) {
                return collect($skill->toArray())->except(['created_at', 'updated_at'])
                                                 ->toArray();
            })->toArray();
            // 如果技能種類 ID 陣列和技能資料中的種類 ID 陣列對不起來就要把空資料推進集合裡
            if (count($diff) > 0) {
                foreach ($diff as $lack) {
                    $skills[] = [
                        'skill_name' => '',
                        'skill_type_of' => $lack,
                        'description' => '',
                        'effect' => '',
                    ];
                }
            }
            $character->likes = implode("\n", json_decode($character->likes));
            $character->birthday = is_null($character->birthday) ? null : Carbon::parse($character->birthday)->toISOString(true);
            $character = (empty($character)) ? [] : $character->toArray();
            $character['skills'] = $skills;
            $character['nicknames'] = implode("\n", collect($character['nicknames'])->pluck('nickname')->toArray());
        }

        return $character;
    }

    /**
     * 更新聲優、公會、種族、技能種類資料
     *
     * @param string $type <cv|guild|race|skilltype> 更新的資料種類
     * @param string $name 聲優、公會、種族或技能種類的名稱
     * @return int 插入資料後資料庫中的 ID
     *
     * @throws \App\Exceptions\DuplicateEntryException
     * @throws \App\Exceptions\IllegalArgumentsException
     */
    public function addRelatedData(string $type, string $name)
    {
        return $this->character->addRelatedData($type, $name);
    }

    /**
     * 編輯聲優、公會、種族、技能種類資料
     *
     * @param string $type <cv|guild|race|skilltype> 更新的資料種類
     * @param array $data 更新的資料
     * @return void
     *
     * @throws \App\Exceptions\NoResultException
     */
    public function editRelatedData(string $type, array $data)
    {
        $this->character->editRelatedData($type, $data);
    }

    /**
     * 取得指定的專用武器資料
     *
     * @param int $id 專用武器 ID
     * @return array 專用武器資料
     *
     * @throws \App\Exceptions\NoResultException
     */
    public function getSpecialWeaponInfo(int $id)
    {
        $data = $this->character->getSpecialWeaponInfo($id);

        // 處理能力資料
        $abilities = json_decode($data->ability, true);
        $resultAbilities = '';
        $times = 0;
        foreach ($abilities as $level => $ability) {
            $resultAbilities .= $level.'=';

            $innerTimes = 0;
            foreach ($ability as $abName => $values) {
                $resultAbilities .= $abName.':'
                                 .trim(explode('(', $values)[0]).'>'
                                 .str_replace(')', '', explode('(', $values)[1]);

                if ($innerTimes != count($ability) - 1) {
                    $resultAbilities .= "|";
                }

                $innerTimes++;
            }
            if ($times != count($abilities) - 1) {
                $resultAbilities .= ",\n";
            }

            $times++;
        }

        $data->ability = $resultAbilities;

        return $data->toArray();
    }

    /**
     * 新增專用武器資料
     *
     * @param array $data 專用武器資料
     * @return void
     *
     * @throws \App\Exceptions\IllegalArgumentsException
     */
    public function addSpecialWeapon(array $data)
    {
        // 處理能力資料
        $tmpAbilities = explode(',', preg_replace( "/\r|\n/", '', $data['ability']));
        $abilities = [];
        foreach ($tmpAbilities as $tmpAbility) {
            // 沒有等號
            if (stripos($tmpAbility, '=') === false) {
                throw new IllegalArgumentsException('專武能力格式錯誤，請再次確認後再送出');
                break;
            }

            // 等級值
            $key = explode('=', $tmpAbility)[0];
            // 各能力值
            $values = explode('|', explode('=', $tmpAbility)[1]);
            // 處理後的資料
            $tmpValue = [];
            foreach ($values as $value) {
                // 加成能力名稱
                $vKey = explode(':', $value)[0];
                // 加成值
                $vValues = explode('>', explode(':', $value)[1]);
                $vValues = $vValues[0].' ('.$vValues[1].')';
                // 寫入處理後資料
                $tmpValue[$vKey] = $vValues;
                unset($vKey, $vValues);
            }
            // 寫回陣列
            $abilities[$key] = $tmpValue;
            unset($values, $tmpValue);
        }

        $data['ability'] = json_encode($abilities);
        unset($abilities);

        // 處理開專時間
        if (is_null($data['apply']) || strlen($data['apply']) == 0) {
            $data['apply_time'] = null;
        } else {
            $data['apply_time'] = Carbon::parse($data['apply']);
            unset($data['apply']);
        }

        $this->character->addSpecialWeapon($data);
    }

    /**
     * 編輯專用武器資料
     *
     * @param array $data 專用武器資料
     * @return void
     */
    public function editSpecialWeapon(array $data)
    {
        // 處理能力資料
        $tmpAbilities = explode(',', preg_replace( "/\r|\n/", '', $data['ability']));
        $abilities = [];
        foreach ($tmpAbilities as $tmpAbility) {
            // 沒有等號
            if (stripos($tmpAbility, '=') === false) {
                throw new IllegalArgumentsException('專武能力格式錯誤，請再次確認後再送出');
                break;
            }

            // 等級值
            $key = explode('=', $tmpAbility)[0];
            // 各能力值
            $values = explode('|', explode('=', $tmpAbility)[1]);
            // 處理後的資料
            $tmpValue = [];
            foreach ($values as $value) {
                // 加成能力名稱
                $vKey = explode(':', $value)[0];
                // 加成值
                $vValues = explode('>', explode(':', $value)[1]);
                $vValues = $vValues[0].' ('.$vValues[1].')';
                // 寫入處理後資料
                $tmpValue[$vKey] = $vValues;
                unset($vKey, $vValues);
            }
            // 寫回陣列
            $abilities[$key] = $tmpValue;
            unset($values, $tmpValue);
        }
        $data['ability'] = $abilities;
        unset($abilities);

        // 處理開專時間
        if (is_null($data['apply']) || strlen($data['apply']) == 0) {
            $data['apply_time'] = null;
        } else {
            $data['apply_time'] = Carbon::parse($data['apply']);
            unset($data['apply']);
        }

        $id = $data['id'];
        unset($data['id']);

        $this->character->editSpecialWeapon($id, $data);
    }
}
