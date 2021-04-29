<?php

namespace App\Repositories\v1;

use App\Exceptions\DatabaseException;
use App\Exceptions\NoResultException;
use App\Models\User;
use App\Models\Version;
use Exception;

class SystemVarRepository
{
    /**
     * User Model
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Version Model
     *
     * @var \App\Models\Version
     */
    protected $version;

    /**
     * Lazyload 時單次讀取資料筆數
     *
     * @var int
     */
    protected $lazyload;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(User $user, Version $version) {
        $this->user = $user;
        $this->version = $version;
        $this->lazyload = config('database.lazyload');
    }

    /**
     * 取得目前版本號碼
     *
     * @return string|null 目前版本號碼
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function getVersionId()
    {
        try {
            $versionID = $this->version->orderBy('id', 'desc')->first();
        } catch (Exception $e) {
            throw new DatabaseException('目前無法取得版本號碼');
        }

        return is_null($versionID) ? null : $versionID->version_id;
    }

    /**
     * 取得所有版本資訊
     *
     * @param int $start 請求次數
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function getAllVersions(int $start)
    {
        $start = ($start - 1) * $this->lazyload;

        try {
            return $this->version
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($this->lazyload)
                        ->get();
        } catch (Exception $e) {
            throw new DatabaseException('讀取資料失敗');
        }
    }

    /**
     * 取得待審核及已經過審核的使用者
     *
     * @param string $type [verifying|verified] 要取得的使用者種類
     * @return array 待審核及已經過審核的使用者
     */
    public function getVerifyUsers(string $type)
    {
        $data = null;

        switch ($type) {
            case 'verifying':
                $data = $this->user
                             ->where('status', 0)
                             ->get();
                break;
            case 'verified':
                $data = $this->user
                             ->where('status', '!=', 0)
                             ->where('id', '!=', 1)
                             ->get();
                break;
        }

        return $data->toArray();
    }

    /**
     * 通過或拒絕審核
     *
     * @param string $type [accept|denied] 更新種類
     * @param int $id 使用者 ID
     * @return void
     */
    public function verifyUser(string $type, int $id)
    {
        switch ($type) {
            case 'accept':
                $this->user
                     ->where('id', $id)
                     ->update([
                         'role_of' => 3,
                         'status' => 1,
                     ]);
                break;
            case 'denied':
                $this->user
                     ->where('id', $id)
                     ->update([
                         'role_of' => 2,
                         'status' => 1,
                     ]);
                break;
        }
    }

    /**
     * 停權或復權帳號
     *
     * @param string $type [enable|disable] 停權或復權
     * @param int $id 使用者 ID
     * @return void
     */
    public function adminAccount(string $type, int $id)
    {
        switch ($type) {
            case 'enable':
                $this->user
                     ->where('id', $id)
                     ->update([
                         'status' => 1,
                     ]);
                break;
            case 'disable':
                $this->user
                     ->where('id', $id)
                     ->update([
                         'status' => 2,
                     ]);
                break;
        }
    }

    /**
     * 新增版本
     *
     * @param string $versionID 版本號碼
     * @param string $versionContent 轉為 JSON 後的更新內容
     * @return int 新增後的版本 ID
     */
    public function addVersion(string $versionID, string $versionContent)
    {
        $new = $this->version
                    ->create([
                        'version_id' => $versionID,
                        'content' => $versionContent,
                    ]);

        return $new->id;
    }

    /**
     * 取得單一版本資料
     *
     * @param int $id 版本 ID
     * @return \App\Models\Version
     *
     * @throws \App\Exceptions\NoResultException
     */
    protected function findVersion(int $id)
    {
        $data = $this->version->where('id', $id)->first();
        if (is_null($data)) {
            throw new NoResultException('找不到該版本資料，請再次確認您的資料是否正確');
        }

        return $data;
    }

    /**
     * 編輯版本資料
     *
     * @param int $id 版本 ID
     * @param string $versionID 版本號碼
     * @param string $versionContent 轉為 JSON 後的更新內容
     * @return void 新增後的版本 ID
     *
     * @throws \App\Exceptions\NoResultException
     */
    public function editVersion(int $id, string $versionID, string $versionContent)
    {
        $this->findVersion($id);

        $this->version
             ->where('id', $id)
             ->update([
                 'version_id' => $versionID,
                 'content' => $versionContent,
             ]);
    }

    /**
     * 刪除版本資料
     *
     * @param int $id 版本 ID
     * @return void
     *
     * @throws \App\Exceptions\NoResultException
     */
    public function deleteVersion(int $id)
    {
        $this->findVersion($id);

        $this->version
             ->where('id', $id)
             ->delete();

        $this->version
             ->selectRaw('ALTER TABLE `versions` AUTO_INCREMENT = '.$id);
    }
}
