<script setup>
import {ref} from "vue";
import {useRouter} from "vue-router";
import {store} from "../store";
const router = useRouter();

const session = document.getElementsByTagName("meta").session_id.content;
const cartItems = ref([]);
const cartTotal = ref(8);
const email = ref('');
const phoneNo = ref('');
const firstName = ref('');
const lastName = ref('');
const cardHolder = ref('');
const cardNumber = ref('');
const cardExpMonth = ref('');
const cardExpYear = ref('');
const cardCCV = ref('');
const errorMessage = ref('');
const errorStatus = ref('');
const purchase = async () => {

    await axios.post('api/orders/purchase/', {
        email: email.value,
        phone_number: phoneNo.value,
        first_name: firstName.value,
        last_name: lastName.value,
        card_holder: cardHolder.value,
        card_number: cardNumber.value,
        card_exp_month: cardExpMonth.value,
        card_exp_year: cardExpYear.value,
        card_security_code: cardCCV.value,
        total_amount: cartTotal.value,
        session_id: session,
        customer_id:localStorage.getItem('customer_id'),


    })
        .then(response => {
            router.push("/thank-you");
        })
        .catch(error => {
            console.error("Error completing purchase:", error);
            if (error.response) {
                errorMessage.value = error.response.data.error.message;
                errorStatus.value = error.response.status;
            } else {
                errorMessage.value = 'An unknown error occurred.';
                errorStatus.value = 500; // Default status for unknown errors
            }
        })

}

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
                cartItem.image_url= productDetails.image.original_url;
            }
        })
    })
    .catch(function(error){

    })
    .finally(function(){
    });



</script>

<template>


    <div class="grid sm:px-10 lg:grid-cols-2 lg:px-20 xl:px-32 mt-8">
        <div class="px-4 pt-8">
            <div v-if="errorMessage" class="text-red-600 mb-4 text-xl font-semibold">
                Error ({{ errorStatus }}): {{ errorMessage }}
            </div>
            <p class="text-xl font-medium">Order Summary</p>
            <p class="text-gray-400">Check your items. And select a suitable shipping method.</p>
            <div class="mt-8 space-y-3 rounded-lg border bg-white px-2 py-4 sm:px-6">
                <div
                    v-for="cartItem in cartItems"
                    :key="cartItem.id"
                    class="flex flex-col rounded-lg bg-white sm:flex-row">

                    <img class="m-2 h-20 w-20 rounded-md border object-cover object-center" :src="cartItem.image_url" alt="" />

                    <!--                    <img v-if="cartItem.product_id.image !== null" :src="cartItem.product_id.image.original_url"-->

