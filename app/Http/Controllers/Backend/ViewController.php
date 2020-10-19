<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewController extends Controller
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
     * 後臺首頁
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function index()
    {
        return $this->response->setView('backend.index')->view();
    }

    /**
     * 登入
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function login()
    {
        if (!Auth::check()) {
            return $this->response->setView('backend.login')->view();
        } else {
            return $this->response->setRedirectTarget(route('admin.index'))->redirect();
        }
    }

    /**
     * 審核申請
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function verifyEditableApply()
    {
        return $this->response->setView('backend.verifyapply')->view();
    }

    /**
     * 角色一覽
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function characterList()
    {
        return $this->response->setView('backend.characters.list')->view();
    }

    /**
     * 角色關聯的資料管理
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function characterRelatedData()
    {
        return $this->response->setView('backend.characters.relate')->view();
    }
}
