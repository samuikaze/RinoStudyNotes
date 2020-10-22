@extends('backend.layouts.master')

@section('title', '登入')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            new Vue({
                el: '#auth',
                data: {
                    login: {
                        username: '',
                        password: ''
                    },
                    register: {
                        username: '',
                        password: '',
                        pswdconf: '',
                        nickname: '',
                    },
                    loading: false,
                    msg: '',
                    msgType: 'info',
                    adminConfirm: false,
                },
                methods: {
                    showMsg: function (type, msg) {
                        this.msgType = type;
                        this.msg = msg;
                        $('#alertMsg').modal('show');
                    },
                    fireLogin: function (event) {
                        if (this.login.username.length > 0 && this.login.password.length > 0) {
                            event.target.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;登入中';
                            event.target.disabled = true;
                            axios.post('/admin/login', {
                                username: this.login.username,
                                password: this.login.password,
                            })
                                .then((res) => {
                                    event.target.innerHTML = '跳轉中...';
                                    Cookies.set('token', res.headers.authorization.replace('Bearer ', '').trim(), {sameSite: 'lax'});
                                    if (res.data.length < 1) {
                                        window.location.href = '/admin';
                                    } else {
                                        this.showMsg('warning', res.data);
                                        this.adminConfirm = true;
                                    }
                                })
                                .catch((errors) => {
                                    event.target.innerHTML = '登入';
                                    event.target.disabled = false;
                                    this.showMsg('error', RSN.getErrorMsg(errors));
                                });
                        } else {
                            if (this.login.username.length < 1) {
                                this.showMsg('error', '使用者名稱欄位不可為空');
                            } else {
                                this.showMsg('error', '密碼欄位不可為空');
                            }
                        }
                    },
                    fireRegister: function (event) {
                        if (this.register.password != this.register.pswdconf) {
                            this.showMsg('error', '兩次輸入的密碼不相同，請重新輸入');
                            return ;
                        }
                        if (
                            this.register.username.length == 0
                            || this.register.password.length == 0
                            || this.register.pswdconf.length == 0
                        ) {
                            this.showMsg('error', '請確實填寫申請的欄位！');
                            return ;
                        }
                        event.target.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;讀取中';
                        event.target.disabled = true;
                        axios.post('/admin/register', {
                            username: this.register.username,
                            password: this.register.password,
                            password_confirmation: this.register.pswdconf,
                            nickname: (this.register.nickname.length > 0) ? this.register.nickname : null
                        })
                            .then((res) => {
                                Cookies.set('token', res.headers.authorization.replace('Bearer ', '').trim(), {sameSite: 'lax'});
                                event.target.innerHTML = '跳轉中';
                                window.location.href = '/admin';
                            })
                            .catch((errors) => {
                                this.showMsg('error', RSN.getErrorMsg(errors));
                                event.target.innerHTML = '申請';
                                event.target.disabled = false;
                            });
                    },
                },
                computed: {
                    msgTitle: function () {
                        switch (this.msgType) {
                            case 'error':
                                return '錯誤';
                                break;
                            case 'warn':
                            case 'warning':
                                return '警告';
                                break;
                            case 'info':
                            default:
                                return '訊息';
                                break;
                        }
                    },
                    msgClass: function () {
                        switch (this.msgType) {
                            case 'error':
                                return 'modal-title text-danger';
                                break;
                            case 'warn':
                            case 'warning':
                                return 'modal-title text-warning';
                                break;
                            case 'info':
                            default:
                                return 'modal-title text-primary';
                                break;
                        }
                    }
                }
            });
        });
    </script>

    <div id="auth">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">登入共同編輯資料</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="true">申請共同資料編輯權</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="lUsername">使用者名稱</label>
                            <input type="text" class="form-control" id="lUsername" v-model="login.username" placeholder="請輸入使用者名稱" required>
                        </div>
                        <div class="form-group">
                            <label for="lPassword">密碼</label>
                            <input type="password" class="form-control" id="lPassword" v-on:keyup.enter="fireLogin($event)" v-model="login.password" placeholder="請輸入密碼" required>
                        </div>
                        <div class="text-center">
                            <button type="button" v-on:keyup.enter="fireLogin($event)" v-on:click="fireLogin($event)" class="btn btn-primary">登入</button>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="rUsername">使用者名稱</label>
                            <input type="text" class="form-control" id="rUsername" v-model="register.username" placeholder="請輸入使用者名稱" required>
                        </div>
                        <div class="form-group">
                            <label for="rPassword">密碼</label>
                            <input type="password" class="form-control" id="rPassword" v-model="register.password" placeholder="請輸入密碼" required>
                        </div>
                        <div class="form-group">
                            <label for="rPasswordConf">確認密碼</label>
                            <input type="password" class="form-control" id="rPasswordConf" v-model="register.pswdconf" placeholder="請再次輸入密碼" required>
                        </div>
                        <div class="form-group">
                            <label for="rNickname">暱稱</label>
                            <input type="text" class="form-control" id="rNickname" v-model="register.nickname" placeholder="請輸入暱稱，留空會使用使用者名稱當作暱稱">
                        </div>
                        <div class="text-center">
                            <button type="button" v-on:click="fireRegister($event)" class="btn btn-primary">申請</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="alertMsg" tabindex="-1" aria-labelledby="alertMsgLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 :class="msgClass" id="alertMsgLabel">@{{ msgTitle }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p v-html="msg"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" v-if="!adminConfirm" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                        <button
                            type="button"
                            v-if="adminConfirm"
                            class="btn btn-success"
                            onclick="this.disabled = true; this.innerHTML = '跳轉中...'; window.location.href = '/admin';"
                        >
                            跳轉至管理首頁
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
