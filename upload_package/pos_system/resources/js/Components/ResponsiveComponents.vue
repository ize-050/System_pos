<template>
  <div class="responsive-component-wrapper">
    <!-- Mobile Navigation Drawer -->
    <div v-if="showMobileNav" class="fixed inset-0 z-50 lg:hidden">
      <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeMobileNav"></div>
      <div class="fixed left-0 top-0 h-full w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out">
        <div class="p-4 border-b">
          <h2 class="text-lg font-semibold text-gray-900">เมนู</h2>
          <button @click="closeMobileNav" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <nav class="p-4 space-y-2">
          <slot name="mobile-nav-items"></slot>
        </nav>
      </div>
    </div>

    <!-- Responsive Grid Container -->
    <div class="responsive-grid">
      <slot></slot>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 lg:hidden z-40">
      <div class="grid grid-cols-4 gap-1 p-2">
        <slot name="bottom-nav-items"></slot>
      </div>
    </div>

    <!-- Responsive Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl sm:max-w-lg md:max-w-xl lg:max-w-2xl">
          <slot name="modal-content"></slot>
        </div>
      </div>
    </div>

    <!-- Responsive Table Wrapper -->
    <div class="responsive-table-wrapper">
      <div class="overflow-x-auto scrollbar-thin">
        <slot name="table-content"></slot>
      </div>
    </div>

    <!-- Mobile Card Layout -->
    <div class="mobile-card-layout lg:hidden">
      <slot name="mobile-cards"></slot>
    </div>

    <!-- Desktop Table Layout -->
    <div class="desktop-table-layout hidden lg:block">
      <slot name="desktop-table"></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

// Props
const props = defineProps({
  showMobileNav: {
    type: Boolean,
    default: false
  },
  showModal: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close-mobile-nav', 'close-modal'])

// Reactive data
const screenSize = ref('desktop')
const isMobile = ref(false)
const isTablet = ref(false)
const isDesktop = ref(true)

// Methods
const closeMobileNav = () => {
  emit('close-mobile-nav')
}

const closeModal = () => {
  emit('close-modal')
}

const updateScreenSize = () => {
  const width = window.innerWidth
  
  if (width < 768) {
    screenSize.value = 'mobile'
    isMobile.value = true
    isTablet.value = false
    isDesktop.value = false
  } else if (width < 1024) {
    screenSize.value = 'tablet'
    isMobile.value = false
    isTablet.value = true
    isDesktop.value = false
  } else {
    screenSize.value = 'desktop'
    isMobile.value = false
    isTablet.value = false
    isDesktop.value = true
  }
}

// Lifecycle
onMounted(() => {
  updateScreenSize()
  window.addEventListener('resize', updateScreenSize)
})

onUnmounted(() => {
  window.removeEventListener('resize', updateScreenSize)
})

// Expose reactive data
defineExpose({
  screenSize,
  isMobile,
  isTablet,
  isDesktop
})
</script>

<style scoped>
.responsive-grid {
  @apply grid gap-4;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

@media (max-width: 640px) {
  .responsive-grid {
    grid-template-columns: 1fr;
  }
}

@media (min-width: 641px) and (max-width: 1024px) {
  .responsive-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1025px) {
  .responsive-grid {
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  }
}

.responsive-table-wrapper {
  @apply w-full;
}

.mobile-card-layout {
  @apply space-y-4;
}

.desktop-table-layout {
  @apply w-full;
}

/* Custom scrollbar for mobile */
@media (max-width: 768px) {
  .scrollbar-thin::-webkit-scrollbar {
    height: 2px;
  }
}

/* Animation classes */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease-in-out;
}

.slide-enter-from {
  transform: translateX(-100%);
}

.slide-leave-to {
  transform: translateX(-100%);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>