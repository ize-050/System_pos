<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import BreezeDropdown from '@/Components/Dropdown.vue';
import BreezeDropdownLink from '@/Components/DropdownLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const showingSidebar = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);
const userRole = computed(() => user.value?.role || 'cashier');

// Navigation items based on user role
const navigationItems = computed(() => {
    const items = [
        {
            name: 'แดชบอร์ด',
            route: 'dashboard',
            icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
            roles: ['admin', 'manager', 'cashier'],
            description: 'ภาพรวมระบบ'
        },
        {
            name: 'ขายสินค้า (POS)',
            route: 'pos.index',
            icon: 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
            roles: ['admin', 'manager', 'cashier'],
            description: 'หน้าขายของ'
        },
        {
            name: 'ประวัติการขาย',
            route: 'sales.index',
            icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
            roles: ['admin', 'manager', 'cashier'],
            description: 'ดูประวัติการขาย'
        },
        {
            name: 'ลูกค้า/สมาชิก',
            route: 'customers.index',
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            roles: ['admin', 'manager', 'cashier'],
            description: 'จัดการข้อมูลลูกค้า'
        },
        {
            name: 'จัดการสินค้า (Stock)',
            route: 'products.index',
            icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            roles: ['admin', 'manager'],
            description: 'คลังสินค้า'
        },
        {
            name: 'หมวดหมู่สินค้า',
            route: 'categories.index',
            icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            roles: ['admin', 'manager'],
            description: 'จัดหมวดหมู่'
        },
        {
            name: 'จัดการผู้ใช้',
            route: 'users.index',
            icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
            roles: ['admin'],
            description: 'จัดการพนักงาน'
        },
        {
            name: 'Analytics',
            route: 'analytics.index',
            icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            roles: ['admin', 'manager'],
            description: 'วิเคราะห์ข้อมูล'
        },
        {
            name: 'รายงาน',
            icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            roles: ['admin', 'manager', 'accountant'],
            description: 'รายงานต่างๆ',
            children: [
                {
                    name: 'รายงานยอดขาย-ซื้อรายวัน',
                    route: 'reports.daily-sales-purchase',
                    description: 'รายงานยอดขาย-ซื้อและสินค้าคงคลัง'
                },
                {
                    name: 'รายงานการขาย',
                    route: 'reports.sales',
                    description: 'รายงานการขายทั้งหมด'
                },
                {
                    name: 'รายงานการซื้อ',
                    route: 'reports.purchases',
                    description: 'รายงานการซื้อสินค้า'
                },
                {
                    name: 'รายงานสินค้าคงคลัง',
                    route: 'reports.inventory',
                    description: 'รายงานสต็อกสินค้า'
                },
                {
                    name: 'รายงานการเบิกสินค้า',
                    route: 'reports.stock-requisitions',
                    description: 'รายงานการเบิกสินค้า'
                }
            ]
        },
        // {
        //     name: 'ลูกหนี้/เจ้าหนี้',
        //     route: 'debtors.index',
        //     icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
        //     roles: ['admin', 'manager'],
        //     description: 'จัดการลูกหนี้เจ้าหนี้'
        // },
        {
            name: 'โปรโมชั่น',
            route: 'promotions.index',
            icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            roles: ['admin', 'manager'],
            description: 'จัดการโปรโมชั่น'
        },
        {
            name: 'ใบสั่งซื้อ (PO)',
            route: 'purchase-orders.index',
            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            roles: ['admin', 'manager', 'accountant'],
            description: 'จัดการใบสั่งซื้อสินค้า'
        },
        {
            name: 'ใบเบิกสินค้า',
            route: 'stock-requisitions.index',
            icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            roles: ['admin', 'manager', 'accountant'],
            description: 'จัดการใบเบิกสินค้า'
        },
        {
            name: 'ตั้งค่าใบเสร็จ',
            route: 'receipt-settings.edit',
            icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            roles: ['admin', 'manager'],
            description: 'ตั้งค่าใบเสร็จและพิมพ์'
        }
    ];

    return items.filter(item => item.roles.includes(userRole.value));
});

const toggleSidebar = () => {
    showingSidebar.value = !showingSidebar.value;
};

const openMenus = ref({});

