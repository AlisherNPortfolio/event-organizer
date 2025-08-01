<template>
  <div class="space-y-6">
    <!-- Event Info Card -->
    <EventInfoCardComponent :event="event" />

    <!-- Action Buttons -->
    <ActionButtonsComponent
      @join-event="$emit('join-event')"
      @leave-event="$emit('leave-event')"
      @show-participants="$emit('show-participants')"
      @toggle-attendance="$emit('toggle-attendance')"
      @toggle-photo-modal="$emit('toggle-photo-modal')"
    />
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import EventInfoCardComponent from './EventInfoCardComponent.vue'
import ActionButtonsComponent from './ActionButtonsComponent.vue'

const props = defineProps({
  event: {
    type: Object,
    required: true
  }
})

const emit = defineEmits([
  'join-event',
  'leave-event',
  'show-participants',
  'toggle-attendance',
  'toggle-photo-modal'
])
</script>
