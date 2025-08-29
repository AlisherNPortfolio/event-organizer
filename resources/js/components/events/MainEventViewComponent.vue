<!-- MainEventViewComponent.vue (Complete Final) -->
<template>
  <div class="space-y-6">
    <!-- Breadcrumb -->
    <BreadcrumbComponent :event="event" />

    <!-- Event Header -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">

        <div class="p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ event.title }}</h1>
                    <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Tashkilotchi: <strong>{{ event.organizer_name }}</strong></span>
                    </div>
                </div>
                <!-- Share and Edit buttons -->
                <div class="flex space-x-2">
                    <button @click="handleShareEvent" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                    </button>

                    <a
                    v-if="permissions.canEdit.value && event.status == 'upcoming'"
                    :href="`/events/${event.id}/edit`"
                    class="p-2 text-blue-600 hover:text-blue-800 rounded-full hover:bg-blue-50"
                    >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    </a>
                </div>
            </div>
        </div>
      <!-- Event Images -->
      <EventImagesComponent :event="event" />

      <div class="p-6">
        <!-- Title and organizer -->


        <!-- Event Details Grid -->
        <EventDetailsGridComponent
          :event="event"
          @join-event="handleJoinEvent"
          @leave-event="handleLeaveEvent"
          @show-participants="handleShowParticipants"
          @toggle-attendance="handleToggleAttendance"
          @toggle-photo-modal="handleTogglePhotoModal"
        />
      </div>
    </div>

    <!-- Event Photos -->
    <div v-if="event.status !== 'upcoming'" class="bg-white shadow rounded-lg overflow-hidden">
      <div class="p-6">
        <photo-gallery
          :event-id="event.id"
          :can-upload="permissions.canUploadPhoto.value"
          :csrf-token="csrfToken"
          :temp-bearer-token="tempBearerToken"
        />
      </div>
    </div>

    <!-- Similar Events -->
    <SimilarEventsComponent :current-event-id="event.id" />

    <!-- Modals - Har biri alohida, faqat kerakli ko'rsatiladi -->
    <PhotoUploadModal
      v-if="modals.showPhotoModal.value"
      :show="modals.showPhotoModal"
      :event-id="event.id"
      @close="handleClosePhotoModal"
      @upload="handleUploadPhoto"
    />

    <ParticipantsModal
      v-if="modals.showParticipantsModal.value"
      :show="modals.showParticipantsModal"
      :participants-data="participantsData"
      :loading="loadingParticipants"
      @close="handleCloseParticipantsModal"
    />

    <AttendanceModal
      v-if="modals.showAttendanceModal.value"
      :show="modals.showAttendanceModal"
      :attendance-data="attendanceData"
      :loading="loadingAttendance"
      @close="handleCloseAttendanceModal"
      @mark-attendance="handleMarkAttendance"
    />

    <!-- Notification -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="transform opacity-0 translate-y-2"
      enter-to-class="transform opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="transform opacity-100 translate-y-0"
      leave-to-class="transform opacity-0 translate-y-2"
    >
      <div
        v-if="notification.show"
        :class="[
          'fixed top-4 right-4 p-4 rounded-md z-50 shadow-lg',
          notification.type === 'success' ? 'bg-green-500 text-white' :
          notification.type === 'error' ? 'bg-red-500 text-white' :
          'bg-blue-500 text-white'
        ]"
      >
        <div class="flex items-center">
          <svg v-if="notification.type === 'success'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <svg v-else-if="notification.type === 'error'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          <span>{{ notification.message }}</span>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, toRef, onMounted, onUnmounted, provide } from 'vue'
import BreadcrumbComponent from './BreadcrumbComponent.vue'
import EventImagesComponent from './EventImagesComponent.vue'
import EventDetailsGridComponent from './EventDetailsGridComponent.vue'
import SimilarEventsComponent from './SimilarEventsComponent.vue'
import PhotoUploadModal from './modals/PhotoUploadModal.vue'
import ParticipantsModal from './modals/ParticipantsModal.vue'
import AttendanceModal from './modals/AttendanceModal.vue'

// Composables
import { useEventActions } from '../../composables/events/useEventActions'
import { useNotification } from '../../composables/events/useNotification'
import { useModal } from '../../composables/events/useModal'
import { useEventFormatters } from '../../composables/events/useEventFormatters'
import { useEventPermissions } from '../../composables/events/useEventPermissions'
import { useKeyboardShortcuts } from '../../composables/events/useKeyboardShortcuts'
import { useShareEvent } from '../../composables/events/useShareEvent'

// Props
const props = defineProps({
  event: {
    type: Object,
    required: true
  },
  currentUser: {
    type: Object,
    default: null
  },
  isParticipating: {
    type: Boolean,
    default: false
  },
  csrfToken: {
    type: String,
    required: true
  },
  tempBearerToken: {
    type: String,
    default: ''
  },
  backendErrors: {
    type: String,
    default: null
  }
});

// Permissions
const permissions = useEventPermissions(
  toRef(props, 'event'),
  toRef(props, 'currentUser'),
  toRef(props, 'isParticipating')
);

provide('event_details', {
    'event': props.event,
    'csrfToken': props.csrfToken,
    'tempBearerToken': props.tempBearerToken,
    'permissions': permissions
});

// Development mode detection
const isDevelopment = computed(() => {
  return process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost'
})

