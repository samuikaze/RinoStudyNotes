<?php

namespace App\Http\Controllers\v1\Backend;

use App\Exceptions\NoResultException;
use App\Http\Controllers\Controller;
use App\Services\v1\ResponseService;
use App\Services\v1\SystemVarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemVarController extends Controller
{
    /**
     * System Var Service
     *
     * @var \App\Services\SystemVarService
     */
    protected $sysvar;

    /**
     * 回應
     *
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * 建構函式
     *
     * @param \App\Services\SystemVarService $sysvar
     * @param \App\Services\ResponseService $response
     * @return void
     */
    public function __construct(
        SystemVarService $sysvar,
        ResponseService $response
    ) {
        $this->sysvar = $sysvar;
        $this->response = $response;
    }

    /**
     * 取得待審核及已經過審核的使用者
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifyUsers()
    {
        $users = $this->sysvar->getVerifyUsers();

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

        $this->sysvar->verifyUser($request->input('type'), $request->input('id'));

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

        $this->sysvar->adminAccount($request->input('type'), $request->input('id'));

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
            return $this->response
                        ->setError('請確認是否所有欄位都已填實')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        $id = $this->sysvar->addVersion(
            $request->input('version_id'),
            $request->input('content')
        );

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

        try {
            $this->sysvar->editVersion(
                $request->input('id'),
                $request->input('version_id'),
                $request->input('content')
            );
        } catch (NoResultException $e) {
            return $this->response
                        ->setCode($this->response::NOT_MODIFIED)
                        ->setErrorMsg($e->getMessage())
                        ->json();
        }

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

        try {
            $this->sysvar->deleteVersion($request->input('id'));
        } catch (NoResultException $e) {
            return $this->response
                        ->setCode($this->response::NOT_MODIFIED)
                        ->setErrorMsg($e->getMessage())
                        ->json();
        }

        return $this->response->json();
    }
}
