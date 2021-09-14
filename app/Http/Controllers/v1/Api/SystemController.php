<?php

namespace App\Http\Controllers\v1\Api;

use App\Http\Controllers\Controller;
use App\Services\v1\ResponseService;

class SystemController extends Controller
{
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
    public function __construct(ResponseService $response)
    {
        $this->response = $response;
    }

    /**
     * 讓 Uptime Robot 檢查網站狀態用
     *
     * @return \Illuminate\Http\JsonResponse 200 OK
     */
    public function uptimeCheck()
    {
        return $this->response->json();
    }
}
