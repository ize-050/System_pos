<template>
  <AppLayout title="Receipt Preview">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Receipt Preview</h2>
    </template>
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6 mb-4">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Receipt Preview</h2>
            <div class="flex gap-2">
              <button @click="print" class="px-4 py-2 bg-blue-600 text-white rounded-md">Print</button>
              <button @click="downloadPDF" class="px-4 py-2 bg-green-600 text-white rounded-md">Download PDF</button>
              <button @click="showEmailModal = true" class="px-4 py-2 bg-purple-600 text-white rounded-md">Send Email</button>
            </div>
          </div>

          <!-- Receipt Content -->
          <div id="receipt-content" :class="getReceiptContainerClass">
            <A4Template v-if="settings.printer_type === 'a4'" :sale="sale" :settings="settings" :qrCode="qrCode" :barcode="barcode" />
            <DotMatrixTemplate v-else-if="settings.printer_type === 'dot_matrix'" :sale="sale" :settings="settings" />
            <ThermalTemplate v-else :sale="sale" :settings="settings" :qrCode="qrCode" :barcode="barcode" />
          </div>
        </div>
      </div>
    </div>

    <!-- Email Modal -->
    <div v-if="showEmailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-bold mb-4">Send Receipt via Email</h3>
        <input v-model="emailForm.email" type="email" placeholder="Email address" required class="w-full rounded-md border-gray-300 mb-4" />
        <div class="flex justify-end gap-2">
          <button @click="showEmailModal = false" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
          <button @click="sendEmail" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Send</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import A4Template from './Templates/A4Template.vue';
import ThermalTemplate from './Templates/ThermalTemplate.vue';
import DotMatrixTemplate from './Templates/DotMatrixTemplate.vue';

const props = defineProps({ sale: Object, settings: Object, qrCode: String, barcode: String });

const showEmailModal = ref(false);
const emailForm = ref({ email: props.sale.customer?.email || '' });

// Compute container class based on printer type
const getReceiptContainerClass = computed(() => {
  if (props.settings.printer_type === 'a4') {
    return 'border p-8'
  } else if (props.settings.printer_type === 'dot_matrix') {
    return 'flex justify-center overflow-x-auto'
  } else {
    return 'flex justify-center'
  }
})

const print = () => window.print();
const downloadPDF = () => window.location.href = route('receipts.download-pdf', props.sale.id);
const sendEmail = () => {
  router.post(route('receipts.send-email', props.sale.id), emailForm.value);
  showEmailModal.value = false;
};
</script>
