<?php

namespace Tests\Unit\Controllers\v1\Backend;

use App\Http\Controllers\v1\Backend\ViewController;
use Tests\TestCase;

class ViewControllerTest extends TestCase
{
    /**
     * ViewController
     *
     * @var \App\Http\Controllers\Backend\ViewController
     */
    protected $controller;

    /**
     * 注入 Controller
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = $this->app->make(ViewController::class);
    }

    /**
     * 測試後臺首頁
     *
     * @return void
     */
    public function testIndexTest()
    {
        $response = $this->controller->index();
        $this->assertEquals('backend.index', $response->name());
    }

    /**
     * 測試登入
     *
     * @return void
     */
    public function testLoginTest()
    {
        $response = $this->controller->login();
        $this->assertEquals('backend.login', $response->name());
    }

    /**
     * 測試角色一覽
     *
     * @return void
     */
    public function testCharacterListTest()
    {
        $response = $this->controller->characterList();
        $this->assertEquals('backend.characters.list', $response->name());
    }

    /**
     * 測試角色關聯的資料管理
     *
     * @return void
     */
    public function testCharacterRelatedDataTest()
    {
        $response = $this->controller->characterRelatedData();
        $this->assertEquals('backend.characters.relate', $response->name());
    }

    /**
     * 測試角色專用武器管理
     *
     * @return void
     */
    public function testCharacterSpecialWeaponTest()
    {
        $response = $this->controller->characterSpecialWeapon();
        $this->assertEquals('backend.characters.specialweapon', $response->name());
    }

    /**
     * 測試審核申請
     *
     * @return void
     */
    public function testVerifyEditableApplyTest()
    {
        $response = $this->controller->verifyEditableApply();
        $this->assertEquals('backend.systemconfigs.verifyapply', $response->name());
    }

    /**
     * 測試版本管理
     *
     * @return void
     */
    public function testVersionControlTest()
    {
        $response = $this->controller->versionControl();
        $this->assertEquals('backend.systemconfigs.version', $response->name());
    }
}
