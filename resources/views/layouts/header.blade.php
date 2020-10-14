<script>
    document.addEventListener('DOMContentLoaded', function (e) {
        new Vue({
            el: '#header',
            data: {
                routes: [
                    {
                        name: '首頁',
                        route: '/'
                    },
                    {
                        name: 'API 一覽',
                        route: '/api/all'
                    },
                    {
                        name: '版本紀錄',
                        route: '/version/history'
                    }
                ],
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
        })
    });
</script>

<nav id="header" class="navbar navbar-expand-lg navbar-light bg-light" v-cloak>
    <a class="navbar-brand" href="/">超異域公主連結資料 API</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li v-for="r in routes" :class="routeClass(r)">
                <a class="nav-link" :href="r.route" onclick="return false;">@{{ r.name }} <span v-if="route == r.route" class="sr-only">(current)</span></a>
            </li>
            {{-- <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Dropdown
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li> --}}
        </ul>
    </div>
</nav>