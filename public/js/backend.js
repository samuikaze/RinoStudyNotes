/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/backend.js":
/*!*********************************!*\
  !*** ./resources/js/backend.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

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
      routes: [{
        id: 1,
        name: '首頁',
        route: '/admin'
      }, {
        id: 2,
        name: '系統管理',
        route: [{
          id: 1,
          name: '審核申請',
          route: '/admin/verify'
        }, {
          id: 2,
          name: '管理共同編輯者',
          route: '/admin/editors',
          disabled: true
        }, {
          id: 3,
          name: '版本資料管理',
          route: '/admin/versions',
          disabled: true
        }],
        sysop: true
      }, {
        id: 3,
        name: 'API 管理',
        route: [{
          id: 1,
          name: '角色資料',
          route: '/admin/character'
        }, {
          id: 2,
          name: '角色雜項資料',
          route: '/admin/character/related'
        }]
      }]
    },
    methods: {
      getErrorMsg: function getErrorMsg(error) {
        if (error.response == null) {
          return error;
        } else {
          return error.response.data.errors;
        }
      },
      fireEditProfile: function fireEditProfile(event) {
        var _this = this;

        event.target.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;送出請求中...';
        event.target.disabled = true;
        var data = {
          _method: 'patch',
          nickname: this.eUser.nickname.length == 0 ? null : this.eUser.nickname
        };
        var changePW = false;

        if (this.eOrigPassword.length > 0 && this.ePassword.length > 0 && this.ePasswordConf.length > 0) {
          Object.assign(data, {
            origPswd: this.eOrigPassword,
            newPswd: this.ePassword,
            newPswd_confirmation: this.ePasswordConf
          });
          changePW = true;
        }

        axios.post('/api/v1/user', data).then(function (res) {
          _this.user = res.data.data;
          _this.eUser = _.cloneDeep(_this.user);
          _this.user.nickname = _this.user.nickname == null ? _this.user.username : _this.user.nickname;
          _this.eOrigPassword = '';
          _this.ePassword = '';
          _this.ePasswordConf = '';

          if (changePW) {
            window.location.href = '/admin/logout';
            event.target.innerHTML = '登出中';
          } else {
            event.target.innerHTML = '儲存';
            event.target.disabled = false;
            $('#editUserData').modal('hide');
          }
        })["catch"](function (errors) {
          alert(_this.getErrorMsg(errors));
          event.target.innerHTML = '儲存';
          event.target.disabled = false;
        });
      },
      fireLogout: function fireLogout() {
        var authFailed = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

        if (authFailed) {
          if (window.location.pathname.search('/admin/authentication') < 0) {
            var modal = "\n                        <div class=\"modal fade\" id=\"authFailedModal\" data-backdrop=\"static\" data-keyboard=\"false\" tabindex=\"-1\" aria-labelledby=\"staticBackdropLabel\" aria-hidden=\"true\">\n                            <div class=\"modal-dialog modal-dialog-centered\">\n                                <div class=\"modal-content\">\n                                    <div class=\"modal-header\">\n                                        <h5 class=\"modal-title\" id=\"authFailedModalLabel\">\u8CC7\u8A0A</h5>\n                                    </div>\n                                    <div class=\"modal-body\">\n                                        <p class=\"text-center\">\n                                            <span class=\"h5\"><strong>\u767B\u5165\u671F\u9593\u5DF2\u904E\u671F\uFF0C\u8ACB\u91CD\u65B0\u767B\u5165</strong></span><br>\n                                            <span id=\"redirector\" class=\"text-secondary\"></span>\n                                        </p>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                        ";
            document.body.innerHTML = modal;
          }
        } else {
          if (window.location.pathname.search('/admin/authentication') < 0) {
            var _modal = "\n                        <div class=\"modal fade\" id=\"authFailedModal\" data-backdrop=\"static\" data-keyboard=\"false\" tabindex=\"-1\" aria-labelledby=\"staticBackdropLabel\" aria-hidden=\"true\">\n                            <div class=\"modal-dialog modal-dialog-centered\">\n                                <div class=\"modal-content\">\n                                    <div class=\"modal-header\">\n                                        <h5 class=\"modal-title\" id=\"authFailedModalLabel\">\u8CC7\u8A0A</h5>\n                                    </div>\n                                    <div class=\"modal-body\">\n                                        <p class=\"text-center\">\n                                            <span class=\"h5\"><strong>\u767B\u51FA\u4E2D</strong></span><br>\n                                            <span id=\"redirector\" class=\"text-secondary\">\u7CFB\u7D71\u6B63\u5728\u5C07\u60A8\u91CD\u65B0\u5C0E\u5411\u81F3\u767B\u5165\u9801\u9762...</span>\n                                        </p>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                        ";
            document.body.innerHTML = _modal;
          }
        }

        $('#authFailedModal').on('shown.bs.modal', function () {
          axios.get('/admin/logout').then(function (res) {
            Cookies.remove('token');

            if (authFailed && window.location.pathname.search('/admin/authentication') < 0) {
              setTimeout(function () {
                document.getElementById('redirector').innerHTML = '系統正在將您重新導向至登入頁面...';
                window.location.href = '/admin/authentication';
              }, 2000);
            } else {
              window.location.href = '/admin/authentication';
            }
          })["catch"](function (errors) {
            alert(errors);
          });
        });
        $('#authFailedModal').modal('show');
      }
    },
    created: function created() {
      var _this2 = this;

      var token = Cookies.get('token');

      if (token != null) {
        window.axios.defaults.headers.common['Authorization'] = "Bearer ".concat(token);
      }

      axios.get('/api/v1/user').then(function (res) {
        _this2.user = res.data.data;
        _this2.eUser = _.cloneDeep(_this2.user);
        _this2.user.nickname = _this2.user.nickname == null ? _this2.user.username : _this2.user.nickname;
      })["catch"](function (errors) {
        if (errors.response.status === 401) {
          _this2.fireLogout(true);
        }

        _this2.user = [];
      })["finally"](function () {
        _this2.loading = false;
        Vue.nextTick(function () {
          $('#user-popover').popover({
            placement: 'bottom',
            title: '使用者選單',
            content: "<ul class=\"list-group\">\n                                  <button\n                                    class=\"list-group-item list-group-item-action text-center h5 text-primary\"\n                                    data-toggle=\"modal\"\n                                    data-target=\"#editUserData\"\n                                  >\n                                    \u4FEE\u6539\u8CC7\u6599\n                                  </button>\n                                </ul>",
            html: true,
            sanitize: false,
            trigger: 'focus'
          });
        });
      });
    },
    computed: {
      route: function route() {
        return window.location.pathname;
      },
      routeClass: function routeClass() {
        var _this3 = this;

        return function (r) {
          if (_this3.route == r.route) {
            return r.disabled === true ? 'nav-item disabled' : 'nav-item active';
          } else {
            return r.disabled === true ? 'nav-item disabled' : 'nav-item';
          }
        };
      }
    }
  });
});

/***/ }),

/***/ 1:
/*!***************************************!*\
  !*** multi ./resources/js/backend.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! D:\Development\web\DataAPI\resources\js\backend.js */"./resources/js/backend.js");


/***/ })

/******/ });