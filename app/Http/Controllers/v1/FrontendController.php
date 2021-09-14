<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Services\v1\ResponseService;
use Illuminate\Http\Request;

class FrontendController extends Controller
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
     * 首頁
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function index()
    {
        return $this->response->setView('index')->view();
    }

    /**
     * API 資料一覽頁面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function apiList()
    {
        return $this->response->setView('apilist')->view();
    }

    /**
     * 版本紀錄
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function versionList()
    {
        return $this->response->setView('version')->view();
    }
}
