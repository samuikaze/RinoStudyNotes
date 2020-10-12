@extends('backend.layouts.master')

@section('title', '登入')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            new Vue({
                el: '#auth',
                data: {
                    lusername: '',
                    lpassword: '',
                    loading: false,
                    msg: '',
                    msgType: 'info',
                },
                methods: {
                    showMsg: function (type, msg) {
                        this.msgType = type;
                        this.msg = msg;
                        $('#alertMsg').modal('show');
                    },
                    getErrorMsg: function (error) {
                        if (error.response == null) {
                            return error;
                        } else {
                            return error.response.data.errors;
                        }
                    },
                    fireLogin: function () {
                        if (this.lusername.length > 0 && this.lpassword.length > 0) {
                            axios.post('/admin/login', {
                                username: this.lusername,
                                password: this.lpassword,
                            })
                                .then((res) => {
                                    Cookies.set('token', res.headers.authorization.replace('Bearer ', '').trim(), {sameSite: 'lax'});
                                    window.location.href = '/admin/';
                                })
                                .catch((errors) => {
                                    this.showMsg('error', this.getErrorMsg(errors));
                                });
                        } else {
                            if (this.lusername.length < 1) {
                                this.showMsg('error', '使用者名稱欄位不可為空');
                            } else {
                                this.showMsg('error', '密碼欄位不可為空');
                            }
                        }
                    },
                    fireRegister: function () {
                        //
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
                            <input type="text" class="form-control" id="lUsername" v-model="lusername" placeholder="請輸入使用者名稱" required>
                        </div>
                        <div class="form-group">
                            <label for="lPassword">密碼</label>
                            <input type="password" class="form-control" id="lPassword" v-model="lpassword" placeholder="請輸入密碼" required>
                        </div>
                        <div class="text-center">
                            <button type="button" v-on:click="fireLogin()" class="btn btn-primary">登入</button>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                    <div class="card-body text-center">
                        <span class="h4 text-primary"><strong>建置中...</strong></span>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection