<template>
    <div class="flex items-center justify-center">
        <img :src="logoPath" alt="Logo" class="logo">
    </div>
</template>
<script setup>
import { computed } from "vue";
const logoPath = computed(() => "/storage/logo.png"); 
</script>
<style scoped>
.logo {
    height: 56px;
    max-width: 100%;
    filter: drop-shadow(0px 0px 8px rgba(108, 108, 115, 0.8)) 
            brightness(0.9) contrast(1.2);
    transition: transform 0.3s ease-in-out, filter 0.3s ease-in-out;
}

.logo:hover {
    transform: scale(1.05);
    filter: drop-shadow(0px 0px 12px rgb(147, 147, 150)) 
            brightness(1) contrast(1.4);
}
</style>
