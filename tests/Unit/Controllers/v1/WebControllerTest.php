<?php

namespace Tests\Unit\Controllers\v1;

use App\Http\Controllers\v1\WebController;
use App\Models\Version;
use Illuminate\Http\Request;
use Tests\TestCase;

class WebControllerTest extends TestCase
{
    /**
     * WebControllerTest
     *
     * @var \App\Http\Controllers\WebController
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

        $this->controller = $this->app->make(WebController::class);
    }

    /**
     * 測試取得目前版本號碼
     *
     * @return void
     */
    public function testGetVersionIdTest()
    {
        $response = $this->controller->getVersionId();
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $response = $response->getOriginalContent();
        $nowVer = Version::orderBy('created_at', 'desc')->first()->version_id;
        $this->assertEquals($nowVer, $response);
    }

    /**
     * 測試取得所有版本資訊
     *
     * @return void
     */
    public function testGetAllVersionsTest()
    {
        $request = new Request();
        $request->merge(['start' => 1]);
        $response = $this->controller->getAllVersions($request);
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
