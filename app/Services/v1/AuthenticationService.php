<?php

namespace App\Services\v1;

use App\Exceptions\AuthenticationException;
use App\Repositories\v1\AuthenticationRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticationService
{
    const LOGIN_ERROR = '帳號或密碼不正確';

    /**
     * 驗證儲存庫
     *
     * @var \App\Repositories\AuthenticationRepository
     */
    protected $auth;

    /**
     * 權杖有效期間
     *
     * @var int
     */
    protected $lifetime;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(AuthenticationRepository $auth)
    {
        $this->auth = $auth;
        $this->lifetime = (int) env('SESSION_LIFETIME', 120);
    }

    /**
     * 註冊帳號
     *
     * @param string $username 帳號
     * @param string $password 加密過的密碼
     * @param string|null $nickname 暱稱或帳號
     * @return void
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function registerUser(
        string $username,
        string $password,
        string $nickname = null
    ) {
        $password = Hash::make($password);
        $nickname = (is_null($nickname)) ? $username : $nickname;

        $this->auth->registerUser($username, $password, $nickname);
    }

    /**
     * 驗證登入狀態及是否被停權
     *
     * @return bool
     */
    public function verifyAuthStatus()
    {
        if (Auth::check()) {
            return Auth::user()->status != 2;
        }

        return false;
    }

    /**
     * 登入
     *
     * @param string $username 帳號
     * @param array $credentials ['username', 'password'] 帳號及密碼的陣列
     * @return int|bool 成功返回整數，否則返回 false
     */
    public function login(array $credentials)
    {
        try {
            $user = $this->auth->getUserByUsername($credentials['username']);
        } catch (Exception $e) {
            return false;
        }

        if (
            !is_null($user)
            && $user->status != 2
        ) {
            if (Auth::attempt($credentials)) {
                return $user->id;
            }
        }

        return false;
    }

    /**
     * 登出
     *
     * @return void
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function logout()
    {
        $token = session()->get('user-token');
        if (!is_null($token)) {
            $this->removeToken($token);
        }

        Auth::logoutCurrentDevice();
        session()->regenerate();
    }

    /**
     * 取得使用者資訊
     *
     * @param int $userID 使用者 ID
     * @param bool $passwordVisible [false] 是否設定密碼可以存取
     * @return \App\Models\User|false
     */
    public function retrievingUserInfo(int $userID, bool $passwordVisible = false)
    {
        $user = $this->auth->getUserByUserID($userID, $passwordVisible);

        if (!is_null($user)) {
            return $user;
        }

        return false;
    }

    /**
     * 編輯使用者資料
     *
     * @param array $profile
     * @return \App\Models\User 新使用者資料
     *
     * @throws \App\Exceptions\AuthenticationException
     * @throws \App\Exceptions\DatabaseException
     */
    public function editProfile(array $profile)
    {
        $user = $this->retrievingUserInfo(Auth::user()->id, true);

        $profile['nickname'] = is_null($profile['nickname'])
                             ? $user->username
                             : $profile['nickname'];

        if (array_key_exists('newPswd', $profile) && !is_null($profile['newPswd'])) {
            if (! Hash::check($profile['origPswd'], $user->password)) {
                throw new AuthenticationException('密碼不正確');
            }
            $profile['newPswd'] = Hash::make($profile['newPswd']);
        } else {
            $profile['newPswd'] = null;
        }

        $newUser = $this->auth->editProfile(Auth::user()->id, $profile);

        return $newUser;
    }

    /**
     * 產生權杖，因為會檢查資料庫中是否有重複項目，故最多嘗試 10 次
     *
     * @param int $userID 使用者 ID
     * @return string|bool 返回產生的權杖，失敗時會返回 false
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function generateToken(int $userID)
    {
        $plainToken = '';

        for ($i = 0; $i < 10; $i++) {
            $plainToken = Str::random(120);
            try {
                $duplicate = $this->auth->getToken($plainToken);
            } catch (Exception $e) {
                continue;
            }

            if (!is_null($duplicate)) {
                break;
            }
        }

        if (strlen($plainToken) == 0) {
            return false;
        }

        $this->auth->generateToken(
            $userID,
            $plainToken,
            Carbon::now()->addMinutes($this->lifetime)
        );

        $token = encrypt($plainToken);

        session()->put('user-token', $token);

        return $token;
    }

    /**
     * 驗證權杖
     *
     * @param string|null $token 由前端傳入的權杖
     * @return int|bool 回傳使用者 ID，失敗時返回 false
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function verifyToken(string $token = null)
    {
        if (is_null($token) || $token == 'null') {
            return false;
        }

        $plainToken = decrypt($token);

        $token = $this->auth->getToken($plainToken);

        if (is_null($token)) {
            return false;
        }

        return $token->user_of;
    }

    /**
     * 刪除權杖
     *
     * @param string|null $token 由前端傳入的權杖
     * @return bool
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function removeToken(string $token = null)
    {
        if (is_null($token)) {
            return false;
        }

        $plainToken = decrypt($token);

        $this->auth->deleteToken($plainToken);

        return true;
    }

    /**
     * 延長權杖生命週期
     *
     * @param string $token 由前端傳入的權杖
     * @return bool
     */
    public function extendExpireTime(string $token)
    {
        $plainToken = decrypt($token);

        $this->auth
            ->extendExpireTime(
                $plainToken,
                Carbon::now()->addMinutes($this->lifetime)
            );

        return true;
    }
}
