<?php

namespace Tests\Unit\Services\v1;

use App\Services\v1\ResponseService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ResponseServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * ResponseService
     *
     * @var \App\Services\ResponseService
     */
    protected $service;

    /**
     * 注入 Service
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ResponseService::class);
    }

    /**
     * 測試設定回應碼
     *
     * @return void
     */
    public function testSetCodeTest()
    {
        $code = $this->service::OK;

        try {
            $res = $this->service->setCode($code);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試設定回應資料
     *
     * @return void
     */
    public function testSetDataTest()
    {
        $data = [
            'test',
        ];

        try {
            $res = $this->service->setData($data);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試設定錯誤資料
     *
     * @return void
     */
    public function testSetErrorTest()
    {
        $data = [
            'test',
        ];

        try {
            $res = $this->service->setError($data);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試設定標頭
     *
     * @return void
     */
    public function testSetHeadersTest()
    {
        $headers = [
            'test',
        ];

        try {
            $res = $this->service->setError($headers);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試設定視圖名稱
     *
     * @return void
     */
    public function testSetViewTest()
    {
        $headers = 'index';

        try {
            $res = $this->service->setView($headers);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試設定重新導向目的地
     *
     * @return void
     */
    public function testRedirectTargetTest()
    {
        $target = 'logout';

        try {
            $res = $this->service->setRedirectTargetName($target);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試設定 Cookies
     *
     * @return void
     */
    public function testCookiesTest()
    {
        $cookie = [
            cookie('test', 'test'),
        ];

        try {
            $res = $this->service->setCookies($cookie);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試設定錯誤訊息 (Laravel Session)
     *
     * @return void
     */
    public function testErrorMsgTest()
    {
        $error = 'test';

        try {
            $res = $this->service->setErrorMsg($error);
            $this->assertTrue($res instanceof ResponseService);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試返回 JSON 格式回應
     *
     * @return void
     */
    public function testJsonTest()
    {
        $this->service = new ResponseService();

        try {
            $res = $this->service->setCode(200)->setData('test')->json();
            $this->assertNotNull($res);
            $this->assertEquals('test', $res->getOriginalContent());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試返回視圖
     *
     * @return void
     */
    public function testViewTest()
    {
        $this->service = new ResponseService();

        try {
            $view = $this->service->setView('index')->view();
            $this->assertEquals('index', $view->name());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試重新導向回應
     *
     * @return void
     */
    public function testRedirectTest()
    {
        $this->service = new ResponseService();

        try {
            $res = $this->service->setRedirectTarget('logout')->redirect();
            $this->assertEquals(route('logout'), $res->getTargetUrl());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試返回（重導回）上一個路由
     *
     * @return void
     */
    public function testBackTest()
    {
        $this->service = new ResponseService();

        try {
            $res = $this->service->back();
            $this->assertEquals(302, $res->getStatusCode());
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }
}
