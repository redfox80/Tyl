<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DefaultContent from "@/Components/DefaultContent.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import NumberInput from "@/Components/NumberInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {router, useForm} from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    view: {
        type: Object
    }
});

const searchResults = ref([]);
const showAddBatchModal = ref(false);
const addBatchForm = ref(null);
const batchRefs = ref(null);

const batchForm = useForm({
    batchNumber: NaN,
    productType: "",
    quantity: NaN,
    palletSize: NaN,
    description: "",
    viewId: props.view.id
})

function addBatch() {
    batchForm.post(route('paller.batch.new'), {onSuccess: () => batchForm.reset(), onError: () => console.log(batchForm.errors)});
}

function deleteView() {
    router.delete(route('paller.view', props.view.id));
}

function editBatch(batchKey) {
    // console.log(props.view.batches[batchKey].product_type)
    let batch = props.view.batches[batchKey];
    batchForm.batchNumber = batch.number;
    batchForm.productType = batch.product_type;
    batchForm.quantity = batch.quantity;
    batchForm.palletSize = batch.pallet_size;
    batchForm.description = batch.description;

    showAddBatchModal.value = true;
}

function searchExistingBatch() {
    if (batchForm.batchNumber > 0) {
        let res = axios(route('paller.batch.search'), {
            method: 'post',
            data: {
                search: batchForm.batchNumber
            }
        }).then(res => {
            console.log(res.data.search_results)
            if(res.data.search_results.length == 1 && res.data.search_results[0].number == batchForm.batchNumber) {
                batchForm.productType = res.data.search_results[0].product_type;
                batchForm.quantity = res.data.search_results[0].quantity;
                batchForm.palletSize = res.data.search_results[0].pallet_size;
                batchForm.description = res.data.search_results[0].description;
                searchResults.value = [];
            } else {
                searchResults.value = res.data.search_results;
            }
        });
    }
}

function updateBatchNumber(numb) {
    batchForm.batchNumber = numb;
    searchExistingBatch();
}

function closeAndResetAddBatchModal() {
    showAddBatchModal.value = false;
    batchForm.reset();
}
</script>

<template>
    <AuthenticatedLayout>
        <DefaultContent>
            <div class="flex space-x-2">
                <NavLink :href="route('paller.dashboard')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                    </svg>
                </NavLink>
                <p class="text-xl font-bold mt-1">{{ view.name }}</p>
            </div>
            <br/>
            <div class="[&>*:not(:first-child)]:mx-1 [&>*:first-child]:mr-1">
                <secondary-button @click="showAddBatchModal=true">New batch</secondary-button>
                <danger-button @click="deleteView()">Delete</danger-button>
            </div>

            <br/><br/>

            <table class="striped-table">
                <thead>
                    <tr>
                        <th>Batch number</th>
                        <th>Product type</th>
                        <th>Quantity</th>
                        <th>Pallets</th>
                        <th>pcs leftover</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(batch, key) in view.batches" ref="batchRefs">
                        <td class="text-center">{{ batch.number }}</td>
                        <td>{{ batch.product.name }} {{ batch.product.size}}</td>
                        <td class="text-center">{{ Number(batch.quantity).toLocaleString() }}
                            pcs.
                        </td>
                        <td class="text-right">
                            {{ Math.floor(batch.quantity / batch.pallet_size) }} pl.
                        </td>
                        <td class="text-center">+ {{ batch.quantity % batch.pallet_size }} pcs.</td>
                        <td>{{ batch.description }}</td>
                        <td class="p-1">
                            <div class="inline-block cursor-pointer" @click="editBatch(key)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                            </div>
                            <div class="inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </DefaultContent>
    </AuthenticatedLayout>

    <Modal :show="showAddBatchModal" @close="closeAndResetAddBatchModal()">
        <div class="w-full p-5" @click.stop>
            <p class="text-xl">Add batch to view</p>

            <br/>

            <form ref="addBatchForm" @submit.prevent>
                <div class="flex space-x-5 items-center">
                    <div>
                        <InputLabel for="batchNumberInput">Batch number</InputLabel>
                        <NumberInput v-model="batchForm.batchNumber" id="batchNumberInput" autofocus @input="searchExistingBatch()" :class="batchForm.errors.batchNumber ? 'border-rose-600':''"/>
                    </div>
                    <div v-if="searchResults.length > 0">
                        <b>Existing batches</b><br/>
                        <div class="absolute">
                            <div v-for="res in searchResults">
                                <a href="#" class="underline text-cyan-600" @click="updateBatchNumber(res.number)">{{ res.number }}</a><br/>
                            </div>
                        </div>
                    </div>
                </div>
                <InputError :message="batchForm.errors.batchNumber"/>

                <br/><br/>

                <InputLabel for="batchProductTypeInput">Product type</InputLabel>
                <TextInput v-model="batchForm.productType" id="batchDescriptionInput" :class="batchForm.errors.productType ? 'border-rose-600':''"/>
                <InputError :message="batchForm.errors.productType"/>

                <br/><br/>

                <InputLabel for="batchQuantityInput">Quantity (pcs)</InputLabel>
                <NumberInput v-model="batchForm.quantity" id="batchQuantityInput" :class="batchForm.errors.quantity ? 'border-rose-600':''"/>
                <InputError :message="batchForm.errors.quantity"/>

                <br/><br/>

                <InputLabel for="batchPalletSizeInput">Pallet size (pcs / pl)</InputLabel>
                <NumberInput v-model="batchForm.palletSize" id="batchPalletSizeInput" :class="batchForm.errors.palletSize ? 'border-rose-600':''"/>
                <InputError :message="batchForm.errors.palletSize"/>

                <br/><br/>

                <InputLabel for="batchDescriptionInput">Additional info</InputLabel>
                <TextInput class="w-full" v-model="batchForm.description" id="batchDescriptionInput" :class="batchForm.errors.description ? 'border-rose-600':''"/>
                <InputError :message="batchForm.errors.description"/>

                <br/><br/>

                <div class="space-x-2">
                    <primary-button @click="addBatch()">Save</primary-button>
                    <secondary-button @click="closeAndResetAddBatchModal()">Cancel</secondary-button>
                </div>
            </form>
        </div>
    </Modal>
</template>
