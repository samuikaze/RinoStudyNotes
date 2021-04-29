<?php

namespace Tests\Unit\Services\v1;

use App\Models\Token;
use App\Models\User;
use App\Services\v1\AuthenticationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * AuthenticationService
     *
     * @var \App\Services\AuthenticationService
     */
    protected $service;

    /**
     * 權杖生命週期
     *
     * @var int
     */
    protected $lifetime;

    /**
     * 注入 Service
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AuthenticationService::class);
        $this->lifetime = env('SESSION_LIFETIME', 120);
    }

    /**
     * 產生測試資料
     *
     * @param string $type <token|user|expire> 種類
     * @return string|int|\Carbon\Carbon 返回指定的測試資料
     */
    protected function generateTestData(string $type)
    {
        switch ($type) {
            case 'username':
                return 'test';
                break;
            case 'password':
                return Hash::make('test');
                break;
            case 'token':
                return Str::random(120);
                break;
            case 'user':
                return 1;
                break;
            case 'expire':
                return Carbon::now()->addMinutes($this->lifetime);
                break;
        }
    }

    /**
     * 測試註冊帳號
     *
     * @return void
     */
    public function testRegisterUserTest()
    {
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');

        try {
            $this->service->registerUser($usernameFaker, $passwordFaker);
            $row = User::where('username', $usernameFaker)->count();
            $this->assertEquals(1, $row);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
            print($e->getMessage());
            print($e->getTraceAsString());
        }

    }

    /**
     * 測試驗證登入狀態及是否被停權
     *
     * @return void
     */
    public function testVerifyAuthStatusTest()
    {
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->service->registerUser($usernameFaker, $passwordFaker);
        Auth::attempt([
            'username' => $usernameFaker,
            'password' => $passwordFaker
        ]);

        try {
            $auth = $this->service->verifyAuthStatus();
            $this->assertTrue($auth);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
            print($e->getMessage());
            print($e->getTraceAsString());
        }
    }

    /**
     * 測試登入
     *
     * @return void
     */
    public function testLoginTest()
    {
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->service->registerUser($usernameFaker, $passwordFaker);
        $credentials = [
            'username' => $usernameFaker,
            'password' => $passwordFaker,
        ];

        $success = $this->service->login($credentials);
        $this->assertTrue($success !== false);
    }

    /**
     * 測試登出
     *
     * @return void
     */
    public function testLogoutTest()
    {
        try {
            $this->service->logout();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
            print($e->getMessage());
            print($e->getTraceAsString());
        }
    }

    /**
     * 測試取得使用者資訊
     *
     * @return void
     */
    public function testRetrievingUserInfoTest()
    {
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->service->registerUser($usernameFaker, $passwordFaker);
        $id = $this->service->login([
            'username' => $usernameFaker,
            'password' => $passwordFaker,
        ]);

        $user = $this->service->retrievingUserInfo($id);
        $this->assertTrue($user !== false);
        $this->assertTrue($user instanceof User);
    }

    /**
     * 測試編輯使用者資訊
     *
     * @return void
     */
    public function testEditProfileTest()
    {
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->service->registerUser($usernameFaker, $passwordFaker);
        $id = $this->service->login([
            'username' => $usernameFaker,
            'password' => $passwordFaker,
        ]);

        $profile = [
            'nickname' => 'test',
        ];
        $this->service->editProfile($profile);
        $user = User::where('username', $usernameFaker)->first();
        $this->assertEquals($profile['nickname'], $user->nickname);
    }

    /**
     * 測試產生權杖
     *
     * @return void
     */
    public function testGenerateTokenTest()
    {
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->service->registerUser($usernameFaker, $passwordFaker);
        $id = $this->service->login([
            'username' => $usernameFaker,
            'password' => $passwordFaker,
        ]);

        try {
            $result = $this->service->generateToken($id);
            $this->assertIsString($result);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋錯');
            print($e->getMessage());
            print($e->getTraceAsString());
        }
    }

    /**
     * 測試驗證權杖
     *
     * @return void
     */
    public function testVerifyTokenTest()
    {
        $userFaker = $this->generateTestData('user');
        $tokenFaker = $this->generateTestData('token');
        $expireFaker = $this->generateTestData('expire');

        Token::create([
            'user_of' => $userFaker,
            'token' => $tokenFaker,
            'expire_at' => $expireFaker,
        ]);

        $tokenFaker = encrypt($tokenFaker);
        $test = $this->service->verifyToken($tokenFaker);
        $this->assertIsInt($test);
        $this->assertEquals($userFaker, $test);
    }

    /**
     * 測試刪除權杖
     *
     * @return void
     */
    public function testRemoveToken()
    {
        $userFaker = $this->generateTestData('user');
        $tokenFaker = $this->generateTestData('token');
        $expireFaker = $this->generateTestData('expire');

        Token::create([
            'user_of' => $userFaker,
            'token' => $tokenFaker,
            'expire_at' => $expireFaker,
        ]);

        $tokenFaker = encrypt($tokenFaker);
        $test = $this->service->removeToken($tokenFaker);
        $this->assertIsBool($test);
        $this->assertEquals(true, $test);
        $row = Token::where('token', $tokenFaker)->count();
        $this->assertEquals(0, $row);
    }

    /**
     * 測試延長權杖生命週期
     *
     * @return void
     */
    public function testExtendExpireTime()
    {
        $userFaker = $this->generateTestData('user');
        $tokenFaker = $this->generateTestData('token');
        $expireFaker = $this->generateTestData('expire');

        Token::create([
            'user_of' => $userFaker,
            'token' => $tokenFaker,
            'expire_at' => $expireFaker,
        ]);

        $tokenFaker = encrypt($tokenFaker);
        $test = $this->service->extendExpireTime($tokenFaker);
        $this->assertIsBool($test);
        $this->assertEquals(true, $test);
    }
}
