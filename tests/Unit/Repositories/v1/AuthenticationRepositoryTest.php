<?php

namespace Tests\Unit\Repositories\v1;

use App\Models\Token;
use App\Repositories\v1\AuthenticationRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * AuthenticationRepository
     *
     * @var \App\Repositories\v1\AuthenticationRepository
     */
    protected $repository;

    /**
     * 權杖生命週期
     *
     * @var int
     */
    protected $lifetime;

    /**
     * 注入 Repository
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(AuthenticationRepository::class);
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
    public function testRegisterUser()
    {
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');

        try {
            $this->repository->registerUser(
                $usernameFaker, $passwordFaker, $usernameFaker
            );
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋出任何例外，卻拋出 '.$e->getMessage());
        }
    }

    /**
     * 測試以帳號取得使用者資料
     *
     * @return void
     */
    public function testGetUserByUsernameTest()
    {
        // 新增測試資料
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->repository->registerUser(
            $usernameFaker, $passwordFaker, $usernameFaker
        );

        try {
            $user = $this->repository->getUserByUsername($usernameFaker);
            $this->assertNotNull($user);
            $this->assertEquals($usernameFaker, $user->username);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試以 ID 取得使用者資料
     *
     * @return void
     */
    public function testGetUserByIDTest()
    {
        // 新增測試資料
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->repository->registerUser(
            $usernameFaker, $passwordFaker, $usernameFaker
        );
        $userID = $this->repository->getUserByUsername($usernameFaker)->id;

        try {
            $user = $this->repository->getUserByUserID($userID);
            $this->assertNotNull($user);
            $this->assertEquals($usernameFaker, $user->username);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試編輯使用者資料
     *
     * @return void
     */
    public function testEditProfileTest()
    {
        // 新增測試資料
        $usernameFaker = $this->generateTestData('username');
        $passwordFaker = $this->generateTestData('password');
        $this->repository->registerUser(
            $usernameFaker, $passwordFaker, $usernameFaker
        );
        $userID = $this->repository->getUserByUsername($usernameFaker)->id;

        try {
            $user = $this->repository->editProfile($userID, ['nickname' => 'test']);
            $this->assertNotNull($user);
            $this->assertEquals('test', $user->nickname);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得權杖
     *
     * @return void
     */
    public function testGetToken()
    {
        $tokenFaker = $this->generateTestData('token');
        try {
            $result = $this->repository->getToken($tokenFaker);
            $this->assertTrue(is_null($result) || $result instanceof Token);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋出任何例外，卻拋出 '.$e->getMessage());
        }
    }

    /**
     * 測試寫入權杖
     *
     * @return void
     */
    public function testGenerateToken()
    {
        $userIDFaker = $this->generateTestData('user');
        $tokenFaker = $this->generateTestData('token');
        $expireTimeFaker = $this->generateTestData('expire');

        try {
            $this->repository->generateToken($userIDFaker, $tokenFaker, $expireTimeFaker);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, '不應拋出任何例外，卻拋出 '.$e->getMessage());
        }
    }

    /**
     * 測試延長權杖生命週期
     *
     * @return void
     */
    public function testExtendExpireTime()
    {
        // 產生假權杖
        $tokenFaker = $this->generateTestData('token');
        $expireTimeFaker = $this->generateTestData('expire');
        $newExpireTimeFaker = $expireTimeFaker->addMinutes($this->lifetime);

        try {
            // 先寫一筆權杖進資料庫
            $this->repository->generateToken($this->generateTestData('user'), $tokenFaker, $expireTimeFaker);
            // 延長權杖生命週期
            $this->repository->extendExpireTime($tokenFaker, $newExpireTimeFaker);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, '處理資料庫資料時失敗，拋出 '. $e->getMessage());
        }
    }

    /**
     * 測試刪除權杖
     *
     * @return void
     */
    public function testDeleteToken()
    {
        // 產生假權杖
        $tokenFaker = $this->generateTestData('token');
        $expireTimeFaker = $this->generateTestData('expire');

        try {
            // 先寫一筆權杖進資料庫
            $this->repository->generateToken($this->generateTestData('user'), $tokenFaker, $expireTimeFaker);
            // 延長權杖生命週期
            $this->repository->deleteToken($tokenFaker);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, '處理資料庫資料時失敗，拋出 '. $e->getMessage());
        }
    }
}
