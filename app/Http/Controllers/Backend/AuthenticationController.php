<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BearerTokenService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
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

        $auth = Hash::check($request->input('password'), $user->password);

        if (!$auth) {
            return $this->response->setError('密碼不正確')->setCode($this->response::BAD_REQUEST)->json();
        }

        $token = $this->token->putUserInformation(collect($user->toArray())->except('password'));

        $user->token = $token;
        $user = $user->toArray();

        session()->put('user', $user);

        return $this->response->setHeaders(['Authorization' => 'Bearer ' . $token])->json();
    }

    /**
     * 登出
     * 
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse 重新導向
     */
    public function logout()
    {
        $token = session()->get('user.token');

        $this->token->forgetUserInformation($token);

        session()->forget('user');
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

        $user = $this->token->getUserInformation($token);

        if ($user) {
            return $this->response->setData($user)->json();
        }

        return $this->response->json();
    }
}