const toggleMenu = (itemName) => {
    openMenus.value[itemName] = !openMenus.value[itemName];
};
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="min-h-screen bg-gray-50 flex">
            <!-- Mobile sidebar overlay -->
            <div
                v-show="showingSidebar"
                class="fixed inset-0 z-40 lg:hidden"
                @click="showingSidebar = false"
            >
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>
            </div>

            <!-- Sidebar -->
            <div
                :class="{
                    'translate-x-0': showingSidebar,
                    '-translate-x-full': !showingSidebar
                }"
                class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:flex-shrink-0"
            >
                <!-- Logo Header -->
                <div class="flex items-center justify-center h-20 px-6 border-b border-gray-100" style="background: linear-gradient(135deg, #6B7B47 0%, #8A9B5A 100%);">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <BreezeApplicationLogo class="block h-9 w-auto text-white" />
                        <span class="ml-3 text-xl font-bold text-white tracking-wide">POS System</span>
                    </Link>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    <template v-for="item in navigationItems" :key="item.name">
                        <!-- Menu with children (dropdown) -->
                        <div v-if="item.children">
                            <button
                                @click="toggleMenu(item.name)"
                                class="group w-full flex items-center justify-between px-4 py-3.5 text-sm font-medium rounded-lg transition-all duration-200 hover:shadow-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900"
                            >
                                <div class="flex items-center flex-1 min-w-0">
                                    <svg
                                        class="mr-4 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                    </svg>
                                    <div class="flex-1 min-w-0 text-left">
                                        <div class="font-semibold truncate">{{ item.name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 truncate">{{ item.description }}</div>
                                    </div>
                                </div>
                                <svg
                                    class="ml-2 h-4 w-4 transition-transform duration-200"
                                    :class="{ 'rotate-180': openMenus[item.name] }"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <!-- Submenu -->
                            <div
                                v-show="openMenus[item.name]"
                                class="mt-1 ml-4 space-y-1"
                            >
                                <Link
                                    v-for="child in item.children"
                                    :key="child.route"
                                    :href="route(child.route)"
                                    :class="{
                                        'bg-green-50 text-green-700 border-l-4 border-green-500': route().current(child.route + '*'),
                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent': !route().current(child.route + '*')
                                    }"
                                    class="group flex items-center px-4 py-2.5 text-sm rounded-lg transition-all duration-200"
                                >
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium truncate">{{ child.name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 truncate">{{ child.description }}</div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                        
                        <!-- Regular menu item -->
                        <Link
                            v-else
                            :href="route(item.route)"
                            :class="{
                                'bg-gradient-to-r from-green-50 to-green-100 text-green-800 border-r-4 border-green-500 shadow-sm': route().current(item.route + '*'),
                                'text-gray-700 hover:bg-gray-50 hover:text-gray-900': !route().current(item.route + '*')
                            }"
                            class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-lg transition-all duration-200 hover:shadow-sm"
                        >
                            <svg
                                class="mr-4 h-5 w-5 flex-shrink-0"
                                :class="{
                                    'text-green-600': route().current(item.route + '*'),
                                    'text-gray-400 group-hover:text-gray-600': !route().current(item.route + '*')
                                }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold truncate">{{ item.name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5 truncate">{{ item.description }}</div>
                            </div>
                        </Link>
                    </template>
                </nav>
            </div>

            <!-- Main content -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top navigation -->
                <div class="sticky top-0 z-10 bg-white shadow-sm border-b border-gray-200">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button
                                @click="toggleSidebar"
                                type="button"
                                class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <!-- Breadcrumb or page title -->
                            <div class="ml-4 lg:ml-0">
                                <slot name="header" />
                            </div>
                        </div>

                        <!-- Right side items -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button
                                type="button"
                                class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2"
                                style="--tw-ring-color: #6B7B47;"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 1 5.25 8.25l-1.444 2.889a1.125 1.125 0 0 1-1.006.611H6.699a1.125 1.125 0 0 1-1.006-.611L4.25 12A6 6 0 0 1 10.5 3.75z" />
                                </svg>
                            </button>

                            <!-- User menu -->
                            <BreezeDropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2"
                                        style="--tw-ring-color: #6B7B47;"
                                    >
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center" style="background-color: #6B7B47;">
                                            <span class="text-sm font-medium text-white">
                                                {{ user?.name?.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="ml-3 hidden md:block">
                                            <div class="text-sm font-medium text-gray-700">{{ user?.name }}</div>
                                            <div class="text-xs text-gray-500 capitalize">{{ userRole }}</div>
                                        </div>
                                        <svg class="ml-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <div class="text-sm font-medium text-gray-900">{{ user?.name }}</div>
                                        <div class="text-sm text-gray-500">{{ user?.email }}</div>
                                    </div>

                                    <BreezeDropdownLink :href="route('profile.edit')">
                                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Profile
                                    </BreezeDropdownLink>

                                    <BreezeDropdownLink :href="route('logout')" method="post" as="button">
                                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Log Out
                                    </BreezeDropdownLink>
                                </template>
                            </BreezeDropdown>
                        </div>
                    </div>
                </div>

                <!-- Page content -->
                <main class="flex-1 bg-gray-50 overflow-y-auto">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom styles for better mobile experience */
@media (max-width: 1024px) {
    .sidebar-overlay {
        backdrop-filter: blur(4px);
    }
}

/* Smooth transitions */
.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Active navigation item styles */
.nav-item-active {
    background: linear-gradient(135deg, #6B7B47 0%, #8A9B5A 100%);
    color: white;
    box-shadow: 0 4px 15px 0 rgba(107, 123, 71, 0.4);
}

/* Hover effects */
.nav-item:hover {
    transform: translateX(4px);
    transition: all 0.2s ease-in-out;
}
</style>
