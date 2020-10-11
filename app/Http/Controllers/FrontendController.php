<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class FrontendController extends Controller
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
}