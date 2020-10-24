<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Version;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SystemConfigController extends Controller
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
     * 取得待審核及已經過審核的使用者
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifyUsers()
    {
        $verifying = User::where('status', 0)->get()->toArray();
        $verified = User::where('status', '!=', 0)->where('id', '!=', 1)->get()->toArray();

        $users = [
            'verifying' => $verifying,
            'verified' => $verified,
        ];

        return $this->response->setData($users)->json();
    }

    /**
     * 通過或拒絕審核
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 200 回應或錯誤訊息
     */
    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'type' => ['required', 'string', 'in:accept,denied'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('請再次檢查是否有遺漏或送錯參數')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        switch ($request->input('type')) {
            case 'accept':
                User::where('id', $request->input('id'))->update([
                    'role_of' => 3,
                    'status' => 1,
                ]);
                break;
            case 'denied':
                User::where('id', $request->input('id'))->update([
                    'role_of' => 2,
                    'status' => 1,
                ]);
                break;
        }

        return $this->response->json();
    }

    /**
     * 停權或復權帳號
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 200 回應或錯誤訊息
     */
    public function adminAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'type' => ['required', 'string', 'in:enable,disable'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('請再次檢查是否有遺漏或送錯參數')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        switch ($request->input('type')) {
            case 'enable':
                User::where('id', $request->input('id'))->update([
                    'status' => 1,
                ]);
                break;
            case 'disable':
                User::where('id', $request->input('id'))->update([
                    'status' => 2,
                ]);
                break;
        }

        return $this->response->json();
    }

    /**
     * 新增版本
     *
     * @param \Illuminate\Http\Request $request HTTP 請求，應當包含版本號碼與變更點資料
     * @return \Illuminate\Http\JsonResponse 成功或失敗訊息
     */
    public function addVersion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'version_id' => ['required', 'string'],
            'content' => ['required', 'array'],
            'content.*' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response->setError('請確認是否所有欄位都已填實')->setCode(400)->json();
        }

        $id = Version::create([
            'version_id' => $request->input('version_id'),
            'content' => json_encode($request->input('content'), JSON_UNESCAPED_UNICODE),
        ]);

        $id = $id->id;

        return $this->response->setData($id)->json();
    }

    /**
     * 編輯版本資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求，應當包含版本 ID、號碼與變更點資料
     * @return \Illuminate\Http\JsonResponse 成功或失敗訊息
     */
    public function editVersion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'version_id' => ['required', 'string'],
            'content' => ['required', 'array'],
            'content.*' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response->setError('請確認所有欄位是否皆已填實')->setCode(400)->json();
        }

        if (empty(Version::where('id', $request->input('id'))->first())) {
            return $this->response->setError('找不到該版本資料，請再次確認您的資料是否正確')->setCode(400)->json();
        }

        Version::where('id', $request->input('id'))->update([
            'version_id' => $request->input('version_id'),
            'content' => json_encode($request->input('content'), JSON_UNESCAPED_UNICODE),
        ]);

        return $this->response->json();
    }

    /**
     * 刪除版本資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求，應當包含版本 ID、號碼與變更點資料
     * @return \Illuminate\Http\JsonResponse 成功或失敗訊息
     */
    public function deleteVersion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return $this->response->setError('請確實傳送 ID 值')->setCode(400)->json();
        }

        if (empty(Version::where('id', $request->input('id'))->first())) {
            return $this->response->setError('找不到該版本資料，請再次確認 ID 值是否正確')->setCode(400)->json();
        }

        Version::where('id', $request->input('id'))->delete();
        DB::select('ALTER TABLE `versions` AUTO_INCREMENT = '.$request->input('id').';');

        return $this->response->json();
    }
}
