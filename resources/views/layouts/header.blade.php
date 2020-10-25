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
    <a class="navbar-brand" href="/">璃乃學習筆記</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <template v-for="r in routes">
                <li v-if="!Array.isArray(r.route) && ((r.sysop && user.role_of === 1) || !r.sysop)" :key="r.id" :class="routeClass(r)">
                    <a class="nav-link"
                       v-bind:class="{disabled: r.disabled}"
                       :href="r.route" :onclick="(route == r.route) ? 'return false;' : 'return true;'"
                       :aria-disabled="(r.disabled) ? 'true' : ''"
                    >
                        @{{ r.name }} <span v-if="route == r.route" class="sr-only">(current)</span>
                    </a>
                </li>

                <li v-if="Array.isArray(r.route) && ((r.sysop && user.role_of === 1) || !r.sysop)" :key="r.id" class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @{{ r.name }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a v-for="subroute in r.route"
                           :key="subroute.id"
                           class="dropdown-item"
                           v-bind:class="{disabled: subroute.disabled}"
                           :href="subroute.route"
                           :aria-disabled="(subroute.disabled) ? 'true' : ''"
                        >
                            @{{ subroute.name }}
                        </a>
                    </div>
                </li>
            </template>
        </ul>
        @if(env('FRONTEND_SHOW_ADMIN'))
        <div class="text-right">
            <a class="text-dark text-decoration-none" href="/admin">
                <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                </svg>&nbsp;&nbsp;
                後台管理
            </a>
        </div>
        @endif
    </div>
</nav>
