<?php

namespace App\Services\v1;

use App\Repositories\v1\SystemVarRepository;
use Exception;

class SystemVarService
{
    /**
     * Version 儲存庫
     *
     * @var \App\Repositories\v1\SystemVarRepository
     */
    protected $sysvar;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(SystemVarRepository $sysvar)
    {
        $this->sysvar = $sysvar;
    }

    /**
     * 取得目前版本號碼
     *
     * @return string|null 目前版本號碼
     */
    public function getVersionId()
    {
        try {
            return $this->sysvar->getVersionId();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 取得所有版本資訊（一次 10 筆）
     *
     * @param int $start 請求次數
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function getAllVersions(int $start)
    {
        return $this->sysvar->getAllVersions($start);
    }

    /**
     * 取得待審核及已經過審核的使用者
     *
     * @return array 待審核及已經過審核的使用者
     */
    public function getVerifyUsers()
    {
        $verifying = $this->sysvar->getVerifyUsers('verifying');
        $verified = $this->sysvar->getVerifyUsers('verified');

        $users = [
            'verifying' => $verifying,
            'verified' => $verified,
        ];

        return $users;
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
        $this->sysvar->verifyUser($type, $id);
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
        $this->sysvar->adminAccount($type, $id);
    }

    /**
     * 新增或編輯版本資料
     *
     * @param string $versionID 版本號碼
     * @param array $versionContent 更新內容
     * @return int 新增後的版本 ID
     */
    public function addVersion(string $versionID, array $versionContent)
    {
        $versionContent = json_encode($versionContent, JSON_UNESCAPED_UNICODE);
        return $this->sysvar->addVersion($versionID, $versionContent);
    }

    /**
     * 更新版本資料
     *
     * @param int $id 版本 ID
     * @param string $versionID 版本號碼
     * @param array $versionContent 更新內容
     * @return void
     *
     * @throws \App\Exceptions\NoResultException
     */
    public function editVersion(int $id, string $versionID, array $versionContent)
    {
        $versionContent = json_encode($versionContent, JSON_UNESCAPED_UNICODE);
        $this->sysvar->editVersion($id, $versionID, $versionContent);
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
        $this->sysvar->deleteVersion($id);
    }
}
