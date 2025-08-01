<template>
  <nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-1">
      <li>
        <a href="/events" class="text-gray-500 hover:text-gray-700">Tadbirlar</a>
      </li>
      <li>
        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
      </li>
      <li>
        <span class="text-gray-900 font-medium line-clamp-1">{{ event.title }}</span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  event: {
    type: Object,
    required: true
  }
})
</script>
