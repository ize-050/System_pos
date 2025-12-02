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

            <!-- Logo Upload -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold mb-4">Shop Logo</h3>
              <div class="space-y-4">
                <!-- Current Logo Preview -->
                <div v-if="currentLogoUrl" class="flex items-center gap-4">
                  <div class="relative">
                    <img :src="currentLogoUrl" alt="Current Logo" class="h-24 w-24 object-contain border border-gray-300 rounded-lg p-2 bg-white" />
                    <button
                      type="button"
                      @click="removeLogo"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <div class="text-sm text-gray-600">
                    <p class="font-medium">Current Logo</p>
                    <p class="text-xs">Click X to remove</p>
                  </div>
                </div>

                <!-- New Logo Preview -->
                <div v-if="logoPreview" class="flex items-center gap-4">
                  <div class="relative">
                    <img :src="logoPreview" alt="Logo Preview" class="h-24 w-24 object-contain border border-green-300 rounded-lg p-2 bg-white" />
                    <button
                      type="button"
                      @click="clearLogoPreview"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <div class="text-sm text-green-600">
                    <p class="font-medium">New Logo (Preview)</p>
                    <p class="text-xs">This will replace the current logo</p>
                  </div>
                </div>

                <!-- Upload Input -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Logo (PNG, JPG, max 2MB)
                  </label>
                  <input
                    ref="logoInput"
                    type="file"
                    accept="image/*"
                    @change="handleLogoUpload"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Recommended: Square image (500x500px) for best results
                  </p>
                </div>
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
                <option value="dot_matrix">Dot Matrix / กระดาษต่อเนื่อง (9.5" x 11")</option>
              </select>
              <p v-if="form.printer_type === 'dot_matrix'" class="mt-2 text-sm text-blue-600">
                📄 กระดาษต่อเนื่องสำหรับเครื่องพิมพ์ Dot Matrix (EPSON LQ-310, LX-310 ฯลฯ)
              </p>
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

import { ref, computed } from 'vue';

const props = defineProps({ settings: Object });

const logoInput = ref(null);
const logoPreview = ref(null);
const removedLogo = ref(false);

const currentLogoUrl = computed(() => {
  if (removedLogo.value) return null;
  if (props.settings.logo_path) {
    return `/storage/${props.settings.logo_path}`;
  }
  return null;
});

const form = useForm({
  shop_name: props.settings.shop_name,
  shop_address: props.settings.shop_address,
  shop_phone: props.settings.shop_phone,
  shop_email: props.settings.shop_email,
  tax_id: props.settings.tax_id,
  logo: null,
  remove_logo: false,
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

const handleLogoUpload = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    form.logo = file;
    form.remove_logo = false;
    removedLogo.value = false;

    const reader = new FileReader();
    reader.onload = (e) => {
      logoPreview.value = e.target?.result;
    };
    reader.readAsDataURL(file);
  }
};

const clearLogoPreview = () => {
  form.logo = null;
  logoPreview.value = null;
  if (logoInput.value) {
    logoInput.value.value = '';
  }
};

const removeLogo = () => {
  removedLogo.value = true;
  form.remove_logo = true;
  form.logo = null;
  logoPreview.value = null;
  if (logoInput.value) {
    logoInput.value.value = '';
  }
};

const submit = () => {
  form.transform((data) => ({
    ...data,
    _method: 'post',
  })).post(route('receipt-settings.update'), {
    forceFormData: true,
  });
};
</script>
