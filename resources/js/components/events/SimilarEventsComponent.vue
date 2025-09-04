<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">O'xshash tadbirlar</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          v-if="loading"
          class="col-span-full text-center py-8"
        >
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-gray-500 text-sm">O'xshash tadbirlar yuklanmoqda...</p>
        </div>

        <div
          v-else-if="similarEvents.length === 0"
          class="col-span-full text-center py-8 text-gray-500"
        >
          <p class="text-sm">O'xshash tadbirlar topilmadi</p>
        </div>

        <div
          v-else
          v-for="event in similarEvents"
          :key="event.id"
          class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
          @click="goToEvent(event.id)"
        >
          <div class="aspect-video bg-gray-100 rounded-lg mb-3 overflow-hidden">
            <img
              v-if="event.image"
              :src="`/storage/${event.image}`"
              :alt="event.title"
              class="w-full h-full object-cover"
            >
            <div
              v-else
              class="w-full h-full flex items-center justify-center text-gray-400"
            >
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>

          <div class="space-y-2">
            <h4 class="font-medium text-gray-900 line-clamp-2">{{ event.title }}</h4>

            <div class="flex items-center text-sm text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span>{{ formatDate(event.startTime) }}</span>
            </div>

            <div class="flex items-center text-sm text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span class="truncate">{{ event.address }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span
                :class="[
                  'px-2 py-1 text-xs font-semibold rounded-full',
                  getStatusBadgeClass(event.status)
                ]"
              >
                {{ getStatusText(event.status) }}
              </span>

              <div class="text-sm font-medium text-gray-900">
                <span v-if="event.isFree" class="text-green-600">Bepul</span>
                <span v-else>{{ formatPrice(event.price) }} {{ event.currency }}</span>
              </div>
            </div>

            <div class="flex items-center text-sm text-gray-500">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <span>{{ event.currentParticipants }}/{{ event.maxParticipants }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, defineProps } from 'vue'

const props = defineProps({
  currentEventId: {
    type: [String, Number],
    required: true
  },
  csrfToken: {
    type: String,
    required: true
  },
  tempBearerToken: {
    type: String,
    required: true
  }
})

const loading = ref(true)
const similarEvents = ref([])

onMounted(async () => {
  await loadSimilarEvents()
})

const loadSimilarEvents = async () => {
  try {
    loading.value = true

    const response = await fetch(`/api/v1/events/${props.currentEventId}/similar`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': props.csrfToken,
                    'Authorization': `Bearer ${props.tempBearerToken}`,
                }})

    if (response.ok) {
      const data = await response.json();
      if (data.success) {
            similarEvents.value = data?.data || []
      } else {
        alert(data.message)
      }

    }
  } catch (error) {
    console.error('Error loading similar events:', error)
  } finally {
    loading.value = false
  }
}

const goToEvent = (eventId) => {
  window.location.href = `/events/${eventId}`
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('uz-UZ', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('uz-UZ').format(price)
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'upcoming':
      return 'bg-blue-100 text-blue-800'
    case 'ongoing':
      return 'bg-green-100 text-green-800'
    case 'completed':
      return 'bg-gray-100 text-gray-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'upcoming':
      return 'Kutilmoqda'
    case 'ongoing':
      return 'Davom etmoqda'
    case 'completed':
      return 'Tugallangan'
    case 'cancelled':
      return 'Bekor qilingan'
    default:
      return 'Noma\'lum'
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
