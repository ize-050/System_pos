<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150" 
            style="border-color: #E2E8F0; color: #2D3748;" 
            onmouseover="this.style.color='#4A5568'" 
            onmouseout="this.style.color='#2D3748'" 
            onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'" 
            onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'">
        <slot />
    </button>
</template>