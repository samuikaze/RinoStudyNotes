document.addEventListener('DOMContentLoaded', function (e) {
    new Vue({
        el: '#header',
        data: {
            user: {},
            eUser: {},
            eOrigPassword: '',
            ePassword: '',
            ePasswordConf: '',
            loading: true,
            routes: [
                {
                  id: 1,
                  name: '首頁',
                  route: '/admin',
                },
                {
                  id: 2,
                  name: '系統管理',
                  route: [
                    {
                      id: 1,
                      name: '審核申請',
                      route: '/admin/verify'
                    },
                    {
                      id: 2,
                      name: '管理共同編輯者',
                      route: '/admin/editors',
                      disabled: true
                    },
                    {
                      id: 3,
                      name: '版本資料管理',
                      route: '/admin/versions',
                    }
                  ],
                  sysop: true
                },
                {
                  id: 3,
                  name: 'API 管理',
                  route: [
                    {
                      id: 1,
                      name: '角色資料',
                      route: '/admin/character',
                    },
                    {
                      id: 2,
                      name: '角色雜項資料',
                      route: '/admin/character/related',
                    },
                    {
                      id: 3,
                      name: '角色專用武器',
                      route: '/admin/character/specialweapon',
                    },
                  ]
                }
            ],
        },
        methods: {
            getErrorMsg: function (error) {
                if (error.response == null) {
                    return error;
                } else {
                    return error.response.data.errors;
                }
            },
            fireEditProfile: function (event) {
                event.target.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;送出請求中...';
                event.target.disabled = true;
                let data = {
                    _method: 'patch',
                    nickname: (this.eUser.nickname.length == 0) ? null : this.eUser.nickname,
                };
                let changePW = false;
                if (this.eOrigPassword.length > 0 && this.ePassword.length > 0 && this.ePasswordConf.length > 0) {
                    Object.assign(data, {
                        origPswd: this.eOrigPassword,
                        newPswd: this.ePassword,
                        newPswd_confirmation: this.ePasswordConf
                    });

                    changePW = true;
                }
                axios.post('/webapi/user', data)
                    .then((res) => {
                        this.user = res.data;
                        this.eUser = _.cloneDeep(this.user);
                        this.user.nickname = (this.user.nickname == null) ? this.user.username : this.user.nickname;
                        this.eOrigPassword = '';
                        this.ePassword = '';
                        this.ePasswordConf = '';
                        if (changePW) {
                            this.fireLogout();
                        } else {
                          event.target.innerHTML = '儲存';
                          event.target.disabled = false;
                          $('#editUserData').modal('hide');
                        }
                    })
                    .catch((errors) => {
                        alert(this.getErrorMsg(errors));
                        event.target.innerHTML = '儲存';
                        event.target.disabled = false;
                    });
            },
            fireLogout: function() {
                window.location.href = '/admin/logout';
            }
        },
        created: function () {
            let token = Cookies.get('token');
            if (token != null) {
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }

            axios.get('/webapi/user')
                .then((res) => {
                    this.user = res.data;
                    this.eUser = _.cloneDeep(this.user);
                    this.user.nickname = (this.user.nickname == null) ? this.user.username : this.user.nickname;
                })
                .catch((errors) => {
                    if (errors.response.status === 401 && location.pathname != '/admin/authentication') {
                        window.location.replace('/admin/logout');
                    }
                    this.user = [];
                })
                .finally(() => {
                    this.loading = false;
                    Vue.nextTick(() => {
                        $('#user-popover').popover({
                            placement: 'bottom',
                            title: '使用者選單',
                            content: (
                                `<ul class="list-group">
                                  <button
                                    class="list-group-item list-group-item-action text-center h5 text-primary"
                                    data-toggle="modal"
                                    data-target="#editUserData"
                                  >
                                    修改資料
                                  </button>
                                </ul>`
                            ),
                            html: true,
                            sanitize: false,
                            trigger: 'focus',
                        });
                    });
                });
        },
        computed: {
            route: function () {
                return window.location.pathname;
            },
            routeClass: function () {
                return (r) => {
                    if (this.route == r.route) {
                        return (r.disabled === true) ? 'nav-item disabled' : 'nav-item active';
                    } else {
                        return (r.disabled === true) ? 'nav-item disabled' : 'nav-item';
                    }
                }
            }
        }
    });
});
