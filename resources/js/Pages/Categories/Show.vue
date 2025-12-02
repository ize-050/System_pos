<template>
  <AppLayout title="Category Details">
    <template #header>
      <div class="flex items-center justify-between">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight">Category Details</h1>
        <div class="flex space-x-3">
          <Link :href="route('categories.edit', category.id)" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
                style="background-color: #6B7B47; border-color: #6B7B47;"
                onmouseover="this.style.backgroundColor='#8A9B5A'"
                onmouseout="this.style.backgroundColor='#6B7B47'">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
          </Link>
          <Link :href="route('categories.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Categories
          </Link>
        </div>
      </div>
    </template>

    <Head title="Category Details" />

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Category Overview Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-8">
              <div class="flex items-start space-x-6">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ category.name }}</h2>
                    <span :class="getStatusClass(category.is_active)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ category.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  <p v-if="category.description" class="text-gray-600 mb-3">{{ category.description }}</p>
                  <p v-else class="text-gray-400 italic mb-3">No description provided</p>
                  <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                      </svg>
                      {{ category.products_count || 0 }} Products
                    </span>
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 13a2 2 0 002 2h6a2 2 0 002-2L16 7m-6 0h6"></path>
                      </svg>
                      ID: {{ category.id }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Category Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
              <!-- Basic Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Basic Information
                </h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Category Name</dt>
                    <dd class="text-sm text-gray-900 font-semibold">{{ category.name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="text-sm text-gray-900">{{ category.description || 'No description provided' }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd>
                      <span :class="getStatusClass(category.is_active)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ category.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Statistics -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                  Statistics
                </h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Total Products</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ category.products_count || 0 }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Active Products</dt>
                    <dd class="text-lg font-semibold text-green-600">{{ category.active_products_count || 0 }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Inactive Products</dt>
                    <dd class="text-lg font-semibold text-red-600">{{ (category.products_count || 0) - (category.active_products_count || 0) }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Timeline
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Created</dt>
                  <dd class="text-sm text-gray-900">{{ formatDate(category.created_at) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                  <dd class="text-sm text-gray-900">{{ formatDate(category.updated_at) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Created By</dt>
                  <dd class="text-sm text-gray-900">{{ category.created_by?.name || 'System' }}</dd>
                </div>
              </div>
            </div>

            <!-- Products in this Category -->
            <div v-if="category.products && category.products.length > 0" class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Products in this Category ({{ category.products.length }})
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="product in category.products.slice(0, 6)" :key="product.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                  <div class="flex items-center space-x-3">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                      <p class="text-sm text-gray-500">{{ formatPrice(product.selling_price) }}</p>
                      <span :class="getStockStatusClass(product.stock_quantity)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                        {{ getStockStatus(product.stock_quantity) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="category.products.length > 6" class="mt-4 text-center">
                <Link :href="route('products.index', { category: category.id })" class="text-green-600 hover:text-green-800 text-sm font-medium">
                  View all {{ category.products.length }} products →
                </Link>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
              <button
                @click="deleteCategory"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Category
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
})

const category = computed(() => props.category)

const getStatusClass = (isActive) =>
  isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'

const getStockStatus = (quantity) => {
  if (quantity <= 0) return 'Out of Stock'
  if (quantity <= 10) return 'Low Stock'
  return 'In Stock'
}

const getStockStatusClass = (quantity) => {
  if (quantity <= 0) return 'bg-red-100 text-red-800'
  if (quantity <= 10) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const formatPrice = (price) =>
  new Intl.NumberFormat('th-TH', {
    style: 'currency',
    currency: 'THB',
  }).format(price)

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const deleteCategory = () => {
  if ((category.value?.products_count ?? 0) > 0) {
    alert('Cannot delete category that has products. Please move or delete all products first.')
    return
  }

  if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
    router.delete(route('categories.destroy', category.value.id))
  }
}
</script>