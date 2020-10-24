@extends('backend.layouts.master')

@section('title', '角色一覽 - 編輯角色資料')

@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            new Vue({
                el: '#character-list',
                data: {
                    characters: [],
                    guilds: [
                        {id: null, name: '-- 請選擇公會 --'},
                    ],
                    cvs: [
                        {id: null, name: '-- 請選擇聲優 --'},
                    ],
                    races: [
                        {id: null, name: '-- 請選擇種族 --'},
                    ],
                    skillTypes: [],
                    edittype: null,
                    characterInfo: {
                        tw_name: '',
                        jp_name: '',
                        guild_of: null,
                        cv_of: null,
                        race_of: null,
                        description: '',
                        ages: null,
                        height: null,
                        weight: null,
                        blood_type: null,
                        nicknames: '',
                        likes: '',
                        birthday: '',
                        s_image_url: null,
                        f_image_url: null,
                        t_image_url: null,
                        skills: [],
                    },
                    createCV: {
                        name: ''
                    },
                    createGuild: {
                        name: ''
                    },
                    createRace: {
                        name: ''
                    },
                    msg: '',
                    msgType: 'info',
                    loading: true,
                    saving: false,
                    subsaving: false,
                    requestingData: false,
                    mainFormDisabled: false,
                    fadeDuration: 200,
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
                    setType: function (type) {
                        this.edittype = type;
                        $('#modifyCharacter').modal('show');
                    },
                    resetFormStatus: function () {
                        this.edittype = null;
                        let skills = [];
                        this.skillTypes.forEach((st) => {
                            skills.push({
                                skill_type_of: st.id,
                                skill_name: '',
                                description: '',
                                effect: '',
                            });
                        });

                        this.characterInfo = {
                            tw_name: '',
                            jp_name: '',
                            cv_of: null,
                            race_of: null,
                            description: '',
                            ages: null,
                            height: null,
                            weight: null,
                            blood_type: null,
                            nicknames: '',
                            likes: '',
                            birthday: '',
                            guild_of: null,
                            s_image_url: null,
                            f_image_url: null,
                            t_image_url: null,
                            skills: skills,
                        };
                    },
                    showEditForm: function (id) {
                        this.edittype = 'edit';
                        this.requestingData = true;
                        $('#modifyCharacter').modal('show');

                        axios.get(`/webapi/admin/api/character/${id}`).then((res) => {
                            this.characterInfo = res.data;
                            this.characterInfo.birthday = (this.characterInfo.birthday == null)
                                                        ? null
                                                        : RSN.processDate(this.characterInfo.birthday, true);
                        }).catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.requestingData = false;
                        });
                    },
                    fireAddCharacter: function () {
                        this.saving = true;
                        this.characterInfo.blood_type = (this.characterInfo.blood_type == null) ? null : this.characterInfo.blood_type.toString().toUpperCase();
                        this.characterInfo.nicknames = (this.characterInfo.nicknames == null) ? null : this.characterInfo.nicknames.trim().replace(/\r/g, '').split('\n');
                        this.characterInfo.likes = (this.characterInfo.likes == null) ? null : this.characterInfo.likes.trim().replace(/\r/g, '').split('\n');
                        axios.post('/webapi/character', this.characterInfo).then((res) => {
                            this.characters.push({
                                id: res.data,
                                tw_name: this.characterInfo.tw_name,
                                jp_name: this.characterInfo.jp_name
                            });
                            $('#modifyCharacter').modal('hide');
                        }).catch((errors) => {
                            this.characterInfo.nicknames = (this.characterInfo.nicknames == null) ? null : this.characterInfo.nicknames.join('\n');
                            this.characterInfo.likes = (this.characterInfo.likes == null) ? null : this.characterInfo.likes.join('\n');
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.saving = false;
                        });
                    },
                    fireEditCharacter: function () {
                        this.saving = true;
                        this.characterInfo.blood_type = (this.characterInfo.blood_type == null) ? null : this.characterInfo.blood_type.toString().toUpperCase();
                        this.characterInfo.nicknames = (this.characterInfo.nicknames == null) ? null : this.characterInfo.nicknames.trim().replace(/\r/g, '').split('\n');
                        this.characterInfo.likes = (this.characterInfo.likes == null) ? null : this.characterInfo.likes.trim().replace(/\r/g, '').split('\n');
                        let data = Object.assign(this.characterInfo, {
                            _method: 'patch',
                        });
                        axios.post('/webapi/character', data).then((res) => {
                            let index = this.characters.indexOf(this.characters.filter(chara => chara.id == this.characterInfo.id)[0]);

                            if (index > -1) {
                                this.characters[index].tw_name = this.characterInfo.tw_name;
                                this.characters[index].jp_name = this.characterInfo.jp_name;
                            }
                            $('#modifyCharacter').modal('hide');
                        }).catch((errors) => {
                            this.characterInfo.nicknames = (this.characterInfo.nicknames == null) ? null : this.characterInfo.nicknames.join('\n');
                            this.characterInfo.likes = (this.characterInfo.likes == null) ? null : this.characterInfo.likes.join('\n');
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.saving = false;
                        });
                    },
                    toggleForm: function (type) {
                        $('#main-form').fadeOut(this.fadeDuration, () => {
                            switch (type) {
                                case 'cv':
                                    $('#add-cv, #title-add-cv').fadeIn(this.fadeDuration);
                                    this.mainFormDisabled = true;
                                    break;
                                case 'guild':
                                    $('#add-guild, #title-add-guild').fadeIn(this.fadeDuration);
                                    this.mainFormDisabled = true;
                                    break;
                                case 'race':
                                    $('#add-race, #title-add-race').fadeIn(this.fadeDuration);
                                    this.mainFormDisabled = true;
                                    break;
                                case 'close':
                                    this.createCV.name = '';
                                    this.createGuild.name = '';
                                    this.mainFormDisabled = false;
                                    $('#add-cv, #add-guild, #add-race, #title-add-cv, #title-add-guild, #title-add-race').fadeOut(this.fadeDuration, function () {
                                        $('#main-form').fadeIn(this.fadeDuration);
                                    });
                                    break;
                                default:
                                    break;
                            }
                        });
                    },
                    fireAddCV: function () {
                        this.subsaving = true;
                        axios.post('/webapi/character/cv', {
                            name: this.createCV.name,
                        }).then((res) => {
                            this.cvs.push({id: res.data, name: this.createCV.name});
                            this.characterInfo.cv_of = res.data;
                            this.createCV.name = '';
                            this.toggleForm('close');
                        }).catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.subsaving = false;
                        });
                    },
                    fireAddGuild: function () {
                        this.subsaving = true;
                        axios.post('/webapi/character/guild', {
                            name: this.createGuild.name,
                        }).then((res) => {
                            this.guilds.push({id: res.data, name: this.createGuild.name});
                            this.characterInfo.guild_of = res.data;
                            this.createGuild.name = '';
                            this.toggleForm('close');
                        }).catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.subsaving = false;
                        });
                    },
                    fireAddRace: function () {
                        this.subsaving = true;
                        axios.post('/webapi/character/race', {
                            name: this.createRace.name,
                        }).then((res) => {
                            this.races.push({id: res.data, name: this.createRace.name});
                            this.characterInfo.race_of = res.data;
                            this.createRace.name = '';
                            this.toggleForm('close');
                        }).catch((errors) => {
                            this.showMsg('error', this.getErrorMsg(errors));
                        }).finally(() => {
                            this.subsaving = false;
                        });
                    }
                },
                mounted: function () {
                    let promises = [
                        axios.get('/api/v1/characters'),
                        axios.get('/api/v1/guilds'),
                        axios.get('/api/v1/skill/types'),
                        axios.get('/api/v1/cvs'),
                        axios.get('/api/v1/races')
                    ];

                    Promise.all(promises).then((result) => {
                        result.forEach((res, i) => {
                            switch (i) {
                                case 0:
                                    this.characters = res.data;
                                    break;
                                case 1:
                                    this.guilds = this.guilds.concat(res.data);
                                    break;
                                case 2:
                                    this.skillTypes = res.data;

                                    this.skillTypes.forEach((st) => {
                                        this.characterInfo.skills.push({
                                            skill_type_of: st.id,
                                            skill_name: '',
                                            description: '',
                                            effect: '',
                                        });
                                    });
                                    break;
                                case 3:
                                    this.cvs = this.cvs.concat(res.data);
                                    break;
                                case 4:
                                    this.races = this.races.concat(res.data);
                                    break;
                            }
                        });
                    }).catch((errors) => {
                        this.showMsg('error', this.getErrorMsg(errors));
                    }).finally(() => {
                        this.loading = false;
                    });

                    $('#modifyCharacter').on('hidden.bs.modal', () => {
                        this.resetFormStatus();
                        this.createCV.name = '';
                        this.createGuild.name = '';
                        $('#add-cv, #add-guild, #add-race, #title-add-cv, #title-add-guild, #title-add-race').fadeOut(this.fadeDuration, function () {
                            this.mainFormDisabled = false;
                            $('#main-form').fadeIn(this.fadeDuration);
                        });
                    });

                    $('[data-toggle="tooltip"]').tooltip();
                },
                computed: {
                    modalTitle: function () {
                        switch (this.edittype) {
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
                    }
                }
            });
        });
    </script>

    <div id="character-list" v-cloak>
        <div class="d-flex justify-content-end col-12 mb-3">
            <button type="button" class="btn btn-outline-dark d-flex" v-on:click="setType('add')" :disabled="loading">
                <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                </svg>&nbsp;&nbsp;
                新增角色
            </button>
        </div>
        <table id="character-table" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th scope="col" class="align-middle bg-secondary text-white sticky">#</th>
                    <th scope="col" class="align-middle bg-secondary text-white sticky">中文名稱</th>
                    <th scope="col" class="align-middle bg-secondary text-white sticky">日文名稱</th>
                    <th scope="col" class="align-middle bg-secondary text-white sticky">編輯</th>
                </tr>
            </thead>
            <tbody v-if="!loading">
                <template v-if="characters.length > 0">
                    <tr v-for="(chara, i) in characters" :key="chara.id">
                        <td class="align-middle">@{{ i + 1 }}</td>
                        <td class="align-middle">@{{ chara.tw_name }}</td>
                        <td class="align-middle">@{{ chara.jp_name }}</td>
                        <td class="align-middle">
                            <button type="button" class="btn btn-outline-dark mr-2" v-on:click="showEditForm(chara.id)">
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
                        <td colspan="4" class="h4 text-center align-middle"><strong>目前尚未輸入任何角色資料</strong></td>
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
        {{-- 新增/編輯角色開始 --}}
        <div class="modal fade" id="modifyCharacter" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modifyCharacterLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modifyCharacterLabel">
                            @{{ modalTitle }}角色
                            <span class="h6 text-secondary form-hide" id="title-add-cv">&nbsp;&nbsp;>&nbsp;&nbsp;新增聲優</span>
                            <span class="h6 text-secondary form-hide" id="title-add-guild">&nbsp;&nbsp;>&nbsp;&nbsp;新增公會</span>
                            <span class="h6 text-secondary form-hide" id="title-add-race">&nbsp;&nbsp;>&nbsp;&nbsp;新增種族</span>
                        </h5>
                    </div>
                    <div class="modal-body">
                        {{-- 主表單 --}}
                        <template v-if="requestingData">
                            <div class="h4 text-dark text-center mt-2">
                                <span class="spinner-border" role="status" aria-hidden="true"></span>&nbsp;
                                <strong>資料讀取中...</strong>
                            </div>
                        </template>
                        <template v-else>
                            <div id="main-form">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="chara-tw-name">中文名稱</label>
                                        <input type="text" class="form-control" id="chara-tw-name" v-model.trim="characterInfo.tw_name" placeholder="請輸入角色中文名稱">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="chara-jp-name">日文名稱</label>
                                        <input type="text" class="form-control" id="chara-jp-name" v-model.trim="characterInfo.jp_name" placeholder="請輸入角色日文名稱">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">簡介</label>
                                    <textarea class="form-control" id="description" v-model.trim="characterInfo.description" rows="3" placeholder="請輸入角色簡介"></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex pb-2">
                                        <div class="col-10 justify-content-start p-0">
                                            <label for="cv">
                                                聲優
                                            </label>
                                        </div>
                                        <div class="col-2 justify-content-end text-right p-0">
                                            <button type="button" v-on:click="toggleForm('cv')" class="btn btn-dark btn-sm">找不到聲優？</button>
                                        </div>
                                    </div>
                                    <select class="form-control" id="cv" v-model="characterInfo.cv_of">
                                        <option v-for="cv in cvs" :value="cv.id">@{{ cv.name }}</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="chara-s_image_url">角色小圖網址</label>
                                        <input type="text" class="form-control" v-model.trim="characterInfo.s_image_url" id="chara-s_image_url" placeholder="請輸入角色小圖網址" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="chara-f_image_url">角色大圖網址</label>
                                        <input type="text" class="form-control" v-model.trim="characterInfo.f_image_url" id="chara-f_image_url" placeholder="請輸入角色大圖網址" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="chara-t_image_url">角色縮圖網址</label>
                                        <input type="text" class="form-control" v-model.trim="characterInfo.t_image_url" id="chara-t_image_url" placeholder="請輸入角色縮圖網址" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="chara-age">年齡</label>
                                        <input type="number" class="form-control" v-model.number="characterInfo.ages" id="chara-age" placeholder="請輸入角色年齡">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="chara-height">身高</label>
                                        <input type="number" class="form-control" v-model.number="characterInfo.height" id="chara-height" placeholder="請輸入角色身高">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="chara-weight">體重</label>
                                        <input type="number" class="form-control" v-model.number="characterInfo.weight" id="chara-weight" placeholder="請輸入角色體重">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="chara-blood_type">血型</label>
                                        <input type="text" class="form-control" v-model="characterInfo.blood_type" id="chara-blood_type" placeholder="請輸入角色血型">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex pb-2">
                                        <div class="col-10 justify-content-start p-0">
                                            <label for="race">
                                                種族
                                            </label>
                                        </div>
                                        <div class="col-2 justify-content-end text-right p-0">
                                            <button type="button" v-on:click="toggleForm('race')" class="btn btn-dark btn-sm">找不到種族？</button>
                                        </div>
                                    </div>
                                    <select class="form-control" id="race" v-model="characterInfo.race_of">
                                        <option v-for="race in races" :value="race.id">@{{ race.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nicknames">別名</label>
                                    <textarea class="form-control" id="nicknames" v-model.trim="characterInfo.nicknames" rows="3" placeholder="請輸入角色別名，多個請用換行分開"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="likes">喜好</label>
                                    <textarea class="form-control" id="likes" v-model="characterInfo.likes" rows="3" placeholder="請輸入角色喜好，多個請用換行分開"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="birthday">生日</label>
                                    <input type="date" class="form-control" v-model="characterInfo.birthday" id="birthday" placeholder="請輸入角色別生日日期">
                                </div>
                                <div class="form-group">
                                    <div class="d-flex pb-2">
                                        <div class="col-10 justify-content-start p-0">
                                            <label for="guild">
                                                所屬公會
                                            </label>
                                        </div>
                                        <div class="col-2 justify-content-end text-right p-0">
                                            <button type="button" v-on:click="toggleForm('guild')" class="btn btn-dark btn-sm">找不到公會？</button>
                                        </div>
                                    </div>
                                    <select class="form-control" id="guild" v-model="characterInfo.guild_of">
                                        <option v-for="guild in guilds" :value="guild.id">@{{ guild.name }}</option>
                                    </select>
                                </div>
                                <hr>
                                <p class="text-danger text-center">
                                    <strong>
                                        @{{ (edittype == 'edit') ? '如需刪除技能資料，請將該技能的名稱與說明欄位整個留空！' : '請注意，技能名稱或說明如有其中一項留空，則該技能不會被寫入資料庫！' }}
                                    </strong>
                                </p>
                                <template v-for="(st, i) in skillTypes">
                                    <div class="form-group" :key="st.id">
                                        <label :for="`skillType-${st.id}`">@{{ `${st.name}名稱` }}</label>
                                        <input type="text" class="form-control" v-model.trim="characterInfo.skills[i].skill_name" :id="`skillType-${st.id}`" :placeholder="`請輸入${st.name}名稱`">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label :for="`skillType-${st.id}-description`">技能說明</label>
                                            <textarea class="form-control" v-model.trim="characterInfo.skills[i].description" :id="`skillType-${st.id}-description`" rows="3" placeholder="請輸入技能說明"></textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label :for="`skillType-${st.id}-effect`">技能效果</label>
                                            <textarea class="form-control" v-model.trim="characterInfo.skills[i].effect" :id="`skillType-${st.id}-effect`" rows="3" placeholder="請輸入技能效果"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- 新增聲優 --}}
                        <div id="add-cv" class="form-hide">
                            <div class="form-inline pl-4">
                                <div class="form-group mb-2">
                                    <label for="staticEmail2">聲優名稱</label>
                                </div>
                                <div class="form-group col-9 mb-2">
                                    <label for="cv_name" class="sr-only">聲優名稱</label>
                                    <input type="text" class="form-control w-100" id="cv_name" v-model.trim="createCV.name" placeholder="請輸入聲優日文名稱">
                                </div>
                                <button type="submit" class="btn btn-outline-dark mb-2 mr-2" v-on:click="toggleForm('close')">
                                    <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                    </svg>
                                    &nbsp;&nbsp;取消
                                </button>
                                <button v-if="!subsaving" type="submit" v-on:click="fireAddCV()" class="btn btn-dark mb-2">
                                    <svg v-if="edittype == 'add'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                    </svg>
                                    &nbsp;&nbsp;新增
                                </button>
                                <button v-else type="submit" class="btn btn-dark mb-2" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    &nbsp;&nbsp;儲存中...
                                </button>
                            </div>
                        </div>

                        {{-- 新增公會 --}}
                        <div id="add-guild" class="form-hide">
                            <div class="form-inline pl-4">
                                <div class="form-group mb-2">
                                    <label for="staticEmail2">公會名稱</label>
                                </div>
                                <div class="form-group col-9 mb-2">
                                    <label for="guild_name" class="sr-only">公會名稱</label>
                                    <input type="text" class="form-control w-100" id="guild_name" v-model.trim="createGuild.name" placeholder="請輸入公會名稱">
                                </div>
                                <button type="submit" class="btn btn-outline-dark mb-2 mr-2" v-on:click="toggleForm('close')">
                                    <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                    </svg>
                                    &nbsp;&nbsp;取消
                                </button>
                                <button v-if="!subsaving" type="submit" v-on:click="fireAddGuild()" class="btn btn-dark mb-2">
                                    <svg v-if="edittype == 'add'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                    </svg>
                                    &nbsp;&nbsp;新增
                                </button>
                                <button v-else type="submit" class="btn btn-dark mb-2" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    &nbsp;&nbsp;儲存中...
                                </button>
                            </div>
                        </div>

                        {{-- 新增種族 --}}
                        <div id="add-race" class="form-hide">
                            <div class="form-inline pl-4">
                                <div class="form-group mb-2">
                                    <label for="raceName">種族名稱</label>
                                </div>
                                <div class="form-group col-9 mb-2">
                                    <label for="race_name" class="sr-only">種族名稱</label>
                                    <input type="text" class="form-control w-100" id="race_name" v-model.trim="createRace.name" placeholder="請輸入種族名稱">
                                </div>
                                <button type="submit" class="btn btn-outline-dark mb-2 mr-2" v-on:click="toggleForm('close')">
                                    <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                    </svg>
                                    &nbsp;&nbsp;取消
                                </button>
                                <button v-if="!subsaving" type="submit" v-on:click="fireAddRace()" class="btn btn-dark mb-2">
                                    <svg v-if="edittype == 'add'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                    </svg>
                                    &nbsp;&nbsp;新增
                                </button>
                                <button v-else type="submit" class="btn btn-dark mb-2" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    &nbsp;&nbsp;儲存中...
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
                        <button type="button" class="btn btn-outline-dark" data-dismiss="modal" data-toggle="tooltip" data-placement="top" title="關閉此視窗會清除所有已輸入的資料" :disabled="mainFormDisabled">
                            <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            </svg>
                            &nbsp;&nbsp;取消
                        </button>
                        <button v-if="!saving" type="button" class="btn btn-dark" v-on:click="(edittype == 'add') ? fireAddCharacter() : fireEditCharacter()" :disabled="mainFormDisabled">
                            <svg v-if="edittype == 'add'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                            </svg>
                            <svg v-if="edittype == 'edit'" width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
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
        {{-- 新增/編輯角色結束 --}}

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
