<?php

namespace Tests\Unit\Controllers\v1;

use App\Http\Controllers\v1\FrontendController;
use Tests\TestCase;

class FrontendControllerTest extends TestCase
{
    /**
     * FrontendController
     *
     * @var \App\Http\Controllers\FrontendController
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

        $this->controller = $this->app->make(FrontendController::class);
    }

    /**
     * 測試首頁
     *
     * @return void
     */
    public function testIndexTest()
    {
        $response = $this->controller->index();
        $this->assertEquals('index', $response->name());
    }

    /**
     * 測試 API 資料一覽頁面
     *
     * @return void
     */
    public function testApiListTest()
    {
        $response = $this->controller->apiList();
        $this->assertEquals('apilist', $response->name());
    }

    /**
     * 測試版本紀錄
     *
     * @return void
     */
    public function testVersionListTest()
    {
        $response = $this->controller->versionList();
        $this->assertEquals('version', $response->name());
    }
}
