<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <div>
        <Head title="Welcome" />

        <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-8 bg-white border-b border-gray-200">
                        
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                                🎉 ยินดีต้อนรับสู่ POS System
                            </h1>
                            <p class="text-xl text-gray-600">
                                ระบบขายหน้าร้าน (Point of Sale) ที่ทันสมัย
                            </p>
                        </div>

                        <!-- Status Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold">✓</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-green-900">Laravel Backend</h3>
                                        <p class="text-green-700">ทำงานได้ปกติ</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold">✓</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-green-900">Vue.js Frontend</h3>
                                        <p class="text-green-700">เชื่อมต่อสำเร็จ</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold">✓</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-green-900">Inertia.js</h3>
                                        <p class="text-green-700">พร้อมใช้งาน</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Links -->
                        <div class="text-center space-y-4">
                            <div v-if="canLogin" class="space-x-4">
                                <Link v-if="$page.props.auth.user" 
                                      :href="route('dashboard')" 
                                      style="background-color: #6B7B47; border-color: #6B7B47;"
                                      class="inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-white transition ease-in-out duration-150"
                                      @mouseover="$event.target.style.backgroundColor='#8A9B5A'"
                                      @mouseout="$event.target.style.backgroundColor='#6B7B47'">
                                    เข้าสู่แดชบอร์ด
                                </Link>

                                <template v-else>
                                    <Link :href="route('login')" 
                                          style="background-color: #6B7B47; border-color: #6B7B47;"
                                          class="inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-white transition ease-in-out duration-150"
                                          @mouseover="$event.target.style.backgroundColor='#8A9B5A'"
                                          @mouseout="$event.target.style.backgroundColor='#6B7B47'">
                                        เข้าสู่ระบบ
                                    </Link>

                                    <Link v-if="canRegister" 
                                          :href="route('register')" 
                                          class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        สมัครสมาชิก
                                    </Link>
                                </template>
                            </div>

                            <!-- Test Links -->
                            <div class="pt-6 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-4">หน้าทดสอบระบบ:</p>
                                <div class="space-x-4">
                                    <Link href="/test" 
                                          style="background-color: #6B7B47; border-color: #6B7B47;"
                                          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-white transition ease-in-out duration-150"
                                          @mouseover="$event.target.style.backgroundColor='#8A9B5A'"
                                          @mouseout="$event.target.style.backgroundColor='#6B7B47'">
                                        ทดสอบ Vue.js
                                    </Link>
                                    
                                    <Link href="/debug" 
                                          class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        หน้า Debug
                                    </Link>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
