import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from '../js/views/Home';
import Grafik from '../js/views/Grafik';
import History from '../js/views/History';
import Information from '../js/views/Information';
import Input from '../js/views/Input'

Vue.use(VueRouter);

const router = new VueRouter({
    mode:'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/grafik',
            name: 'grafik',
            component: Grafik
        },
        {
            path: '/history',
            name: 'history',
            component: History
        },
        {
            path: '/information',
            name: 'information',
            component: Information
        },
        {
            path: '/input',
            name: 'input',
            component: Input
        }
    ]
});

export default router;