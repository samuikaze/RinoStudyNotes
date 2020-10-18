<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use App\Models\Character;
use App\Models\CV;
use App\Models\Guild;
use App\Models\Race;
use App\Models\SkillType;
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
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 角色資料
     *
     * @todo 未完成
     */
    public function characterInfo(Request $request)
    {
        if (!$request->has('nickname') && !$request->has('id') && !$request->has('tw_name') && !$request->has('jp_name')) {
            return $this->response
                        ->setError('沒有搜尋條件，無法取得角色資料')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        $validator = Validator::make($request->all(), [
            'id' => ['nullable', 'numeric'],
            'nickname' => ['nullable', 'string'],
            'tw_name' => ['nullable', 'string'],
            'jp_name' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('搜尋條件格式不正確，無法取得角色資料')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        $condition = $request->all();

        $key = array_keys($condition)[0];

        if (in_array($key, ['id', 'nickname', 'tw_name', 'jp_name'])) {
            $character = Character::select('id', 'guild_of', 'cv_of', 'race_of', 'tw_name', 'jp_name', 'description', 'ages', 'height', 'weight', 'blood_type', 'likes', 'birthday')
                                  ->where($key, $condition[$key])
                                  ->with(
                                      ['guild' => function ($q) {
                                          $q->select('id', 'name');
                                      }, 'cv' => function ($q) {
                                          $q->select('id', 'name');
                                      }, 'race' => function ($q) {
                                          $q->select('id', 'name');
                                      }]
                                  )
                                  ->first();

            $character->likes = json_decode($character->likes);
            $character->guild_of = $character->guild->name;
            $character->race_of = $character->race->name;
            $character->cv_of = $character->cv->name;
            $character = (empty($character)) ? [] : $character->toArray();
            unset($character['guild'], $character['race'], $character['cv']);
        } else {
            $character = [];
        }

        dd($character);
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
     * @return
     */
    public function raceList()
    {
        $races = Race::select('id', 'name')->get();

        return $this->response->setData($races)->json();
    }
}
