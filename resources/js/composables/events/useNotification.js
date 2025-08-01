import { ref } from 'vue'

export function useNotification() {
    const notification = ref({
        show: false,
        message: '',
        type: 'info'
    })

    const showNotification = (message, type = 'info') => {
        notification.value = {
            show: true,
            message,
            type
        }

        setTimeout(() => {
            notification.value.show = false
        }, 3000)
    }

    const hideNotification = () => {
        notification.value.show = false
    }

    return {
        notification,
        showNotification,
        hideNotification
    }
}
