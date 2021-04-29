<?php

namespace App\Repositories\v1;

use App\Exceptions\DatabaseException;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthenticationRepository
{
    /**
     * Token Model
     *
     * @var \App\Models\Token
     */
    protected $token;

    /**
     * User Model
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * 建構函式
     *
     * @param \App\Models\Token $token
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(Token $token, User $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    /**
     * 註冊帳號
     *
     * @param string $username 帳號
     * @param string $password 加密過的密碼
     * @param string $nickname 暱稱或帳號
     * @return void
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function registerUser(string $username, string $password, string $nickname)
    {
        DB::beginTransaction();

        try {
            $this->user->create([
                'username' => $username,
                'password' => $password,
                'nickname' => $nickname,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }
    }

    /**
     * 以帳號取得使用者資料
     *
     * @param string $username 帳號
     * @param bool $passwordVisible [false] 是否設定密碼可以存取
     * @return \App\Models\User|null
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function getUserByUsername(string $username, bool $passwordVisible = false)
    {
        try {
            $user = $this->user->where('username', $username)->first();
        } catch (Exception $e) {
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }

        if ($passwordVisible === true) {
            $user = $user->makeVisible(['password']);
        }

        return $user;
    }

    /**
     * 以 ID 取得使用者資料
     *
     * @param string $username 帳號
     * @param bool $passwordVisible [false] 是否設定密碼可以存取
     * @return \App\Models\User|null
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function getUserByUserID(int $userID, bool $passwordVisible = false)
    {
        try {
            $user = $this->user->where('id', $userID)->first();
        } catch (Exception $e) {
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }

        if ($passwordVisible === true) {
            $user = $user->makeVisible(['password']);
        }

        return $user;
    }

    /**
     * 編輯使用者資料
     *
     * @param int $userID 目標使用者資料
     * @param array $profile 欲更新的使用者資料
     * @return \App\Models\User 新使用者資料
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function editProfile(int $userID, array $profile)
    {
        DB::beginTransaction();

        try {
            if (! empty($profile['newPswd'])) {
                $this->user->where('id', $userID)->update([
                    'nickname' => $profile['nickname'],
                    'password' => $profile['newPswd'],
                ]);
            } else {
                $this->user->where('id', $userID)->update([
                    'nickname' => $profile['nickname'],
                ]);

            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }

        return $this->getUserByUserID($userID);
    }

    /**
     * 垃圾回收，將已過期的權杖從資料庫中移除
     *
     * @return void
     */
    protected function GC()
    {
        DB::beginTransaction();

        try {
            $this->token->where('expire_at', '<', Carbon::now())->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * 取得權杖
     *
     * @param string $token 產生的權杖
     * @return \App\Models\Token|null 返回查詢結果
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function getToken(string $token)
    {
        try {
            $result = $this->token
                ->where('token', $token)
                ->where('expire_at', '>', Carbon::now())
                ->first();
        } catch (Exception $e) {
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }

        $this->GC();

        return $result;
    }

    /**
     * 寫入權杖
     *
     * @param int $userID 使用者 ID
     * @param string $token 由服務產生的權杖
     * @param \Carbon\Carbon $expireTime 失效時間
     * @return void
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function generateToken(int $userID, string $token, Carbon $expireTime)
    {
        DB::beginTransaction();

        try {
            $this->token->create([
                'user_of' => $userID,
                'token' => $token,
                'expire_at' => $expireTime,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }

        $this->GC();
    }

    /**
     * 延長權杖生命週期
     *
     * @param string $token 要延長生命周期的權杖
     * @param \Carbon\Carbon $newExpireTime 新過期時間
     * @return void
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function extendExpireTime(string $token, Carbon $newExpireTime)
    {
        DB::beginTransaction();

        try {
            $this->token
                ->where('token', $token)
                ->update([
                    'expire_at' => $newExpireTime,
                ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }
    }

    /**
     * 刪除權杖
     *
     * @param string $token
     * @return void
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function deleteToken(string $token)
    {
        DB::beginTransaction();

        try {
            $this->token->where('token', $token)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $msg = $e->getFile().'('.$e->getLine().'): '.$e->getMessage();
            throw new DatabaseException($msg);
        }

        $this->GC();
    }
}
