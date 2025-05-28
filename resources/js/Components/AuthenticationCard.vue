<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <img :src="logoPath" alt="Logo" class="logo">
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const logoPath = computed(() => "/storage/logo.png");
</script>

<style scoped>
.logo {
    height: 96px; 
    max-width: 100%;
    object-fit: contain;
    filter: drop-shadow(0px 0px 8px rgba(56, 72, 100, 0.8)) 
            brightness(0.9) contrast(1.2);
    transition: transform 0.3s ease-in-out, filter 0.3s ease-in-out;
}

.logo:hover {
    transform: scale(1.05);
    filter: drop-shadow(0px 0px 12px rgb(82, 102, 116)) 
            brightness(1) contrast(1.4);
}
</style>
