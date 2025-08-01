import { ref } from 'vue'

export function useFileUpload() {
    const selectedFile = ref(null)
    const uploading = ref(false)
    const uploadProgress = ref(0)

    const selectFile = (file) => {
        selectedFile.value = file
    }

    const clearFile = () => {
        selectedFile.value = null
        uploadProgress.value = 0
    }

    const validateFile = (file, options = {}) => {
        const {
            maxSize = 2 * 1024 * 1024, // 2MB
            allowedTypes = ['image/jpeg', 'image/png', 'image/gif']
        } = options

        if (file.size > maxSize) {
            throw new Error('Fayl hajmi juda katta')
        }

        if (!allowedTypes.includes(file.type)) {
            throw new Error('Fayl turi noto\'g\'ri')
        }

        return true
    }

    const uploadFile = async (file, uploadFunction) => {
        try {
            uploading.value = true
            uploadProgress.value = 0

            validateFile(file)

            const formData = new FormData()
            formData.append('photo', file)

            // Progress simulation (real progress tracking needs XMLHttpRequest)
            const progressInterval = setInterval(() => {
                if (uploadProgress.value < 90) {
                    uploadProgress.value += 10
                }
            }, 100)

            const result = await uploadFunction(formData)

            clearInterval(progressInterval)
            uploadProgress.value = 100

            return result
        } finally {
            uploading.value = false
        }
    }

    return {
        selectedFile,
        uploading,
        uploadProgress,
        selectFile,
        clearFile,
        validateFile,
        uploadFile
    }
}
