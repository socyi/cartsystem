<script setup>
import {ref} from "vue";
import {useRouter} from "vue-router";

const session = document.getElementsByTagName("meta").session_id.content;
const cartItems = ref([]);
const cartTotal = ref(0);
const router = useRouter();


const getProductDetails = async (productId) => {
    try {
        const response = await axios.get(`api/products/${productId}`);
        return response.data.data;

    }
    catch (error){
        console.error("Error fetching product details:", error);
        return null;
    }
}

axios.get(`api/cart-items/${session}`)
     .then (function (response) {
         cartItems.value = response.data.data;
         cartItems.value.forEach(async (cartItem) => {

             const productDetails = await getProductDetails(cartItem.product_id);
             if (productDetails){
                 cartItem.product_name = productDetails.name;
                 cartItem.product_price = productDetails.price.toFixed(2);
                 cartItem.total = (cartItem.product_price * cartItem.quantity);
                 cartTotal.value+= cartItem.total;
                 cartItem.total = cartItem.total.toFixed(2);
             }
         })
     })
    .catch(function(error){

    })
    .finally(function(){
    });


const incrementQuantity = async (cartItem) => {
    cartItem.quantity=cartItem.quantity+1;
    await axios.post('api/cart-items/', {
        product_id: cartItem.product_id,
        quantity: cartItem.quantity,
        fromCart: true

    }).then(response => {
        cartItem.total=(parseFloat(cartItem.product_price) + parseFloat(cartItem.total)).toFixed(2);
        cartTotal.value = cartTotal.value + parseFloat(cartItem.product_price);

        storeCartCount.count++;


    }).catch(error => {

    })


}

const decrementQuantity = async (cartItem) => {

    cartItem.quantity=cartItem.quantity-1;

    if (cartItem.quantity == 0){
        console.log("zero");
        const product_id = cartItem.product_id;
        console.log('PROD 1', product_id, 'is' ,typeof product_id);
        console.log('PROD 2', cartItem.product_id, 'is' ,typeof cartItem.product_id);

        await axios.delete(`api/cart-items/${session}/${product_id}`, {
        }).then(response => {
            console.log('gone?',cartItem.product_id);
            cartTotal.value = cartTotal.value - parseFloat(cartItem.product_price);
            cartItems.value = cartItems.value.filter((item) => item.product_id !== cartItem.product_id);

            storeCartCount.count--;

        }).catch(error => {

        })
    }
    else {
        console.log("not zero");
        await axios.post('api/cart-items/', {
            product_id: cartItem.product_id,
            quantity: cartItem.quantity,
            fromCart: true

        }).then(response => {
            cartItem.total = (parseFloat(cartItem.total) - parseFloat(cartItem.product_price)).toFixed(2);
            cartTotal.value = cartTotal.value - parseFloat(cartItem.product_price);

            storeCartCount.count--;

        }).catch(error => {

        })

    }

}


const Checkout = () =>{
    router.push('/checkout');
}

</script>

<template>
    <div class="container mx-auto">
        <h1 class="text-6xl text-teal-500 pt-8 mb-8 ">Your Cart</h1>
        <div class="flex mt-4 pt-8 pb-8">
            <!-- component -->
            <div class="mb-8 rounded-lg shadow-lg">
                <table>
                    <thead>
                    <tr class="text-md font-semibold tracking-wide text-left text-gray-900 bg-gray-100 uppercase border-b border-gray-600">
                        <th class="px-4 py-3">Product Name</th>
                        <th class="px-4 py-3 ">Price</th>
                        <th class="px-4 py-3">Quantity</th>
                        <th class="px-4 py-3">Total Price</th>

                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    <tr v-for="cartItem in cartItems"
                        :key="cartItem.product_id"
                        class="text-gray-700">
                        <td class="px-4 py-3 border">
                            <p class="font-semibold text-black">{{ cartItem.product_name }}</p>
                        </td>
                        <td class="px-4 py-3 font-semibold border">${{cartItem.product_price}}</td>
                        <td class="px-4 py-3 font-semibold border">
                            <button class="text-2xl font-bold text-teal-600 dark:text-white pr-5 "
                                    @click="decrementQuantity(cartItem)">
                                -
                            </button>
                            {{cartItem.quantity}}
                        <button class="text-2xl font-bold text-teal-600 dark:text-white pt-2 pl-5"
                                @click="incrementQuantity(cartItem)">
                            +
                        </button>
                        </td>
                        <td class="px-4 py-3 font-semibold border">${{cartItem.total}}</td>

                    </tr>

                    </tbody>
                </table>
            </div>

        </div>


        <div class="lg:w-6/12 xl:w-4/12 md:w-8/12 w-6">
            <div class="border-4 border-teal-400">
                <p class="text-gray-600 mb-6 text-2xl px-6 pt-8 flex justify-between">
                    Cart Amount: <span class="pr-20">${{cartTotal.toFixed(2)}}</span>
                </p>

                <div class="bg-gray-300 flex items-center justify-end px-2 py-3 space-x-2">
                    <a class="bg-teal-500 hover:bg-teal-800 py-2 px-4 font-bold rounded text-white text-sm w-full" href="home">Return to Store</a>
                    <button class="bg-white hover:bg-teal-200 text-teal-600 py-2 px-4 rounded text-white-600 font-bold text-sm w-full"
                            @click="Checkout"

                    >Checkout</button>
                </div>
            </div>
        </div>



    </div>

</template>

