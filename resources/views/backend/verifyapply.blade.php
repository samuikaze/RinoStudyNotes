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
                    verifyData: {
                        type: null,
                        user: null,
                        userID: null,
                    },
                    accountAdminData: {
                        type: null,
                        user: null,
                        userID: null,
                    },
                    loading: true,
                    msg: '',
                    msgType: 'info',
                    fireverifying: false,
                    fireadmining: false,
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
                    showVerifyConfirm: function (id, user, type) {
                        this.verifyData.type = type;
                        this.verifyData.user = user;
                        this.verifyData.userID = id;
                        $('#verifyConfirmModal').modal('show');
                    },
                    fireVerify: function () {
                        this.fireverifying = true;
                        axios.post('/api/v1/user/verify/verify', {
                            _method: 'patch',
                            id: this.verifyData.userID,
                            type: this.verifyData.type,
                        }).then((res) => {
                            let index = this.verifying.indexOf(this.verifying.filter(item => item.id == this.verifyData.userID)[0]);

                            if (index > -1) {
                                this.verified.push(this.verifying[index]);
                                this.verifying.splice(index, 1);
                            }
                            $('#verifyConfirmModal').modal('hide');
                        }).catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.fireverifying = false;
                        });
                    },
                    showAccountAdminConfirm: function (id, user, type) {
                        this.accountAdminData.type = type;
                        this.accountAdminData.user = user;
                        this.accountAdminData.userID = id;
                        $('#accountAdminModal').modal('show');
                    },
                    fireAccountAdmin: function () {
                        this.fireadmining = true;
                        axios.post('/api/v1/user/verify/admin', {
                            _method: 'patch',
                            id: this.accountAdminData.userID,
                            type: this.accountAdminData.type,
                        }).then((res) => {
                            let index = this.verified.indexOf(this.verified.filter(item => item.id == this.accountAdminData.userID)[0]);
                            if (index > -1) {
                                this.verified[index].status = (this.accountAdminData.type == 'enable') ? 1 : 2;
                            }
                            $('#accountAdminModal').modal('hide');
                        }).catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.fireadmining = false;
                        })
                    }
                },
                mounted: function () {
                    axios.get('/api/v1/user/verify').then((res) => {
                        this.verifying = res.data.data.verifying;
                        this.verified = res.data.data.verified;
                    }).catch((errors) => {
                        this.showMsg('error', this.getErrorMsg(errors));
                    }).finally(() => {
                        this.loading = false;
                    });

                    $('#verifyConfirmModal').on('hide.bs.modal', (e) => {
                        this.verifyData.type = null;
                        this.verifyData.user = null;
                        this.verifyData.userID = null;
                    });

                    $('#accountAdminModal').on('hide.bs.modal', (e) => {
                        this.accountAdminData.type = null;
                        this.accountAdminData.user = null;
                        this.accountAdminData.userID = null;
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
                                        <td class="align-middle">@{{ i + 1 }}</td>
                                        <td class="align-middle">@{{ verify.username }}</td>
                                        <td class="align-middle">@{{ (verify.nickname == null) ? verify.username : verify.nickname }}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn btn-outline-dark mr-2" v-on:click="showVerifyConfirm(verify.id, verify.username, 'accept')">
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                </svg>&nbsp;
                                                通過
                                            </button>
                                            <button type="button" class="btn btn-dark" v-on:click="showVerifyConfirm(verify.id, verify.username, 'denied')">
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
                                        <th scope="col" class="sticky bg-secondary text-white">ID</th>
                                        <th scope="col" class="sticky bg-secondary text-white">使用者名稱</th>
                                        <th scope="col" class="sticky bg-secondary text-white">暱稱</th>
                                        <th scope="col" class="sticky bg-secondary text-white">操作</th>
                                    </tr>
                                </thead>
                                <tbody v-if="verified.length > 0">
                                    <tr v-for="(verify, i) in verified" :key="verify.id">
                                        <td class="align-middle">@{{ verify.id }}</td>
                                        <td class="align-middle">@{{ verify.username }} <span v-if="verify.status == 2" class="text-secondary ml-2">(已停權)</span></td>
                                        <td class="align-middle">@{{ (verify.nickname == null) ? verify.username : verify.nickname }}</td>
                                        <td class="align-middle">
                                            <button type="button" v-if="verify.status != 2" v-on:click="showAccountAdminConfirm(verify.id, verify.username, 'disable')" class="btn btn btn-dark mr-2">
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                                </svg>&nbsp;
                                                停權
                                            </button>
                                            <button type="button" v-else v-on:click="showAccountAdminConfirm(verify.id, verify.username, 'enable')" class="btn btn btn-outline-dark mr-2">
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                </svg>&nbsp;
                                                復權
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
        {{-- 審核確認開始 --}}
        <div class="modal fade" id="verifyConfirmModal" tabindex="-1" aria-labelledby="verifyConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verifyConfirmModalLabel">審核確認</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>確定要@{{ (verifyData.type == 'accept' ? '通過' : '拒絕') + ` ${verifyData.user} ` }}的審核嗎？</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn btn-outline-dark mr-2" data-dismiss="modal">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            </svg>&nbsp;
                            取消
                        </button>
                        <button type="button" class="btn btn-dark" v-if="!fireverifying && verifyData.type == 'accept'" v-on:click="fireVerify()">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>&nbsp;
                            通過
                        </button>
                        <button type="button" class="btn btn-dark" v-on:click="fireVerify()" v-if="!fireverifying && verifyData.type == 'denied'">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                            </svg>&nbsp;
                            拒絕
                        </button>
                        <button type="button" class="btn btn-dark" v-if="fireverifying" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;
                            送出審核中...
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- 審核確認結束 --}}

        {{-- 停權復權確認開始 --}}
        <div class="modal fade" id="accountAdminModal" tabindex="-1" aria-labelledby="accountAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accountAdminModalLabel">確認</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>確定要@{{ (accountAdminData.type == 'disable' ? '停權' : '復權') + ` ${accountAdminData.user} ` }}這支帳號嗎？</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn btn-outline-dark mr-2" data-dismiss="modal">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            </svg>&nbsp;
                            取消
                        </button>
                        <button type="button" class="btn btn-dark" v-if="!fireadmining && accountAdminData.type == 'disable'" v-on:click="fireAccountAdmin()">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-exclamation-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                            </svg>&nbsp;
                            停權
                        </button>
                        <button type="button" class="btn btn-dark" v-on:click="fireAccountAdmin()" v-if="!fireadmining && accountAdminData.type == 'enable'">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>&nbsp;
                            復權
                        </button>
                        <button type="button" class="btn btn-dark" v-if="fireadmining" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;
                            送出中...
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- 停權復權確認結束 --}}

        {{-- 訊息開始 --}}
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
        {{-- 訊息結束 --}}
    </div>
@endsection
