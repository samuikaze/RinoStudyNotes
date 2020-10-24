@extends('backend.layouts.master')

@section('title', '版本資料管理')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Vue({
                el: '#version',
                data: {
                    versions: [],
                    editContent: {
                        id: null,
                        version_id: '',
                        content: '',
                    },
                    systemVar: {
                        start: 0,
                        loading: true,
                        submitting: false,
                        editType: null,
                        msg: '',
                        msgType: 'info'
                    },
                },
                methods: {
                    showMsg: function (type, msg) {
                        this.systemVar.msgType = type;
                        this.systemVar.msg = msg;
                        $('#alertMsg').modal('show');
                    },
                    processData: function (data) {
                        data = data.map((item) => {
                            item.created_at = RSN.processDate(item.created_at);
                            item.updated_at = RSN.processDate(item.updated_at);
                            return item;
                        });

                        return data;
                    },
                    openModal: function (type, id = null) {
                        this.systemVar.editType = type;
                        let versions = _.cloneDeep(this.versions);
                        if (id != null) {
                            this.editContent = versions.filter(ver => ver.id == id).map((ver) => {
                                ver.content = JSON.parse(ver.content).join("\n");
                                delete ver.created_at;
                                delete ver.updated_at;
                                return ver;
                            })[0];
                        }

                        $('#versionModal').modal('show');
                    },
                    fireAddVersion: function () {
                        this.systemVar.submitting = true;
                        axios.post('/webapi/version', {
                            version_id: this.editContent.version_id,
                            content: this.editContent.content.trim().replace(/\r/g, '').split('\n'),
                        }).then((res) => {
                            this.editContent.id = res.data;
                            this.editContent.content = JSON.stringify(this.editContent.content.trim().replace(/\r/g, '').split('\n'));
                            this.versions.unshift(this.editContent);
                            $('#versionModal').modal('hide');
                        }).catch((errors) => {
                            this.showMsg('error', RSN.getErrorMsg(errors));
                        }).finally(() => {
                            this.systemVar.submitting = false;
                        });
                    },
                    fireEditVersion: function () {
                        this.systemVar.submitting = true;
                        axios.post('/webapi/version', {
                            _method: 'patch',
                            id: this.editContent.id,
                            version_id: this.editContent.version_id,
                            content: this.editContent.content.trim().replace(/\r/g, '').split('\n'),
                        }).then((res) => {
                            let index = this.versions.indexOf(this.versions.filter(ver => ver.id == this.editContent.id)[0]);

                            if (index > -1) {
                                this.versions[index].version_id = this.editContent.version_id;
                                this.versions[index].content = JSON.stringify(this.editContent.content.trim().replace(/\r/g, '').split('\n'));
                            }
                            $('#versionModal').modal('hide');
                        }).catch((errors) => {
                            this.showMsg('error', RSN.getErrorMsg(errors));
                        }).finally(() => {
                            this.systemVar.submitting = false;
                        });
                    },
                    fireDeleteVersion: function () {
                        this.systemVar.submitting = true;
                        axios.post('/webapi/version', {
                            _method: 'delete',
                            id: this.editContent.id,
                        }).then((res) => {
                            let index = this.versions.indexOf(this.versions.filter(ver => ver.id == this.editContent.id)[0]);

                            if (index > -1) {
                                this.versions.splice(index, 1);
                            }
                            $('#versionModal').modal('hide');
                        }).catch((errors) => {
                            this.showMsg('error', RSN.getErrorMsg(errors));
                        }).finally(() => {
                            this.systemVar.submitting = false;
                        });
                    },
                    fireSave: function () {
                        switch (this.systemVar.editType) {
                            case 'add':
                                this.fireAddVersion();
                                break;
                            case 'edit':
                                this.fireEditVersion();
                                break;
                            case 'delete':
                                this.fireDeleteVersion();
                                break;
                        }
                    },
                    toggleDisabledField: function (event) {
                        if (this.systemVar.editType == 'edit') {
                            if (confirm('版本號碼通常不會更改，您確定要更改版本號碼嗎？')) {
                                document.getElementById('version_id').disabled = false;
                                event.target.disabled = true;
                            }
                        }
                    }
                },
                mounted: function () {
                    axios.get('/frontend/version/all', {params: {
                        start: this.systemVar.start,
                    }})
                        .then((res) => {
                            this.versions = this.versions.concat(this.processData(res.data));
                        })
                        .catch((errors) => {
                            this.showMsg('error', RSN.getErrorMsg(errors));
                        })
                        .finally(() => {
                            this.systemVar.loading = false;
                        });

                    $('#versionModal').on('hidden.bs.modal', () => {
                        this.editContent = {
                            id: null,
                            version_id: '',
                            content: '',
                        };
                    })
                },
                computed: {
                    editTitle: function () {
                        switch (this.systemVar.editType) {
                            case 'add':
                                return '新增';
                                break;
                            case 'edit':
                                return '編輯';
                                break;
                            case 'delete':
                                return '刪除';
                                break;
                            default:
                                return null;
                        }
                    },
                    msgTitle: function () {
                        switch (this.systemVar.msgType) {
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
                        switch (this.systemVar.msgType) {
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
                    },
                    lastestVersion: function () {
                        return this.versions[0].version_id;
                    }
                }
            });
        });
    </script>

    <div id="version" v-cloak>
        <div class="text-right mb-3">
            <button class="btn btn-dark" type="button" v-on:click="openModal('add')">新增版本號碼</button>
        </div>
        <table class="table table-hover table-bordered version-table">
            <thead>
                <tr>
                    <th class="bg-secondary align-middle text-white sticky" scope="col">ID</th>
                    <th class="bg-secondary align-middle text-white sticky" scope="col">版本號碼</th>
                    <th class="bg-secondary align-middle text-white sticky" scope="col">編輯</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="version in versions" :key="version.id">
                    <td class="align-middle">@{{ version.id }}</td>
                    <td class="align-middle">@{{ version.version_id }}</td>
                    <td class="align-middle">
                        <button type="button" class="btn btn-outline-dark" v-on:click="openModal('edit', version.id)">編輯</button>
                        <button type="button" class="btn btn-dark ml-1" v-on:click="openModal('delete', version.id)">刪除</button>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- 編輯視窗 --}}
        <div class="modal fade" id="versionModal" tabindex="-1" aria-labelledby="versionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="versionModalLabel">@{{ editTitle }}版本</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <template v-if="systemVar.editType == 'delete'">
                            <p class="h5 text-center"><strong>確定要刪除 @{{ editContent.version_id }} 的版本資訊嗎？</strong></p>
                        </template>
                        <template v-else>
                            <div class="form-group">
                                <div class="d-flex" v-bind:class="{'pb-2': (systemVar.editType == 'edit')}">
                                    <div class="col-10 justify-content-start p-0">
                                        <label for="version_id">版本號碼</label>
                                    </div>
                                    <div class="col-2 justify-content-end text-right p-0">
                                        <button type="button" id="toggle-disabled" v-on:click="toggleDisabledField($event)" class="btn btn-dark btn-sm" v-if="systemVar.editType == 'edit'">編輯版本號碼</button>
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="editContent.version_id"
                                    id="version_id"
                                    placeholder="請輸入此版本的號碼"
                                    :disabled="systemVar.editType == 'edit'"
                                    :aria-describedby="(systemVar.editType == 'add') ? 'versionHelp' : ''">
                                <small id="versionHelp" v-if="systemVar.editType == 'add'" class="form-text text-muted">目前最新的版本號碼為 @{{ lastestVersion }}</small>
                            </div>
                            <div class="form-group">
                                <label for="content" class="m-0">版本內容</label>
                                <small id="contentHelp" class="form-text text-muted mt-0 mb-1">多個內容請以換行分開</small>
                                <textarea
                                    class="form-control"
                                    v-model="editContent.content"
                                    id="content"
                                    rows="3"
                                    aria-describedby="contentHelp"
                                    placeholder="請輸入此版本的變更點"
                                ></textarea>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">取消</button>
                        <button v-if="!systemVar.submitting" type="button" class="btn btn-dark" v-on:click="fireSave()">@{{ systemVar.editType == 'delete' ? '刪除' : '儲存'}}</button>
                        <button v-else type="button" class="btn btn-dark" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            &nbsp;儲存中...
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 訊息 --}}
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
                        <p v-html="systemVar.msg"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
