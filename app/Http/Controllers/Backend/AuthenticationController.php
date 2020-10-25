<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BearerTokenService;
use App\Services\ResponseService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * @var \App\Services\BearerTokenService
     */
    protected $token;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(
        BearerTokenService $token,
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
     * @return \Illuminate\Http\JsonResponse 註冊成功或失敗的回應
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
            return $this->response->setErrorMsg($validator->errors()->first())->back();
        }

        User::create([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'nickname' => (is_null($request->input('nickname'))) ? $request->input('username') : $request->input('nickname'),
        ]);

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

        $user = User::where('username', $request->input('username'))->first();

        if (empty($user)) {
            return $this->response->setErrorMsg(self::LOGIN_ERROR)->back();
        }

        $user = $user->makeVisible(['password']);

        if ($user->status == 2) {
            return $this->response->setErrorMsg('該帳號已被停權！')->back();
        }

        $auth = Auth::attempt($request->only('username', 'password'));

        if (!$auth) {
            return $this->response->setErrorMsg(self::LOGIN_ERROR)->back();
        }

        $token = $this->token->generateToken($user->id);
        $cookie = cookie('token', $token, 120, null, null, false, false, false, 'lax');

        session()->put('user-token', $token);

        if ($user->username == 'administrator' && Hash::check('123', $user->password)) {
            return $this->response
                        ->setCookies([
                            $cookie,
                            cookie('securityWarn', '看起來是第一次使用系統，記得將密碼更改為較安全的密碼！')
                        ])
                        ->setRedirectTargetName('admin.index')
                        ->redirect();
        }

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
        $token = session()->get('user-token');

        $this->token->removeToken($token);

        Auth::logoutCurrentDevice();
        session()->regenerate();

        return $this->response->setRedirectTargetName('login')->setCookies([cookie()->forget('token')])->redirect();
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
            $user = User::where('id', $verify)->first()->toArray();

            $this->token->extendExpireTime($token);

            return $this->response->setData($user)->json();
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

        if ($request->has('newPswd')) {
            $user = User::where('id', Auth::user()->id)
                        ->first();

            if (empty($user)) {
                return $this->response
                            ->setError('找不到此使用者名稱')
                            ->setCode(400)
                            ->json();
            } else {
                $user = $user->makeVisible(['password']);
            }

            if (! Hash::check($request->input('origPswd'), $user->password)) {
                return $this->response
                            ->setError('密碼不正確')
                            ->setCode(400)
                            ->json();
            }

            User::where('id', Auth::user()->id)->update([
                'nickname' => $request->input('nickname'),
                'password' => Hash::make($request->input('newPswd')),
            ]);
        }

        User::where('id', Auth::user()->id)->update([
            'nickname' => $request->input('nickname'),
        ]);

        $user = $request->input('user');
        $user['nickname'] = $request->input('nickname');

        return $this->response->setData($user)->json();
    }
}
