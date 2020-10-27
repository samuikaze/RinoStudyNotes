<?php

namespace App\Services;

class ResponseService
{
    const OK = 200;
    const FOUND = 302;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOW = 405;
    const INTERNAL_SERVER_ERROR = 500;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;

    /**
     * 回應碼
     *
     * @var int
     */
    protected $code;

    /**
     * 回應資料
     *
     * @var \Illuminate\Support\Collection|array|null
     */
    protected $data;

    /**
     * 錯誤資料
     *
     * @var \Illuminate\Support\Collection|array|string|null
     */
    protected $errors;

    /**
     * 標頭
     *
     * @var \Illuminate\Support\Collection|array|null
     */
    protected $headers;

    /**
     * 視圖名稱
     *
     * @var string|null
     */
    protected $view;

    /**
     * 重新導向目的地，可為路由名稱或路由字串
     *
     * @var string
     */
    protected $redirectTarget;

    /**
     * Cookies
     *
     * @var array
     */
    protected $cookies;

    /**
     * 錯誤訊息 (Laravel Session)
     *
     * @var string
     */
    protected $errorMsg;

    /**
     * 建構函式
     */
    public function __construct()
    {
        $this->code = null;
        $this->data = null;
        $this->errors = null;
        $this->headers = null;
        $this->view = null;
        $this->redirectTarget = null;
        $this->cookies = null;
        $this->errorMsg = null;
    }

    /**
     * 處理空值
     *
     * @return void
     */
    protected function emptyValueProcessor()
    {
        $this->code = (is_null($this->code)) ? self::OK : $this->code;
        $this->data = (empty($this->data)) ? null : $this->data;
        $this->errors = (empty($this->errors)) ? null : $this->errors;
        $this->headers = (empty($this->headers)) ? [] : $this->headers;
    }

    /**
     * 設定回應碼
     *
     * @param int $code 回應碼
     * @return $this
     */
    public function setCode(int $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * 設定回應資料
     *
     * @param \Illuminate\Support\Collection|array|null $data 回應資料
     * @return $this
     */
    public function setData($data)
    {
        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        $this->data = $data;

        return $this;
    }

    /**
     * 設定錯誤資料
     *
     * @param \Illuminate\Support\Collection|string|array|null
     * @return $this
     */
    public function setError($errors)
    {
        if ($errors instanceof \Illuminate\Support\Collection) {
            $errors = $errors->toArray();
        }

        $this->errors = $errors;

        return $this;
    }

    /**
     * 設定標頭
     *
     * @param \Illuminate\Support\Collection|array|null $headers 標頭
     * @return $this
     */
    public function setHeaders($headers)
    {
        if ($headers instanceof \Illuminate\Support\Collection) {
            $headers = $headers->toArray();
        }

        $this->headers = $headers;

        return $this;
    }

    /**
     * 設定視圖名稱
     *
     * @param string $view
     * @return $this
     */
    public function setView(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * 設定重新導向目的地
     *
     * @param string $route
     * @return $this
     */
    public function setRedirectTarget(string $route)
    {
        $this->redirectTarget = $route;

        return $this;
    }

    /**
     * 設定重新導向目的地 (路由名稱)
     *
     * @param string $route
     * @return $this
     */
    public function setRedirectTargetName(string $route)
    {
        $this->redirectTarget = route($route);

        return $this;
    }

    /**
     * 設定 Cookies
     *
     * @param array $cookies 多個 Cookies
     * @return $this
     */
    public function setCookies(array $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * 設定錯誤訊息 (Laravel Session)
     *
     * @param string $errors 錯誤訊息
     * @return $this
     */
    public function setErrorMsg(string $errors)
    {
        $this->errorMsg = $errors;

        return $this;
    }

    /**
     * 返回 JSON 格式回應
     *
     * @return \Illuminate\Http\JsonResponse JSON 回應
     */
    public function json()
    {
        $this->emptyValueProcessor();

        if (is_null($this->errors)) {
            return response()->json($this->data, $this->code, $this->headers);
        }

        return response()->json([
            'errors' => $this->errors,
            'data' => $this->data,
        ], $this->code, $this->headers);
    }

    /**
     * 返回視圖回應，其中視圖資料請以 setData 設定
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function view()
    {
        if (empty($this->data) || $this->data->isEmpty()) {
            return view($this->view);
        } else {
            return view($this->view, $this->data);
        }
    }

    /**
     * 重新導向回應
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse 重新導向
     */
    public function redirect()
    {
        $this->emptyValueProcessor();

        if (!empty($this->cookies)) {
            return redirect($this->redirectTarget, self::FOUND, $this->headers)->withCookies($this->cookies);
        } elseif (!empty($this->errorMsg)) {
            return redirect($this->redirectTarget, self::FOUND, $this->headers)->withErrors($this->errorMsg);
        }

        return redirect($this->redirectTarget, self::FOUND, $this->headers);
    }

    /**
     * 返回（重導回）上一個路由
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse 重新導向
     */
    public function back()
    {
        $this->emptyValueProcessor();

        if (!empty($this->cookies)) {
            return redirect()->back(self::FOUND, $this->headers)->withCookies($this->cookies);
        } elseif (!empty($this->errorMsg)) {
            return redirect()->back(self::FOUND, $this->headers)->withErrors($this->errorMsg);
        }

        return redirect()->back(self::FOUND, $this->headers);
    }
}
