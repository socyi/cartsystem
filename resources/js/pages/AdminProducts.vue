<script setup>
import {ref} from "vue";
import {store} from "../store";

const items = ref([]);
const price = ref(0);
const name = ref('');
const selectedImageFile = ref(null);

const isCreateModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isUpdateModalOpen = ref(false);

const selectedItemId = ref(null);
const itemToUpdate = ref(null); // Store the item being updated


let i=0;

function openCreateModal() {
    isCreateModalOpen.value = true;
}

function closeCreateModal() {
    isCreateModalOpen.value = false;
}

const openDeleteModal = (item) => {
    isDeleteModalOpen.value = true;
    selectedItemId.value = item.id;
}

const  closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    selectedItemId.value = null;

}
// function openUpdateModal(item) {
//     // Populate itemToUpdate with the selected item
//     itemToUpdate.value = { ...item };
//     isUpdateModalOpen.value = true;
// }

// function closeUpdateModal() {
//     isUpdateModalOpen.value = false;
// }
function handleDeleteItem(productId) {

    axios.delete(`/api/products/${productId}`)
        .then(response => {
            console.log('Product deleted:', response.data.message);
            items.value = items.value.filter((item) => item.id !== productId);
            closeDeleteModal();
        })
        .catch(error => {
            console.error('Error deleting product:', error);
        });
}

// function handleImageUpload(event) {
//     // Get the selected image file from the event
//     const file = event.target.files[0];
//     // Store the file in the ref
//     selectedImageFile.value = file;
// }
// async function handleCreateItem() {
//     try {
//         // Create the product with name and price
//         const productData = {
//             name: name.value,
//             price: price.value,
//         };
//
//         // If an image file was selected, upload it using FormData
//         if (selectedImageFile.value) {
//             const formData = new FormData();
//             formData.append("image", selectedImageFile.value);
//             console.log(selectedImageFile.value);
//
//             // Upload the image and get the image URL
//             const imageUploadResponse = await axios.post("/api/products/upload-image", formData, {
//
//             });
//             const imageUrl = imageUploadResponse.data.url;
//
//             // Add the image URL to the product data
//             productData.url = imageUrl;
//         }
//
//         // Send the product data (including the image URL if provided) to create the product
//         const response = await axios.post("/api/products/", productData);
//
//
//
//         console.log("Product created successfully!");
//         window.location.reload();
//     } catch (error) {
//         console.error("Error creating product:", error);
//     }
//
//     closeCreateModal();
// }


async function handleCreateItem() {
    try {
        // Create a FormData object to include the product data and image
        const formData = new FormData();
        formData.append("name", name.value);
        formData.append("price", price.value);
        formData.append("image", selectedImageFile.value); // Assuming selectedImageFile contains the selected file

        // Send the FormData object to create the product
        const response = await axios.post("/api/products/", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        console.log("Product created successfully!");
        window.location.reload();
    } catch (error) {
        console.error("Error creating product:", error);
    }
    closeCreateModal();
}


axios.get('api/products/',{headers :{ Authorization: `Bearer ${localStorage.getItem('access_token')}` }})
    .then(function (response) {

        items.value = response.data.data;

    })
    .catch(function (error) {
        // handle error
        console.log(error);
    })

    .finally(function () {
        // always executed
    });





</script>

<template>
    <!-- Index Post -->
    <div class="container max-w-7xl mx-auto mt-8">
        <div class="mb-4">
            <h1 class="font-serif text-3xl font-bold underline text-teal-600"> Products </h1>
            <div class="flex justify-end">
                <button class="px-4 py-2 rounded-md bg-teal-500 text-sky-100 hover:bg-sky-600 font-semibold"
                        @click="openCreateModal">Create new product
                </button>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div
                    class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                    <table
                        class="min-w-full">
                        <thead>
                        <tr>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                ID
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Product Name
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Price
                            </th>

                            <th class="px-6 py-3 text-sm text-left text-gray-500 border-b border-gray-200 bg-gray-50"
                                colspan="3">
                                Action
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white">
                        <tr v-for="item in items" :key="item.id">
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ item.id }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ item.name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">${{ item.price}}</td>

                            <td class="text-sm font-medium leading-5 text-center whitespace-no-wrap border-b border-gray-200">

                                <a @click="openDeleteModal(item)" class="text-red-600 hover:text-red-800 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isCreateModalOpen" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75">
        <div class="bg-white p-6 max-w-sm mx-auto rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Create Product</h2>
            <form @submit.prevent="handleCreateItem">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700" for="name">Name</label>
                    <input
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        type="text"
                        v-model="name"
                        placeholder="Enter name"
                    />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700" for="price">Price</label>
                    <input
                        v-model="price"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        placeholder="Enter price"
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700" for="image">Image</label>
                    <input
                        type="file"
                        accept="image/*"
                    @change="handleImageUpload"
                    />
                </div>
                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-semibold rounded-md shadow-md text-teal-100 bg-teal-500 hover:bg-sky-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300"
                    >
                        Save
                    </button>
                    <button
                        type="button"
                        @click="closeCreateModal"
                        class="px-4 py-2 text-sm font-semibold text-gray-100 bg-gray-400 rounded-md shadow-md hover:bg-gray-600 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75">
        <div class="z-40 bg-white p-6 max-w-sm mx-auto rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
            <p class="mb-4">Are you sure you want to delete this item?</p>
            <div class="flex justify-end mt-4">
                <button
                    type="button"
                    @click="closeDeleteModal"
                    class="px-4 py-2 text-sm font-semibold text-gray-100 bg-gray-400 rounded-md shadow-md hover:bg-gray-600 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="handleDeleteItem(selectedItemId)"
                    class="ml-2 px-4 py-2 text-sm font-semibold rounded-md shadow-md text-red-100 bg-red-500 hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-gray-300"
                >
                    Delete
                </button>
            </div>
        </div>

    </div>
</template>

<style scoped>

</style>
