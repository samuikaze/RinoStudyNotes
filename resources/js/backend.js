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
                    name: '首頁',
                    route: '/admin'
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
                axios.post('/api/v1/user', {
                    _method: 'patch',
                    nickname: (this.eUser.nickname.length == 0) ? null : this.eUser.nickname,
                })
                    .then((res) => {
                        this.user = res.data.data;
                        this.eUser = _.cloneDeep(this.user);
                        this.user.nickname = (this.user.nickname == null) ? this.user.username : this.user.nickname;
                    })
                    .catch((errors) => {
                        alert(this.getErrorMsg(errors));
                    })
                    .finally(() => {
                        event.target.innerHTML = '儲存';
                        event.target.disabled = false;
                        $('#editUserData').modal('hide');
                    });
            },
            fireLogout: function () {
                Cookies.remove('token');
                window.location.href = '/admin/logout';
            }
        },
        created: function () {
            let token = Cookies.get('token');
            if (token != null) {
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }

            axios.get('/api/v1/user')
                .then((res) => {
                    this.user = res.data.data;
                    this.eUser = _.cloneDeep(this.user);
                    this.user.nickname = (this.user.nickname == null) ? this.user.username : this.user.nickname;
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    this.loading = false;
                    if (this.user.length === 0 && window.location.pathname.search('/admin/authentication') < 0) {
                        window.location.href = '/admin/authentication';
                    }
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
