<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebController extends Controller
{
    /**
     * 回應
     *
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * LazyLoad 時單次請求取得的資料筆數
     *
     * @var int
     */
    protected $lazyload;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(
        ResponseService $response
    ) {
        $this->response = $response;
        $this->lazyload = 10;
    }

    /**
     * 取得目前版本號碼
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVersionId()
    {
        $version = Version::orderBy('id', 'desc')->first()->version_id;

        return $this->response->setData($version)->json();
    }

    /**
     * 取得所有版本資訊（一次 10 筆）
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllVersions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return $this->response->setError('請確實告知這次是第幾次請求。')->setCode($this->response::BAD_REQUEST)->json();
        }

        $start = ($request->input('start') - 1) * $this->lazyload;

        $versions = Version::orderBy('id', 'desc')->skip($start)->take($this->lazyload)->get();

        return $this->response->setData($versions)->json();
    }
}
