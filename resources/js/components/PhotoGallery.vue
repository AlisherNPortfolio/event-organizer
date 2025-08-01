<template>
  <div class="space-y-4">
    <div class="flex justify-between items-center">
      <h3 class="text-lg font-medium text-gray-900">Tadbir rasmlari</h3>
      <button
        v-if="canUpload"
        @click="showUploadModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
        Rasm yuklash
      </button>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-2 text-gray-500">Rasmlar yuklanmoqda...</p>
    </div>

    <div v-else-if="error" class="text-center py-8 text-red-500">
      <p>{{ error }}</p>
      <button @click="loadPhotos" class="mt-2 text-blue-600 hover:text-blue-500">
        Qayta urinish
      </button>
    </div>

    <div v-else-if="photos.length === 0" class="text-center py-8 text-gray-500">
      Hozircha rasmlar yo'q
    </div>

    <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div
        v-for="photo in photos"
        :key="photo.id"
        class="relative group cursor-pointer"
        @click="openLightbox(photo)">
        <img
          :src="photo.full_url || `/storage/${photo.path}`"
          :alt="photo.uploaded_by.name"
          class="w-full h-32 object-cover rounded-lg transition-transform group-hover:scale-105"
          @error="handleImageError($event)">
        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 rounded-b-lg">
          <p class="text-xs">{{ photo.uploaded_by.name }}</p>
          <p class="text-xs opacity-75">{{ formatDate(photo.uploaded_at) }}</p>
        </div>

        <!-- Delete button for photo owner or event organizer -->
        <button
          v-if="canDeletePhoto(photo)"
          @click.stop="deletePhoto(photo)"
          class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
          ×
        </button>
      </div>
    </div>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 text-center">Rasm yuklash</h3>
          <form @submit.prevent="uploadPhoto" class="mt-4 space-y-4" enctype="multipart/form-data">
            <div>
              <input
                ref="photoInput"
                type="file"
                accept="image/*"
                required
                @change="handlePhotoSelect"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md">
              <div v-if="uploadError" class="mt-1 text-sm text-red-600">{{ uploadError }}</div>
            </div>

            <!-- Photo Preview -->
            <div v-if="photoPreview" class="mt-4">
              <img :src="photoPreview" alt="Preview" class="w-full h-32 object-cover rounded">
            </div>

            <div class="flex space-x-3">
              <button
                type="button"
                @click="closeUploadModal"
                class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                Bekor qilish
              </button>
              <button
                type="submit"
                :disabled="uploading || !selectedPhoto"
                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50">
                {{ uploading ? 'Yuklanmoqda...' : 'Yuklash' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Lightbox -->
    <div v-if="lightboxPhoto" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" @click="closeLightbox">
      <div class="max-w-4xl max-h-full p-4">
        <img
          :src="lightboxPhoto.full_url || `/storage/${lightboxPhoto.path}`"
          :alt="lightboxPhoto.uploaded_by.name"
          class="max-w-full max-h-full object-contain">
        <div class="text-white text-center mt-4">
          <p>{{ lightboxPhoto.uploaded_by.name }}</p>
          <p class="text-sm opacity-75">{{ formatDate(lightboxPhoto.uploaded_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  eventId: {
    type: String,
    required: true
  },
  canUpload: {
    type: Boolean,
    default: false
  },
  currentUserId: {
    type: String,
    default: null
  },
  isEventOrganizer: {
    type: Boolean,
    default: false
  },
  csrfToken: {
    type: String,
    default: ""
  },
  tempBearerToken: {
    type: String,
    default: ""
  }
})

const photos = ref([])
const loading = ref(false)
const error = ref('')
const showUploadModal = ref(false)
const lightboxPhoto = ref(null)
const selectedPhoto = ref(null)
const photoPreview = ref(null)
const uploading = ref(false)
const uploadError = ref('')
const photoInput = ref(null)

onMounted(() => {
  loadPhotos()
})

const loadPhotos = async () => {
  loading.value = true
  error.value = ''

  try {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };

        if (props.csrfToken) {
            headers['X-CSRF-TOKEN'] = props.csrfToken;
        }

        if (props.tempBearerToken && props.tempBearerToken != "") {
            headers['Authorization'] = props.tempBearerToken;
        }

        const response = await fetch(`/api/v1/events/${props.eventId}/photos`, {
            method: 'GET',
            headers: headers
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }

    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error('Response is not JSON')
    }

    const data = await response.json()

    if (data.success) {
      photos.value = data.photos || []
    } else {
      error.value = data.error || 'Failed to load photos'
    }
  } catch (err) {
    console.error('Error loading photos:', err)
    error.value = 'Rasmlarni yuklashda xatolik yuz berdi'
  } finally {
    loading.value = false
  }
}

const handlePhotoSelect = (event) => {
  const file = event.target.files[0]
  uploadError.value = ''

  if (!file) {
    selectedPhoto.value = null
    photoPreview.value = null
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    uploadError.value = 'Rasm hajmi 2MB dan kichik bo\'lishi kerak'
    return
  }

  selectedPhoto.value = file

  const reader = new FileReader()
  reader.onload = (e) => {
    photoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const uploadPhoto = async () => {
  if (!selectedPhoto.value) return

  uploading.value = true
  uploadError.value = ''

  try {
    const formData = new FormData()
    formData.append('photo', selectedPhoto.value)

    const csrfToken = props.csrfToken
    const token = props.tempBearerToken;


    const headers = {};
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken
    }

    const response = await fetch(`/api/v1/events/${props.eventId}/photos`, {
      method: 'POST',
      headers: headers,
      body: formData
    })

    const data = await response.json()

    if (response.ok && data.success) {
      closeUploadModal()
      loadPhotos()
    } else {
      uploadError.value = data.error || 'Xatolik yuz berdi'
    }
  } catch (error) {
    console.error('Upload error:', error)
    uploadError.value = 'Xatolik yuz berdi'
  } finally {
    uploading.value = false
  }
}

const deletePhoto = async (photo) => {
  if (!confirm('Rasmni o\'chirishni tasdiqlaysizmi?')) {
    return
  }

  try {
    const token = localStorage.getItem('token')
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const headers = {}
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken
    }

    const response = await fetch(`/api/v1/events/${props.eventId}/photos/${photo.id}`, {
      method: 'DELETE',
      headers
    })

    const data = await response.json()

    if (response.ok && data.success) {
      loadPhotos()
    } else {
      alert(data.error || 'Xatolik yuz berdi')
    }
  } catch (error) {
    console.error('Delete error:', error)
    alert('Xatolik yuz berdi')
  }
}

const canDeletePhoto = (photo) => {
  return props.currentUserId && (
    photo.uploaded_by.id === props.currentUserId ||
    props.isEventOrganizer
  )
}

const closeUploadModal = () => {
  showUploadModal.value = false
  selectedPhoto.value = null
  photoPreview.value = null
  uploadError.value = ''
  if (photoInput.value) {
    photoInput.value.value = ''
  }
}

const openLightbox = (photo) => {
  lightboxPhoto.value = photo
}

const closeLightbox = () => {
  lightboxPhoto.value = null
}

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src)
  event.target.src = '/images/placeholder.jpg' // Fallback image
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('uz-UZ', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
