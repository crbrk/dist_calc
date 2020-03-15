import Home from './Home.js';

const routes = [
    { path: '/', component: Home},
    { path: '*', component: Home}
];

export const router = new VueRouter({
    mode: 'history',
    routes
});