<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
      @click="handleBackdropClick"
    >
      <div
        class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white"
        @click.stop
      >
        <div class="mt-3">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Davomat belgilash</h3>
            <button
              @click="handleClose"
              class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 rounded-full p-1"
              type="button"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="max-h-96 overflow-y-auto">
            <!-- Loading state -->
            <div v-if="loading" class="text-center py-4">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-2 text-gray-500">Yuklanmoqda...</p>
            </div>

            <!-- Attendance data -->
            <div v-else-if="attendanceData && attendanceData.length > 0">
              <!-- Summary -->
              <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-3 gap-4 text-center">
                  <div>
                    <div class="text-lg font-semibold text-green-600">{{ attendanceStats.attended }}</div>
                    <div class="text-sm text-gray-500">Qatnashgan</div>
                  </div>
                  <div>
                    <div class="text-lg font-semibold text-red-600">{{ attendanceStats.notAttended }}</div>
                    <div class="text-sm text-gray-500">Qatnashmagan</div>
                  </div>
                  <div>
                    <div class="text-lg font-semibold text-gray-600">{{ attendanceStats.pending }}</div>
                    <div class="text-sm text-gray-500">Kutilmoqda</div>
                  </div>
                </div>
              </div>

              <!-- Filter buttons -->
              <div class="mb-4 flex space-x-2 flex-wrap">
                <button
                  @click="filterStatus = 'all'"
                  :class="[
                    'px-3 py-1 text-sm rounded-full transition-colors',
                    filterStatus === 'all'
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  ]"
                  type="button"
                >
                  Barchasi ({{ attendanceData.length }})
                </button>
                <button
                  @click="filterStatus = 'pending'"
                  :class="[
                    'px-3 py-1 text-sm rounded-full transition-colors',
                    filterStatus === 'pending'
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  ]"
                  type="button"
                >
                  Kutilmoqda ({{ attendanceStats.pending }})
                </button>
                <button
                  @click="filterStatus = 'attended'"
                  :class="[
                    'px-3 py-1 text-sm rounded-full transition-colors',
                    filterStatus === 'attended'
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  ]"
                  type="button"
                >
                  Qatnashgan ({{ attendanceStats.attended }})
                </button>
                <button
                  @click="filterStatus = 'not_attended'"
                  :class="[
                    'px-3 py-1 text-sm rounded-full transition-colors',
                    filterStatus === 'not_attended'
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  ]"
                  type="button"
                >
                  Qatnashmagan ({{ attendanceStats.notAttended }})
                </button>
              </div>

              <!-- Participants List -->
              <div class="space-y-3">
                <div
                  v-for="participant in filteredAttendanceData"
                  :key="participant.user.id"
                  class="flex items-center justify-between p-3 border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors"
                >
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <img
                        v-if="participant.user.avatar_url"
                        :src="participant.user.avatar_url"
                        :alt="participant.user.name"
                        class="h-10 w-10 rounded-full object-cover"
                      >
                      <div
                        v-else
                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center"
                      >
                        <span class="text-sm font-medium text-gray-700">
                          {{ participant.user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ participant.user.name }}</p>
                      <p class="text-xs text-gray-500">
                        Reyting: {{ participant.user.rating }} ball
                        <span class="mx-1">•</span>
                        Qo'shilgan: {{ formatDate(participant.participation.joined_at) }}
                      </p>
                    </div>
                  </div>

                  <div class="flex items-center space-x-2">
                    <span
                      v-if="participant.participation.marked"
                      :class="[
                        'px-2 py-1 text-xs font-semibold rounded-full',
                        participant.participation.attended
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ participant.participation.attended ? 'Qatnashgan' : 'Qatnashmagan' }}
                    </span>

                    <div v-else class="flex space-x-1">
                      <button
                        @click="handleMarkAttendance(participant.user.id, true)"
                        :disabled="markingAttendance"
                        class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                        type="button"
                      >
                        ✓ Qatnashgan
                      </button>
                      <button
                        @click="handleMarkAttendance(participant.user.id, false)"
                        :disabled="markingAttendance"
                        class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                        type="button"
                      >
                        ✗ Qatnashmagan
                      </button>
                    </div>

                    <!-- Quick action for already marked -->
                    <button
                      v-if="participant.participation.marked"
                      @click="handleResetAttendance(participant.user.id)"
                      class="text-gray-400 hover:text-gray-600 text-xs focus:outline-none focus:ring-2 focus:ring-gray-300 rounded p-1"
                      title="Qayta belgilash"
                      type="button"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Empty state for filtered -->
              <div v-if="filteredAttendanceData.length === 0" class="text-center py-8 text-gray-500">
                <p>Bu kategoriyada hech kim yo'q</p>
              </div>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-4 text-gray-500">
              <p>Qatnashchilar yo'q</p>
            </div>
          </div>

          <!-- Footer -->
          <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-500">
              Jami: {{ attendanceData?.length || 0 }} ishtirokchi
            </div>
            <div class="flex space-x-2">
              <button
                @click="handleMarkAllAttended"
                :disabled="markingAttendance || pendingParticipants.length === 0"
                class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                type="button"
              >
                Barchasini qatnashgan
              </button>
              <button
                @click="handleClose"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300"
                type="button"
              >
                Yopish
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  attendanceData: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'mark-attendance'])

