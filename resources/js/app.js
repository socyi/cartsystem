import './bootstrap';

import {createApp, ref} from 'vue'
import {createRouter, createWebHistory} from 'vue-router'
import Home from "./pages/Home.vue";
import App from "./App.vue";
import Login from "./pages/Login.vue";
import Cart from "./pages/Cart.vue";
import Checkout from "./pages/Checkout.vue";
import AdminLogin from "./pages/AdminLogin.vue";
import AdminProducts from "./pages/AdminProducts.vue";
import AdminOrders from "./pages/AdminOrders.vue";
import AdminCustomers from "./pages/AdminCustomers.vue";
import AdminUsers from "./pages/AdminUsers.vue";
import ThankYou from "./pages/ThankYou.vue";




// 2. Define some routes
// Each route should map to a component.
// We'll talk about nested routes later.
const routes = [
    {path: '/', component: Login, name: 'Login'},
    {path: '/home', component: Home, name: 'Home'},
    {path: '/cart', component: Cart, name: 'Cart'},
    {path: '/checkout', component: Checkout, name: 'Checkout'},
    {path: '/admin-login', component: AdminLogin, name: 'AdminLogin'},
    {path: '/admin-products', component: AdminProducts, name: 'AdminProducts'},
    {path: '/admin-users', component: AdminUsers, name: 'AdminUsers'},
    {path: '/admin-customers', component: AdminCustomers, name: 'AdminCustomers'},
    {path: '/admin-orders', component: AdminOrders, name: 'AdminOrders'},
    {path: '/thank-you', component: ThankYou, name: 'ThankYou'},




]


const router = createRouter({

    history: createWebHistory(),
    routes,

    // `
})


const app = createApp(App);

app.use(router)



app.mount('#app')


