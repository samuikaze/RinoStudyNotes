@extends('backend.layouts.master')

@section('title', '角色關聯的資料管理')

@section('content')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Vue({
            el: '#relate',
            data: {
                guilds: [],
                cvs: [],
                races: [],
                skillTypes: [],
                msg: '',
                msgType: 'info',
                systemVar: {
                    thisTab: 'guild',
                    edittype: null,
                    loading: true,
                    saving: false,
                },
                modifyContent: {
                    id: null,
                    name: ''
                },
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
                changeSystemVar: function (key, value) {
                    this.systemVar[key] = value;
                },
                showModifyModal: function (type) {
                    this.systemVar.edittype = type;
                    $('#modifyItem').modal('show');
                },
                fireAddItem: function () {
                    this.systemVar.saving = true;
                    let apiUri = '';
                    switch (this.systemVar.thisTab) {
                        case 'guild':
                            apiUri = '/api/v1/guild';
                            break;
                        case 'cv':
                            apiUri = '/api/v1/cv';
                            break;
                        case 'race':
                            apiUri = '/api/v1/race';
                            break;
                        case 'skillType':
                            apiUri = '/api/v1/skill/type';
                            break;
                        default:
                            this.systemVar.saving = false;
                            return;
                    }

                    axios.post(apiUri, {
                        name: this.modifyContent.name,
                    }).then((res) => {
                        switch (this.systemVar.thisTab) {
                            case 'guild':
                                this.guilds.push({
                                    id: res.data.data,
                                    name: this.modifyContent.name,
                                });
                                break;
                            case 'cv':
                                this.cvs.push({
                                    id: res.data.data,
                                    name: this.modifyContent.name,
                                });
                                break;
                            case 'race':
                                this.races.push({
                                    id: res.data.data,
                                    name: this.modifyContent.name,
                                });
                                break;
                            case 'skillType':
                                this.skillTypes.push({
                                    id: res.data.data,
                                    name: this.modifyContent.name,
                                });
                                break;
                        }

                        this.modifyContent.name = '';
                        $('#modifyItem').modal('hide');
                    }).catch((errors) => {
                        this.showMsg('error', this.getErrorMsg(errors));
                    }).finally(() => {
                        this.systemVar.saving = false;
                    });
                }
            },
            mounted: function () {
                let promises = [
                    axios.get('/api/v1/guilds'),
                    axios.get('/api/v1/cvs'),
                    axios.get('/api/v1/races'),
                    axios.get('/api/v1/skill/types')
                ];

                Promise.all(promises).then((result) => {
                    result.forEach((res, i) => {
                        switch (i) {
                            case 0:
                                this.guilds = res.data.data;
                                break;
                            case 1:
                                this.cvs = res.data.data;
                                break;
                            case 2:
                                this.races = res.data.data;
                                break;
                            case 3:
                                this.skillTypes = res.data.data;
                                break;
                        }
                    })
                }).catch((errors) => {
                    this.showMsg('error', this.getErrorMsg(errors));
                }).finally(() => {
                    this.systemVar.loading = false;
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
                },
                addButtonName: function () {
                    switch (this.systemVar.thisTab) {
                        case 'guild':
                            return '公會';
                            break;
                        case 'cv':
                            return '聲優';
                            break;
                        case 'race':
                            return '種族';
                            break;
                        case 'skillType':
                            return '技能種類';
                            break;
                    }
                },
                actionTitle: function () {
                    switch (this.systemVar.edittype) {
                        case 'add':
                            return '新增';
                            break;
                        case 'edit':
                            return '編輯';
                            break;
                    }
                }
            }
        });
    });
</script>

