<?php

namespace App\Http\Controllers\v1\Backend;

use App\Exceptions\{
    DuplicateEntryException,
    IllegalArgumentsException,
    NoResultException
};
use App\Http\Controllers\Controller;
use App\Services\v1\CharacterService;
use App\Services\v1\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CharacterController extends Controller
{
    /**
     * Character service
     *
     * @var \App\Services\v1\CharacterService
     */
    protected $character;

    /**
     * 回應
     *
     * @var \App\Services\v1\ResponseService
     */
    protected $response;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(
        ResponseService $response,
        CharacterService $character
    ) {
        $this->response = $response;
        $this->character = $character;
    }

    /**
     * 新增角色資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 200 回應或錯誤訊息
     */
    public function addCharacter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tw_name' => ['required', 'string', 'max:10'],
            'jp_name' => ['required', 'string', 'max:15'],
            'cv_of' => ['required', 'numeric'],
            'race_of' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max: 255'],
            'ages' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'nicknames' => ['required', 'array', 'min:1'],
            'nicknames.*' => ['required', 'string'],
            'likes' => ['required', 'array', 'min:1'],
            'likes.*' => ['required', 'string'],
            'birthday' => ['required', 'date'],
            'guild_of' => ['required', 'numeric'],
            'blood_type' => ['nullable', 'string', 'in:A,B,O,AB'],
            's_image_url' => ['nullable', 'string'],
            'f_image_url' => ['nullable', 'string'],
            't_image_url' => ['nullable', 'string'],
            'skills' => ['required', 'array'],
            'skills.*.skill_type_of' => ['required', 'numeric'],
            'skills.*.skill_name' => ['nullable', 'string', 'max:15'],
            'skills.*.description' => ['nullable', 'string', 'max:255'],
            'skills.*.effect' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('請確認是否仍有資料未填或有資料格式不正確')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        $request->merge(['birthday' => Carbon::parse($request->input('birthday'))]);
        $id = $this->character->addCharacter($request);

        return $this->response->setData($id)->json();
    }

    /**
     * 編輯角色資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 200 回應或錯誤訊息
     */
    public function editCharacter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'tw_name' => ['required', 'string', 'max:10'],
            'jp_name' => ['required', 'string', 'max:15'],
            'cv_of' => ['required', 'numeric'],
            'race_of' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max: 255'],
            'ages' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'nicknames' => ['required', 'array', 'min:1'],
            'nicknames.*' => ['required', 'string'],
            'likes' => ['required', 'array', 'min:1'],
            'likes.*' => ['required', 'string'],
            'birthday' => ['required', 'date'],
            'guild_of' => ['required', 'numeric'],
            'blood_type' => ['nullable', 'string', 'in:A,B,O,AB'],
            's_image_url' => ['nullable', 'string'],
            'f_image_url' => ['nullable', 'string'],
            't_image_url' => ['nullable', 'string'],
            'skills' => ['required', 'array'],
            'skills.*.skill_type_of' => ['required', 'numeric'],
            'skills.*.skill_name' => ['nullable', 'string', 'max:15'],
            'skills.*.description' => ['nullable', 'string', 'max:255'],
            'skills.*.effect' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('請確認是否仍有資料未填或有資料格式不正確')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        $this->character->editCharacter($request);

        return $this->response->json();
    }

    /**
     * 以角色 ID 取得角色資料
     *
     * @param int|null $id 角色 ID
     * @return \Illuminate\Http\JsonResponse 角色資料
     */
    public function characterInfo(int $id = null)
    {
        if (is_null($id)) {
            return $this->response
                        ->setError('沒有搜尋條件，無法取得角色資料')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        $character = $this->character->characterDetailInfo($id);

        return $this->response->setData($character)->json();
    }

    /**
     * 取得公會清單
     *
     * @return \Illuminate\Http\JsonResponse 所有公會清單
     */
    public function guildList()
    {
        $guilds = $this->character->guildList(true);

        return $this->response->setData($guilds)->json();
    }

    /**
     * 取得所有技能種類
     *
     * @return \Illuminate\Http\JsonResponse 所有技能種類清單
     */
    public function skillTypeList()
    {
        $skillTypes = $this->character->skillTypeList(true);

        return $this->response->setData($skillTypes)->json();
    }

    /**
     * 取得所有聲優清單
     *
     * @return \Illuminate\Http\JsonResponse 所有聲優清單
     */
    public function CVList()
    {
        $cvs = $this->character->CVList(true);

        return $this->response->setData($cvs)->json();
    }

    /**
     * 取得所有種族清單
     *
     * @return \Illuminate\Http\JsonResponse 所有種族清單
     */
    public function raceList()
    {
        $races = $this->character->raceList(true);

        return $this->response->setData($races)->json();
    }

    /**
     * 新增聲優、公會、種族、技能種類資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @param string|null $data [cv|guild|race|skilltype]
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRelatedData(Request $request, string $data = null)
    {
        $validator = Validator::make(['data' => $data], [
            'data' => ['required', 'string', 'in:cv,guild,race,skilltype'],
        ]);

        if ($validator->fails()) {
            return $this->response->setCode($this->response::NOT_FOUND)->json();
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $failMsg = '';
            switch ($data) {
                case 'cv':
                    $failMsg = '聲優';
                    break;
                case 'guild':
                    $failMsg = '公會';
                    break;
                case 'race':
                    $failMsg = '種族';
                    break;
                case 'skilltype':
                    $failMsg = '技能種類';
                    break;
            }

            return $this->response
                        ->setError($failMsg.'的名稱未填或格式不正確')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        try {
            $id = $this->character->addRelatedData($data, $request->input('name'));
        } catch (DuplicateEntryException | IllegalArgumentsException $e) {
            return $this->response
                        ->setCode($this->response::NOT_MODIFIED)
                        ->setError($e->getMessage())
                        ->json();
        }

        return $this->response->setData($id)->json();
    }

    /**
     * 編輯聲優、公會、種族、技能種類資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @param string|null $data [cv|guild|race|skilltype]
     */
    public function editRelatedData(Request $request, string $data = null)
    {
        $request->merge(['data' => $data]);
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'string', 'in:cv,guild,race,skilltype'],
            'id' => ['required', 'numeric'],
            'name' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response->setCode($this->response::NOT_FOUND)->json();
        }

        try {
            $this->character->editRelatedData($data, $request->only('id', 'name'));
        } catch (NoResultException $e) {
            return $this->response
                        ->setCode($this->response::NOT_MODIFIED)
                        ->setError($e->getMessage())
                        ->json();
        }

        return $this->response->json();
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
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        try {
            $data = $this->character->getSpecialWeaponInfo($request->input('id'));
        } catch (NoResultException $e) {
            return $this->response
                        ->setCode($this->response::NO_CONTENT)
                        ->json();
        }

        return $this->response->setData($data)->json();
    }

    /**
     * 取得專用武器清單
     *
     * @return \Illuminate\Http\JsonResponse 所有專用武器清單
     */
    public function specialWeaponList()
    {
        $specialWeapons = $this->character->specialWeaponList();

        return $this->response->setData($specialWeapons)->json();
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
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        try {
            $this->character->addSpecialWeapon(
                $request->only('name', 'description', 'ability', 'apply')
            );
        } catch (IllegalArgumentsException $e) {
            return $this->response
                        ->setCode($this->response::BAD_REQUEST)
                        ->setErrorMsg($e->getMessage())
                        ->json();
        }

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
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        // 先驗證專武存不存在
        try {
            $this->character->getSpecialWeaponInfo($request->input('id'));
        } catch (NoResultException $e) {
            return $this->response
                        ->setError($e->getMessage())
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        // 更新專武資料
        try {
            $this->character->editSpecialWeapon($request->only('id', 'name', 'description', 'ability', 'apply'));
        } catch (IllegalArgumentsException $e) {
            return $this->response
                        ->setError($e->getMessage())
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        return $this->specialWeaponList();
    }
}
