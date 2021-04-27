<?php

namespace Tests\Unit\Repositories\v1;

use App\Models\User;
use App\Models\Version;
use App\Repositories\v1\AuthenticationRepository;
use App\Repositories\v1\SystemVarRepository;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SystemVarRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * SystemVarReporitory
     *
     * @var \App\Repositories\v1\SystemVarRepository
     */
    protected $repository;

    /**
     * AuthenticationRepository
     *
     * @var \App\Repositories\v1\AuthenticationRepository
     */
    protected $authRepository;

    /**
     * 注入 Repository
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(SystemVarRepository::class);
        $this->authRepository = $this->app->make(AuthenticationRepository::class);
    }

    /**
     * 測試取得目前版本號碼
     *
     * @return void
     */
    public function testGetVersionIdTest()
    {
        try {
            $version = $this->repository->getVersionId();
            $this->assertNotNull($version);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得所有版本資訊
     *
     * @return void
     */
    public function testGetAllVersionsTest()
    {
        try {
            $versions = $this->repository->getAllVersions(1);
            $this->assertNotNull($versions);
            $this->assertTrue($versions->count() > 0);
        } catch (Exception $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }

    /**
     * 測試取得待審核及已經過審核的使用者
     *
     * @return void
     */
    public function testGetVerifyUsers()
    {
        try {
            $data = $this->repository->getVerifyUsers('verified');
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

        $this->authRepository->registerUser($username, $password, $nickname);
        $user = User::where('username', $username)->first();

        try {
            $this->repository->verifyUser('accept', $user->id);
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

        $this->authRepository->registerUser($username, $password, $nickname);
        $user = User::where('username', $username)->first();

        try {
            $this->repository->adminAccount('disable', $user->id);
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
        $content = json_encode([
            '測試內容'
        ], JSON_UNESCAPED_UNICODE);

        $response = $this->repository->addVersion($versionId, $content);
        $this->assertNotNull($response);
        $this->assertIsInt($response);
        $real = Version::where('id', $response)->first()->content;
        $this->assertEquals(
            $content,
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
        $content = json_encode([
            '測試內容'
        ], JSON_UNESCAPED_UNICODE);

        $id = $this->repository->addVersion($versionId, $content);
        unset($request);

        $versionId = '1.0.1';
        $content = json_encode([
            '測試內容',
        ], JSON_UNESCAPED_UNICODE);

        try {
            $this->repository->editVersion($id, $versionId, $content);
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
        $content = json_encode([
            '測試內容',
        ], JSON_UNESCAPED_UNICODE);

        $id = $this->repository->addVersion($versionId, $content);
        unset($request);

        try {
            $this->repository->deleteVersion($id);
            $row = Version::where('id', $id)->count();
            $this->assertEquals(0, $row);
        } catch (Exception $e) {
            print($e->getMessage()."\n\n");
            print($e->getTraceAsString());
            $this->assertTrue(false, '不應拋錯');
        }
    }
}
