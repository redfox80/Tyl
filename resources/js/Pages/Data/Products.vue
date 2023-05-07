<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DefaultContent from "@/Components/DefaultContent.vue";
import {ref} from 'vue';
import Modal from "@/Components/Modal.vue";
import {router, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    products: {
        type: Array
    }
});

const productForm = useForm({
    name: "",
    size: "",
    id: NaN,
});
const showProductModal = ref(false);
const deleteProductButton = ref(null);

function openProductModal(key) {
    productForm.name = props.products[key].name || "";
    productForm.size = props.products[key].size || "";
    productForm.id = props.products[key].id || NaN;
    showProductModal.value = true;
}

function cancelProductModal() {
    productForm.name = "";
    productForm.size = "";
    productForm.id = NaN;
    showProductModal.value = false;
}

function deleteProduct() {
    let el = deleteProductButton.value.$el;
    if (el.getAttribute('data-confirm') !== "true") {
        el.innerText = 'DO YOU REALLY WANT TO DELETE THIS?';
        el.setAttribute('data-confirm', "true");
    } else {
        router.post(route('data.products.delete'), {id: productForm.id});
        cancelProductModal();
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <DefaultContent>
            <p class="text-xl font-bold">
                Products
            </p>

            <br/>

            <SecondaryButton @click="showProductModal=true">Add product</SecondaryButton>

            <br/>
            <br/>

            <table class="striped-table">
                <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Size</th>
                </thead>
                <tbody>
                <tr v-for="(product, key) in products" class="cursor-pointer hover:bg-slate-200 even:hover:bg-slate-200"
                    @click="openProductModal(key)">
                    <td>{{ product.id }}</td>
                    <td>{{ product.name }}</td>
                    <td>{{ product.size }}</td>
                </tr>
                </tbody>
            </table>
        </DefaultContent>
    </AuthenticatedLayout>

    <Modal :show="showProductModal" @close="showProductModal=false">
        <div class="w-full p-5" @click.stop>

            <form @submit.prevent>

                <input type="hidden" name="id" v-model="productForm.id"/>
                <InputLabel for="name">Name</InputLabel>
                <TextInput v-model="productForm.name" name="name" :class="productForm.errors.name ? 'border-rose-600':''" autofocus/>
                <InputError :message="productForm.errors.name"/>

                <br/>
                <br/>

                <InputLabel for="size">Size</InputLabel>
                <TextInput v-model="productForm.size" name="size" :class="productForm.errors.size ? 'border-rose-600':''"/>
                <InputError :message="productForm.errors.size"/>

                <br/>
                <br/>

                <div class="space-x-2">
                    <PrimaryButton @click="productForm.submit('post', route('data.products'), {onSuccess: () => cancelProductModal()})">Save</PrimaryButton>
                    <SecondaryButton @click="cancelProductModal()">Cancel</SecondaryButton>
                    <DangerButton @click="deleteProduct()" v-show="productForm.id > 0" ref="deleteProductButton">Delete</DangerButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
