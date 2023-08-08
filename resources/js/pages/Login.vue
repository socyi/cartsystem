<script setup>
import {onMounted, ref} from "vue";
import {useRouter} from "vue-router";
import {store} from "../store";

const router = useRouter();
const email = ref('');
const password = ref('');


const login = async () => {
    await axios.post('api/customer/login', {
        email: email.value,
        password: password.value
    }).then(response => {
        store.access_token= response.data.access_token;
        store.hasToken= true;
        store.customer_id =response.data.customer_id.id;
        localStorage.setItem('access_token',store.access_token);
        localStorage.setItem('customer_id', store.customer_id);
        localStorage.setItem('hasToken', store.hasToken);
        router.push('/home');

    }).catch(error => {
        console.error('Login error: ', error)
    })
}


</script>

<template>
    <div class="relative flex flex-col justify-center min-h-screen overflow-hidden">
        <div
            class="w-full p-6 m-auto bg-white border-t border-purple-600 rounded shadow-lg shadow-purple-800/50 lg:max-w-md">

            <h1 class="text-3xl font-semibold text-center text-purple-700">Grocery Store</h1>

            <form class="mt-6" @submit.prevent="login">
                <div>
                    <label class="block text-sm text-gray-800 ">Email</label>
                    <input type="email"
                           placeholder="your.email@gmail.com"
                           v-model="email"
                           class="block w-full px-4 py-2 mt-2 text-purple-700 bg-white border rounded-md focus:border-purple-400 focus:ring-purple-300 focus:outline-none focus:ring focus:ring-opacity-40">
                </div>
                <div class="mt-4">
                    <div>
                        <label class="block text-sm text-gray-800">Password</label>
                        <input type="password"
                               v-model="password"
                               placeholder="password"
                               class="block w-full px-4 py-2 mt-2 text-purple-700 bg-white border rounded-md focus:border-purple-400 focus:ring-purple-300 focus:outline-none focus:ring focus:ring-opacity-40">
                    </div>
                    <div class="mt-6">
                        <button
                            type="submit"
                            class="w-full px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-purple-700 rounded-md hover:bg-purple-600 focus:outline-none focus:bg-purple-600">
                            Login
                        </button>
                    </div>
                </div>
            </form>
            <p class="mt-8 text-xs font-light text-center text-gray-700"> Don't have an account? <a href="/home"
                                                                                                    class="font-medium text-purple-600 hover:underline">Continue
                as a guest</a></p>
        </div>
    </div>
</template>

<style scoped>

</style>
