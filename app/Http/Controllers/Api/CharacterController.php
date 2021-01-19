<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use App\Models\{
    Character,
    CV,
    Guild,
    Race,
    SkillType,
    SpecialWeapon
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CharacterController extends Controller
{
    /**
     * 回應
     *
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(ResponseService $response)
    {
        $this->response = $response;
    }

    /**
     * 取得全部角色清單
     *
     * @return \Illuminate\Http\JsonResponse 所有角色清單
     */
    public function characterList()
    {
        $characters = Character::select('id', 'tw_name', 'jp_name')->get();

        return $this->response->setData($characters)->json();
    }

    /**
     * 以角色 ID 取得角色資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求，可用角色名稱、暱稱或 ID 當作搜尋條件
     * @param string $search 搜尋字串
     * @return \Illuminate\Http\JsonResponse 角色資料
     */
    public function characterInfo(Request $request, string $search = null)
    {
        // 先判斷是用哪種路由傳條件進來的
        if (! is_null($search)) {
            $condition = $search;
        } else {
            $condition = collect($request->all())->first();
        }

        // 驗證條件是否為空
        $validator = Validator::make(['condition' => $condition], [
            'condition' => ['required', 'min:1'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('請確實傳入搜尋條件！')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        // 判斷是 ID 還是暱稱/名稱當條件
        if (is_numeric($condition)) {
            $type = 'id';
        } else {
            $type = 'name';
        }

        switch ($type) {
            case 'id':
                $character = Character::where('id', $condition)
                                      ->with('guild', 'cv', 'race', 'nicknames', 'skills')
                                      ->first();
                break;
            case 'name':
                $character = Character::whereHas('nicknames', function ($q) use ($condition) {
                    $q->where('nickname', $condition);
                })->orWhere('tw_name', $condition)
                  ->orWhere('jp_name', $condition)
                  ->with('guild', 'cv', 'race', 'nicknames', 'skills')
                  ->first();
                break;
        }

        // 找不到就返回空資料
        if (empty($character)) {
            return $this->response->json();
        }

        // 處理技能資料
        $skillTypes = SkillType::get();
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
        $character->likes = json_decode($character->likes);
        $character->birthday = Carbon::parse($character->birthday)->toISOString(true);
        $guild = $character->guild->name;
        $cv = $character->cv->name;
        $race = $character->race->name;
        $nicknames = $character->nicknames->pluck('nickname');
        $character = collect($character)->except(['guild_of', 'cv_of', 'race_of', 'created_at', 'updated_at'])->toArray();
        $character['guild'] = $guild;
        $character['cv'] = $cv;
        $character['race'] = $race;
        $character['skills'] = $skills;
        $character['nicknames'] = $nicknames;

        return $this->response->setData($character)->json();
    }

    /**
     * 取得公會清單
     *
     * @return \Illuminate\Http\JsonResponse 所有公會清單
     */
    public function guildList()
    {
        $guilds = Guild::select('id', 'name')->get();

        return $this->response->setData($guilds)->json();
    }

    /**
     * 取得所有技能種類
     *
     * @return \Illuminate\Http\JsonResponse 所有公會清單
     */
    public function skillTypeList()
    {
        $skillTypes = SkillType::select('id', 'name')->get();

        return $this->response->setData($skillTypes)->json();
    }

    /**
     * 取得所有聲優清單
     *
     * @return \Illuminate\Http\JsonResponse 所有聲優清單
     */
    public function CVList()
    {
        $cvs = CV::select('id', 'name')->get();

        return $this->response->setData($cvs)->json();
    }

    /**
     * 取得所有種族清單
     *
     * @return \Illuminate\Http\JsonResponse 所有種族清單
     */
    public function raceList()
    {
        $races = Race::select('id', 'name')->get();

        return $this->response->setData($races)->json();
    }

    /**
     * 取得專用武器清單
     *
     * @return \Illuminate\Http\JsonResponse 所有專用武器清單
     */
    public function specialWeaponList()
    {
        $specialWeapons = SpecialWeapon::select('id', 'name')->get();

        return $this->response->setData($specialWeapons)->json();
    }

    /**
     * 取得指定的專用武器資料
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecialWeaponInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError($validator->errors()->first())
                        ->setCode(400)
                        ->json();
        }

        $data = SpecialWeapon::select('id', 'name', 'description', 'ability', 'apply_time AS apply')->where('id', $request->input('id'))->first();

        if (is_null($data)) {
            return $this->response
                        ->setData([])
                        ->json();
        }

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

        return $this->response->setData($data)->json();
    }

    /**
     * 新增專用武器資料
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSpecialWeapon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:20'],
            'description' => ['required', 'string', 'max:255'],
            'ability' => ['required', 'string'],
            'apply' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError($validator->errors()->first())
                        ->setCode(400)
                        ->json();
        }

        // 處理能力資料
        $tmpAbilities = explode(',', preg_replace( "/\r|\n/", '', $request->input('ability')));
        $abilities = [];
        foreach ($tmpAbilities as $tmpAbility) {
            // 沒有等號
            if (stripos($tmpAbility, '=') === false) {
                return $this->response
                            ->setError('專武能力格式錯誤，請再次確認後再送出')
                            ->setCode(400)
                            ->json();
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

        // 處理開專時間
        if (is_null($request->input('apply')) || strlen($request->input('apply')) == 0) {
            $applyTime = null;
        } else {
            $applyTime = $request->input('apply');
        }

        SpecialWeapon::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'ability' => json_encode($abilities, JSON_UNESCAPED_UNICODE),
            'apply_time' => $applyTime,
        ]);

        return $this->specialWeaponList();
    }

    /**
     * 編輯專用武器資料
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editSpecialWeapon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'name' => ['required', 'string', 'max:20'],
            'description' => ['required', 'string', 'max:255'],
            'ability' => ['required', 'string'],
            'apply' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError($validator->errors()->first())
                        ->setCode(400)
                        ->json();
        }

        if (is_null(SpecialWeapon::where('id', $request->input('id'))->first())) {
            return $this->response
                        ->setError('找不到該專用武器，請再次確認後再行送出')
                        ->setCode(400)
                        ->json();
        }

        // 處理能力資料
        $tmpAbilities = explode(',', preg_replace( "/\r|\n/", '', $request->input('ability')));
        $abilities = [];
        foreach ($tmpAbilities as $tmpAbility) {
            // 沒有等號
            if (stripos($tmpAbility, '=') === false) {
                return $this->response
                            ->setError('專武能力格式錯誤，請再次確認後再送出')
                            ->json();
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

        // 處理開專時間
        if (is_null($request->input('apply')) || strlen($request->input('apply')) == 0) {
            $applyTime = null;
        } else {
            $applyTime = $request->input('apply');
        }

        SpecialWeapon::where('id', $request->input('id'))->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'ability' => json_encode($abilities, JSON_UNESCAPED_UNICODE),
            'apply_time' => $applyTime,
        ]);

        return $this->specialWeaponList();
    }
}
