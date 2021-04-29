<?php

namespace Tests\Unit\Controllers\v1\Api;

use App\Http\Controllers\v1\Api\SystemController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SystemControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * SystemController
     *
     * @var \App\Http\Controllers\Api\SystemController
     */
    protected $systemController;

    /**
     * 注入 Controller
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->systemController = $this->app->make(SystemController::class);
    }

    /**
     * 測試讓 Uptime Robot 檢查網站狀態用的方法
     *
     * @return void
     */
    public function testUptimeCheckTest()
    {
        $response = $this->systemController->uptimeCheck();

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
