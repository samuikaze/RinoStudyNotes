window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.Cookies = require('js-cookie');

let token = (Cookies.get('token') == null) ? null : Cookies.get('token');
window.axios.defaults.headers.common = {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
};

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

window.RSN = (function () {
    let data = {
        //
    };
    return {
        processDate: function (raw, forDateField = false) {
            let date = new Date(raw);
            let Y = `${date.getFullYear()}`;
            let M = ((date.getMonth() + 1) < 10) ? `0${(date.getMonth() + 1).toString()}` : (date.getMonth() + 1).toString();
            let D = (date.getDate() < 10) ? `0${date.getDate().toString()}` : date.getDate().toString();
            let H = (date.getHours() < 10) ? `0${date.getHours().toString()}` : date.getHours().toString();
            let i = (date.getMinutes() < 10) ? `0${date.getMinutes().toString()}` : date.getMinutes().toString();
            let S = (date.getSeconds() < 10) ? `0${date.getSeconds().toString()}` : date.getSeconds().toString();

            if (forDateField) {
                return `${Y}-${M}-${D}`;
            }

            return `${Y}-${M}-${D} ${H}:${i}:${S}`;
        },
        getErrorMsg: function (error) {
            if (error.response.data.message != null) {
                return error.response.data.message;
            } else {
                return error.response.data.errors;
            }
        },
        set: function (key, val) {
            data[key] = val;
        },
        get: function (key) {
            return data[key];
        }
    }
})();

document.addEventListener('DOMContentLoaded', function (e) {
    new Vue({
        el: '#footer',
        data: {
            versionId: null,
            loading: true
        },
        created: function () {
            axios.get('/frontend/version')
                .then((res) => {
                    this.versionId = res.data;
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
