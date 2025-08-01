import { ref } from 'vue'

export function useModal() {
    const showPhotoModal = ref(false)
    const showParticipantsModal = ref(false)
    const showAttendanceModal = ref(false)

    const openPhotoModal = () => {
        closeAllModals() // Avval barcha modallarni yopish
        showPhotoModal.value = true
    }

    const openParticipantsModal = () => {
        closeAllModals() // Avval barcha modallarni yopish
        showParticipantsModal.value = true
    }

    const openAttendanceModal = () => {
        closeAllModals() // Avval barcha modallarni yopish
        showAttendanceModal.value = true
    }

    const closePhotoModal = () => {
        showPhotoModal.value = false
    }

    const closeParticipantsModal = () => {
        showParticipantsModal.value = false
    }

    const closeAttendanceModal = () => {
        showAttendanceModal.value = false
    }

    const closeAllModals = () => {
        showPhotoModal.value = false
        showParticipantsModal.value = false
        showAttendanceModal.value = false
    }

    return {
        // State
        showPhotoModal,
        showParticipantsModal,
        showAttendanceModal,

        // Actions
        openPhotoModal,
        openParticipantsModal,
        openAttendanceModal,
        closePhotoModal,
        closeParticipantsModal,
        closeAttendanceModal,
        closeAllModals
    }
}
