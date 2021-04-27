<?php

namespace Tests\Unit\Controllers\v1\Backend;

use App\Http\Controllers\v1\Backend\AuthenticationController;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * AuthenticationController
     *
     * @var \App\Http\Controllers\Backend\AuthenticationController
     */
    protected $authController;

    /**
     * 注入 controller
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authController = $this->app->make(AuthenticationController::class);
    }

    /**
     * 測試註冊功能
     *
     * @return void
     */
    public function testRegisterTest()
    {
        // 正常註冊
        $request = new Request();
        $request->merge([
            'username' => 'testuser',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
            'nickname' => 'testUserNickname',
        ]);

        $response = $this->authController->register($request);
        $this->assertNotNull($response);
        $targetUri = explode('/', $response->getTargetUrl());
        $targetUri = $targetUri[count($targetUri) - 1];
        $this->assertEquals('admin', $targetUri);

        $this->authController->logout();

        // 重複註冊
        $request = new Request();
        $request->merge([
            'username' => 'testuser',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
            'nickname' => 'testUserNickname',
        ]);

        $response = $this->authController->register($request);
        $this->assertNotNull($response);
        $this->assertEquals(302, $response->getStatusCode());
        $targetUri = explode('/', $response->getTargetUrl());
        $targetUri = $targetUri[count($targetUri) - 1];
        $this->assertNotEquals('admin', $targetUri);

        // 使用不允許的字元註冊帳號
        $request = new Request();
        $request->merge([
            'username' => 'administrator',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
            'nickname' => 'testUserNickname',
        ]);

        $response = $this->authController->register($request);
        $this->assertNotNull($response);
        $this->assertEquals(302, $response->getStatusCode());
        $targetUri = explode('/', $response->getTargetUrl());
        $targetUri = $targetUri[count($targetUri) - 1];
        $this->assertNotEquals('admin', $targetUri);

        // 密碼兩次輸入不一樣
        $request = new Request();
        $request->merge([
            'username' => 'administrator',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword123',
            'nickname' => 'testUserNickname',
        ]);

        $response = $this->authController->register($request);
        $this->assertNotNull($response);
        $this->assertEquals(302, $response->getStatusCode());
        $targetUri = explode('/', $response->getTargetUrl());
        $targetUri = $targetUri[count($targetUri) - 1];
        $this->assertNotEquals('admin', $targetUri);
    }

    /**
     * 測試登入功能
     *
     * @return void
     */
    public function testLoginTest()
    {
        $username = 'testuser';
        $password = 'testpassword';

        // 先註冊一支帳號測試
        $request = new Request();
        $request->merge([
            'username' => $username,
            'password' => $password,
            'password_confirmation' => $password,
            'nickname' => 'testUserNickname',
        ]);
        $response = $this->authController->register($request);

        // 正常登入
        $request = new Request();
        $request->merge([
            'username' => $username,
            'password' => $password,
        ]);
        $response = $this->authController->login($request);
        $this->assertNotNull($response);
        $targetUri = explode('/', $response->getTargetUrl());
        $targetUri = $targetUri[count($targetUri) - 1];
        $this->assertEquals('admin', $targetUri);

        // 使用不正確的密碼登入
        $request = new Request();
        $request->merge([
            'username' => $username,
            'password' => $password.'unformed',
        ]);
        $response = $this->authController->login($request);
        $this->assertNotNull($response);
        $targetUri = explode('/', $response->getTargetUrl());
        $targetUri = $targetUri[count($targetUri) - 1];
        $this->assertNotEquals('admin', $targetUri);

        // 使用不存在的帳號登入
        $request = new Request();
        $request->merge([
            'username' => $username.'unformed',
            'password' => $password,
        ]);
        $response = $this->authController->login($request);
        $this->assertNotNull($response);
        $targetUri = explode('/', $response->getTargetUrl());
        $targetUri = $targetUri[count($targetUri) - 1];
        $this->assertNotEquals('admin', $targetUri);
    }

    /**
     * 測試登出功能
     *
     * @return void
     */
    public function testLogoutTest()
    {
        try {
            $response = $this->authController->logout();
            $this->assertNotNull($response);
            $targetUri = explode('/', $response->getTargetUrl());
            $targetUri = $targetUri[count($targetUri) - 1];
            $this->assertNotEquals('admin', $targetUri);
        } catch (Exception $e) {
            $this->assertTrue(false, '拋出'.$e);
        }
    }

    /**
     * 測試取得登入的使用者資訊
     *
     * @return void
     */
    public function testUserInfoTest()
    {
        $username = 'testuser';
        $password = 'testpassword';

        // 先註冊一支帳號測試
        $request = new Request();
        $request->merge([
            'username' => $username,
            'password' => $password,
            'password_confirmation' => $password,
            'nickname' => 'testUserNickname',
        ]);
        $response = $this->authController->register($request);

        // 正常登入
        $request = new Request();
        $request->merge([
            'username' => $username,
            'password' => $password,
        ]);
        $response = $this->authController->login($request);

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer '.$response->getSession()->get('user-token'));

        $user = User::first();
        $response = $this->actingAs($user)
                         ->authController
                         ->userInfo($request);

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $response = $response->getOriginalContent();
        $this->assertEquals($username, $response->username);
    }

    /**
     * 測試編輯使用者資料
     *
     * @return void
     */
    public function testEditProfileTest()
    {
        $username = 'testuser';
        $password = 'testpassword';

        // 先註冊一支帳號測試
        $request = new Request();
        $request->merge([
            'username' => $username,
            'password' => $password,
            'password_confirmation' => $password,
            'nickname' => 'testUserNickname',
        ]);
        $response = $this->authController->register($request);

        $newPassword = 'new'.$password;
        $request = new Request();
        // 先測試全填
        $request->merge([
            'nickname' => '測試暱稱',
            'origPswd' => $password,
            'newPswd' => $newPassword,
            'newPswd_confirmation' => $newPassword,
        ]);

        $response = $this->authController->editProfile($request);
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $response = $response->getOriginalContent();
        $this->assertTrue(Hash::check($newPassword, $response->password));

        $request = new Request();
        // 測試密碼未填（應當要正常更新）
        $request->merge([
            'nickname' => '測試暱稱',
        ]);

        $response = $this->authController->editProfile($request);
        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $response = $response->getOriginalContent();
        $this->assertEquals('測試暱稱', $response->nickname);

        // 測試密碼確認欄位未填（應當回應 400）
        $request = new Request();
        // 先測試全填
        $request->merge([
            'nickname' => '測試暱稱',
            'origPswd' => $password,
            'newPswd' => $newPassword,
        ]);

        $response = $this->authController->editProfile($request);
        $this->assertNotNull($response);
        $this->assertEquals(400, $response->getStatusCode());
    }
}
