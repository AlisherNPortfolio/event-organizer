import { ref } from 'vue'

const user = ref(null)
const isAuthenticated = ref(false)

export function useAuth() {
  const login = async (credentials) => {
    try {
      const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(credentials)
      })

      const data = await response.json()

      if (response.ok) {
        user.value = data.user
        isAuthenticated.value = true
        localStorage.setItem('token', data.token)
        return { success: true, data }
      } else {
        return { success: false, error: data.message }
      }
    } catch (error) {
      return { success: false, error: 'Xatolik yuz berdi' }
    }
  }

  const logout = async () => {
    try {
      await fetch('/api/auth/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
      })
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      isAuthenticated.value = false
      localStorage.removeItem('token')
    }
  }

  const checkAuth = async () => {
    const token = localStorage.getItem('token')
    if (!token) return false

    try {
      const response = await fetch('/api/auth/user', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })

      if (response.ok) {
        const data = await response.json()
        user.value = data.user
        isAuthenticated.value = true
        return true
      } else {
        localStorage.removeItem('token')
        return false
      }
    } catch (error) {
      localStorage.removeItem('token')
      return false
    }
  }

  return {
    user,
    isAuthenticated,
    login,
    logout,
    checkAuth
  }
}
