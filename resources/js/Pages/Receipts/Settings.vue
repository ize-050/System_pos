<template>
  <AppLayout title="Receipt Settings">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Receipt Settings</h2>
    </template>
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h2 class="text-2xl font-bold mb-6">Receipt Settings</h2>
          <form @submit.prevent="submit">
            <!-- Shop Info -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold mb-4">Shop Information</h3>
              <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium">Shop Name *</label><input v-model="form.shop_name" required class="mt-1 block w-full rounded-md border-gray-300" /></div>
                <div><label class="block text-sm font-medium">Tax ID</label><input v-model="form.tax_id" class="mt-1 block w-full rounded-md border-gray-300" /></div>
                <div class="col-span-2"><label class="block text-sm font-medium">Address</label><textarea v-model="form.shop_address" rows="2" class="mt-1 block w-full rounded-md border-gray-300"></textarea></div>
                <div><label class="block text-sm font-medium">Phone</label><input v-model="form.shop_phone" class="mt-1 block w-full rounded-md border-gray-300" /></div>
                <div><label class="block text-sm font-medium">Email</label><input v-model="form.shop_email" type="email" class="mt-1 block w-full rounded-md border-gray-300" /></div>
              </div>
            </div>

            <!-- PromptPay -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold mb-4">PromptPay</h3>
              <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium">PromptPay Number</label><input v-model="form.promptpay_number" class="mt-1 block w-full rounded-md border-gray-300" /></div>
                <div><label class="block text-sm font-medium">Account Name</label><input v-model="form.promptpay_name" class="mt-1 block w-full rounded-md border-gray-300" /></div>
              </div>
            </div>

            <!-- Display Options -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold mb-4">Display Options</h3>
              <div class="space-y-2">
                <label class="flex items-center"><input v-model="form.show_logo" type="checkbox" class="rounded border-gray-300" /><span class="ml-2">Show Logo</span></label>
                <label class="flex items-center"><input v-model="form.show_customer_info" type="checkbox" class="rounded border-gray-300" /><span class="ml-2">Show Customer Info</span></label>
                <label class="flex items-center"><input v-model="form.show_vat" type="checkbox" class="rounded border-gray-300" /><span class="ml-2">Show VAT</span></label>
                <label class="flex items-center"><input v-model="form.show_qr_code" type="checkbox" class="rounded border-gray-300" /><span class="ml-2">Show QR Code</span></label>
                <label class="flex items-center"><input v-model="form.show_barcode" type="checkbox" class="rounded border-gray-300" /><span class="ml-2">Show Barcode</span></label>
              </div>
            </div>

            <!-- Printer Type -->
            <div class="mb-6">
              <label class="block text-sm font-medium mb-2">Printer Type *</label>
              <select v-model="form.printer_type" required class="block w-full rounded-md border-gray-300">
                <option value="a4">A4 (210mm x 297mm)</option>
                <option value="thermal_80mm">Thermal 80mm</option>
                <option value="thermal_58mm">Thermal 58mm</option>
              </select>
            </div>

            <!-- Notes -->
            <div class="mb-6">
              <label class="block text-sm font-medium">Receipt Notes</label>
              <textarea v-model="form.receipt_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
            </div>

            <div class="flex justify-end gap-2">
              <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Save Settings</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ settings: Object });

const form = useForm({
  shop_name: props.settings.shop_name,
  shop_address: props.settings.shop_address,
  shop_phone: props.settings.shop_phone,
  shop_email: props.settings.shop_email,
  tax_id: props.settings.tax_id,
  promptpay_number: props.settings.promptpay_number,
  promptpay_name: props.settings.promptpay_name,
  show_logo: props.settings.show_logo,
  show_customer_info: props.settings.show_customer_info,
  show_vat: props.settings.show_vat,
  show_qr_code: props.settings.show_qr_code,
  show_barcode: props.settings.show_barcode,
  show_notes: props.settings.show_notes,
  receipt_notes: props.settings.receipt_notes,
  printer_type: props.settings.printer_type,
});

const submit = () => form.post(route('receipt-settings.update'));
</script>
