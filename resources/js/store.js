import {reactive} from "vue";

export const store = reactive({
    access_token: 'guest',
    hasToken:false,
    customer_id: -1,
    user_id: -1,
})
