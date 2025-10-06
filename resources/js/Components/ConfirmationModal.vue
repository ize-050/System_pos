<template>
  <Modal :show="show" @close="$emit('close')" max-width="md">
    <div class="p-6">
      <div class="flex items-center">
        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
          <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
      </div>

      <div class="mt-3 text-center sm:mt-5">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ title || 'ยืนยันการดำเนินการ' }}
        </h3>
        <div class="mt-2">
          <p class="text-sm text-gray-500">
            {{ message || 'คุณแน่ใจหรือไม่ที่จะดำเนินการนี้? การดำเนินการนี้ไม่สามารถยกเลิกได้' }}
          </p>
        </div>
      </div>

      <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
        <button
          type="button"
          @click="$emit('confirm')"
          class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm"
        >
          {{ confirmText || 'ยืนยัน' }}
        </button>
        <button
          type="button"
          @click="$emit('close')"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
        >
          {{ cancelText || 'ยกเลิก' }}
        </button>
      </div>
    </div>
  </Modal>
</template>

<script>
import Modal from '@/Components/Modal.vue'

export default {
  name: 'ConfirmationModal',
  components: {
    Modal,
  },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: '',
    },
    message: {
      type: String,
      default: '',
    },
    confirmText: {
      type: String,
      default: '',
    },
    cancelText: {
      type: String,
      default: '',
    },
  },
  emits: ['close', 'confirm'],
}
</script>
