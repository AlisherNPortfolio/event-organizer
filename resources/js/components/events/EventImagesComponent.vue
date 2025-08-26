<template>
  <div class="relative" v-if="event.images && event.images.length > 0">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
        <img :src="`/storage/${event.image}`" :alt="event.title" class="hidden">
        <div
            v-if="event.image"
            class="md:col-span-2 md:row-span-2"
        >
            <img
                :src="`/storage/${event.image}`"
                :alt="event.title"
                class="w-full object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity h-64 md:h-96"
                @click="openImageModal(`/storage/${event.image}`)"
            >
        </div>
      <div
        v-for="(image, index) in event.images"
        :key="index"
        :class="index === 0 ? 'md:col-span-2 md:row-span-2' : ''"
      >
        <img
          :src="`/storage/${image}`"
          :alt="event.title"
          :class="[
            'w-full object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity',
            index === 0 && event.image ? 'h-64 md:h-96' : 'h-32'
          ]"
          @click="openImageModal(`/storage/${image}`)"
        >
      </div>
    </div>

    <!-- Price badge -->
    <div class="absolute top-6 left-6">
      <span
        v-if="event.is_free"
        class="bg-green-500 text-white px-3 py-1 text-sm font-semibold rounded-full"
      >
        Bepul
      </span>
      <span
        v-else
        class="bg-blue-500 text-white px-3 py-1 text-sm font-semibold rounded-full"
      >
        {{ formatPrice(event.price) }} {{ event.currency }}
      </span>
      <span
            :class="[
              'px-3 py-1 text-sm font-semibold rounded-full ml-2',
              getStatusBadgeClass(event.status)
            ]"
          >
            {{ getStatusText(event.status) }}
          </span>
    </div>
  </div>

  <!-- Image Modal -->
  <div
    v-if="showImageModal"
    class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
    @click="closeImageModal"
  >
    <div class="max-w-4xl max-h-full p-4">
      <img
        :src="selectedImage"
        alt="Event image"
        class="max-w-full max-h-full object-contain rounded-lg"
      >
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import { useEventFormatters } from '../../composables/events/useEventFormatters'
const props = defineProps({
  event: {
    type: Object,
    required: true
  }
})
const { getStatusBadgeClass, getStatusText } = useEventFormatters()

const emit = defineEmits(['imageClick'])

const showImageModal = ref(false)
const selectedImage = ref('')

const openImageModal = (imageSrc) => {
  selectedImage.value = imageSrc
  showImageModal.value = true
}

const closeImageModal = () => {
  showImageModal.value = false
  selectedImage.value = ''
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('uz-UZ').format(price)
}
console.log('Event', props.event, props.event.image)
</script>
