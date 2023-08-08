<script setup>
import {ref} from "vue";
import {store} from "../store";

const items = ref([]);
const itemAddedToCart = ref(false);
const total = ref();

const guestItems = [];
//
let i=0;

function increment(item){
    item.quantity++;
}

function decrement(item){
    if (item.quantity > 1)
        item.quantity--;
}


const addCart = async (item) => {


    total.value += item.price * item.quantity;

    await axios.post('api/cart-items/', {
        product_id: item.id,
        quantity: item.quantity,
        customer_id: store.customer_id,
    })
        .then(response => {
        itemAddedToCart.value = true;
        setTimeout(() => {
            itemAddedToCart.value = false;
        }, 2500);
    })
        .catch(error => {
        console.error("Error adding item to cart:", error);

    })
}

axios.get('api/products/',{headers :{ Authorization: `Bearer ${localStorage.getItem('access_token')}` }})
    .then(function (response) {

        items.value = response.data.data;

        for (i in items.value) {
            items.value[i].quantity = 1;

            const guestItem = {
                id: items.value[i].id,
                name: items.value[i].name,
                price: items.value[i].price,
                quantity: 0
            }
            guestItems.push(guestItem);
        }
    })
    .catch(function (error) {
        // handle error
        console.log(error);
    })

    .finally(function () {
        // always executed
    });
console.log(items.value);



</script>

<template>

    <div class="container mx-auto">
        <h1 class="text-6xl text-teal-500 pt-8 mb-8">Products</h1>
        <p v-if="itemAddedToCart" class="text-black-500 font-bold text-lg">Item added to cart</p>

        <div class="grid gap-5 grid-cols-3 grid-rows-12">
            <div v-for="(item, index) in items" :key="item.id" class=" w-full shadow-lg border rounded-lg dark:bg-gray-800 dark:border-gray-700 mx-auto bg-white">
                <div class="card-image-wrapper">
                    <img v-if="item.image !== null" :src="item.image.original_url" class="card-image rounded-t-lg" alt="product image"/>
                </div>
                <div class="px-10 pb-5">
                    <h5 class="text-4xl tracking-tight text-gray-900 dark:text-white py-4">{{ item.name}}</h5>
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-bold text-gray-900 dark:text-white pt-4">${{ item.price.toFixed(2) }}</span>
                        <button class="text-3xl font-bold text-gray-900 dark:text-white pt-4 "
                                @click="decrement(item)">
                            -
                        </button>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white pt-4 ml-[-30px] mr-[-30px]">{{ item.quantity  }}</span>
                        <button class="text-3xl font-bold text-gray-900 dark:text-white pt-4 "
                        @click="increment(item)">
                            +
                        </button>
                        <a href="#" class="text-white bg-teal-500 hover:bg-teal-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click="addCart(item)">
                            Add to cart
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- Rest of your content -->
        <div class="flex justify-center items-center mt-16">
            <a class="bg-teal-500 hover:bg-teal-800 py-6 px-40 font-bold rounded text-white text-base " href="cart">Go to Cart</a>
        </div>

    </div>



</template>

<style>

.container {
    height: 100vh; /* Adjust the height as needed */
}

.card-image-wrapper {
    height: 200px; /* Adjust the height as needed */
    display: flex;
    justify-content: center;
    align-items: center;
}

.card-image {
    height: 100%;
    width: auto;
}

</style>
