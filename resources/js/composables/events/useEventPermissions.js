import { computed } from 'vue'

export function useEventPermissions(event, currentUser, isParticipating) {
    const isOrganizer = computed(() => {
        return currentUser.value && event.value.organizer_id === currentUser.value.id
    })

    const isNotOrganizer = computed(() => {
        return currentUser.value && event.value.organizer_id !== currentUser.value.id;
    })

    const canEdit = computed(() => {
        return isOrganizer.value && event.value.status === 'upcoming'
    })

    const canJoin = computed(() => {
        return event.value.status === 'upcoming' &&
               !isFull.value &&
               !isParticipating.value &&
               isNotOrganizer.value
    })

    const canLeave = computed(() => {
        return isParticipating.value && event.value.status === 'upcoming' && isNotOrganizer.value
    })

    const canUploadPhoto = computed(() => {
        return event.value.status === 'ongoing' &&
               (isParticipating.value || isOrganizer.value)
    })

    const canMarkAttendance = computed(() => {
        return isOrganizer.value && event.value.status === 'completed'
    })

    const canViewParticipants = computed(() => {
        return isOrganizer.value
    })

    const isFull = computed(() => {
        return event.value.current_participants >= event.value.max_participants
    })

    const isAuthenticated = computed(() => {
        return !!currentUser.value;
    })

    return {
        isOrganizer,
        canEdit,
        canJoin,
        canLeave,
        canUploadPhoto,
        canMarkAttendance,
        canViewParticipants,
        isFull,
        isNotOrganizer,
        isAuthenticated
    }
}
