/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//require('./bootstrap');

window.Vue = require('vue').default;
window.axios = require('axios');

//import Vue from 'vue'
import Buefy from 'buefy'
// import 'buefy/dist/buefy.css'

//QR Scanner
import VueQrcodeReader from "vue-qrcode-reader";
//for QR CODE Generation
import VueQrcode from '@chenfengyuan/vue-qrcode';


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);

//Vue.component('navbar-component', require('./components/ControlPanel/AdminNavbarComponent.vue').default);
//Vue.component('home-page', require('./components/HomePage.vue').default);
//Vue.component('register-components', require('./components/SignupComponent.vue').default);


//USER
//Vue.component('user-page', require('./components/ControlPanel/User/UserPage.vue').default);




import VueQuillEditor from 'vue-quill-editor'
import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.snow.css' // for snow theme
import 'quill/dist/quill.bubble.css' // for bubble theme

Vue.use(VueQuillEditor, /* { default global options } */)




/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import ElementUI from 'element-ui';


Vue.use(Buefy)
Vue.use(ElementUI)

Vue.use(VueQrcodeReader); //https://gruhn.github.io/vue-qrcode-reader/demos/CustomTracking.html
Vue.component(VueQrcode.name, VueQrcode);




Vue.filter('formatTime', function(value) {
    var timeString = value;
    var H = +timeString.substr(0, 2);
    var h = (H % 12) || 12;
    var ampm = H < 12 ? " AM" : " PM";
    timeString = h + timeString.substr(2, 3) + ampm;
    return timeString;
});





//credit to @Bill Criswell for this filter
Vue.filter('truncate', function (text, stop, clamp) {
    return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '')
});
//let text = Vue.filter('truncate')(sometextToTruncate, 18);



//create function
Vue.prototype.$formatDateAndTime = function(value) {
    if (!value) return '';

    const date = new Date(value);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    const h = String(date.getHours()).padStart(2, '0'); // => 9
    const min = String(date.getMinutes()).padStart(2, '0'); // => 9
    // =>  30

    return `${year}-${month}-${day} ${h}:${min}:00`;
};

Vue.prototype.$formatTime = function(value) {
    var timeString = value;
    var H = +timeString.substr(0, 2);
    var h = (H % 12) || 12;
    var ampm = H < 12 ? " AM" : " PM";
    timeString = h + timeString.substr(2, 3) + ampm;
    return timeString;
};




Vue.prototype.$formatDate = function(value) {
    if (!value) return '';

    const date = new Date(value);

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};




const app = new Vue({
    el: '#app',
});
