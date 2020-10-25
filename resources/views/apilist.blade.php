@extends('layouts.master')

@section('title', 'API 一覽')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">API 清單</h2>
            <p class="card-text">
                下面會以類型區分列出所有可以存取的 API 清單<br>
                目前還在建置中，故暫時不提供 API 存取，待後台及資料完成建置後才會啟用。
            </p>
            <div id="api-list">
                {{-- <h4>角色相關 API</h4>
                <div class="card">
                    <div class="card-header d-flex bg-secondary text-white p-2">
                        <div class="d-flex col-6 justify-content-start">
                            <span class="text-mono">GET: <strong>/api/v1/characters</strong></span>
                        </div>
                        <div class="d-flex col-6 justify-content-end">
                            <a href="/api/v1/characters" class="text-white text-decoration-none" target="_blank">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-up-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                                </svg>&nbsp;
                                開啟資料
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5>取得所有角色清單（目前測試中）</h5>
                        <p class="card-text">這支 API 會返回所有角色的清單，但僅包含 ID、中文名稱和日文名稱的資料。</p>
                        <h5>回應</h5>
                        <ul class="text-mono">
                            <li><pre>Object|Array&#9;errors</pre></li>
                            <li>
                                <pre>Object|Array&#9;data</pre>
                                <ul>
                                    <li><pre>String&#9;name</pre></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
