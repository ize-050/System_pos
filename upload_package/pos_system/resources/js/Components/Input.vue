<script setup>
import { onMounted, ref } from 'vue';

defineProps(['modelValue']);

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <input class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" 
           style="--tw-ring-color: rgba(107, 123, 71, 0.2); border-color: #E2E8F0;" 
           :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
</template>
