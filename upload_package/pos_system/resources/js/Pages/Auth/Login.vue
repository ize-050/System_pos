<script setup>
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-50 to-orange-100 py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #F5F5F0 0%, #E8E6E0 100%);">
        <Head title="เข้าสู่ระบบ - POS System" />
        
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full shadow-lg" style="background-color: #6B7B47;">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold" style="color: #2D3748;">
                    🏗️ เข้าสู่ระบบ POS
                </h2>
                <p class="mt-2 text-center text-sm" style="color: #4A5568;">
                    ระบบจัดการขายวัสดุก่อสร้าง
                </p>
            </div>

            <div v-if="status" class="border text-sm px-4 py-3 rounded-md" style="background-color: #F0FDF4; border-color: #BBF7D0; color: #15803D;">
                {{ status }}
            </div>

            <form class="mt-8 space-y-6 bg-white p-8 rounded-xl shadow-lg" @submit.prevent="submit">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color: #2D3748;">
                            📧 อีเมล
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            autofocus
                            class="appearance-none relative block w-full px-3 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:z-10 sm:text-sm transition-colors"
                            style="border-color: #E2E8F0; color: #2D3748; --tw-ring-color: #6B7B47; --tw-border-opacity: 1;"
                            placeholder="กรุณาใส่อีเมลของคุณ"
                        />
                        <div v-if="form.errors.email" class="text-sm mt-1" style="color: #F56565;">
                            {{ form.errors.email }}
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2" style="color: #2D3748;">
                            🔒 รหัสผ่าน
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="appearance-none relative block w-full px-3 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:z-10 sm:text-sm transition-colors"
                            style="border-color: #E2E8F0; color: #2D3748; --tw-ring-color: #6B7B47; --tw-border-opacity: 1;"
                            placeholder="กรุณาใส่รหัสผ่านของคุณ"
                        />
                        <div v-if="form.errors.password" class="text-sm mt-1" style="color: #F56565;">
                            {{ form.errors.password }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember-me"
                            v-model="form.remember"
                            name="remember-me"
                            type="checkbox"
                            class="h-4 w-4 rounded focus:ring-2"
                            style="color: #6B7B47; --tw-ring-color: #6B7B47; border-color: #E2E8F0;"
                        />
                        <label for="remember-me" class="ml-2 block text-sm" style="color: #2D3748;">
                            จดจำการเข้าสู่ระบบ
                        </label>
                    </div>

                    <div class="text-sm" v-if="canResetPassword">
                        <a :href="route('password.request')" class="font-medium transition-colors" style="color: #6B7B47;" onmouseover="this.style.color='#8A9B5A'" onmouseout="this.style.color='#6B7B47'">
                            ลืมรหัสผ่าน?
                        </a>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-md hover:shadow-lg"
                        style="background-color: #6B7B47; --tw-ring-color: #6B7B47;"
                        onmouseover="this.style.backgroundColor='#8A9B5A'"
                        onmouseout="this.style.backgroundColor='#6B7B47'"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg
                                v-if="!form.processing"
                                class="h-5 w-5 transition-colors"
                                style="color: rgba(255, 255, 255, 0.7);"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <svg
                                v-else
                                class="animate-spin h-5 w-5"
                                style="color: rgba(255, 255, 255, 0.7);"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        {{ form.processing ? '🔄 กำลังเข้าสู่ระบบ...' : '🚀 เข้าสู่ระบบ' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
