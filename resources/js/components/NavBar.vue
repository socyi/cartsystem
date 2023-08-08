<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {useRouter} from "vue-router";
import {store} from "../store";
import {onMounted} from "vue";
console.log('Customer ', localStorage.customer_id);

const router = useRouter();
const navigation = [
    {name: 'Cart', href: '/Cart', current: false},
]
const logout = () => {
     localStorage.removeItem('access_token');
     localStorage.removeItem('hasToken');
     localStorage.removeItem('customer_id');

     store.access_token = 'guest';
     store.hasToken = false;
     store.customer_id = -1;
    router.push('/');
}

onMounted( () => {
    store.hasToken = !!localStorage.getItem('hasToken');
})




</script>


<template>
    <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                    <!-- Mobile menu button-->
                    <DisclosureButton
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Open main menu</span>
                    </DisclosureButton>
                </div>
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">

                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-20">

                            <a class="font-serif text-3xl text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2" href="/home">Grocery Store</a>

                            <a v-for="item in navigation" :key="item.name" :href="item.href"

                               :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'rounded-md px-3 py-2 text-3xl font-medium']"
                               :aria-current="item.current ? 'page' : undefined">{{ item.name }}</a>

<!--                            //-->
                            <!-- Display the cart item count if it's greater than 0 -->
                        </div>
                    </div>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                    <button type="button"
                            class="rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                        <span class="sr-only">View notifications</span>
                    </button>

                    <!-- Profile dropdown -->
                    <Menu v-if = "store.hasToken"
                        as="div" class="relative ml-3">
                        <div>
                            <MenuButton
                                class="flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                <span class="sr-only">Open user menu</span>

                                <div
                                    class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                    <svg class="absolute w-12 h-12 text-gray-400 -left-1" fill="currentColor"
                                         viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </div>

                            </MenuButton>
                        </div>
                        <transition enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">

                            <MenuItems
                                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <MenuItem v-slot="{ active }">
                                    <a href="#"
                                       :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">Your
                                        Profile</a>
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <a href="#"
                                       :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']"
                                        @click = "logout">Sign
                                        out</a>
                                </MenuItem>
                            </MenuItems>



                        </transition>
                    </Menu>



                    <a v-else
                       href="/"
                       class="font-serif text-3xl text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2">
                    Sign in
                    </a>
                </div>
            </div>
        </div>

        <DisclosurePanel class="sm:hidden">
            <div class="space-y-1 px-2 pb-3 pt-2">
                <DisclosureButton
                    v-for="item in navigation"
                    :key="item.name" as="a"
                    :href="item.href"
                    :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-green-500 ', 'block rounded-md px-3 py-2 text-base font-medium']"
                    :aria-current="item.current ? 'page' : undefined">{{ item.name }}
                </DisclosureButton>
            </div>
        </DisclosurePanel>
    </Disclosure>
</template>

