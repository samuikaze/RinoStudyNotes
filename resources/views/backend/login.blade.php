@extends('backend.layouts.master')

@section('title', '登入')

@section('content')
    <div id="auth">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">登入系統</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="true">申請帳號</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form method="POST" action="/admin/login">
                            @csrf
                            <div class="form-group">
                                <label for="lUsername">使用者名稱</label>
                                <input type="text" class="form-control" id="lUsername" name="username" placeholder="請輸入使用者名稱" required>
                            </div>
                            <div class="form-group">
                                <label for="lPassword">密碼</label>
                                <input type="password" class="form-control" id="lPassword" name="password" placeholder="請輸入密碼" required>
                            </div>
                            <div class="text-center">
                                <input type="submit" class="btn btn-primary" value="登入">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                    <div class="card-body">
                        <form method="POST" action="/admin/register">
                            @csrf
                            <div class="form-group">
                                <label for="rUsername">使用者名稱</label>
                                <input type="text" class="form-control" id="rUsername" name="username" placeholder="請輸入使用者名稱" required>
                            </div>
                            <div class="form-group">
                                <label for="rPassword">密碼</label>
                                <input type="password" class="form-control" id="rPassword" name="password" placeholder="請輸入密碼" required>
                            </div>
                            <div class="form-group">
                                <label for="rPasswordConf">確認密碼</label>
                                <input type="password" class="form-control" id="rPasswordConf" name="password_confirmation" placeholder="請再次輸入密碼" required>
                            </div>
                            <div class="form-group">
                                <label for="rNickname">暱稱</label>
                                <input type="text" class="form-control" id="rNickname" name="nickname" placeholder="請輸入暱稱，留空會使用使用者名稱當作暱稱">
                            </div>
                            <div class="text-center">
                                <input type="submit"class="btn btn-primary" value="申請">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
