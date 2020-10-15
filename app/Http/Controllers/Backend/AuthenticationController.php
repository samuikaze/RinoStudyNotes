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
            return $this->response
                        ->setError($validator->errors()->first())
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        User::create([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'nickname' => (is_null($request->input('nickname'))) ? $request->input('username') : $request->input('nickname'),
            'role_of' => 1,
        ]);

        return $this->login($request);
    }

    /**
     * 登入
     * 
     * @param \Illuminate\Http\Request $request HTTP 請求，應當包含登入用的帳號及密碼
     * @return \Illuminate\Http\JsonResponse 登入成功或失敗的回應
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response->setError($validator->errors()->first())->setCode($this->response::BAD_REQUEST)->json();
        }

        $user = User::where('username', $request->input('username'))->first();

        if (empty($user)) {
            return $this->response->setError('找不到該使用者名稱！')->setCode($this->response::BAD_REQUEST)->json();
        }

        $auth = Auth::attempt($request->only('username', 'password'));

        if (!$auth) {
            return $this->response->setError('密碼不正確')->setCode($this->response::BAD_REQUEST)->json();
        }

        $token = $this->token->generateToken($user->id);

        session()->put('user-token', $token);

        return $this->response->setHeaders(['Authorization' => 'Bearer ' . $token])->json();
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

        return redirect(route('login'));
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
            $user = User::where('id', $request->input('user.id'))
                        ->first()
                        ->makeVisible(['password']);

            if (empty($user)) {
                return $this->response
                            ->setError('找不到此使用者名稱')
                            ->setCode(400)
                            ->json();
            }

            if (! Hash::check($request->input('origPswd'), $user->password)) {
                return $this->response
                            ->setError('密碼不正確')
                            ->setCode(400)
                            ->json();
            }

            User::where('id', $request->input('user.id'))->update([
                'nickname' => $request->input('nickname'),
                'password' => Hash::make($request->input('newPswd')),
            ]);
        }

        User::where('id', $request->input('user.id'))->update([
            'nickname' => $request->input('nickname'),
        ]);

        $user = $request->input('user');
        $user['nickname'] = $request->input('nickname');

        return $this->response->setData($user)->json();
    }
}
