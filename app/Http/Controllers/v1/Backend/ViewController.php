<?php

namespace App\Http\Controllers\v1\Backend;

use App\Http\Controllers\Controller;
use App\Services\v1\AuthenticationService;
use App\Services\v1\ResponseService;

class ViewController extends Controller
{
    /**
     * Authentication Service
     *
     * @var \App\Services\AuthenticationService
     */
    protected $auth;

    /**
     * 回應
     *
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * 建構函式
     *
     * @param \App\Services\AuthenticationService $auth
     * @param \App\Services\ResponseService $response
     * @return void
     */
    public function __construct(AuthenticationService $auth, ResponseService $response)
    {
        $this->auth = $auth;
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
        if (! $this->auth->verifyAuthStatus()) {
            return $this->response->setView('backend.login')->view();
        } else {
            return $this->response->setRedirectTarget(route('admin.index'))->redirect();
        }
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

    /**
     * 角色專用武器管理
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function characterSpecialWeapon()
    {
        return $this->response->setView('backend.characters.specialweapon')->view();
    }

    /**
     * 審核申請
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function verifyEditableApply()
    {
        return $this->response->setView('backend.systemconfigs.verifyapply')->view();
    }

    /**
     * 版本管理
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function versionControl()
    {
        return $this->response->setView('backend.systemconfigs.version')->view();
    }
}
