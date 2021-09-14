<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Services\v1\ResponseService;
use App\Services\v1\SystemVarService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebController extends Controller
{
    /**
     * System Var Service
     *
     * @var \App\Services\v1\SystemVarService
     */
    protected $sysvar;

    /**
     * 回應
     *
     * @var \App\Services\v1\ResponseService
     */
    protected $response;

    /**
     * 建構函式
     *
     * @param \App\Services\v1\SystemVarService $sysvar
     * @param \App\Services\v1\ResponseService $response
     * @return void
     */
    public function __construct(
        SystemVarService $sysvar,
        ResponseService $response
    ) {
        $this->sysvar = $sysvar;
        $this->response = $response;
    }

    /**
     * 取得目前版本號碼
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVersionId()
    {
        $version = $this->sysvar->getVersionId();

        return $this->response->setData($version)->json();
    }

    /**
     * 取得所有版本資訊
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllVersions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('請確實告知這次是第幾次請求。')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        try {
            $versions = $this->sysvar->getAllVersions($request->input('start'));
        } catch (Exception $e) {
            return $this->response
                        ->setError($e->getMessage())
                        ->setCode($this->response::UNPROCESSABLE_ENTITY)
                        ->json();
        }

        return $this->response->setData($versions)->json();
    }
}