<div id="relate" v-cloak>
    <div class="card">
        <div class="card-header d-flex pb-1 pr-1 pl-1 border-0">
            <div class="d-flex col-6 justify-content-start pt-2">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation" v-on:click="changeSystemVar('thisTab', 'guild')">
                        <a class="nav-link active" id="guilds-tab" data-toggle="tab" href="#guilds" role="tab" aria-controls="guilds" aria-selected="true">公會資料</a>
                    </li>
                    <li class="nav-item" role="presentation" v-on:click="changeSystemVar('thisTab', 'cv')">
                        <a class="nav-link" id="cvs-tab" data-toggle="tab" href="#cvs" role="tab" aria-controls="cvs" aria-selected="true">聲優資料</a>
                    </li>
                    <li class="nav-item" role="presentation" v-on:click="changeSystemVar('thisTab', 'race')">
                        <a class="nav-link" id="races-tab" data-toggle="tab" href="#races" role="tab" aria-controls="races" aria-selected="true">種族資料</a>
                    </li>
                    <li class="nav-item" role="presentation" v-on:click="changeSystemVar('thisTab', 'skillType')">
                        <a class="nav-link" id="skill-types-tab" data-toggle="tab" href="#skill-types" role="tab" aria-controls="skill-types" aria-selected="true">技能種類資料</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex col-6 justify-content-end">
                <button type="button" class="btn btn-dark mb-2" v-on:click="showModifyModal('add')">新增@{{ addButtonName }}</button>
            </div>
        </div>
        <div class="tab-content" id="myTabContent">
            <template v-if="systemVar.loading">
                <div class="text-center h4 m-3 p-1 text-dark">
                    <span class="spinner-border mr-1" role="status" aria-hidden="true"></span>
                    <strong>資料讀取中...</strong>
                </div>
            </template>
            <template v-else>
                <div class="tab-pane fade show active" id="guilds" role="tabpanel" aria-labelledby="guilds-tab">
                    <div class="card-body">
                        <table class="table table-hover table-bordered related-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="bg-secondary text-white sticky">ID</th>
                                    <th scope="col" class="bg-secondary text-white sticky">名稱</th>
                                    <th scope="col" class="bg-secondary text-white sticky">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="guilds.length > 0">
                                    <tr v-for="guild in guilds">
                                        <td class="align-middle">@{{ guild.id }}</td>
                                        <td class="align-middle">@{{ guild.name }} @{{ (guild.deleted_at == null) ? '' : '(已刪除)' }}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-outline-dark mr-2" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                </svg>&nbsp;&nbsp;
                                                編輯
                                            </button>
                                            <button type="button" class="btn btn-dark" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z"/>
                                                </svg>&nbsp;&nbsp;
                                                刪除
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="text-center h5"><strong>目前尚未輸入任何公會資料</strong></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="cvs" role="tabpanel" aria-labelledby="cvs-tab">
                    <div class="card-body">
                        <table class="table table-hover table-bordered related-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="bg-secondary text-white sticky">ID</th>
                                    <th scope="col" class="bg-secondary text-white sticky">名稱</th>
                                    <th scope="col" class="bg-secondary text-white sticky">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="cvs.length > 0">
                                    <tr v-for="cv in cvs">
                                        <td class="align-middle">@{{ cv.id }}</td>
                                        <td class="align-middle">@{{ cv.name }} @{{ (cv.deleted_at == null) ? '' : '(已刪除)' }}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-outline-dark mr-2" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                </svg>&nbsp;&nbsp;
                                                編輯
                                            </button>
                                            <button type="button" class="btn btn-dark" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z"/>
                                                </svg>&nbsp;&nbsp;
                                                刪除
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="text-center h5"><strong>目前尚未輸入任何聲優資料</strong></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="races" role="tabpanel" aria-labelledby="races-tab">
                    <div class="card-body">
                        <table class="table table-hover table-bordered related-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="bg-secondary text-white sticky">ID</th>
                                    <th scope="col" class="bg-secondary text-white sticky">名稱</th>
                                    <th scope="col" class="bg-secondary text-white sticky">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="races.length > 0">
                                    <tr v-for="race in races">
                                        <td class="align-middle">@{{ race.id }}</td>
                                        <td class="align-middle">@{{ race.name }} @{{ (race.deleted_at == null) ? '' : '(已刪除)' }}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-outline-dark mr-2" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                </svg>&nbsp;&nbsp;
                                                編輯
                                            </button>
                                            <button type="button" class="btn btn-dark" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z"/>
                                                </svg>&nbsp;&nbsp;
                                                刪除
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="text-center h5"><strong>目前尚未輸入任何種族資料</strong></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="skill-types" role="tabpanel" aria-labelledby="skill-types-tab">
                    <div class="card-body">
                        <table class="table table-hover table-bordered related-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="bg-secondary text-white sticky">ID</th>
                                    <th scope="col" class="bg-secondary text-white sticky">種類名稱</th>
                                    <th scope="col" class="bg-secondary text-white sticky">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="skillTypes.length > 0">
                                    <tr v-for="st in skillTypes">
                                        <td class="align-middle">@{{ st.id }}</td>
                                        <td class="align-middle">@{{ st.name }} @{{ (st.deleted_at == null) ? '' : '(已刪除)' }}</td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-outline-dark mr-2" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                </svg>&nbsp;&nbsp;
                                                編輯
                                            </button>
                                            <button type="button" class="btn btn-dark" disabled>
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z"/>
                                                </svg>&nbsp;&nbsp;
                                                刪除
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="3" class="text-center h5"><strong>目前尚未輸入任何技能種類資料</strong></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>
    {{-- 新增/編輯 Modal --}}
    <div class="modal fade" id="modifyItem" tabindex="-1" aria-labelledby="modifyItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifyItemLabel">@{{ `${actionTitle + addButtonName}資料` }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-inline pl-5">
                        <div class="form-group mb-2">
                            <label for="staticEmail2">@{{ addButtonName }}名稱</label>
                        </div>
                        <div class="form-group col-10 mb-2">
                            <label for="guild_name" class="sr-only">@{{ addButtonName }}名稱</label>
                            <input type="text" class="form-control w-100" id="guild_name" v-model.trim="modifyContent.name" :placeholder="`請輸入${addButtonName}名稱`">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-dismiss="modal">
                        <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                        </svg>
                        &nbsp;&nbsp;取消
                    </button>
                    <button v-if="!systemVar.saving" v-on:click="fireAddItem()" type="button" class="btn btn-dark">
                        <svg v-if="systemVar.edittype == 'add'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                        </svg>
                        <svg v-if="systemVar.edittype == 'edit'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        &nbsp;&nbsp;儲存
                    </button>
                    <button v-else type="button" class="btn btn-dark" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        &nbsp;儲存中...
                    </button>
                </div>
            </div>
        </div>
    </div>

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
