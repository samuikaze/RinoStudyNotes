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
            <template v-for="r in routes">
                <li v-if="!Array.isArray(r.route) && ((r.sysop && user.role_of === 2) || !r.sysop)" :key="r.id" :class="routeClass(r)">
                    <a class="nav-link"
                       v-bind:class="{disabled: r.disabled}"
                       :href="r.route" :onclick="(route == r.route) ? 'return false;' : 'return true;'"
                       :aria-disabled="(r.disabled) ? 'true' : ''"
                    >
                        @{{ r.name }} <span v-if="route == r.route" class="sr-only">(current)</span>
                    </a>
                </li>

                <li v-if="Array.isArray(r.route) && ((r.sysop && user.role_of === 2) || !r.sysop)" :key="r.id" class="nav-item dropdown">
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
    </div>
</nav>
