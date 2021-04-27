<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Services\v1\ResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyPermission
{
    /**
     * 回應
     *
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(ResponseService $response)
    {
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string    $type
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $type)
    {
        // 如果是 API
        if ($request->wantsJson() || $request->isJson()) {
            // 取得可存取的權限
            if (empty($accessible = Role::where('id', Auth::user()->role_of)->first())) {
                return $this->response->setError('Forbidden')->setCode($this->response::FORBIDDEN)->json();
            }

            $accessible = json_decode($accessible->accessibles);

            // 系統管理員直接給過
            if (in_array('sysop', $accessible)) {
                return $next($request);
            }

            switch ($type) {
                case 'public':
                    // 公共 API 也直接過
                    return $next($request);
                    break;
                case 'view':
                    // 只看資料才給過
                    if (in_array('viewdata', $accessible) || in_array('editdata', $accessible)) {
                        return $next($request);
                    }
                    break;
                case 'edit':
                    // 有編輯權限才給過
                    if (in_array('editdata', $accessible)) {
                        return $next($request);
                    }
                    break;
                case 'admin':
                    break;
            }

            // 其餘全部不給過
            return $this->response->setError('Forbidden')->setCode($this->response::FORBIDDEN)->json();
        }
        // 如果是瀏覽器
        else {
            $accessible = Role::where('id', Auth::user()->role_of)->first();
            $accessible = json_decode($accessible->accessibles);

            // 系統管理員直接給過
            if (in_array('sysop', $accessible)) {
                return $next($request);
            }

            switch ($type) {
                case 'public':
                    // 公共 API 也直接過
                    return $next($request);
                    break;
                case 'view':
                case 'edit':
                    // 有檢視或編輯權也都給過
                    if (in_array('viewdata', $accessible) || in_array('editdata', $accessible)) {
                        return $next($request);
                    }
                    break;
                case 'admin':
                    break;
            }

            // 其餘全部不給過
            return abort($this->response::FORBIDDEN, 'Forbidden');
        }
    }
}
