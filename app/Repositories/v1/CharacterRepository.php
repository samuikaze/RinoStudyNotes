<?php

namespace App\Repositories\v1;

use App\Exceptions\{
    DuplicateEntryException,
    IllegalArgumentsException,
    NoResultException
};
use App\Models\{
    Character,
    CV,
    Guild,
    Nickname,
    Race,
    Skill,
    SkillType,
    SpecialWeapon
};
use Illuminate\Support\Collection;

class CharacterRepository
{
    /**
     * Character Model
     *
     * @var \App\Models\Character
     */
    protected $character;

    /**
     * CV model
     *
     * @var \App\Models\CV
     */
    protected $cv;

    /**
     * Gulid model
     *
     * @var \App\Models\Guild
     */
    protected $guild;

    /**
     * Nickname model
     *
     * @var \App\Models\Nickname
     */
    protected $nickname;

    /**
     * Race model
     *
     * @var \App\Models\Race
     */
    protected $race;

    /**
     * Skill model
     *
     * @var \App\Models\Skill
     */
    protected $skill;

    /**
     * SkillType model
     *
     * @var \App\Models\SkillType
     */
    protected $skillType;

    /**
     * SpecialWeapon model
     *
     * @var \App\Models\SpecialWeapon
     */
    protected $specialWeapon;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(
        Character $character,
        CV $cv,
        Guild $guild,
        Nickname $nickname,
        Race $race,
        Skill $skill,
        SkillType $skillType,
        SpecialWeapon $specialWeapon
    ) {
        $this->character = $character;
        $this->cv = $cv;
        $this->guild = $guild;
        $this->nickname = $nickname;
        $this->race = $race;
        $this->skill = $skill;
        $this->skillType = $skillType;
        $this->specialWeapon = $specialWeapon;
    }

    /**
     * 取得全部角色清單
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function characterList()
    {
        return $this->character->select('id', 'tw_name', 'jp_name')->get();
    }

    /**
     * 以角色 ID 取得角色資料
     *
     * @param string $condition [id|name] 搜尋條件
     * @param string $type 搜尋方式
     * @return \App\Models\Character|null 角色資料
     *
     * @throws \App\Exceptions\IllegalArgumentsException
     */
    public function characterInfo(string $condition, string $type)
    {
        switch ($type) {
            case 'id':
                return $this->character
                    ->where('id', $condition)
                    ->with('guild', 'cv', 'race', 'nicknames', 'skills')
                    ->first();
                break;
            case 'name':
                return $this->character
                    ->whereHas('nicknames', function ($q) use ($condition) {
                        $q->where('nickname', $condition);
                    })
                    ->orWhere('tw_name', $condition)
                    ->orWhere('jp_name', $condition)
                    ->with('guild', 'cv', 'race', 'nicknames', 'skills')
                    ->first();
                break;
            default:
                throw new IllegalArgumentsException('給定的搜尋方式不為 id 或 name');
                break;
        }
    }

    /**
     * 取得所有技能種類
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有技能種類清單
     */
    public function skillTypeList(bool $withTrashed = false)
    {
        if ($withTrashed === true) {
            return $this->skillType
                        ->select('id', 'name', 'deleted_at')
                        ->withTrashed()
                        ->get();
        }

        return $this->skillType
                    ->select('id', 'name')
                    ->get();
    }

    /**
     * 取得公會清單
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有公會清單
     */
    public function guildList(bool $withTrashed = false)
    {
        if ($withTrashed === true) {
            return $this->guild
                        ->select('id', 'name', 'deleted_at')
                        ->withTrashed()
                        ->get();
        }

        return $this->guild
                    ->select('id', 'name')
                    ->get();
    }

    /**
     * 取得所有聲優清單
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有聲優清單
     */
    public function CVList(bool $withTrashed = false)
    {
        if ($withTrashed === true) {
            return $this->cv
                        ->select('id', 'name', 'deleted_at')
                        ->withTrashed()
                        ->get();
        }

        return $this->cv
                    ->select('id', 'name')
                    ->get();
    }

    /**
     * 取得所有種族清單
     *
     * @param bool $withTrashed [選填] 是否包含已刪除的資料，預設為 false
     * @return \Illuminate\Database\Eloquent\Collection 所有種族清單
     */
    public function raceList(bool $withTrashed = false)
    {
        if ($withTrashed === true) {
            return $this->race
                        ->select('id', 'name', 'deleted_at')
                        ->withTrashed()
                        ->get();
        }

        return $this->race
                    ->select('id', 'name')
                    ->get();
    }

    /**
     * 取得專用武器清單
     *
     * @return \Illuminate\Database\Eloquent\Collection 所有專用武器清單
     */
    public function specialWeaponList()
    {
        return $this->specialWeapon->select('id', 'name')->get();
    }

    /**
     * 新增角色資料
     *
     * @param array $character 角色資料
     * @return \App\Models\Character 新增後的角色資料
     */
    public function addCharacter(array $character)
    {
        return $this->character->create($character);
    }

    /**
     * 新增角色暱稱資料
     *
     * @param array $nicknames 暱稱
     * @return void
     */
    public function addNickname(array $nicknames)
    {
        $this->nickname->insert($nicknames);
    }

    /**
     * 新增技能資料
     *
     * @param array $skills
     * @return \App\Models\Skill 新增後的技能資料
     */
    public function addSkill(array $skills)
    {
        $this->skill->insert($skills);
    }

