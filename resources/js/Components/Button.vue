<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none transition ease-in-out duration-150" 
            style="background-color: #6B7B47; border-color: #6B7B47;" 
            onmouseover="this.style.backgroundColor='#8A9B5A'" 
            onmouseout="this.style.backgroundColor='#6B7B47'" 
            onmousedown="this.style.backgroundColor='#5A6B3A'" 
            onmouseup="this.style.backgroundColor='#8A9B5A'">
        <slot />
    </button>
</template>
