<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;

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
     * 取得等待審核中的使用者
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifyingUsers()
    {
        $users = User::where('role_of', 1)->get()->toArray();

        return $this->response->setData($users)->json();
    }
}
