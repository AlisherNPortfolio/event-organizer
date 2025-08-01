import { onMounted, onUnmounted } from 'vue'

export function useKeyboardShortcuts(callbacks) {
    const handleKeydown = (e) => {
        if (e.key === 'Escape' && callbacks.onEscape) {
            callbacks.onEscape()
        }

        if (e.key === 'Enter' && callbacks.onEnter) {
            callbacks.onEnter()
        }

        // Ctrl/Cmd + S ni oldini olish
        if ((e.ctrlKey || e.metaKey) && e.key === 's' && callbacks.onSave) {
            e.preventDefault()
            callbacks.onSave()
        }

        // Ctrl/Cmd + K ni oldini olish
        if ((e.ctrlKey || e.metaKey) && e.key === 'k' && callbacks.onSearch) {
            e.preventDefault()
            callbacks.onSearch()
        }
    }

    onMounted(() => {
        document.addEventListener('keydown', handleKeydown)
    })

    onUnmounted(() => {
        document.removeEventListener('keydown', handleKeydown)
    })

    return {
        handleKeydown
    }
}
