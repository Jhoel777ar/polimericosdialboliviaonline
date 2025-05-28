<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from "vue";
const logoPath = computed(() => "/storage/logo.png");
</script>

<template>
    <Link :href="'/'">
        <img
            :src="logoPath"
            alt="Logo"
            class="logo"
        />
    </Link>
</template>

<style scoped>
.logo {
    height: 94px;
    max-width: 100%;
    object-fit: contain;
    filter: drop-shadow(0px 0px 8px rgba(113, 113, 122, 0.8)) 
            brightness(0.9) contrast(1.2);
    transition: transform 0.3s ease-in-out, filter 0.3s ease-in-out;
}

.logo:hover {
    transform: scale(1.05);
    filter: drop-shadow(0px 0px 12px rgb(63, 63, 70)) 
            brightness(1) contrast(1.4);
}
</style>
