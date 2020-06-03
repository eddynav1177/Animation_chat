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

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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

/*import Echo from "laravel-echo";

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY
});*/


import Echo from "laravel-echo";
import Pusher from 'pusher-js';
window.Pusher = require('pusher-js');

/*var pusher = new Pusher('5f9a91e9b32d04b2f253', {
authEndpoint: 'http://animation.test/api/message/check_message/5',
cluster: 'eu',
encrypted: true,
auth: {
    headers: {
        Authorization: 'Test autorisation'
    }
}
});

var channel = pusher.subscribe('chat');

channel.bind('message-1 sended', function (data) {
    alert(JSON.stringify(data));
});*/

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '5f9a91e9b32d04b2f253',
    cluster: 'eu',
    encrypted: true,
    forceTLS: true
});