// Reactive state
const participantsData = ref(null)
const attendanceData = ref([])
const loadingParticipants = ref(false)
const loadingAttendance = ref(false)

// Composables
const eventActions = useEventActions(props)
const { notification, showNotification } = useNotification()
const modals = useModal()
const { getStatusBadgeClass, getStatusText } = useEventFormatters()
const { shareEvent } = useShareEvent()

// Keyboard shortcuts
useKeyboardShortcuts({
  onEscape: () => {
    console.log('ESC pressed, closing all modals')
    modals.closeAllModals()
  }
})

// Event handlers
const handleJoinEvent = async () => {
  try {
    const result = await eventActions.joinEvent(props.event.id)
    showNotification(result.message || 'Muvaffaqiyatli qo\'shildingiz!', 'success')
    // Reload page to update participation status
    setTimeout(() => window.location.reload(), 1000)
  } catch (error) {
    showNotification(error.message, 'error')
  }
}

const handleLeaveEvent = async () => {
  try {
    const result = await eventActions.leaveEvent(props.event.id)
    showNotification(result.message || 'Tadbirdan chiqib ketdingiz', 'success')
    setTimeout(() => window.location.reload(), 1000)
  } catch (error) {
    showNotification(error.message, 'error')
  }
}

const handleShowParticipants = async () => {
  console.log('Opening participants modal')
  modals.openParticipantsModal()
  await loadParticipants()
}

const handleToggleAttendance = async () => {
  console.log('Opening attendance modal')
  modals.openAttendanceModal()
  await loadAttendance()
}

const handleTogglePhotoModal = () => {
  console.log('Opening photo modal')
  modals.openPhotoModal()
}

// Modal close handlers
const handleClosePhotoModal = () => {
  console.log('Closing photo modal')
  modals.closePhotoModal()
}

const handleCloseParticipantsModal = () => {
  console.log('Closing participants modal')
  modals.closeParticipantsModal()
}

const handleCloseAttendanceModal = () => {
  console.log('Closing attendance modal')
  modals.closeAttendanceModal()
}

const handleUploadPhoto = async (formData) => {
  try {
    const result = await eventActions.uploadPhoto(props.event.id, formData)
    showNotification(result.message || 'Rasm muvaffaqiyatli yuklandi', 'success')
    handleClosePhotoModal()
    // Reload photos section after 1 second
    setTimeout(() => window.location.reload(), 1000)
  } catch (error) {
    showNotification(error.message, 'error')
  }
}

const handleMarkAttendance = async ({ participantId, attended, reset = false }) => {
  try {
    if (reset) {
      // Reset attendance logic - this would need a separate API endpoint
      showNotification('Qayta tiklash funksiyasi hali ishlamaydi', 'error')
      return
    }

    const result = await eventActions.markAttendance(props.event.id, participantId, attended)

    // Update attendance data locally
    const participant = attendanceData.value.find(p => p.user.id === participantId)
    if (participant) {
      participant.participation.attended = attended
      participant.participation.marked = true
    }

    showNotification(
      result.message || (attended ? 'Qatnashchi ishtirok etgan deb belgilandi' : 'Qatnashchi ishtirok etmagan deb belgilandi'),
      'success'
    )
  } catch (error) {
    showNotification(error.message, 'error')
  }
}

const handleShareEvent = async () => {
  try {
    const result = await shareEvent(props.event)

    if (result.success) {
      if (result.method === 'clipboard') {
        showNotification('Havola nusxalandi!', 'success')
      }
    }
  } catch (error) {
    showNotification(error.message, 'error')
  }
}

// Helper functions
const loadParticipants = async () => {
  try {
    loadingParticipants.value = true
    participantsData.value = await eventActions.getParticipants(props.event.id)
  } catch (error) {
    showNotification('Qatnashchilarni yuklashda xatolik yuz berdi', 'error')
  } finally {
    loadingParticipants.value = false
  }
}

const loadAttendance = async () => {
  try {
    loadingAttendance.value = true
    const data = await eventActions.getParticipants(props.event.id)
    attendanceData.value = data.participants || []
  } catch (error) {
    showNotification('Davomat ma\'lumotlarini yuklashda xatolik yuz berdi', 'error')
  } finally {
    loadingAttendance.value = false
  }
}

// Lifecycle hooks
onMounted(() => {
    // Force close all modals on mount
  modals.closeAllModals()

  // Debug functions for browser console
  if (isDevelopment.value) {
    window.eventDebug = {
      modals,
      permissions,
      event: props.event,
      forceCloseModals: () => modals.closeAllModals(),
      openAttendanceModal: () => modals.openAttendanceModal(),
      checkModalStates: () => {
        console.log('Current modal states:', {
          showPhotoModal: modals.showPhotoModal.value,
          showParticipantsModal: modals.showParticipantsModal.value,
          showAttendanceModal: modals.showAttendanceModal.value
        })
      }
    }
    console.log('Debug functions available: window.eventDebug')
  }
})

onUnmounted(() => {
  console.log('MainEventViewComponent unmounted')
  // Clean up
  modals.closeAllModals()
  if (window.eventDebug) {
    delete window.eventDebug
  }
})
</script>

<style scoped>
/* Modal transition styles */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

/* Notification styles */
.notification-enter-active {
  transition: all 0.3s ease-out;
}

.notification-leave-active {
  transition: all 0.2s ease-in;
}

.notification-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.notification-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
