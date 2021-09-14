<?php

namespace App\Http\Middleware;

use App\Services\v1\AuthenticationService;
use App\Services\v1\ResponseService;
use Closure;
use Illuminate\Http\Request;

class VerifyAuthentication
{
    /**
     * 回應
     *
     * @var \App\Services\v1\ResponseService
     */
    protected $response;

    /**
     * 權杖
     *
     * @var \App\Services\v1\AuthenticationService
     */
    protected $token;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(
        AuthenticationService $token,
        ResponseService $response
    ) {
        $this->response = $response;
        $this->token = $token;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 如果是 API
        if ($request->wantsJson() || $request->isJson()) {
            $token = $request->bearerToken();

            if (empty($token)) {
                return $this->response
                            ->setError('Access Denied')
                            ->setCode($this->response::FORBIDDEN)
                            ->json();
            }

            $verify = $this->token->verifyToken($token);

            if ($verify === false) {
                $this->token->logout();
                return $this->response
                            ->setError('Unauthorized')
                            ->setCode($this->response::UNAUTHORIZED)
                            ->json();
            }

            $user = $this->token->retrievingUserInfo($verify);

            if ($user === false || $user->status == 2) {
                $this->token->logout();
                return $this->response
                            ->setError('Unauthorized')
                            ->setCode($this->response::UNAUTHORIZED)
                            ->json();
            }

            return $next($request);
        }
        // 如果是瀏覽器
        else {
            $authCheck = $this->token->verifyAuthStatus();
            if ($authCheck) {
                // 若有登入就在瀏覽器首次發起請求時延長使用者權杖的生命週期
                $this->token->extendExpireTime(session()->get('user-token'));

                return $next($request);
            }
            $this->token->logout();
            return $this->response->setRedirectTarget('login')->redirect();
        }
    }
}
