import { computed } from 'vue'

export function useEventFormatters() {
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

    const formatPrice = (price) => {
        return new Intl.NumberFormat('uz-UZ').format(price)
    }

    const getStatusBadgeClass = (status) => {
        switch (status) {
            case 'upcoming':
                return 'bg-blue-100 text-blue-800'
            case 'ongoing':
                return 'bg-green-100 text-green-800'
            case 'completed':
                return 'bg-gray-100 text-gray-800'
            case 'cancelled':
                return 'bg-red-100 text-red-800'
            default:
                return 'bg-gray-100 text-gray-800'
        }
    }

    const getStatusText = (status) => {
        switch (status) {
            case 'upcoming':
                return 'Kutilmoqda'
            case 'ongoing':
                return 'Davom etmoqda'
            case 'completed':
                return 'Tugallangan'
            case 'cancelled':
                return 'Bekor qilingan'
            default:
                return 'Noma\'lum'
        }
    }

    const formatDescription = (description) => {
        return description ? description.replace(/\n/g, '<br>') : ''
    }

    const calculateParticipantProgress = (current, max) => {
        if (!max) return 0
        return Math.round((current / max) * 100)
    }

    return {
        formatDate,
        formatPrice,
        getStatusBadgeClass,
        getStatusText,
        formatDescription,
        calculateParticipantProgress
    }
}
