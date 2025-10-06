<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
            <h1 class="text-2xl font-bold mb-6 text-center text-green-800">Laravel + Inertia Debug</h1>
            
            <div class="space-y-4">
                <div class="p-4 bg-green-100 border border-green-400 rounded">
                    <h2 class="font-semibold text-green-800">✅ Laravel Backend</h2>
                    <p class="text-sm text-green-700">Route working correctly</p>
                </div>
                
                <div class="p-4 bg-green-100 border border-green-400 rounded">
                    <h2 class="font-semibold text-green-800">✅ Inertia.js</h2>
                    <p class="text-sm text-green-700">Component rendered successfully</p>
                </div>
                
                <div class="p-4 bg-green-100 border border-green-400 rounded">
                    <h2 class="font-semibold text-green-800">✅ Vue.js</h2>
                    <p class="text-sm text-green-700">Framework working properly</p>
                </div>
                
                <div class="p-4 bg-green-100 border border-green-400 rounded">
                    <h2 class="font-semibold text-green-800">📊 System Info</h2>
                    <p class="text-sm text-green-700">Current URL: {{ currentUrl }}</p>
                    <p class="text-sm text-green-700">Timestamp: {{ timestamp }}</p>
                </div>
                
                <div class="space-y-2">
                    <Link href="/test" 
                        :style="{
                            backgroundColor: '#6B7B47',
                            borderColor: '#6B7B47'
                        }"
                        @mouseover="$event.target.style.backgroundColor = '#8A9B5A'"
                        @mouseout="$event.target.style.backgroundColor = '#6B7B47'"
                        class="block w-full text-white px-4 py-2 rounded text-center transition-all duration-200"
                    >
                        Go to Test Page
                    </Link>
                    <Link href="/login" class="block w-full bg-green-500 text-white px-4 py-2 rounded text-center hover:bg-green-600">
                        Go to Login Page
                    </Link>
                    <Link href="/" class="block w-full bg-purple-500 text-white px-4 py-2 rounded text-center hover:bg-purple-600">
                        Go to Home Page
                    </Link>
                    <a :href="route('pos.index')" class="text-green-600 hover:text-green-800 underline">
                        Go to POS System
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
    name: 'Debug',
    components: {
        Link
    },
    data() {
        return {
            currentUrl: window.location.href,
            timestamp: new Date().toLocaleString()
        }
    },
    mounted() {
        console.log('Debug component mounted successfully!');
        console.log('Laravel + Inertia.js + Vue.js integration working!');
    }
}
</script>