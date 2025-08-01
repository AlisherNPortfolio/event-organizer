<template>
  <div
    v-if="showValue"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    @click="closeModal"
  >
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Qatnashchilar</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="max-h-96 overflow-y-auto">
          <!-- Loading state -->
          <div v-if="loading" class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-500">Yuklanmoqda...</p>
          </div>

          <!-- Participants data -->
          <div v-else-if="participantsData">
            <!-- Statistics -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                  <div class="text-lg font-semibold text-blue-600">{{ participantsData.stats.total }}</div>
                  <div class="text-sm text-gray-500">Jami</div>
                </div>
                <div>
                  <div class="text-lg font-semibold text-green-600">{{ participantsData.stats.attended }}</div>
                  <div class="text-sm text-gray-500">Qatnashgan</div>
                </div>
                <div>
                  <div class="text-lg font-semibold text-red-600">{{ participantsData.stats.not_attended }}</div>
                  <div class="text-sm text-gray-500">Qatnashmagan</div>
                </div>
                <div>
                  <div class="text-lg font-semibold text-gray-600">{{ participantsData.stats.pending }}</div>
                  <div class="text-sm text-gray-500">Kutilmoqda</div>
                </div>
              </div>
            </div>

            <!-- Search/Filter -->
            <div class="mb-4">
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Qatnashchilarni qidiring..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <!-- Participants List -->
            <div class="space-y-3">
              <div
                v-for="participant in filteredParticipants"
                :key="participant.user.id"
                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors"
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
                    <div class="text-sm font-medium text-gray-900">{{ participant.user.name }}</div>
                    <div class="text-sm text-gray-500">
                      <span>Reyting: {{ participant.user.rating }} ball</span>
                      <span class="mx-1">•</span>
                      <span>{{ participant.user.email }}</span>
                    </div>
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
                  <span
                    v-else
                    class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"
                  >
                    Kutilmoqda
                  </span>
                  <span class="text-xs text-gray-500">
                    {{ formatDate(participant.participation.joined_at) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Empty state -->
            <div v-if="filteredParticipants.length === 0" class="text-center py-8 text-gray-500">
              <p>{{ searchTerm ? 'Hech kim topilmadi' : 'Hali hech kim qatnashmagan' }}</p>
            </div>
          </div>

          <!-- Error state -->
          <div v-else class="text-center py-8 text-gray-500">
            <p>Ma'lumotlarni yuklashda xatolik yuz berdi</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-4 flex justify-between items-center">
          <span class="text-sm text-gray-500">
            Jami: {{ participantsData?.stats?.total || 0 }} ishtirokchi
          </span>
          <button
            @click="closeModal"
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors"
          >
            Yopish
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, isRef } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  participantsData: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const showValue = isRef(props.show) ? props.show.value : props.show;

const emit = defineEmits(['close'])

const searchTerm = ref('')

const filteredParticipants = computed(() => {
  if (!props.participantsData?.participants) return []

  if (!searchTerm.value) {
    return props.participantsData.participants
  }

  return props.participantsData.participants.filter(participant =>
    participant.user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    participant.user.email.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('uz-UZ')
}

const closeModal = () => {
  searchTerm.value = ''
  emit('close')
}
</script>