const filterStatus = ref('all')
const markingAttendance = ref(false)

// Modal ochilganda filter ni reset qilish
watch(() => props.show, (newValue) => {
  if (newValue) {
    filterStatus.value = 'all'
  }
})

const attendanceStats = computed(() => {
  const stats = {
    attended: 0,
    notAttended: 0,
    pending: 0
  }

  props.attendanceData.forEach(participant => {
    if (participant.participation.marked) {
      if (participant.participation.attended) {
        stats.attended++
      } else {
        stats.notAttended++
      }
    } else {
      stats.pending++
    }
  })

  return stats
})

const filteredAttendanceData = computed(() => {
  if (filterStatus.value === 'all') {
    return props.attendanceData
  }

  return props.attendanceData.filter(participant => {
    const { marked, attended } = participant.participation

    switch (filterStatus.value) {
      case 'pending':
        return !marked
      case 'attended':
        return marked && attended
      case 'not_attended':
        return marked && !attended
      default:
        return true
    }
  })
})
console.log(filteredAttendanceData.value)
const pendingParticipants = computed(() => {
  return props.attendanceData.filter(participant => !participant.participation.marked)
})

const handleMarkAttendance = async (participantId, attended) => {
  markingAttendance.value = true

  try {
    await emit('mark-attendance', { participantId, attended })
  } finally {
    markingAttendance.value = false
  }
}

const handleResetAttendance = async (participantId) => {
  if (confirm('Davomat belgilarini qayta tiklamoqchimisiz?')) {
    markingAttendance.value = true

    try {
      await emit('mark-attendance', { participantId, attended: null, reset: true })
    } finally {
      markingAttendance.value = false
    }
  }
}

const handleMarkAllAttended = async () => {
  if (confirm('Barcha kutilayotgan qatnashchilarni qatnashgan deb belgilamoqchimisiz?')) {
    markingAttendance.value = true

    try {
      for (const participant of pendingParticipants.value) {
        await emit('mark-attendance', { participantId: participant.user.id, attended: true })
      }
    } finally {
      markingAttendance.value = false
    }
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('uz-UZ')
}

const handleClose = () => {
  filterStatus.value = 'all'
  emit('close')
}

const handleBackdropClick = () => {
  handleClose()
}

// ESC tugmasi bilan modalni yopish
const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.show) {
    handleClose()
  }
}

// Component mount bo'lganda event listener qo'shish
import { onMounted, onUnmounted } from 'vue'

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>
