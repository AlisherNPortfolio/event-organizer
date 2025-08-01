<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Details -->
    <MainDetailsComponent :event="event" />

    <!-- Sidebar -->
    <SidebarComponent
      :event="event"
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
import MainDetailsComponent from './MainDetailsComponent.vue'
import SidebarComponent from './SidebarComponent.vue'

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
