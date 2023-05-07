<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DefaultContent from "@/Components/DefaultContent.vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {router} from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";

defineProps({
    views: {
        type: Array
    }
});

document.addEventListener('keydown', e => {
    if(e.key === "Escape") showAddViewModal.value = false;
});

const showAddViewModal = ref(false);
const newViewName = ref("");
const newViewForm = ref(null);
const newViewNameInput = ref(null);

function postNewView() {
    router.post(route('paller.createView'), {
        name: newViewName.value
    });
}

function reloadViewsList() {
    router.reload({ only: ['views']});
}
</script>

<template>

    <AuthenticatedLayout>
        <DefaultContent>

            <p class="text-xl font-bold">Visninger</p>

            <br/>

            <div v-if="views.length > 0">
                <SecondaryButton @click="showAddViewModal=true">New view</SecondaryButton>
                <br/>
                <br/>
                <ul>
                    <li v-for="view in views">
                        <NavLink :href="route('paller.view', view.id)">{{ view.name }}</NavLink>
                    </li>
                </ul>
            </div>

            <p v-else class="text-gray-600 text-sm">
                Du har ingen visninger, vil du <a href="#" @click="showAddViewModal=true" class="underline cursor-pointer">lage en ny</a>?
            </p>

        </DefaultContent>
    </AuthenticatedLayout>

    <Modal :show="showAddViewModal" @close="showAddViewModal=false">
        <div class="w-full p-5" @click.stop>
            <p class="text-xl">Add view</p>

            <br/>

            <form @submit.prevent ref="newViewForm">

                <InputLabel for="newViewName">Name</InputLabel>
                <TextInput id="newViewName" v-model="newViewName" autofocus="autofocus"/>

                <div class="mt-4">
                    <PrimaryButton class="mx-1 ml-0" @click="postNewView()">Add</PrimaryButton>
                    <SecondaryButton class="mx-1" @click="showAddViewModal=false;">Cancel</SecondaryButton>
                </div>
            </form>
        </div>
    </Modal>

</template>
