import { ref } from 'vue'

export function useEvents() {
  const events = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchEvents = async (params = {}) => {
    loading.value = true
    error.value = null

    try {
      const queryString = new URLSearchParams(params).toString()
      const response = await fetch(`/api/v1/events?${queryString}`)
      const data = await response.json()

      if (response.ok) {
        events.value = data.data
        return data
      } else {
        error.value = data.message
        return null
      }
    } catch (err) {
      error.value = 'Xatolik yuz berdi'
      return null
    } finally {
      loading.value = false
    }
  }

  const createEvent = async (eventData) => {
    loading.value = true
    error.value = null

    try {
      const token = localStorage.getItem('token')
      const response = await fetch('/api/v1/events', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: eventData // FormData
      })

      const data = await response.json()

      if (response.ok) {
        return { success: true, data }
      } else {
        error.value = data.message
        return { success: false, error: data.message, errors: data.errors }
      }
    } catch (err) {
      error.value = 'Xatolik yuz berdi'
      return { success: false, error: 'Xatolik yuz berdi' }
    } finally {
      loading.value = false
    }
  }

  const joinEvent = async (eventId) => {
    try {
      const token = localStorage.getItem('token')
      const response = await fetch(`/api/v1/events/${eventId}/join`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
      })

      const data = await response.json()
      return { success: response.ok, message: data.message }
    } catch (error) {
      return { success: false, message: 'Xatolik yuz berdi' }
    }
  }

  return {
    events,
    loading,
    error,
    fetchEvents,
    createEvent,
    joinEvent
  }
}
