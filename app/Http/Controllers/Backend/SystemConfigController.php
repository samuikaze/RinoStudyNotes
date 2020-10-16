<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;
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
}
