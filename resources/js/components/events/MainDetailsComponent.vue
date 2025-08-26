<template>
  <div class="lg:col-span-2 space-y-6">
    <EventMainImageComponent :imageUrl="event.image" />
    <!-- Description -->
    <div>
      <h3 class="text-lg font-semibold text-gray-900 mb-3">Tadbir haqida</h3>
      <div class="prose prose-sm max-w-none text-gray-700">
        <p v-html="formattedDescription"></p>
      </div>
    </div>

    <!-- Participants progress -->
    <div>
      <h3 class="text-lg font-semibold text-gray-900 mb-3">Qatnashchilar</h3>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm text-gray-600">
            {{ event.current_participants }}/{{ event.max_participants }} ishtirokchi
          </span>
          <span class="text-sm text-gray-600">{{ participantProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div
            class="bg-blue-600 h-3 rounded-full transition-all duration-300"
            :style="{ width: participantProgress + '%' }"
          ></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">
          Minimal: {{ event.min_participants }} ishtirokchi kerak
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps } from 'vue'
import EventMainImageComponent from './EventMainImageComponent.vue'
const props = defineProps({
  event: {
    type: Object,
    required: true
  }
})

const formattedDescription = computed(() => {
  return props.event.description ? props.event.description.replace(/\n/g, '<br>') : ''
})

const participantProgress = computed(() => {
  if (!props.event.max_participants) return 0
  return Math.round((props.event.current_participants / props.event.max_participants) * 100)
})
</script>