<!--                        class="m-2 h-24 w-28 rounded-md border object-cover object-center" src="" alt="" />-->
                    <div class="flex w-full flex-col px-4 py-4">
                        <span class="font-semibold text-xl">{{cartItem.product_name}} </span>
                        <span class="float-right text-gray-400">Qty {{cartItem.quantity}}</span>
                        <p class="text-lg font-bold">${{cartItem.total}}</p>
                        <hr>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-lg font-medium">Shipping Methods</p>
            <form class="mt-5 grid gap-6">
                <div class="relative">
                    <input class="peer hidden" id="radio_1" type="radio" name="radio" checked />
                    <span class="peer-checked:border-gray-700 absolute right-4 top-1/2 box-content block h-3 w-3 -translate-y-1/2 rounded-full border-8 border-gray-300 bg-white"></span>
                    <label class="peer-checked:border-2 peer-checked:border-gray-700 peer-checked:bg-gray-50 flex cursor-pointer select-none rounded-lg border border-gray-300 p-4" for="radio_1">
                        <img class="w-14 object-contain" src="/images/naorrAeygcJzX0SyNI4Y0.png" alt="" />
                        <div class="ml-5">
                            <span class="mt-2 font-semibold">Fedex Delivery</span>
                            <p class="text-slate-500 text-sm leading-6">Delivery: 2-4 Days</p>
                        </div>
                    </label>
                </div>
                <div class="relative">
                    <input class="peer hidden" id="radio_2" type="radio" name="radio" checked />
                    <span class="peer-checked:border-gray-700 absolute right-4 top-1/2 box-content block h-3 w-3 -translate-y-1/2 rounded-full border-8 border-gray-300 bg-white"></span>
                    <label class="peer-checked:border-2 peer-checked:border-gray-700 peer-checked:bg-gray-50 flex cursor-pointer select-none rounded-lg border border-gray-300 p-4" for="radio_2">
                        <img class="w-14 object-contain" src="/images/oG8xsl3xsOkwkMsrLGKM4.png" alt="" />
                        <div class="ml-5">
                            <span class="mt-2 font-semibold">Akis Express</span>
                            <p class="text-slate-500 text-sm leading-6">Delivery: 1-2 Days</p>
                        </div>
                    </label>
                </div>
            </form>
        </div>
        <div class="mt-10 bg-gray-50 px-4 pt-8 lg:mt-0">
            <p class="text-xl font-medium">Payment Details</p>
            <p class="text-gray-400">Complete your order by providing your payment details.</p>
            <div class="">

                <label for="first_name" class="mt-4 mb-2 block text-sm font-medium">First Name</label>
                <div class="relative">
                    <input
                        v-model="firstName"
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Your first name"
                    />
                    <div class="pointer-events-none absolute inset-y-0 left-0 inline-flex items-center px-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <!-- ... Your icon for first name ... -->
                        </svg>
                    </div>
                </div>

                <label for="last-name" class="mt-4 mb-2 block text-sm font-medium">Last Name</label>
                <div class="relative">
                    <input
                        v-model="lastName"
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Your last name"
                    />
                    <div class="pointer-events-none absolute inset-y-0 left-0 inline-flex items-center px-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <!-- ... Your icon for last name ... -->
                        </svg>
                    </div>
                </div>



                <label for="email" class="mt-4 mb-2 block text-sm font-medium">Email</label>
                <div class="relative">
                    <input
                        v-model="email"
                        type="text" id="email" name="email" class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="your.email@gmail.com" />
                    <div class="pointer-events-none absolute inset-y-0 left-0 inline-flex items-center px-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                </div>

                <label for="phone_number" class="mt-4 mb-2 block text-sm font-medium">Phone Number</label>
                <div class="relative">
                    <input
                        v-model="phoneNo"
                        type="text"
                        id="phone_number"
                        name="phone_number"
                        class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Your phone number"
                    />
                    <div class="pointer-events-none absolute inset-y-0 left-0 inline-flex items-center px-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                            />
                        </svg>
                    </div>
                </div>


                <label for="card-holder" class="mt-4 mb-2 block text-sm font-medium">Card Holder</label>
                <div class="relative">
                    <input
                        v-model="cardHolder"
                        type="text" id="card-holder" name="card-holder" class="w-full rounded-md border border-gray-200 px-4 py-3 pl-11 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="Your full name here" />
                    <div class="pointer-events-none absolute inset-y-0 left-0 inline-flex items-center px-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                        </svg>
                    </div>
                </div>
                <label for="card-no" class="mt-4 mb-2 block text-sm font-medium">Card Details</label>
                <div class="flex">
                    <div class="relative w-7/12 flex-shrink-0">
                        <input
                            v-model="cardNumber"
                            type="text" id="card-no" name="card-no" class="w-full rounded-md border border-gray-200 px-2 py-3 pl-11 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="xxxx-xxxx-xxxx-xxxx" />
                        <div class="pointer-events-none absolute inset-y-0 left-0 inline-flex items-center px-3">
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11 5.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1z" />
                                <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm13 2v5H1V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm-1 9H2a1 1 0 0 1-1-1v-1h14v1a1 1 0 0 1-1 1z" />
                            </svg>
                        </div>
                    </div>


                    <div class="flex">
                        <div class="relative w-1/2">
                            <input
                                v-model="cardExpMonth"
                                type="text"
                                name="credit-expiry-month"
                                class="w-full rounded-md border border-gray-200 px-2 py-3 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="MM"
                            />
                        </div>
                        <div class="relative w-1/2">
                            <input
                                v-model="cardExpYear"
                                type="text"
                                name="credit-expiry-year"
                                class="w-full rounded-md border border-gray-200 px-2 py-3 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="YY"
                            />
                        </div>
                    </div>


                    <!--                    <input type="text" name="credit-expiry" class="w-full rounded-md border border-gray-200 px-2 py-3 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="MM/YY" />-->
                    <input
                        v-model="cardCCV"
                        type="text" name="credit-cvc" class="w-1/6 flex-shrink-0 rounded-md border border-gray-200 px-2 py-3 text-sm shadow-sm outline-none focus:z-10 focus:border-blue-500 focus:ring-blue-500" placeholder="CVC" />
                </div>
                <!-- Total -->
                <div class="mt-6 border-t border-b py-2">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">Subtotal</p>
                        <p class="font-semibold text-gray-900">${{cartTotal.toFixed(2)}}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">Shipping</p>
                        <p class="font-semibold text-gray-900">$8.00</p>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">Total</p>
                    <p class="text-2xl font-semibold text-gray-900">${{(cartTotal).toFixed(2)}}</p>
                </div>
            </div>
            <button class="mt-4 mb-8 w-full rounded-md bg-gray-900 px-6 py-3 font-medium text-white"
            @click="purchase"
            >Place Order</button>
        </div>
    </div>


</template>

<style scoped>

</style>
