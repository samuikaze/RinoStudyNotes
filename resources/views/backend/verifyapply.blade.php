@extends('backend.layouts.master')

@section('title', '審核申請')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            new Vue({
                el: '#verify-apply',
                data: {
                    verifying: [],
                    verified: [],
                    loading: true,
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
                            return (error.message == null) ? error : error.response.data.message;
                        } else {
                            return error.response.data.errors;
                        }
                    },
                },
                mounted: function () {
                    axios.get('/api/v1/user/verifying')
                        .then((res) => {
                            this.verifying = res.data.data;
                        })
                        .catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        })
                        .finally(() => {
                            this.loading = false;
                        });
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

    <div id="verify-apply" v-cloak>
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">待審核</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="true">已審核</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="myTabContent">
                <template v-if="loading">
                    <div class="text-center h4 m-3 p-1 text-dark">
                        <span class="spinner-border mr-1" role="status" aria-hidden="true"></span>
                        <strong>資料讀取中...</strong>
                    </div>
                </template>
                <template v-else>
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <div class="card-body">
                            <table id="verify-table" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" class="sticky bg-secondary text-white">#</th>
                                        <th scope="col" class="sticky bg-secondary text-white">使用者名稱</th>
                                        <th scope="col" class="sticky bg-secondary text-white">暱稱</th>
                                        <th scope="col" class="sticky bg-secondary text-white">審核</th>
                                    </tr>
                                </thead>
                                <tbody v-if="verifying.length > 0">
                                    <tr v-for="(verify, i) in verifying" :key="verify.id">
                                        <td class="align-middle">@{{ verify.id }}</td>
                                        <td class="align-middle">@{{ verify.username }}</td>
                                        <td class="align-middle">@{{ (verify.nickname == null) ? verify.username : verify.nickname }}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn btn-outline-dark mr-2">
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                </svg>&nbsp;
                                                通過
                                            </button>
                                            <button type="button" class="btn btn-dark">
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                                </svg>&nbsp;
                                                拒絕
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="h4 text-dark text-center"><strong>目前無待審核的使用者</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <div class="card-body">
                            <table id="verify-table" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" class="sticky bg-secondary text-white">#</th>
                                        <th scope="col" class="sticky bg-secondary text-white">使用者名稱</th>
                                        <th scope="col" class="sticky bg-secondary text-white">暱稱</th>
                                        <th scope="col" class="sticky bg-secondary text-white">審核</th>
                                    </tr>
                                </thead>
                                <tbody v-if="verified.length > 0">
                                    <tr v-for="(verify, i) in verified" :key="verify.id">
                                        <td class="align-middle">@{{ verify.id }}</td>
                                        <td class="align-middle">@{{ verify.username }}</td>
                                        <td class="align-middle">@{{ (verify.nickname == null) ? verify.username : verify.nickname }}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn btn-outline-dark mr-2">
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                                </svg>&nbsp;
                                                註銷帳號
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="h4 text-dark text-center"><strong>目前尚無共同編輯者</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
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
