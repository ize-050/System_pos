<template>
  <AppLayout title="รายละเอียดซัพพลายเออร์">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        รายละเอียดซัพพลายเออร์
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">{{ supplier.name }}</h1>
              <div class="flex space-x-3">
                <Link :href="route('suppliers.edit', supplier.id)" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                  แก้ไข
                </Link>
                <Link :href="route('suppliers.index')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 ease-in-out">
                  กลับ
                </Link>
              </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
              <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                    :class="supplier.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ supplier.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
              </span>
            </div>

            <!-- Supplier Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500">ชื่อซัพพลายเออร์</h3>
                  <p class="mt-1 text-lg text-gray-900">{{ supplier.name }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">ผู้ติดต่อ</h3>
                  <p class="mt-1 text-lg text-gray-900">{{ supplier.contact_person || '-' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">เบอร์โทรศัพท์</h3>
                  <p class="mt-1 text-lg text-gray-900">{{ supplier.phone || '-' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">อีเมล</h3>
                  <p class="mt-1 text-lg text-gray-900">{{ supplier.email || '-' }}</p>
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500">เลขประจำตัวผู้เสียภาษี</h3>
                  <p class="mt-1 text-lg text-gray-900">{{ supplier.tax_id || '-' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">ที่อยู่</h3>
                  <p class="mt-1 text-lg text-gray-900 whitespace-pre-line">{{ supplier.address || '-' }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">หมายเหตุ</h3>
                  <p class="mt-1 text-lg text-gray-900 whitespace-pre-line">{{ supplier.notes || '-' }}</p>
                </div>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="mt-8 pt-6 border-t border-gray-200">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                <div>
                  <span class="font-medium">สร้างเมื่อ:</span>
                  {{ formatDate(supplier.created_at) }}
                </div>
                <div>
                  <span class="font-medium">อัปเดตล่าสุด:</span>
                  {{ formatDate(supplier.updated_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  supplier: Object,
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>
