<script setup>
defineProps({
    for: String,
    value: String
});
</script>

<template>
    <label :for="$props.for" class="block font-medium text-sm text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>