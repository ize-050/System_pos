<template>
    <AppLayout title="ประวัติการขาย">
        <!-- Statistics Cards -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Sales Card -->
                <div
                    class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">
                                ยอดขายรวม
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                ฿{{ formatPrice(statistics?.total_sales || 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Today Sales Card -->
                <div
                    class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">
                                ยอดขายวันนี้
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                ฿{{ formatPrice(statistics?.today_sales || 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Transactions Card -->
                <div
                    class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">
                                รายการทั้งหมด
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ statistics?.total_transactions || 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Today Transactions Card -->
                <div
                    class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-orange-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">
                                รายการวันนี้
                            </p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ statistics?.today_transactions || 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            ประวัติการขาย
                        </h1>
                        <p class="text-green-100 mt-1">
                            จัดการและติดตามประวัติการขายทั้งหมด
                        </p>
                    </div>
                    <Link
                        :href="route('pos.index')"
                        class="bg-white text-green-600 hover:bg-green-50 font-semibold py-2.5 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            ></path>
                        </svg>
                        <span>ขายสินค้าใหม่</span>
                    </Link>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="p-6 bg-gray-50 border-b">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <svg
                                class="h-5 w-5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                ></path>
                            </svg>
                        </div>
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="ค้นหาด้วยชื่อลูกค้า, เบอร์โทร..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                        />
                    </div>
                    <div>
                        <select
                            v-model="form.payment_method"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                        >
                            <option value="">วิธีการชำระเงินทั้งหมด</option>
                            <option value="cash">เงินสด</option>
                            <option value="transfer">โอนเงิน/QR</option>
                            <option value="credit_card">บัตรเครดิต</option>
                            <option value="customer_account">
                                บัญชีลูกค้า
                            </option>
                        </select>
                    </div>
                    <div>
                        <input
                            v-model="form.date_from"
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                        />
                    </div>
                    <div>
                        <input
                            v-model="form.date_to"
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                        />
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div v-if="sales.data.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                หมายเลขขาย
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                ลูกค้า
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                จำนวนสินค้า
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                ยอดรวม
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                โปรโมชั่น
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                วิธีชำระเงิน
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                วันที่
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
                            >
                                การดำเนินการ
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="sale in sales.data"
                            :key="sale.id"
                            class="hover:bg-gray-50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3"
                                    >
                                        <svg
                                            class="w-5 h-5 text-green-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{
                                                sale.sale_number ||
                                                "#" +
                                                    sale.id
                                                        .toString()
                                                        .padStart(6, "0")
                                            }}
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                            :class="getStatusClass(sale.status)"
                                        >
                                            {{ getStatusLabel(sale.status) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3"
                                    >
                                        <svg
                                            class="w-5 h-5 text-blue-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{
                                                sale.customer
                                                    ? sale.customer.name ||
                                                      sale.customer.company_name
                                                    : "ลูกค้าทั่วไป"
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ sale.customer?.phone || "-" }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2"
                                    >
                                        <span
                                            class="text-sm font-semibold text-purple-600"
                                            >{{ sale.items_count }}</span
                                        >
                                    </div>
                                    <span class="text-sm text-gray-600"
                                        >รายการ</span
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-gray-900">
                                    ฿{{ formatPrice(sale.total_amount) }}
                                </div>
                                <div
                                    v-if="sale.refunded_amount > 0"
                                    class="text-xs text-red-500"
                                >
                                    คืน: ฿{{
                                        formatPrice(sale.refunded_amount)
                                    }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div
                                    v-if="sale.promotion"
                                    class="flex items-center"
                                >
                                    <div
                                        class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-2"
                                    >
                                        <svg
                                            class="w-4 h-4 text-orange-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ sale.promotion.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{
                                                getPromotionTypeLabel(
                                                    sale.promotion.type
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500">
                                    -
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                        :class="
                                            getPaymentMethodClass(
                                                sale.payment_method
                                            )
                                        "
                                    >
                                        <svg
                                            class="w-4 h-4 mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                v-if="
                                                    sale.payment_method ===
                                                    'cash'
                                                "
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                            ></path>
                                            <path
                                                v-else
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                            ></path>
                                        </svg>
                                        {{
                                            formatPaymentMethod(
                                                sale.payment_method
                                            )
                                        }}
                                    </span>
                                    <!-- Credit/Outstanding Badge -->
                                    <div
                                        v-if="sale.payment_method === 'credit'"
                                        class="mt-1"
                                    >
                                        <span
                                            v-if="sale.status === 'paid'"
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700"
                                        >
                                            <svg
                                                class="w-3 h-3 mr-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 13l4 4L19 7"
                                                ></path>
                                            </svg>
                                            เครดิต (ชำระแล้ว)
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700"
                                        >
                                            <svg
                                                class="w-3 h-3 mr-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                ></path>
                                            </svg>
                                            เครดิต (ค้างชำระ)
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ formatDate(sale.created_at) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ formatTime(sale.created_at) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <Link
                                        :href="route('sales.show', sale.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            ></path>
                                        </svg>
                                        ดู
                                    </Link>
                                    <Link
                                        :href="route('sales.receipt', sale.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors duration-200"
                                        target="_blank"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                                            ></path>
                                        </svg>
                                        พิมพ์
                                    </Link>
                                    <button
                                        v-if="
                                            $page.props.auth.user.role ===
                                            'admin'
                                        "
                                        @click="deleteSale(sale.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors duration-200"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            ></path>
                                        </svg>
                                        ลบ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <div
                    class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                    <svg
                        class="w-12 h-12 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        ></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    ไม่พบข้อมูลการขาย
                </h3>
                <p class="text-gray-500 mb-6">
                    เริ่มต้นด้วยการสร้างรายการขายใหม่
                </p>
                <Link
                    :href="route('pos.index')"
                    class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-md hover:shadow-lg"
                >
                    <svg
                        class="w-5 h-5 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        ></path>
                    </svg>
                    เริ่มขายสินค้า
                </Link>
            </div>

            <!-- Pagination -->
            <div
                v-if="sales.data.length > 0"
                class="px-6 py-4 bg-gray-50 border-t border-gray-200"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="sales.prev_page_url"
                            :href="sales.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                        >
                            ก่อนหน้า
                        </Link>
                        <Link
                            v-if="sales.next_page_url"
                            :href="sales.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                        >
                            ถัดไป
                        </Link>
                    </div>
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-gray-700">
                                แสดง
                                <span class="font-medium">{{
                                    sales.from
                                }}</span>
                                ถึง
                                <span class="font-medium">{{ sales.to }}</span>
                                จาก
                                <span class="font-medium">{{
                                    sales.total
                                }}</span>
                                รายการ
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <Link
                                    v-if="sales.prev_page_url"
                                    :href="sales.prev_page_url"
                                    class="relative inline-flex items-center px-3 py-2 rounded-l-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <span class="sr-only">ก่อนหน้า</span>
                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </Link>
                                <Link
                                    v-if="sales.next_page_url"
                                    :href="sales.next_page_url"
                                    class="relative inline-flex items-center px-3 py-2 rounded-r-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                                >
                                    <span class="sr-only">ถัดไป</span>
                                    <svg
                                        class="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { debounce } from "lodash";

const props = defineProps({
    sales: Object,
    filters: Object,
    statistics: Object,
});

const form = ref({
    search: props.filters.search || "",
    payment_method: props.filters.payment_method || "",
    date_from: props.filters.date_from || "",
    date_to: props.filters.date_to || "",
});

// Debounced search function
const debouncedSearch = debounce(() => {
    router.get(route("sales.index"), form.value, {
        preserveState: true,
        replace: true,
    });
}, 300);

// Watch for form changes
watch(form, debouncedSearch, { deep: true });

// Utility functions
const formatPrice = (price) => {
    return parseFloat(price).toLocaleString("th-TH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatPaymentMethod = (method) => {
    const methods = {
        cash: "เงินสด",
        card: "บัตรเครดิต",
        credit_card: "บัตรเครดิต",
        transfer: "โอนเงิน/QR",
        bank_transfer: "โอนเงิน/QR",
        credit: "เครดิต",
        customer_account: "เครดิต",
        e_wallet: "กระเป๋าเงินอิเล็กทรอนิกส์",
    };
    return methods[method] || method;
};

const getPaymentMethodLabel = (method) => {
    return formatPaymentMethod(method);
};

const getPaymentMethodClass = (method) => {
    const classes = {
        cash: "bg-green-100 text-green-800",
        card: "bg-blue-100 text-blue-800",
        credit_card: "bg-blue-100 text-blue-800",
        transfer: "bg-indigo-100 text-indigo-800",
        bank_transfer: "bg-indigo-100 text-indigo-800",
        credit: "bg-purple-100 text-purple-800",
        customer_account: "bg-purple-100 text-purple-800",
        e_wallet: "bg-orange-100 text-orange-800",
    };
    return classes[method] || "bg-gray-100 text-gray-800";
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("th-TH", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString("th-TH", {
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getPromotionTypeLabel = (type) => {
    const types = {
        percentage: "ส่วนลดเปอร์เซ็นต์",
        fixed_amount: "ส่วนลดจำนวนเงิน",
        buy_x_get_y: "ซื้อ X ได้ Y",
    };
    return types[type] || type;
};

const getStatusLabel = (status) => {
    const labels = {
        pending: "รอดำเนินการ",
        completed: "เสร็จสิ้น",
        cancelled: "ยกเลิก",
        refunded: "คืนสินค้าแล้ว",
        partial_refunded: "คืนบางส่วน",
    };
    return labels[status] || status || "เสร็จสิ้น";
};

const getStatusClass = (status) => {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        completed: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
        refunded: "bg-purple-100 text-purple-800",
        partial_refunded: "bg-orange-100 text-orange-800",
    };
    return classes[status] || "bg-green-100 text-green-800";
};

// Print receipt function
const printReceipt = (saleId) => {
    window.open(route("sales.receipt", saleId), "_blank");
};

// Delete sale function
const deleteSale = (id) => {
    if (
        confirm(
            "Are you sure you want to delete this sale? This action cannot be undone."
        )
    ) {
        router.delete(route("sales.destroy", id), {
            onSuccess: () => {
                // Handle success if needed
            },
        });
    }
};
</script>
