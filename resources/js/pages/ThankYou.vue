
<script setup>
import { useRoute } from "vue-router";
</script>


<template>
    <div class="thank-you-container">
        <h2>Thank You!</h2>
        <p>Your purchase was successful.</p>
        <router-link to="/home" class="back-link">Go back to Home</router-link>
    </div>
</template>

<style scoped>
.thank-you-container {
    text-align: center;
    margin-top: 100px;
}

.back-link {
    display: inline-block;
    margin-top: 20px;
    color: #007bff;
    text-decoration: none;
}
</style>
