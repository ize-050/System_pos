<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-bold text-green-800 mb-8">Test Page</h1>
                    
                    <div class="space-y-4">
                        <p class="text-lg text-gray-700">
                            หากคุณเห็นข้อความนี้ แสดงว่า Laravel + Vue + Inertia.js ทำงานได้ปกติ
                        </p>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h2 class="font-semibold text-green-800 mb-2">ข้อมูลการทดสอบ:</h2>
                            <ul class="text-green-700 space-y-1">
                                <li>✅ Vue 3 Component</li>
                                <li>✅ Inertia.js Rendering</li>
                                <li>✅ Tailwind CSS Styling</li>
                                <li>✅ Reactive Data: {{ message }}</li>
                            </ul>
                        </div>
                        
                        <button 
                            @click="updateMessage" 
                            :style="{
                                backgroundColor: '#6B7B47',
                                borderColor: '#6B7B47'
                            }"
                            @mouseover="$event.target.style.backgroundColor = '#8A9B5A'"
                            @mouseout="$event.target.style.backgroundColor = '#6B7B47'"
                            class="px-6 py-3 text-white rounded-lg hover:shadow-md transition-all duration-200 font-semibold"
                        >
                            คลิกเพื่อทดสอบ Reactivity
                        </button>
                        
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                Counter: {{ counter }} | Last Updated: {{ lastUpdate }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'

// Reactive data
const counter = ref(0)
const message = ref('Vue กำลังทำงาน!')
const lastUpdate = ref(new Date().toLocaleTimeString())

// Methods
const updateMessage = () => {
    counter.value++
    message.value = `คลิกแล้ว ${counter.value} ครั้ง!`
    lastUpdate.value = new Date().toLocaleTimeString()
}
</script>
