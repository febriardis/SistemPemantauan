/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

// require('./bootstrap');

import './bootstrap';
import Vue from 'vue';
import Vuetify from 'vuetify';
import axios from 'axios';
import VueAxios from 'vue-axios';
import VueSweetalert2 from 'vue-sweetalert2';
 
// Route Information
import Routes from '../js/routes';

// component file
import App from '../js/views/App';

Vue.use(Vuetify);
Vue.use(VueAxios, axios);
Vue.use(VueSweetalert2);

axios.defaults.baseURL = 'http://sip.billionairecoach.co.id/api';
axios.defaults.headers = {  
    'Content-Type': 'application/json', 
    'X-Requested-With': 'XMLHttpRequest'
}

const app = new Vue({
    el: '#app',
    router: Routes,
    render: h => h(App),
})

export default app;