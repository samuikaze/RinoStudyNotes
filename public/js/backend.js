document.addEventListener('DOMContentLoaded', function (e) {
    new Vue({
        el: '#header',
        data: {
            user: {},
            loading: true,
        },
        methods: {
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
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    });
});
