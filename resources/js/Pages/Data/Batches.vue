<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DefaultContent from "@/Components/DefaultContent.vue";

const props = defineProps({
    batches: {
        type: Array
    }
});

</script>

<template>
    <AuthenticatedLayout>
        <DefaultContent>
            <p class="text-xl font-bold">All batches</p>

            <br/>

            <div v-if="batches.length > 0">
                <table class="striped-table w-full table-fixed">
                    <thead>
                    <th class="sm:w-[200px] xs:w-auto">Batch number</th>
                    <th class="text-left">Product type</th>
                    </thead>
                    <tbody>
                    <tr v-for="batch in batches">
                        <td class="text-center"><a href="#" class="underline">{{ batch.number }}</a></td>
                        <td>{{ batch.product.name }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </DefaultContent>
    </AuthenticatedLayout>
</template>
