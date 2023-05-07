<script setup>
import {nextTick, onMounted, ref} from 'vue';

defineProps({
    modelValue: {
        type: Number,
        required: true,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        nextTick(() => input.value.focus());
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        type="number"
        class="p-2 text-xl border border-gray-300 ring-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', Number($event.target.value))"
        ref="input"
    />
</template>
