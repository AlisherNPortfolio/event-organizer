<template>
<div class="grid grid-cols-1 md:grid-cols-1 gap-4 p-4">
    <img
            v-if="imageUrl"
            :src="`/storage/${imageUrl}`"
            alt="Event Main Image"
            class="w-full object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
        >
</div>
</template>
<script setup>
import { defineProps } from 'vue'
const props = defineProps({
    imageUrl: {
        type: String,
        required: true
    }
})
</script>
