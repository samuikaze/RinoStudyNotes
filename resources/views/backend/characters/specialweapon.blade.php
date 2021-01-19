@extends('backend.layouts.master')

@section('title', '角色專用武器的資料管理')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Vue({
                el: '#special-weapon',
                data: {
                    specialWeapons: [],
                    weaponInfo: {
                        id: null,
                        name: '',
                        description: '',
                        ability: '',
                        apply: '',
                    },
                    msg: '',
                    msgType: 'info',
                    descriptions: [
                        {
                            title: 'physical',
                            values: [
                                {key: "pAtk", name: "物理攻擊力"},
                                {key: "pDef", name: "物理防禦力"},
                                {key: "pCriPerc", name: "物理爆擊機率"},
                                {key: "pCriAtk", name: "物理爆擊攻擊力"},
                            ],
                        },
                        {
                            title: 'magical',
                            values: [
                                {key: "mAtk", name: "魔法攻擊力"},
                                {key: "mDef", name: "魔法防禦力"},
                                {key: "mCriPerc", name: "魔法爆擊機率"},
                                {key: "mCriAtk", name: "魔法爆擊攻擊力"},
                            ],
                        },
                        {
                            title: 'others',
                            values: [
                                {key: "hp", name: "HP"},
                                {key: "hpAb", name: "HP 吸收"},
                                {key: "hpAR", name: "HP 自動回復"},
                                {key: "tp", name: "TP"},
                                {key: "tpAR", name: "TP 自動回復"},
                                {key: "tpRise", name: "TP 上升"},
                                {key: "tpCR", name: "TP 消耗減輕"},
                                {key: "Hit", name: "命中"},
                                {key: "agl", name: "迴避"},
                                {key: "rRise", name: "回復量上升"},
                                {key: "nAD", name: "普通攻擊最長距離"},
                            ],
                        },
                        {
                            title: 'symbols',
                            values: [
                                {key: "=", name: "分隔等級用，等號左邊為等級，右邊為該等級時的加乘數值"},
                                {key: ":", name: "加成能力與加成值的分隔，左邊為能力名稱，右邊為加成值"},
                                {key: ">", name: "加成值與充滿至該等級時的最大值分隔，左邊為加成基礎值，右邊為加成最大值"},
                                {key: "|", name: "加成能力間的分隔符號"},
                                {key: ",", name: "各等級加成能力值的分隔符號"},
                            ],
                        }
                    ],
                    systemVar: {
                        loading: true,
                        saving: false,
                        requestingData: false,
                        mainFormDisabled: false,
                        edittype: '',
                        fadeDuration: 200,
                    },
                },
                methods: {
                    showMsg: function (type, msg) {
                        this.msgType = type;
                        this.msg = msg;
                        $('#alertMsg').modal('show');
                    },
                    setType: function (type) {
                        this.systemVar.edittype = type;
                        $('#modifySpecialWeapon').modal('show');
                    },
                    showEditForm: function (id) {
                        this.systemVar.edittype = 'edit';
                        this.requestingData = true;
                        $('#modifySpecialWeapon').modal('show');

                        axios.get('/webapi/specialweapon', {params: {
                            id: id,
                        }}).then((res) => {
                            this.weaponInfo = res.data;
                            this.weaponInfo.apply = RSN.processDate(this.weaponInfo.apply, true);
                        }).catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.requestingData = false;
                        });
                    },
                    resetFormStatus: function () {
                        this.edittype = null;

                        this.weaponInfo = {
                            id: null,
                            character_of: null,
                            name: '',
                            description: '',
                            ability: '',
                        };
                    },
                    toggleForm: function (type) {
                        $('#main-form').fadeOut(this.fadeDuration, () => {
                            switch (type) {
                                case 'abilityDesc':
                                    $('#abilityDesc-form').fadeIn(this.systemVar.fadeDuration);
                                    this.systemVar.mainFormDisabled = true;
                                    break;
                                case 'close':
                                    this.systemVar.mainFormDisabled = false;
                                    $('#abilityDesc-form').fadeOut(this.fadeDuration, function () {
                                        $('#main-form').fadeIn(this.fadeDuration);
                                    });
                                    break;
                                default:
                                    break;
                            }
                        });
                    },
                    getDescTitle: function (raw) {
                        switch (raw) {
                            case 'physical':
                                return '物理類';
                                break;
                            case 'magical':
                                return '魔法類';
                                break;
                            case 'others':
                                return '其它類';
                                break;
                            case 'symbols':
                                return '符號說明';
                                break;
                            default:
                                return '';
                                break;
                        }
                    },
                    fireAddSpecialWeapon: function () {
                        this.systemVar.saving = true;
                        axios.post('/webapi/specialweapon', this.weaponInfo)
                            .then(res => {
                                this.specialWeapons = res.data;
                            })
                            .catch(errors => {
                                this.showMsg('error', RSN.getErrorMsg(errors));
                            })
                            .finally(() => {
                                this.systemVar.saving = false;
                                $('#modifySpecialWeapon').modal('hide');
                            });
                    },
                    fireEditSpecialWeapon: function () {
                        this.systemVar.saving = true;
                        this.systemVar.edittype = 'edit';

                        let data = Object.assign(this.weaponInfo, {
                            _method: 'patch',
                        });

                        axios.post('/webapi/specialweapon', data)
                            .then(res => {
                                this.specialWeapons = res.data;
                            })
                            .catch(errors => {
                                this.showMsg('error', RSN.getErrorMsg(errors));
                            })
                            .finally(() => {
                                this.systemVar.saving = false;
                                $('#modifySpecialWeapon').modal('hide');
                            });
                    },
                },
                mounted: function () {
                    axios.get('/webapi/specialweapons')
                        .then(res => {
                            this.specialWeapons = res.data;
                        })
                        .catch(errors => {
                            this.showMsg('error', RSN.getErrorMsg(errors));
                        })
                        .finally(() => {
                            this.systemVar.loading = false;
                        });

                    $('#modifySpecialWeapon').on('hidden.bs.modal', () => {
                        this.resetFormStatus();
                        $('#abilityDesc-form').fadeOut(this.systemVar.fadeDuration, () => {
                            this.systemVar.mainFormDisabled = false;
                            $('#main-form').fadeIn(this.systemVar.fadeDuration);
                        });
                    });
                },
                computed: {
                    modalTitle: function () {
                        switch (this.systemVar.edittype) {
                            case 'add':
                                return '新增';
                                break;
                            case 'edit':
                                return '編輯';
                                break;
                            default:
                                return '';
                                break;
                        }
                    },
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
                },
            });
        });
    </script>

    <div id="special-weapon" v-cloak>
        <div class="d-flex justify-content-end col-12 mb-3">
            <button type="button" class="btn btn-outline-dark d-flex" v-on:click="setType('add')" :disabled="systemVar.loading">
                <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                </svg>&nbsp;&nbsp;
                新增專用武器
            </button>
        </div>
        <table class="table table-hover table-bordered related-table">
            <thead>
                <tr>
                    <th scope="col" class="bg-secondary text-white sticky">ID</th>
                    <th scope="col" class="bg-secondary text-white sticky">武器名稱</th>
                    <th scope="col" class="bg-secondary text-white sticky">操作</th>
                </tr>
            </thead>
            <tbody v-if="!systemVar.loading">
                <template v-if="specialWeapons.length > 0">
                    <tr v-for="sw in specialWeapons">
                        <td class="align-middle">@{{ sw.id }}</td>
                        <td class="align-middle">@{{ sw.name }} @{{ (sw.deleted_at == null) ? '' : '(已刪除)' }}</td>
                        <td class="align-middle">
                            <button type="button" class="btn btn-outline-dark mr-2" v-on:click="showEditForm(sw.id)">
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
                        <td colspan="3" class="text-center h5"><strong>目前尚未輸入任何專用武器資料</strong></td>
                    </tr>
                </template>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="4" class="h4 text-center align-middle">
                        <span class="spinner-border" role="status" aria-hidden="true"></span>&nbsp;
                        資料讀取中...
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- 新增或編輯專用武器資料 --}}
        <div class="modal fade" id="modifySpecialWeapon" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modifySpecialWeaponLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifySpecialWeaponLabel">@{{ modalTitle }}專用武器</h5>
                </div>
                <div class="modal-body" id="main-form">
                    <div class="form-group">
                        <label for="specialWeaponName">專用武器名稱</label>
                        <input type="text" class="form-control" id="specialWeaponName" v-model="weaponInfo.name" placeholder="請輸入專用武器名稱">
                    </div>
                    <div class="form-group">
                        <div class="d-flex pb-2">
                            <div class="col-10 justify-content-start p-0">
                                <label for="ability">
                                    專用武器加成能力
                                </label>
                            </div>
                            <div class="col-2 justify-content-end text-right p-0">
                                <button type="button" v-on:click="toggleForm('abilityDesc')" class="btn btn-dark btn-sm">語法說明</button>
                            </div>
                        </div>
                        <small id="abilityHelp" class="form-text text-muted">各等級間可以換行，但請務必加上半形逗點分隔</small>
                        <textarea
                            class="form-control"
                            id="ability"
                            rows="6"
                            v-model="weaponInfo.ability"
                            placeholder="請輸入專武的加成能力，多個能力加成間請用 | 分隔，語法說明可以點選右側按鈕檢視"
                            aria-describedby="abilityHelp"
                        >
                        </textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">專用武器描述</label>
                        <textarea class="form-control" id="description" rows="3" v-model="weaponInfo.description" placeholder="請輸入專武的描述"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="apply">開專時間</label>
                        <small id="applyHelp" class="form-text text-muted">如未知開專時間可留空</small>
                        <input type="date" class="form-control" id="apply" rows="3" v-model="weaponInfo.apply" placeholder="請輸入專武的開專時間" aria-describedby="applyHelp">
                    </div>
                </div>
                <div class="modal-body form-hide" id="abilityDesc-form">
                    <div class="row">
                        <div class="col-12">
                            <p>由於專武能力加成種類太多，這邊直接提供寫鍵值對進系統，方便管理，下面列出來的是鍵名與其對應的中文，在 API 中返回資料時會直接使用。</p>
                            <div class="card mb-3" v-for="description in descriptions">
                                <div class="card-header h5 d-flex">
                                    <div class="col-10 justify-content-start p-0">
                                        @{{ getDescTitle(description.title) }}
                                    </div>
                                    <div class="d-flex col-2 justify-content-end p-0">
                                        <button
                                            class="btn btn-sm btn-dark"
                                            type="button"
                                            data-toggle="collapse"
                                            :data-target="`#${description.title}`"
                                            aria-expanded="false"
                                            :aria-controls="description.title"
                                        >
                                            展開 / 收合
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body collapse" :id="description.title">
                                    <table class="table table-hover table-borderless">
                                        <tbody>
                                            <tr v-for="value in description.values">
                                                <td><code>@{{ value.key }}</code></td>
                                                <td>@{{ value.name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header h5 d-flex">
                                    <div class="col-10 justify-content-start p-0">
                                        範例
                                    </div>
                                    <div class="d-flex col-2 justify-content-end p-0">
                                        <button
                                            class="btn btn-sm btn-dark"
                                            type="button"
                                            data-toggle="collapse"
                                            data-target="#desc-example"
                                            aria-expanded="false"
                                            aria-controls="desc-example"
                                        >
                                            展開 / 收合
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body collapse" id="desc-example">
                                    <table class="table table-hover table-borderless">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <code>
                                                        30=pAtk:15>239|tpRise:9>15,<br>
                                                        50=pAtk:15>325|tpRise:9>18
                                                    </code>
                                                </td>
                                                <td>
                                                    30等時，物理攻擊力 +15（升滿 30 等物攻為 239），TP 上升 +9（升滿 30 等 TP 最大上升 15）<br>
                                                    50等時，物理攻擊力 +15（升滿 30 等物攻為 325），TP 上升 +9（升滿 30 等 TP 最大上升 18）
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <code>
                                                        30=mAtk:15>215|hpAb:1.0>4,<br>
                                                        50=mAtk:15>301|hpAb:1.0>8
                                                    </code>
                                                </td>
                                                <td>
                                                    30等時，魔法攻擊力 +15（升滿 30 等魔攻為 215），HP 吸收 +15（升滿 30 等 HP 吸收最大為 4）<br>
                                                    50等時，魔法攻擊力 +15（升滿 30 等魔攻為 301），HP 吸收 +15（升滿 30 等 HP 吸收最大為 8）
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex col-12 justify-content-end">
                            <button type="submit" class="btn btn-outline-dark mb-2 mr-2" v-on:click="toggleForm('close')">
                                <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                </svg>
                                &nbsp;&nbsp;返回表單
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-6">
                        <p class="text-dark m-3 text-left">
                            <svg width="1.5em" height="1.5em" viewBox="0 0 17 16" class="bi bi-exclamation-triangle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                            </svg>
                            &nbsp;關閉此視窗會清除所有已輸入的資料，如需關閉請按下取消鈕關閉此視窗
                        </p>
                    </div>
                    <button type="button" class="btn btn-outline-dark" data-dismiss="modal" data-toggle="tooltip" data-placement="top" title="關閉此視窗會清除所有已輸入的資料" :disabled="systemVar.mainFormDisabled">
                        <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                        </svg>
                        &nbsp;&nbsp;取消
                    </button>
                    <button v-if="!systemVar.saving" type="button" class="btn btn-dark" v-on:click="(systemVar.edittype == 'add') ? fireAddSpecialWeapon() : fireEditSpecialWeapon()" :disabled="systemVar.mainFormDisabled">
                        <svg v-if="systemVar.edittype == 'add'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                        </svg>
                        <svg v-if="systemVar.edittype == 'edit'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        &nbsp;&nbsp;儲存
                    </button>
                    <button v-else class="btn btn-dark" disabled>
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
    </div>
@endsection
