@extends('layouts.master')

@section('title', '版本歷史紀錄')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            new Vue({
                el: '#versions',
                data: {
                    versions: [],
                    loading: true,
                    msg: '',
                    msgType: 'info'
                },
                methods: {
                    showMsg: function (type, msg) {
                        this.msgType = type;
                        this.msg = msg;
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
                    getVersions: function (times) {
                        this.loading = true;
                        axios.get('/frontend/version/all', {params: {
                            start: times,
                        }})
                            .then((res) => {
                                this.versions = this.versions.concat(this.processData(res.data));
                            })
                            .catch((errors) => {
                                this.showMsg('error', RSN.getErrorMsg(errors));
                            })
                            .finally(() => {
                                this.loading = false;
                            });
                    }
                },
                mounted: function () {
                    this.getVersions(1);
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

    <div id="versions" v-cloak>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">更新紀錄</h5>
                <p class="card-text">下面會逐一列出本站改版的歷史紀錄，可以從 GitHub 取得更詳細的更新紀錄。</p>
                <div v-if="loading" class="h4 text-center pt-3">
                    <span class="spinner-border" role="status" aria-hidden="true"></span>
                    <span><strong>資料讀取中...</strong></span>
                </div>
                <div v-else>
                    <template v-for="ver in versions">
                        <hr>
                        <div class="card-text" :key="ver.id">
                            <h4>@{{ ver.version_id }} <span class="text-secondary h6">更新於 @{{ ver.created_at }}</span></h4>
                            <div class="pl-3">
                                <ul>
                                    <li v-for="content in JSON.parse(ver.content)">@{{ content }}</li>
                                </ul>
                            </div>
                        </div>
                    </template>
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
