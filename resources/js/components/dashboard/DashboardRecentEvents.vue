<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Sizning tadbirlaringiz</h3>
                <a :href="`/events?organizer=${ userId }`" class="text-sm text-blue-600 hover:text-blue-500">
                    Barchasini ko'rish →
                </a>
            </div>
                <div class="divide-y divide-gray-200" v-if="userEvents.length > 0">
                        <div class="p-6 hover:bg-gray-50 transition-colors" v-for="event in someEvents" :key="event.id">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <img
                                            v-if="event.image"
                                            class="h-12 w-12 rounded-lg object-cover border-2 border-gray-200"
                                                :src="`/storage/${event.image}`"
                                                :alt="event.title">
                                        <div v-else class="h-12 w-12 rounded-lg bg-gray-300 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ event.title }}</p>
                                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                                            <span class="flex items-center">
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ formatDate(event.startTime) }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                                </svg>
                                                {{ event.currentParticipants }}/{{ event.maxParticipants }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-blue-100 text-blue-800': event.status === 'upcoming',
                                            'bg-green-100 text-green-800': event.status === 'ongoing',
                                            'bg-gray-100 text-gray-800': event.status === 'completed'
                                        }"
                                    >
                                        <span v-if="event.status == 'upcoming'">Kutilmoqda</span>
                                        <span v-else-if="event.status == 'ongoing'">Kutilmoqda</span>
                                        <span v-else>Tugallangan</span>
                                    </span>
                                    <a :href="`/events/${event.id}`"
                                       class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                        Ko'rish
                                    </a>
                                </div>
                            </div>
                        </div>
                </div>
                <div v-else class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tadbirlar yo'q</h3>
                    <p class="mt-1 text-sm text-gray-500">Birinchi tadbiringizni yaratishni boshlang</p>
                    <div class="mt-6">
                        <a href="/events/create"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tadbir yaratish
                        </a>
                    </div>
                </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">So'nggi faoliyat</h3>
            </div>
            <div class="p-6">
                <div class="flow-root">
                    <ul role="list" class="-mb-8">

                        <li v-for="(activity, index) in activities" :key="index">
                            <div class="relative pb-8">
                                <span v-if="activities.length - 1 !== index" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full {{ $activity.color }} flex items-center justify-center ring-8 ring-white">
                                            <svg v-if="activity.icon == 'user-add'" class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            <svg v-else-if="activity.icon == 'calendar'" class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <svg v-else-if="activity.icon === 'users'" class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <svg v-else class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L.432 9.101c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69L9.049 2.927z"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ activity.content }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ activity.time }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { computed, defineProps } from 'vue';
const props = defineProps({
    userId: {
        type: Number,
        required: true
    },
    userEvents: {
        type: Array,
        required: true
    }
});

const someEvents = computed(() => {
    return props.userEvents.slice(0, 5);
});
// TODO: activities ma'lumotlarini backenddan olish
const activities = [
    {
        'type': 'joined',
        'content': 'Yangi foydalanuvchi platformaga qo\'shildi',
        'time': '2 soat oldin',
        'icon': 'user-add',
        'color': 'bg-green-500'
    },
    {
        'type': 'event',
        'content': '3 ta yangi tadbir yaratildi',
        'time': '5 soat oldin',
        'icon': 'calendar',
        'color': 'bg-blue-500'
    },
    {
        'type': 'participant',
        'content': '15 ta yangi qatnashchi ro\'yxatdan o\'tdi',
        'time': '1 kun oldin',
        'icon': 'users',
        'color': 'bg-purple-500'
    },
    {
        'type': 'rating',
        'content': 'Sizning reytingingiz yangilandi',
        'time': '2 kun oldin',
        'icon': 'star',
        'color': 'bg-yellow-500'
    }
];

const formatDate = (dateStr) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateStr).toLocaleDateString(undefined, options);
};
</script>
