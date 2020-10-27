@extends('layouts.master')

@section('title', 'API 一覽')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Vue({
                el: '#api-list',
                computed: {
                    baseUrl: function () {
                        return `${window.location.href.split(':')[0]}://${window.location.host}`;
                    }
                }
            });
        });
    </script>

    <div id="api-list">
        <div class="alert alert-warning" role="alert">
            <svg width="2em" height="2em" viewBox="0 0 17 16" class="bi bi-exclamation-triangle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
            </svg>
            <strong>注意！</strong>此頁目前作用暫時為排模板用，API 資料都還沒輸入，也尚未正式啟用！
        </div>
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">API 清單</h2>
                <p class="card-text">
                    下面會以類型區分列出所有可以存取的 API 清單<br>
                    目前還在建置中，故暫時不提供 API 存取，待後台及資料完成建置後才會啟用。
                </p>
                <div id="api-list">
                    <h4>角色相關 API</h4>
                    <div class="card">
                        <div class="card-header d-flex bg-secondary text-white p-2">
                            <div class="d-flex col-6 justify-content-start">
                                <span class="text-mono">
                                    GET: <strong>/api/v1/character/[id|name|nickname]</strong>
                                </span>
                            </div>
                            <div class="d-flex col-6 justify-content-end">
                                <a href="/api/v1/character/1" class="text-white text-decoration-none" target="_blank">
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-up-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                                    </svg>&nbsp;
                                    開啟資料
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-1">
                            <strong class="text-mono">
                                <a :href="`${baseUrl}/api/v1/character/1`" target="_blank">@{{ `${baseUrl}/api/v1/character/1` }}</a><br>
                                <a :href="`${baseUrl}/api/v1/character?id=1`" target="_blank">@{{ `${baseUrl}/api/v1/character?id=1` }}</a>
                            </strong>
                            <h5 class="mt-3">取得角色資料</h5>
                            <p class="card-text">這支 API 會返回指令角色資料，如果找到多個結果會返回第一條找到的資料，搜尋條件可以使用網址傳參或 GET 傳參。</p>
                            <h5>網址傳參</h5>
                            <ul class="text-mono">
                                <li>
                                    <pre>int|string&#9;<span class="text-normal">角色 ID 、名稱（日文或中文）或暱稱。</span></pre>
                                </li>
                            </ul>
                            <h5>GET 傳參</h5>
                            <ul class="text-mono">
                                <li>
                                    <pre>int&#9;<strong>id</strong>&#9;&#9;<span class="text-normal">角色 ID。</span></pre>
                                </li>
                                <li>
                                    <pre>string&#9;<strong>name</strong>&#9;&#9;<span class="text-normal">角色名稱（日文或中文）。</span></pre>
                                </li>
                                <li>
                                    <pre>string&#9;<strong>nickname</strong>&#9;<span class="text-normal">角色暱稱。</span></pre>
                                </li>
                            </ul>
                            <h5>回應</h5>
                            <ul class="text-mono">
                                <li>
                                    <pre>Object</pre>
                                    <ul>
                                        <li>
                                            <pre>int&#9;<strong>id</strong>&#9;<span class="text-normal">角色 ID。</span></pre>
                                        </li>
                                        <li>
                                            <pre>string&#9;<strong>tw_name</strong>&#9;<span class="text-normal">角色中文名稱。</span></pre>
                                        </li>
                                        <li>
                                            <pre>string&#9;<strong>jp_name</strong>&#9;<span class="text-normal">角色日文名稱。</span></pre>
                                        </li>
                                        <li>
                                            <pre>int&#9;<strong>id</strong>&#9;<span class="text-normal">角色 ID。</span></pre>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
