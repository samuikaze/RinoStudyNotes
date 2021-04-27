<?php

namespace Tests\Unit\Controllers\v1\Backend;

use App\Http\Controllers\v1\Backend\AuthenticationController;
use App\Http\Controllers\v1\Backend\SystemVarController;
use App\Models\User;
use App\Models\Version;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class SystemVarControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * SystemVarController
     *
     * @var \App\Http\Controllers\Backend\SystemVarController
     */
    protected $controller;

    /**
     * AuthenticationController
     *
     * @var \App\Http\Controllers\Backend\AuthenticationController
     */
    protected $authController;

    /**
     * 注入 Controller
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = $this->app->make(SystemVarController::class);
        $this->authController = $this->app->make(AuthenticationController::class);
    }

    /**
     * 測試取得待審核及已經過審核的使用者
     */
    public function testGetVerifyUsersTest()
    {
        $response = $this->controller->getVerifyUsers();
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * 測試通過或拒絕審核
     *
     * @return void
     */
    public function testVerifyUser()
    {
        // 先新增一筆測試資料
        $testRequestData = new Request();
        $testRequestData->merge([
            'username' => 'testUser',
            'password' => 'testPassword',
            'password_confirmation' => 'testPassword',
            'nickname' => 'testNickName',
        ]);
        $this->authController->register($testRequestData);
        $testData = $this->controller->getVerifyUsers()->getOriginalContent();
        $testData = collect($testData['verifying'])->last();

        $request = new Request();
        $request->merge([
            'id' => $testData['id'],
            'type' => 'accept',
        ]);

        try {
            $response = $this->controller->verifyUser($request);
            $this->assertNotNull($response);
            $this->assertEquals(200, $response->getStatusCode());
            $status = User::where('id', $request->input('id'))->first()->status;
            $this->assertEquals(1, $status);
        } catch (Exception $e) {
            print($e->getMessage()."\n\n");
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試停權或復權帳號
     *
     * @return void
     */
    public function testAdminAccountTest()
    {
        // 先新增一筆測試資料
        $testRequestData = new Request();
        $testRequestData->merge([
            'username' => 'testUser',
            'password' => 'testPassword',
            'password_confirmation' => 'testPassword',
            'nickname' => 'testNickName',
        ]);
        $this->authController->register($testRequestData);
        $testData = $this->controller->getVerifyUsers()->getOriginalContent();
        $testData = collect($testData['verifying'])->last();

        $request = new Request();
        $request->merge([
            'id' => $testData['id'],
            'type' => 'disable',
        ]);

        try {
            $response = $this->controller->adminAccount($request);
            $this->assertNotNull($response);
            $this->assertEquals(200, $response->getStatusCode());
            $status = User::where('id', $request->input('id'))->first()->status;
            $this->assertEquals(2, $status);
        } catch (Exception $e) {
            print($e->getMessage()."\n\n");
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試新增版本
     *
     * @return void
     */
    public function testAddVersionTest()
    {
        $request = new Request();
        $request->merge([
            'version_id' => '1.0.0',
            'content' => [
                '測試內容',
            ],
        ]);

        $response = $this->controller->addVersion($request);
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsInt($response->getOriginalContent());
        $content = Version::where('id', $response->getOriginalContent())->first()->content;
        $this->assertEquals(
            json_encode($request->input('content'), JSON_UNESCAPED_UNICODE),
            $content
        );
    }

    /**
     * 測試編輯版本資料
     *
     * @return void
     */
    public function testEditVersionTest()
    {
        // 先新增一筆測試資料
        $request = new Request();
        $request->merge([
            'version_id' => '1.0.0',
            'content' => [
                '測試內容',
            ],
        ]);

        $response = $this->controller->addVersion($request);
        $id = $response->getOriginalContent();
        unset($response, $request);

        $request = new Request();
        $request->merge([
            'id' => $id,
            'version_id' => '1.0.1',
            'content' => [
                '測試內容',
            ],
        ]);

        try {
            $this->controller->editVersion($request);
            $version = Version::where('id', $id)->first()->version_id;
            $this->assertEquals($request->input('version_id'), $version);
        } catch (Exception $e) {
            print($e->getMessage()."\n\n");
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試刪除版本資料
     *
     * @return void
     */
    public function testDeleteVersionTest()
    {
        // 先新增一筆測試資料
        $request = new Request();
        $request->merge([
            'version_id' => '1.0.0',
            'content' => [
                '測試內容',
            ],
        ]);

        $response = $this->controller->addVersion($request);
        $id = $response->getOriginalContent();
        unset($response, $request);

        $request = new Request();
        $request->merge(['id' => $id]);

        try {
            $response = $this->controller->deleteVersion($request);
            $this->assertNotNull($response);
            $this->assertEquals(200, $response->getStatusCode());
            $row = Version::where('id', $id)->count();
            $this->assertEquals(0, $row);
        } catch (Exception $e) {
            print($e->getMessage()."\n\n");
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }
}
