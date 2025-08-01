export function useShareEvent() {
    const shareEvent = async (event) => {
        const shareData = {
            title: event.title,
            text: event.description ? event.description.substring(0, 100) + '...' : '',
            url: window.location.href
        }

        if (navigator.share) {
            try {
                await navigator.share(shareData)
                return { success: true, method: 'native' }
            } catch (error) {
                if (error.name === 'AbortError') {
                    return { success: false, error: 'Bekor qilindi' }
                }
                throw error
            }
        } else {
            // Fallback: copy to clipboard
            try {
                await navigator.clipboard.writeText(window.location.href)
                return { success: true, method: 'clipboard' }
            } catch (error) {
                throw new Error('Havola nusxalanmadi')
            }
        }
    }

    return {
        shareEvent
    }
}