    /**
     * 編輯角色資料
     *
     * @param int $id 角色 ID
     * @param array $character 新角色資料
     * @return void
     */
    public function editCharacter(int $id, array $character)
    {
        $this->character->where('id', $id)->update($character);
    }

    /**
     * 以角色 ID 取得角色的暱稱
     *
     * @param int $id 角色 ID
     * @param array $fields [選填] 要取得的資料欄位，預設為全部
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNicknameByCharacterId(int $id, array $fields = ['*'])
    {
        return $this->nickname->where('character_of', $id)->get($fields);
    }

    /**
     * 移除不需要的角色暱稱資料，常用於編輯暱稱的場合
     *
     * @param int $id 角色 ID
     * @param array $nicknames 要保留的暱稱
     * @return void
     */
    public function removeUnnecessaryNickname(int $id, array $nicknames)
    {
        $this->nickname
             ->where('character_of', $id)
             ->whereNotIn('nickname', $nicknames)
             ->delete();
    }

    /**
     * 更新已存在的技能
     *
     * @param int $id 角色 ID
     * @param \Illuminate\Support\Collection $target 技能種類
     * @param \Illuminate\Support\Collection $skills 技能資料
     * @return void
     */
    public function updateExistsSkills(int $id, Collection $target, Collection $skills)
    {
        foreach ($target as $t) {
            $data = $skills->where('skill_type_of', $t)->first();
            $this->skill
                 ->where('character_of', $id)
                 ->where('skill_type_of', $t)
                 ->update([
                     'skill_name' => $data['skill_name'],
                     'description' => $data['description'],
                     'effect' => $data['effect'],
                 ]);
        }
    }

    /**
     * 移除不需要的角色技能資料，常用於編輯技能的場合
     *
     * @param int $id
     * @param array|\Illuminate\Support\Collection $except
     * @return void
     */
    public function removeUnnecessarySkill(int $id, $except)
    {
        $this->skill
             ->where('character_of', $id)
             ->whereNotIn('skill_type_of', $except)
             ->delete();
    }

    /**
     * 以角色 ID 取得角色更詳細的資料
     *
     * @param int $id 角色 ID
     * @param array $fields
     * @return \App\Models\Character|null 角色資料
     *
     * @throws \App\Exceptions\IllegalArgumentsException
     */
    public function characterDetailInfo(int $id, array $fields = ['*'])
    {
        return $this->character
                    ->where('id', $id)
                    ->with([
                        'nicknames' => function ($q) {
                            $q->select('id', 'character_of', 'nickname');
                        },
                        'skills' => function ($q) {
                            $q->orderBy('skill_type_of', 'asc');
                        },
                    ])
                    ->first($fields);
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
        $model = null;
        $exceptionMsg = '';
        switch ($type) {
            case 'cv':
                $model = $this->cv;
                $exceptionMsg = '聲優';
                break;
            case 'guild':
                $model = $this->guild;
                $exceptionMsg = '公會';
                break;
            case 'race':
                $model = $this->race;
                $exceptionMsg = '種族';
                break;
            case 'skilltype':
                $model = $this->skillType;
                $exceptionMsg = '技能種類';
                break;
            default:
                throw new IllegalArgumentsException("無此種類");
        }
        $check = $model->where('name', $name)->count();

        if ($check > 0) {
            throw new DuplicateEntryException('該'.$exceptionMsg.'已經存在！');
        }

        $new = $model->create([
            'name' => $name,
        ]);

        return $new->id;
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
        $model = null;
        $exceptionMsg = '';
        $updateData = [
            'name' => $data['name'],
        ];

        switch ($type) {
            case 'cv':
                $model = $this->cv;
                $exceptionMsg = '聲優';
                break;
            case 'guild':
                $model = $this->guild;
                $exceptionMsg = '公會';
                break;
            case 'race':
                $model = $this->race;
                $exceptionMsg = '種族';
                break;
            case 'skilltype':
                $model = $this->skillType;
                $exceptionMsg = '技能種類';
                break;
        }

        $check = $model->where('id', $data['id'])->count();
        if ($check == 0) {
            throw new NoResultException("找不到".$exceptionMsg."資料");
        }

        $model->where('id', $data['id'])->update($updateData);
    }

    /**
     * 取得指定的專用武器資料
     *
     * @param int $id 專用武器 ID
     * @return \App\Models\SpecialWeapon
     *
     * @throws \App\Exceptions\NoResultException
     */
    public function getSpecialWeaponInfo(int $id)
    {
        $data = $this->specialWeapon
                     ->select('id', 'name', 'description', 'ability', 'apply_time AS apply')
                     ->where('id', $id)
                     ->first();

        if (is_null($data)) {
            throw new NoResultException('找不到該專用武器');
        }

        return $data;
    }

    /**
     * 新增專用武器資料
     *
     * @param array $data 專用武器資料
     * @return void
     */
    public function addSpecialWeapon(array $data)
    {
        $this->specialWeapon->create($data);
    }

    /**
     * 更新專用武器資料
     *
     * @param int $id 專用武器 ID
     * @param array $data 專用武器資料
     * @return void
     */
    public function editSpecialWeapon(int $id, array $data)
    {
        $this->specialWeapon->where('id', $id)->update($data);
    }
}
