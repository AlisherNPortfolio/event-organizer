<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    @click="closeModal"
  >
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
      <div class="mt-3">
        <h3 class="text-lg font-medium text-gray-900 text-center">Rasm yuklash</h3>
        <form @submit.prevent="uploadPhoto" class="mt-4 space-y-4">
          <div>
            <input
              type="file"
              ref="photoInput"
              @change="handleFileSelect"
              accept="image/*"
              required
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            >
            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF - maksimal 2MB</p>
          </div>

          <!-- Progress bar -->
          <div v-if="uploading" class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-blue-600 h-2 rounded-full transition-all duration-300"
              :style="{ width: uploadProgress + '%' }"
            ></div>
          </div>

          <div class="flex space-x-3">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors"
            >
              Bekor qilish
            </button>
            <button
              type="submit"
              :disabled="!selectedFile || uploading"
              class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
            >
              {{ uploading ? 'Yuklanmoqda...' : 'Yuklash' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  eventId: {
    type: [String, Number],
    required: true
  }
})

const emit = defineEmits(['close', 'upload'])

const photoInput = ref(null)
const selectedFile = ref(null)
const uploading = ref(false)
const uploadProgress = ref(0)

const handleFileSelect = (event) => {
  selectedFile.value = event.target.files[0]

  // Validate file
  if (selectedFile.value) {
    const maxSize = 2 * 1024 * 1024 // 2MB
    if (selectedFile.value.size > maxSize) {
      alert('Fayl hajmi 2MB dan oshmasligi kerak')
      selectedFile.value = null
      photoInput.value.value = ''
      return
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']
    if (!allowedTypes.includes(selectedFile.value.type)) {
      alert('Faqat JPG, PNG, GIF formatdagi rasmlar ruxsat etilgan')
      selectedFile.value = null
      photoInput.value.value = ''
      return
    }
  }
}

const uploadPhoto = async () => {
  if (!selectedFile.value) return

  uploading.value = true
  uploadProgress.value = 0

  try {
    const formData = new FormData()
    formData.append('photo', selectedFile.value)

    // Progress simulation
    const progressInterval = setInterval(() => {
      if (uploadProgress.value < 90) {
        uploadProgress.value += 10
      }
    }, 100)

    await emit('upload', formData)

    clearInterval(progressInterval)
    uploadProgress.value = 100

    // Reset form
    selectedFile.value = null
    if (photoInput.value) {
      photoInput.value.value = ''
    }

  } catch (error) {
    console.error('Upload error:', error)
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

const closeModal = () => {
  selectedFile.value = null
  uploadProgress.value = 0
  if (photoInput.value) {
    photoInput.value.value = ''
  }
  emit('close')
}
</script>
