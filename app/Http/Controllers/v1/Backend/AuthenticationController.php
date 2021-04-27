<?php

namespace App\Http\Controllers\v1\Backend;

use App\Exceptions\AuthenticationException;
use App\Exceptions\DatabaseException;
use App\Http\Controllers\Controller;
use App\Services\v1\AuthenticationService;
use App\Services\v1\ResponseService;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthenticationController extends Controller
{
    use AuthenticatesUsers;

    const LOGIN_ERROR = '帳號或密碼不正確';

    /**
     * 回應
     *
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * 權杖
     *
     * @var \App\Services\AuthenticationService
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
        $this->token = $token;
        $this->response = $response;
    }

    /**
     * 變更登入使用者名稱使用的欄位
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 註冊
     *
     * @param \Illuminate\Http\Request $request HTTP 請求，應當包含註冊用的帳號及密碼
     * @return \Illuminate\http\RedirectResponse 註冊重新導向的回應
     */
    public function register(Request $request)
    {
        $refused = ['administrator', 'admin', 'systemop', 'sysop', 'root'];

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', Rule::notIn($refused), 'unique:users,username'],
            'password' => ['required', 'confirmed', 'string'],
            'nickname' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setErrorMsg($validator->errors()->first())
                        ->back();
        }

        try {
            $this->token->registerUser(
                $request->input('username'),
                $request->input('password'),
                $request->input('nickname')
            );
        } catch (Exception $e) {
            $this->response
                ->setErrorMsg('註冊失敗，請重新註冊')
                ->back();
        }


        return $this->login($request);
    }

    /**
     * 登入
     *
     * @param \Illuminate\Http\Request $request HTTP 請求，應當包含登入用的帳號及密碼
     * @return \Illuminate\http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response->setErrorMsg(self::LOGIN_ERROR)->back();
        }

        $auth = $this->token->login(
            $request->only('username', 'password')
        );

        if ($auth === false) {
            return $this->response->setErrorMsg(self::LOGIN_ERROR)->back();
        }

        try {
            $token = $this->token->generateToken($auth);
        } catch (DatabaseException $e) {
            $this->token->logout();
            return $this->response
                        ->setErrorMsg('產生權杖失敗，請重新登入')
                        ->back();
        }

        $cookie = cookie('token', $token, 120, null, null, false, false, false, 'lax');

        return $this->response
                    ->setCookies([$cookie])
                    ->setRedirectTargetName('admin.index')
                    ->redirect();
    }

    /**
     * 登出
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse 重新導向
     */
    public function logout()
    {
        try {
            $this->token->logout();
        } catch (DatabaseException $e) {
            return $this->response
                        ->setErrorMsg('登出失敗，請重新登出一次')
                        ->back();
        }

        return $this->response
                    ->setRedirectTargetName('login')
                    ->setCookies([cookie()->forget('token')])
                    ->redirect();
    }

    /**
     * 取得登入的使用者資訊
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 資料
     */
    public function userInfo(Request $request)
    {
        $token = $request->bearerToken();

        $verify = $this->token->verifyToken($token);

        if ($verify !== false) {
            $user = $this->token->retrievingUserInfo($verify);

            if ($user !== false) {
                return $this->response->setData($user)->json();
            }
        }

        return $this->response->json();
    }

    /**
     * 編輯使用者資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求，應當包含要編輯的資料
     * @return \Illuminate\Http\JsonResponse 返回使用者資料
     */
    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => ['nullable', 'string', 'max:10'],
            'origPswd' => ['required_with:newPswd', 'string'],
            'newPswd' => ['nullable', 'confirmed', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError($validator->errors()->first())
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        try {
            $newUser = $this->token->editProfile($request->all());
        } catch (AuthenticationException $e) {
            return $this->response
                        ->setError($e->getMessage())
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        } catch (Exception $e) {
            return $this->response
                        ->setError('更新資料失敗，請再試一次')
                        ->setCode($this->response::NOT_MODIFIED)
                        ->json();
        }

        return $this->response->setData($newUser)->json();
    }
}
