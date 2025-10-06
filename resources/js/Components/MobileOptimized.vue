<template>
  <div class="mobile-optimized-wrapper">
    <!-- Mobile Product Card -->
    <div v-if="type === 'product-card'" class="mobile-product-card bg-white rounded-lg shadow-md p-4 mb-4">
      <div class="flex items-center space-x-4">
        <div class="flex-shrink-0">
          <img :src="data.image || '/images/no-image.png'" :alt="data.name" class="w-16 h-16 rounded-lg object-cover">
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="text-sm font-medium text-gray-900 truncate">{{ data.name }}</h3>
          <p class="text-sm text-gray-500">{{ data.category }}</p>
          <div class="flex items-center justify-between mt-2">
            <span class="text-lg font-bold text-primary-600">฿{{ formatPrice(data.price) }}</span>
            <span class="text-xs px-2 py-1 rounded-full" :class="stockStatusClass(data.stock)">{{ data.stock }} ชิ้น</span>
          </div>
        </div>
        <div class="flex-shrink-0">
          <button @click="$emit('action', data)" class="btn btn-primary btn-sm">
            <slot name="action-text">เลือก</slot>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Sale Item -->
    <div v-else-if="type === 'sale-item'" class="mobile-sale-item bg-white rounded-lg shadow-md p-4 mb-4">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <h3 class="text-sm font-medium text-gray-900">{{ data.product_name }}</h3>
          <div class="flex items-center space-x-4 mt-1">
            <span class="text-sm text-gray-500">฿{{ formatPrice(data.price) }}</span>
            <div class="flex items-center space-x-2">
              <button @click="updateQuantity(data.id, -1)" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
              </button>
              <span class="text-sm font-medium w-8 text-center">{{ data.quantity }}</span>
              <button @click="updateQuantity(data.id, 1)" class="w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        <div class="text-right">
          <div class="text-lg font-bold text-gray-900">฿{{ formatPrice(data.total) }}</div>
          <button @click="$emit('remove', data.id)" class="text-red-500 text-sm mt-1">
            ลบ
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Summary Card -->
    <div v-else-if="type === 'summary-card'" class="mobile-summary-card bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg shadow-lg p-4 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-medium opacity-90">{{ data.title }}</h3>
          <div class="text-2xl font-bold mt-1">{{ data.value }}</div>
          <div v-if="data.change" class="flex items-center mt-2">
            <svg v-if="data.change > 0" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
            </svg>
            <span class="text-sm">{{ Math.abs(data.change) }}%</span>
          </div>
        </div>
        <div class="text-right">
          <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
            <slot name="icon"></slot>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Action Sheet -->
    <div v-else-if="type === 'action-sheet'" class="mobile-action-sheet">
      <div class="fixed inset-x-0 bottom-0 z-50">
        <div class="bg-white rounded-t-2xl shadow-xl border-t border-gray-200">
          <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">{{ data.title }}</h3>
              <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
            <button
              v-for="action in data.actions"
              :key="action.id"
              @click="$emit('action', action)"
              class="w-full flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
              :class="action.class || ''"
            >
              <div v-if="action.icon" class="mr-3">
                <component :is="action.icon" class="w-5 h-5" />
              </div>
              <div class="text-left">
                <div class="font-medium">{{ action.title }}</div>
                <div v-if="action.description" class="text-sm text-gray-500">{{ action.description }}</div>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Search Bar -->
    <div v-else-if="type === 'search-bar'" class="mobile-search-bar sticky top-0 z-30 bg-white border-b border-gray-200 p-4">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <input
          type="text"
          :value="data.value"
          @input="$emit('input', $event.target.value)"
          class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
          :placeholder="data.placeholder || 'ค้นหา...'"
        >
        <div v-if="data.value" class="absolute inset-y-0 right-0 pr-3 flex items-center">
          <button @click="$emit('clear')" class="text-gray-400 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Filter Chips -->
    <div v-else-if="type === 'filter-chips'" class="mobile-filter-chips p-4 bg-gray-50">
      <div class="flex space-x-2 overflow-x-auto scrollbar-hide pb-2">
        <button
          v-for="filter in data.filters"
          :key="filter.id"
          @click="$emit('filter', filter)"
          class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors"
          :class="filter.active ? 'bg-primary-500 text-white' : 'bg-white text-gray-700 border border-gray-300'"
        >
          {{ filter.label }}
          <span v-if="filter.count" class="ml-2 text-xs opacity-75">({{ filter.count }})</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => [
      'product-card',
      'sale-item',
      'summary-card',
      'action-sheet',
      'search-bar',
      'filter-chips'
    ].includes(value)
  },
  data: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits([
  'action',
  'remove',
  'close',
  'input',
  'clear',
  'filter',
  'quantity-update'
])

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('th-TH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

const stockStatusClass = (stock) => {
  if (stock <= 0) return 'bg-red-100 text-red-800'
  if (stock <= 10) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const updateQuantity = (id, change) => {
  emit('quantity-update', { id, change })
}
</script>

<style scoped>
.mobile-optimized-wrapper {
  @apply w-full;
}

.mobile-product-card {
  @apply transform transition-all duration-200;
}

.mobile-product-card:active {
  @apply scale-95;
}

.mobile-sale-item {
  @apply border-l-4 border-primary-500;
}

.mobile-summary-card {
  @apply transform transition-all duration-200;
}

.mobile-summary-card:hover {
  @apply scale-105;
}

.mobile-action-sheet {
  @apply animate-slide-up;
}

.mobile-search-bar input:focus {
  @apply shadow-lg;
}

.mobile-filter-chips {
  @apply -mx-4;
}

.btn-sm {
  @apply px-3 py-1.5 text-xs;
}

/* Touch-friendly sizing */
@media (max-width: 768px) {
  .mobile-product-card {
    @apply p-3;
  }

  .mobile-sale-item {
    @apply p-3;
  }

  button {
    @apply min-h-11;
  }

  input {
    @apply min-h-12;
  }
}

/* Animation for action sheet */
@keyframes slide-up {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slide-up 0.3s ease-out;
}
</style>
