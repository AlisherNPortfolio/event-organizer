<template>
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold">Salom, {{ username }}! 👋</h1>
                <p class="text-blue-0f55ae mt-2">Bugun qanday tadbir tashkil qilmoqchisiz?</p>
            </div>
            <div class="mt-4 md:mt-0 text-right">
                <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                    <div class="text-sm text-blue-0f55ae">Sizning reytingingiz</div>
                    <div class="text-3xl font-bold flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L.432 9.101c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69L9.049 2.927z"></path>
                        </svg>
                        {{ rating }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    username: {
        type: String,
        required: true
    },
    rating: {
        type: Number,
        required: true
    }
});
</script>
