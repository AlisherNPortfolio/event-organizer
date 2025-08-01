<template>
  <div v-if="event_details.permissions.isAuthenticated.value" class="space-y-3">
    <!-- Organizer Actions -->
    <div v-if="event_details.permissions.isOrganizer.value">
      <!-- Mark Attendance Button -->
      <button
        v-if="event_details.event.status === 'completed'"
        @click="handleToggleAttendance"
        type="button"
        class="w-full bg-green-600 text-white py-3 px-4 mb-4 rounded-lg hover:bg-green-700 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-green-500"
      >
        Davomat belgilash
      </button>

      <!-- View Participants Button -->
      <button
        @click="handleShowParticipants"
        type="button"
        class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg hover:bg-gray-700 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-gray-500"
      >
        Qatnashchilarni ko'rish
      </button>
    </div>

    <!-- Participant Actions -->
    <div v-else>
      <!-- Join Event Button -->
      <button
        v-if="event_details.permissions.canJoin.value"
        @click="handleJoinEvent"
        type="button"
        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-green-500"
      >
        <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Qatnashaman
      </button>

      <!-- Event Full Button -->
      <button
        v-else-if="event_details.permissions.isFull.value"
        disabled
        type="button"
        class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg cursor-not-allowed font-medium"
      >
        Tadbir to'ldi
      </button>

      <!-- Event Status Button -->
      <button
        v-else-if="event_details.event.status !== 'upcoming'"
        disabled
        type="button"
        class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg cursor-not-allowed font-medium"
      >
        {{ event_details.event.status === 'ongoing' ? 'Davom etmoqda' : 'Tugallangan' }}
      </button>

      <!-- Leave Event Button -->
      <button
        v-if="event_details.permissions.canLeave.value"
        @click="handleLeaveEvent"
        type="button"
        class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-red-500"
      >
        <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Tadbirdan chiqish
      </button>
    </div>

    <!-- Photo Upload Button -->
    <button
      v-if="event_details.permissions.canUploadPhoto.value"
      @click="handleTogglePhotoModal"
      type="button"
      class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-purple-500"
    >
      <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      Rasm yuklash
    </button>
  </div>

  <!-- Login/Register Section -->
  <div v-else class="bg-blue-50 border border-blue-200 rounded-lg p-4">
    <p class="text-sm text-blue-800 mb-3">Tadbirga qatnashish uchun tizimga kiring</p>
    <div class="space-y-2">
      <a
        href="/login"
        class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
      >
        Kirish
      </a>
      <a
        href="/register"
        class="block w-full bg-gray-100 text-gray-700 text-center py-2 px-4 rounded-md hover:bg-gray-200 transition-colors text-sm font-medium"
      >
        Ro'yxatdan o'tish
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, defineEmits, inject } from 'vue'

const event_details = inject('event_details');

const emit = defineEmits([
  'join-event',
  'leave-event',
  'show-participants',
  'toggle-attendance',
  'toggle-photo-modal'
])

// Event handlers with proper logging
const handleJoinEvent = () => {
  console.log('Join event clicked')
  emit('join-event')
}

const handleLeaveEvent = () => {
  console.log('Leave event clicked')
  if (confirm('Haqiqatan ham tadbirdan chiqmoqchimisiz?')) {
    emit('leave-event')
  }
}

const handleShowParticipants = () => {
  console.log('Show participants clicked')
  emit('show-participants')
}

const handleToggleAttendance = () => {
  console.log('Toggle attendance clicked')
  emit('toggle-attendance')
}

const handleTogglePhotoModal = () => {
  console.log('Toggle photo modal clicked')
  emit('toggle-photo-modal')
}
</script>
