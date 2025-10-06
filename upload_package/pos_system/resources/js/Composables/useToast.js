import { ref, createApp } from 'vue'
import Toast from '@/Components/Toast.vue'

const toasts = ref([])

export function useToast() {
  const showToast = (type, title, message = '', duration = 5000) => {
    const toastId = Date.now()
    
    // Create toast container if it doesn't exist
    let container = document.getElementById('toast-container')
    if (!container) {
      container = document.createElement('div')
      container.id = 'toast-container'
      container.className = 'fixed top-4 right-4 z-50 space-y-2'
      document.body.appendChild(container)
    }

    // Create toast element
    const toastElement = document.createElement('div')
    toastElement.id = `toast-${toastId}`
    container.appendChild(toastElement)

    // Create Vue app for the toast
    const toastApp = createApp(Toast, {
      type,
      title,
      message,
      duration,
      onClose: () => {
        toastApp.unmount()
        if (toastElement.parentNode) {
          toastElement.parentNode.removeChild(toastElement)
        }
        // Remove container if no more toasts
        if (container.children.length === 0) {
          document.body.removeChild(container)
        }
      }
    })

    toastApp.mount(toastElement)

    return toastId
  }

  const success = (title, message = '') => {
    return showToast('success', title, message)
  }

  const error = (title, message = '') => {
    return showToast('error', title, message)
  }

  const info = (title, message = '') => {
    return showToast('info', title, message)
  }

  return {
    showToast,
    success,
    error,
    info
  }
}