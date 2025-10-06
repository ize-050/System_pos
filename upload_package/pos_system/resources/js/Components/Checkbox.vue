<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit("update:checked", val);
    },
});
</script>

<template>
    <input type="checkbox" :value="value" v-model="proxyChecked"
           class="rounded border-gray-300 shadow-sm focus:ring focus:ring-opacity-50" 
           style="color: #6B7B47; --tw-ring-color: rgba(107, 123, 71, 0.2);">
</template>
