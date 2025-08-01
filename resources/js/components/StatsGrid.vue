<template>
  <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
    <div v-for="stat in stats" :key="stat.key" class="bg-white overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <component :is="stat.icon" class="h-6 w-6 text-gray-400" />
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">
                {{ stat.label }}
              </dt>
              <dd class="text-lg font-medium text-gray-900">
                {{ stat.value }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  organizedEvents: {
    type: Number,
    default: 0
  },
  participatedEvents: {
    type: Number,
    default: 0
  },
  upcomingEvents: {
    type: Number,
    default: 0
  },
  completedEvents: {
    type: Number,
    default: 0
  },
  rating: {
    type: Number,
    default: 0
  }
})

const stats = computed(() => [
  {
    key: 'organized',
    label: 'Yaratgan tadbirlar',
    value: props.organizedEvents,
    icon: 'CalendarIcon'
  },
  {
    key: 'participated',
    label: 'Qatnashgan tadbirlar',
    value: props.participatedEvents,
    icon: 'UsersIcon'
  },
  {
    key: 'upcoming',
    label: 'Kutilayotgan',
    value: props.upcomingEvents,
    icon: 'ClockIcon'
  },
  {
    key: 'completed',
    label: 'Tugallangan',
    value: props.completedEvents,
    icon: 'CheckCircleIcon'
  },
  {
    key: 'rating',
    label: 'Reyting',
    value: props.rating,
    icon: 'StarIcon'
  }
])
</script>
