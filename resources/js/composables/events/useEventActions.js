import { ref, computed } from 'vue'

export function useEventActions(props) {
    const loading = ref(false)
    const error = ref(null)

    const apiRequest = async (url, options = {}) => {
        loading.value = true
        error.value = null

        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': props.csrfToken,
                    'Authorization': `Bearer ${props.tempBearerToken}`,
                    ...options.headers
                },
                ...options
            })

            const data = await response.json()

            if (!response.ok) {
                throw new Error(data.message || 'Xatolik yuz berdi')
            }

            return data
        } catch (err) {
            error.value = err.message
            throw err
        } finally {
            loading.value = false
        }
    }

    const joinEvent = async (eventId) => {
        return await apiRequest(`/api/v1/events/${eventId}/join`, {
            method: 'POST'
        })
    }

    const leaveEvent = async (eventId) => {
        return await apiRequest(`/api/v1/events/${eventId}/leave`, {
            method: 'DELETE'
        })
    }

    const getParticipants = async (eventId) => {
        return await apiRequest(`/api/v1/events/${eventId}/participants`)
    }

    const markAttendance = async (eventId, participantId, attended) => {
        return await apiRequest(`/api/v1/events/${eventId}/attendance`, {
            method: 'POST',
            body: JSON.stringify({
                participant_id: participantId,
                attended: attended
            })
        })
    }

    const uploadPhoto = async (eventId, formData) => {
        return await apiRequest(`/api/v1/events/${eventId}/photos`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': props.csrfToken,
                'Authorization': `Bearer ${props.tempBearerToken}`
                // Content-Type ni o'chirish - FormData uchun
            }
        })
    }

    return {
        loading,
        error,
        joinEvent,
        leaveEvent,
        getParticipants,
        markAttendance,
        uploadPhoto
    }
}
