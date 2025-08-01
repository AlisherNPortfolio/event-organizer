<template>
  <div class="bg-gray-50 rounded-lg p-4 space-y-4">
    <!-- Start Time -->
    <div class="flex items-start">
      <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <div>
        <p class="text-sm font-medium text-gray-900">Boshlanish vaqti</p>
        <p class="text-sm text-gray-600">{{ formattedStartTime }}</p>
      </div>
    </div>

    <!-- End Time -->
    <div v-if="event.end_time" class="flex items-start">
      <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <div>
        <p class="text-sm font-medium text-gray-900">Tugash vaqti</p>
        <p class="text-sm text-gray-600">{{ formattedEndTime }}</p>
      </div>
    </div>

    <!-- Address -->
    <div class="flex items-start">
      <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      <div>
        <p class="text-sm font-medium text-gray-900">Manzil</p>
        <p class="text-sm text-gray-600">{{ event.address }}</p>
        <button @click="openMap" class="text-xs text-blue-600 hover:text-blue-800 mt-1">
          Xaritada ko'rish
        </button>
      </div>
    </div>

    <!-- Price -->
    <div class="flex items-start">
      <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
      </svg>
      <div>
        <p class="text-sm font-medium text-gray-900">Narx</p>
        <p class="text-sm text-gray-600">
          <span
            v-if="event.is_free"
            class="text-green-600 font-medium"
          >
            Bepul
          </span>
          <span
            v-else
            class="font-medium"
          >
            {{ formatPrice(event.price) }} {{ event.currency }}
          </span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps } from 'vue'

const props = defineProps({
  event: {
    type: Object,
    required: true
  }
})

const formattedStartTime = computed(() => {
  return formatDateTime(props.event.start_time)
})

const formattedEndTime = computed(() => {
  return formatDateTime(props.event.end_time)
})

const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  return new Date(dateTime).toLocaleString('uz-UZ', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('uz-UZ').format(price)
}

const openMap = () => {
  const address = encodeURIComponent(props.event.address)
  const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${address}`
  window.open(mapsUrl, '_blank')
}
</script>
