<template>
  <AppLayout title="แก้ไขหมวดหมู่สินค้า">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        แก้ไขหมวดหมู่สินค้า
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">แก้ไขหมวดหมู่สินค้า</h1>
              <div class="flex space-x-3">
                <Link :href="route('categories.show', category.id)" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  View
                </Link>
                <Link :href="route('categories.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Back to Categories
                </Link>
              </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category Name -->
                <div class="md:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700">Category Name *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    id="name"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    required
                  />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                  <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    v-model="form.description"
                    id="description"
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                  ></textarea>
                  <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                  <p class="mt-1 text-sm text-gray-500">Optional. Provide a brief description of this category.</p>
                </div>
              </div>

              <!-- Image Section -->
              <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Category Image</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Current Image -->
                  <div v-if="category.image_url || imagePreview">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ imagePreview ? 'New Image Preview' : 'Current Image' }}</label>
                    <div class="relative">
                      <img :src="imagePreview || category.image_url" alt="Category image" class="h-32 w-32 rounded-lg object-cover border border-gray-300">
                      <button
                        v-if="imagePreview"
                        @click="removeImage"
                        type="button"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Image Upload -->
                  <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">{{ category.image_url ? 'Replace Image' : 'Upload Image' }} (Optional)</label>
                    <input
                      id="image"
                      @change="handleImageUpload"
                      type="file"
                      accept="image/*"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none"
                style="border-color: #E2E8F0;"
                onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                      :class="{ 'border-red-500': errors.image }"
                    />
                    <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
                    <p class="mt-1 text-sm text-gray-500">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                    
                    <!-- Remove current image option -->
                    <div v-if="category.image_url && !imagePreview" class="mt-2">
                      <label class="flex items-center">
                        <input
                          v-model="form.remove_image"
                          type="checkbox"
                          class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-sm text-red-600">Remove current image</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Status -->
              <div class="border-t pt-6">
                <div class="flex items-center">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    id="is_active"
                    class="h-4 w-4 border-gray-300 rounded transition ease-in-out duration-150"
                    style="color: #6B7B47; border-color: #E2E8F0;"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.boxShadow='none'"
                  />
                  <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active Category
                  </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Active categories will be available for product assignment.</p>
              </div>

              <!-- Submit Buttons -->
              <div class="flex justify-end space-x-3 pt-6 border-t">
                <Link :href="route('categories.show', category.id)" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150"
                  style="background-color: #6B7B47; border-color: #6B7B47;"
                  onmouseover="this.style.backgroundColor='#8A9B5A'"
                  onmouseout="this.style.backgroundColor='#6B7B47'"
                >
                  <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ processing ? 'กำลังอัปเดต...' : 'อัปเดตหมวดหมู่' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

export default {
  components: {
    Head,
    Link,
  },
  props: {
    category: Object,
    errors: Object,
  },
  setup(props) {
    const { data: form, put, processing } = useForm({
      name: props.category.name,
      description: props.category.description || '',
      image: null,
      remove_image: false,
      is_active: props.category.is_active,
    })

    const imagePreview = ref(null)

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        form.image = file
        form.remove_image = false // Reset remove flag if new image is selected
        
        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
          imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }

    const removeImage = () => {
      form.image = null
      imagePreview.value = null
      // Clear the file input
      const fileInput = document.getElementById('image')
      if (fileInput) {
        fileInput.value = ''
      }
    }

    const submit = () => {
      put(route('categories.update', props.category.id), {
        onSuccess: () => {
          toast.success('อัปเดตหมวดหมู่สำเร็จ!', {
            position: 'top-right',
            autoClose: 3000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
          })
        },
        onError: (errors) => {
          toast.error('เกิดข้อผิดพลาดในการอัปเดตหมวดหมู่', {
            position: 'top-right',
            autoClose: 3000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
          })
        }
      })
    }

    return {
      form,
      processing,
      imagePreview,
      handleImageUpload,
      removeImage,
      submit,
    }
  },
}
</script>