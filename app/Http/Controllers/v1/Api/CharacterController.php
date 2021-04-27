<?php

namespace App\Http\Controllers\v1\Api;

use App\Http\Controllers\Controller;
use App\Services\v1\ResponseService;
use App\Services\v1\CharacterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CharacterController extends Controller
{
    /**
     * 角色服務
     *
     * @var \App\Services\CharacterService
     */
    protected $character;

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
    public function __construct(
        CharacterService $character,
        ResponseService $response
    ) {
        $this->character = $character;
        $this->response = $response;
    }

    /**
     * 取得全部角色清單
     *
     * @return \Illuminate\Http\JsonResponse 所有角色清單
     */
    public function characterList()
    {
        $characters = $this->character->characterList();

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

        // 取資料，找不到就返回空資料
        $character = $this->character->characterInfo($condition);
        if (count($character) == 0) {
            return $this->response->json();
        }

        return $this->response->setData($character)->json();
    }

    /**
     * 取得公會清單
     *
     * @return \Illuminate\Http\JsonResponse 所有公會清單
     */
    public function guildList()
    {
        $guilds = $this->character->guildList();

        return $this->response->setData($guilds)->json();
    }

    /**
     * 取得所有技能種類
     *
     * @return \Illuminate\Http\JsonResponse 所有公會清單
     */
    public function skillTypeList()
    {
        $skillTypes = $this->character->skillTypeList();

        return $this->response->setData($skillTypes)->json();
    }

    /**
     * 取得所有聲優清單
     *
     * @return \Illuminate\Http\JsonResponse 所有聲優清單
     */
    public function CVList()
    {
        $cvs = $this->character->CVList();

        return $this->response->setData($cvs)->json();
    }

    /**
     * 取得所有種族清單
     *
     * @return \Illuminate\Http\JsonResponse 所有種族清單
     */
    public function raceList()
    {
        $races = $this->character->raceList();

        return $this->response->setData($races)->json();
    }
}
