<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
     * 將使用者資訊放入緩存中
     * 
     * @param \Illuminate\Support\Collection|array|null $data 使用者資料
     * @param string|null $token 權杖
     * @return string 權杖
     */
    public function putUserInformation($data = null, string $token = null)
    {
        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        if (is_null($token)) {
            do {
                $token = Str::random(120);
                $result = Cache::get($token);
            } while (!empty($result));

            Cache::put($token, $data, Carbon::now()->addMinutes($this->lifetime));
        } else {
            Cache::put($token, $data);
        }

        return $token;
    }

    /**
     * 取得使用者資訊
     * 
     * @param string|null $token
     */
    public function getUserInformation(string $token = null)
    {
        if (!is_null($token)) {
            $userinfo = Cache::get($token);
            
            if (!empty($userinfo)) {
                return $userinfo;
            }
        }

        return false;
    }

    /**
     * 移除使用者資料
     * 
     * @param string|null $token 權杖
     * @return bool 成功或失敗
     */
    public function forgetUserInformation(string $token = null)
    {
        if (!is_null($token)) {
            Cache::forget($token);
            
            return true;
        }

        return false;
    }

    /**
     * 延長權杖有效期間
     * 
     * @param string|null $token 權杖
     * @return bool
     */
    public function extendExpireTime(string $token)
    {
        if (!is_null($token)) {
            $data = Cache::get($token);

            Cache::put($token, $data, Carbon::now()->addMinute($this->lifetime));

            return true;
        }

        return false;
    }
}