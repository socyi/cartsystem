<script setup>
import NavBar from "./components/NavBar.vue";
import Footer from "./components/Footer.vue";
import {computed, onBeforeMount, onBeforeUpdate, onMounted, ref} from 'vue';
import {useRoute, useRouter} from "vue-router";
import {store} from "./store";
import AdminNavBar from "./components/AdminNavBar.vue";

const router = useRouter();

const showAdminNavBar = computed(() => {
    let adminRoutes = ['AdminDashboard', 'AdminProducts', 'AdminOrders', 'AdminUsers', 'AdminCustomers'];
    return adminRoutes.includes(router.currentRoute.value.name);
});

const showCustomerNavBar = computed(() => {
    let customerRoutes = ['Login', 'AdminProducts', 'Home', 'Cart', 'Checkout'];
    return customerRoutes.includes(router.currentRoute.value.name);
});

</script>


<template>
    <div id="app">
        <AdminNavBar v-if="showAdminNavBar"/>
        <NavBar v-else-if="showCustomerNavBar"/>
        <router-view/>
        <Footer v-if="this.$route.name !== 'AdminLogin'"/>
    </div>
</template>
