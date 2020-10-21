@extends('layouts.master')

@section('title', '首頁')

@section('content')
    <div class="jumbotron">
        <h1 class="display-4">璃乃學習筆記<span class="lead"> RinoStudyNotes</span></h1>
        <p class="lead">這個網站用於輸出 JSON 格式的資料，方便需要取用超異域公主連結資料的相關應用程式可以利用。</p>
        <hr class="my-4">
        <p>
            此網站目前為個人興趣所架設，與 Cygames 或 So-net 並無任何關係。<br>
            如需回報問題，可至 GitHub 上回報。
        </p>
        <a class="btn btn-primary btn-lg" href="https://github.com/samuikaze/PCRedive-DataAPI" target="_blank" role="button">前往 GitHub</a>
    </div>
@endsection
