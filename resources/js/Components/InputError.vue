<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message" class="text-rose-600 absolute text-sm max-w-[200px]">
        {{ message }}
    </div>
</template>
