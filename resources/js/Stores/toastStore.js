import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

// Default toast configuration
const defaultConfig = {
  position: 'top-center',
  autoClose: 3000,
  hideProgressBar: false,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  theme: 'light',
  style: {
    zIndex: 9999,
    marginTop: '60px'
  }
}

// Toast store with different types
const toastStore = {
  success: (message, config = {}) => {
    toast.success(message, { ...defaultConfig, ...config })
  },
  
  error: (message, config = {}) => {
    toast.error(message, { ...defaultConfig, ...config })
  },
  
  warning: (message, config = {}) => {
    toast.warning(message, { ...defaultConfig, ...config })
  },
  
  info: (message, config = {}) => {
    toast.info(message, { ...defaultConfig, ...config })
  },
  
  // CRUD specific toasts
  crud: {
    created: (itemName) => {
      toast.success(`เพิ่ม${itemName}เรียบร้อยแล้ว`, defaultConfig)
    },
    
    updated: (itemName) => {
      toast.success(`แก้ไข${itemName}เรียบร้อยแล้ว`, defaultConfig)
    },
    
    deleted: (itemName) => {
      toast.success(`ลบ${itemName}เรียบร้อยแล้ว`, defaultConfig)
    },
    
    createError: (itemName) => {
      toast.error(`เกิดข้อผิดพลาดในการเพิ่ม${itemName}`, defaultConfig)
    },
    
    updateError: (itemName) => {
      toast.error(`เกิดข้อผิดพลาดในการแก้ไข${itemName}`, defaultConfig)
    },
    
    deleteError: (itemName) => {
      toast.error(`เกิดข้อผิดพลาดในการลบ${itemName}`, defaultConfig)
    }
  }
}

export default toastStore