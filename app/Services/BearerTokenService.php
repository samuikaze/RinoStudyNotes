<?php

namespace App\Services;

use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BearerTokenService
{
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
    public function __construct()
    {
        $this->lifetime = env('SESSION_LIFETIME', 120);
    }

    /**
     * 產生權杖，因為會檢查資料庫中是否有重複項目，故最多嘗試 10 次
     *
     * @param int $userID 使用者 ID
     * @return string|bool 返回產生的權杖，失敗時會返回 false
     */
    public function generateToken(int $userID)
    {
        $plainToken = '';

        for ($i = 0; $i < 10; $i++) {
            $plainToken = Str::random(120);
            $duplicate = Token::where('token', $plainToken)
                              ->where('expire_at', '>', Carbon::now())
                              ->count();
            if ($duplicate < 1) {
                break;
            }
        }

        if (strlen($plainToken) == 0) {
            return false;
        }

        Token::create([
            'user_of' => $userID,
            'token' => $plainToken,
            'expire_at' => Carbon::now()->addMinutes($this->lifetime),
        ]);

        $token = encrypt($plainToken);

        $this->GC();

        return $token;
    }

    /**
     * 驗證權杖
     *
     * @param string|null $token 由前端傳入的權杖
     * @return int|bool 回傳使用者 ID，失敗時返回 false
     */
    public function verifyToken(string $token = null)
    {
        if (is_null($token) || $token == 'null') {
            return false;
        }

        $plainToken = decrypt($token);

        $token = Token::where('token', $plainToken)->first();

        if (empty($token)) {
            return false;
        }

        $this->GC();

        return $token->user_of;
    }

    /**
     * 刪除權杖
     *
     * @param string $token 由前端傳入的權杖
     * @return bool
     */
    public function removeToken(string $token)
    {
        $plainToken = decrypt($token);

        Token::where('token', $plainToken)->delete();

        $this->GC();

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

        Token::where('token', $plainToken)->update([
            'expire_at' => Carbon::now()->addMinutes($this->lifetime),
        ]);

        $this->GC();

        return true;
    }

    /**
     * 回收機制，將已過期的權杖從資料庫中移除
     *
     * @return void
     */
    protected function GC()
    {
        Token::where('expire_at', '<', Carbon::now())->delete();
    }
}
