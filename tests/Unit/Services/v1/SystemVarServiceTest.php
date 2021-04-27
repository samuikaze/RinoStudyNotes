<?php

namespace Tests\Unit\Services\v1;

use App\Models\User;
use App\Models\Version;
use App\Services\v1\AuthenticationService;
use App\Services\v1\SystemVarService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SystemVarServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * SystemVarService
     *
     * @var \App\Services\SystemVarService
     */
    protected $service;

    /**
     * AuthenticationService
     *
     * @var \App\Services\AuthenticationService
     */
    protected $authService;

    /**
     * 注入 Service
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(SystemVarService::class);
        $this->authService = $this->app->make(AuthenticationService::class);
    }

    /**
     * 測試取得目前版本號碼
     *
     * @return void
     */
    public function testGetVersionIdTest()
    {
        $version = $this->service->getVersionId();

        $real = Version::orderBy('id', 'desc')->first();
        $this->assertEquals($real->version_id, $version);
    }

    /**
     * 測試取得所有版本資訊（一次 10 筆）
     *
     * @return void
     */
    public function testGetAllVersionsTest()
    {
        $start = 1;
        $versions = $this->service->getAllVersions($start);
        $this->assertNotNull($versions);
        $this->assertTrue($versions->count() > 0);
    }

    /**
     * 測試取得待審核及已經過審核的使用者
     *
     * @return void
     */
    public function testGetVerifyUsersTest()
    {
        try {
            $data = $this->service->getVerifyUsers();
            $this->assertNotNull($data);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試通過或拒絕審核
     *
     * @return void
     */
    public function testVerifyUser()
    {
        // 先新增一筆測試資料
        $username = 'testUser';
        $password = 'testPassword';
        $nickname = 'testNickName';

        $this->authService->registerUser($username, $password, $nickname);
        $user = User::where('username', $username)->first();

        try {
            $this->service->verifyUser('accept', $user->id);
            $after = User::where('id', $user->id)->first();
            $this->assertEquals(1, $after->status);
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
        $username = 'testUser';
        $password = 'testPassword';
        $nickname = 'testNickName';

        $this->authService->registerUser($username, $password, $nickname);
        $user = User::where('username', $username)->first();

        try {
            $this->service->adminAccount('disable', $user->id);
            $after = User::where('id', $user->id)->first();
            $this->assertEquals(2, $after->status);
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
        $versionId = '1.0.0';
        $content = [
            '測試內容',
        ];

        $response = $this->service->addVersion($versionId, $content);
        $this->assertNotNull($response);
        $this->assertIsInt($response);
        $real = Version::where('id', $response)->first()->content;
        $this->assertEquals(
            json_encode($content, JSON_UNESCAPED_UNICODE),
            $real
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
        $versionId = '1.0.0';
        $content = [
            '測試內容',
        ];

        $id = $this->service->addVersion($versionId, $content);
        unset($request);

        $versionId = '1.0.1';
        $content = [
            '測試內容',
        ];

        try {
            $this->service->editVersion($id, $versionId, $content);
            $version = Version::where('id', $id)->first();
            $this->assertEquals($versionId, $version->version_id);
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
        $versionId = '1.0.0';
        $content = [
            '測試內容',
        ];

        $id = $this->service->addVersion($versionId, $content);
        unset($request);

        try {
            $this->service->deleteVersion($id);
            $row = Version::where('id', $id)->count();
            $this->assertEquals(0, $row);
        } catch (Exception $e) {
            print($e->getMessage()."\n\n");
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }
}
