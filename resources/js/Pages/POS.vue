<template>
    <AppLayout title="ระบบขายหน้าร้าน (POS)">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    ระบบขายหน้าร้าน (POS)
                </h2>
                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-sm text-gray-600"
                        >พนักงาน: {{ $page.props.auth.user.name }}</span
                    >
                    <span class="text-sm text-gray-600">{{
                        currentDateTime
                    }}</span>
                </div>
            </div>
        </template>

        <div class="py-4 lg:py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Mobile Layout -->
                <div class="lg:hidden">
                    <!-- Mobile Header -->
                    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">รายการสินค้า</h3>
                            <button
                                @click="showMobileSearch = !showMobileSearch"
                                class="btn btn-primary btn-sm"
                            >
                                <svg
                                    class="w-4 h-4"
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
                            </button>
                        </div>

                        <!-- Mobile Search -->
                        <div v-if="showMobileSearch" class="mb-4">
                            <input
                                v-model="searchQuery"
                                @input="searchProducts"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500"
                                placeholder="ค้นหาสินค้า..."
                            />
                        </div>

                        <!-- Mobile Filter Chips -->
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="filter in categoryFilters"
                                :key="filter.id"
                                @click="selectCategory(filter)"
                                :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium transition-colors',
                                    filter.active
                                        ? 'bg-primary-500 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                ]"
                            >
                                {{ filter.label }} ({{ filter.count }})
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Product List -->
                    <div class="space-y-3 mb-20">
                        <div
                            v-for="product in products"
                            :key="product.id"
                            class="bg-white rounded-lg shadow-md p-4 cursor-pointer hover:shadow-lg transition-shadow"
                            @click="addToCart(product)"
                        >
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center"
                                >
                                    <img
                                        v-if="product.image"
                                        :src="product.image"
                                        :alt="product.name"
                                        class="w-full h-full object-cover rounded-lg"
                                    />
                                    <svg
                                        v-else
                                        class="w-8 h-8 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">
                                        {{ product.name }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ product.category }}
                                    </p>
                                    <div
                                        class="flex items-center justify-between mt-1"
                                    >
                                        <span
                                            class="text-lg font-bold text-primary-600"
                                            >฿{{
                                                formatPrice(
                                                    product.selling_price
                                                )
                                            }}</span
                                        >
                                        <span
                                            :class="
                                                stockStatusClass(
                                                    product.current_stock
                                                )
                                            "
                                            class="px-2 py-1 rounded-full text-xs font-medium"
                                        >
                                            คงเหลือ {{ product.current_stock }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Cart Summary (Fixed Bottom) -->
                    <div
                        class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 z-50"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600"
                                >{{ cartItems.length }} รายการ</span
                            >
                            <span class="text-lg font-bold text-primary-600"
                                >฿{{ formatPrice(cartGrandTotal) }}</span
                            >
                        </div>
                        <button
                            @click="showMobileCart = true"
                            :disabled="cartItems.length === 0"
                            class="w-full btn btn-primary"
                        >
                            ดูตะกร้าสินค้า
                        </button>
                    </div>

                    <!-- Mobile Cart Modal -->
                    <div
                        v-if="showMobileCart"
                        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end"
                    >
                        <div
                            class="bg-white w-full max-h-[80vh] rounded-t-xl overflow-hidden"
                        >
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold">
                                        ตะกร้าสินค้า
                                    </h3>
                                    <button
                                        @click="showMobileCart = false"
                                        class="text-gray-400 hover:text-gray-600"
                                    >
                                        <svg
                                            class="w-6 h-6"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 overflow-y-auto max-h-96">
                                <div
                                    v-if="cartItems.length === 0"
                                    class="text-center py-8 text-gray-500"
                                >
                                    ไม่มีสินค้าในตะกร้า
                                </div>
                                <div v-else class="space-y-3">
                                    <div
                                        v-for="item in cartItems"
                                        :key="item.id"
                                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                    >
                                        <div class="flex-1">
                                            <h4 class="font-medium">
                                                {{ item.product_name }}
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                {{ item.quantity }} หน่วย x ฿{{
                                                    formatPrice(item.price)
                                                }}
                                                / หน่วย
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <button
                                                @click="
                                                    updateCartQuantity({
                                                        id: item.id,
                                                        change: -1,
                                                    })
                                                "
                                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 12H4"
                                                    ></path>
                                                </svg>
                                            </button>
                                            <span class="w-8 text-center">{{
                                                item.quantity
                                            }}</span>
                                            <button
                                                @click="
                                                    updateCartQuantity({
                                                        id: item.id,
                                                        change: 1,
                                                    })
                                                "
                                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center"
                                            >
                                                <svg
                                                    class="w-4 h-4"
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
                                            </button>
                                            <button
                                                @click="removeFromCart(item.id)"
                                                class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center"
                                            >
                                                <svg
                                                    class="w-4 h-4"
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
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200">
                                <!-- VAT Toggle for Mobile -->
                                <div
                                    class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <h4
                                                class="font-medium text-sm text-blue-800"
                                            >
                                                ภาษีมูลค่าเพิ่ม (VAT)
                                            </h4>
                                            <p class="text-xs text-blue-600">
                                                เลือกใช้หรือไม่ใช้ VAT 7%
                                            </p>
                                        </div>
                                        <label
                                            class="relative inline-flex items-center cursor-pointer"
                                        >
                                            <input
                                                v-model="includeVAT"
                                                type="checkbox"
                                                class="sr-only peer"
                                                aria-label="เปิด/ปิด VAT"
                                            />
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"
                                            ></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span>ยอดรวม:</span>
                                        <span
                                            >฿{{ formatPrice(cartTotal) }}</span
                                        >
                                    </div>
                                    <div
                                        v-if="includeVAT"
                                        class="flex justify-between text-sm"
                                    >
                                        <span>ภาษี (7%):</span>
                                        <span>฿{{ formatPrice(cartTax) }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between font-bold text-lg border-t pt-2"
                                    >
                                        <span>รวมทั้งสิ้น:</span>
                                        <span class="text-primary-600"
                                            >฿{{
                                                formatPrice(cartGrandTotal)
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        @click="clearCart"
                                        class="flex-1 btn btn-outline"
                                    >
                                        ล้างตะกร้า
                                    </button>
                                    <button
                                        @click="
                                            showPaymentModal = true;
                                            showMobileCart = false;
                                        "
                                        class="flex-1 btn btn-primary"
                                    >
                                        ชำระเงิน
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Layout - NEW POS STYLE -->
                <div
                    class="hidden lg:grid lg:grid-cols-12 lg:gap-4"
                    style="height: calc(100vh - 140px)"
                >
                    <!-- LEFT: Cart & Summary (5 columns) -->
                    <div class="lg:col-span-5">
                        <div
                            class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full"
                        >
                            <!-- Cart Header -->
                            <div
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 shadow-md flex-shrink-0"
                            >
                                <div
                                    class="flex items-center justify-between text-white"
                                >
                                    <h2 class="text-xl font-bold">
                                        รายการสินค้า
                                    </h2>
                                    <span
                                        class="bg-white text-indigo-600 px-3 py-1.5 rounded-full text-sm font-bold shadow-sm"
                                    >
                                        {{ cartItems.length }} รายการ
                                    </span>
                                </div>
                            </div>

                            <!-- Cart Items - Fixed Height with Scroll -->
                            <div
                                class="overflow-y-auto bg-white flex-shrink-0"
                                style="height: 280px"
                            >
                                <div
                                    v-if="cartItems.length === 0"
                                    class="flex flex-col items-center justify-center h-full py-8"
                                >
                                    <svg
                                        class="w-16 h-16 text-gray-300 mb-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                        ></path>
                                    </svg>
                                    <p
                                        class="text-gray-500 text-center text-sm font-medium"
                                    >
                                        ยังไม่มีสินค้าในตะกร้า
                                    </p>
                                </div>

                                <div v-else class="p-3 space-y-2">
                                    <div
                                        v-for="item in cartItems"
                                        :key="item.id"
                                        class="bg-gray-50 rounded-lg p-3 border border-gray-200 hover:border-indigo-300 transition-all"
                                    >
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1 pr-2">
                                                <h4 class="font-bold text-gray-900 text-sm leading-tight">
                                                    {{ item.product_name }}
                                                </h4>
                                                <p class="text-xs text-gray-500">
                                                    ฿{{ formatPrice(item.price) }} / หน่วย
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-lg font-bold text-gray-900">
                                                    ฿{{ formatPrice(item.price * item.quantity) }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- Quantity Controls -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <button
                                                    @click="updateCartQuantity({ id: item.id, change: -1 })"
                                                    class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <input
                                                    type="number"
                                                    :value="item.quantity"
                                                    @change="setCartQuantity(item.id, $event.target.value)"
                                                    @keydown.enter.prevent
                                                    min="1"
                                                    :max="item.max_stock"
                                                    class="w-14 text-center border border-gray-300 rounded py-1 text-sm"
                                                />
                                                <button
                                                    @click="updateCartQuantity({ id: item.id, change: 1 })"
                                                    class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <button
                                                @click="removeFromCart(item.id)"
                                                class="w-7 h-7 rounded-full bg-red-100 hover:bg-red-200 flex items-center justify-center text-red-600"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Promotion Section -->
                            <div
                                class="border-t bg-white px-4 py-3 flex-shrink-0"
                            >
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-1.5"
                                    >โปรโมชั่น</label
                                >
                                <select
                                    v-model="selectedPromotion"
                                    @change="handlePromotionChange"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                                >
                                    <option :value="null">
                                        ไม่ใช้โปรโมชั่น
                                    </option>
                                    <option
                                        v-for="promo in availablePromotions"
                                        :key="promo.id"
                                        :value="promo.id"
                                    >
                                        {{ getPromotionLabel(promo) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Invoice Type Selection (Desktop) -->
                            <div
                                class="border-t bg-white px-4 py-3 flex-shrink-0"
                            >
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-2"
                                    >ประเภทบิล</label
                                >
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        @click="invoiceType = 'cash_bill'"
                                        :class="
                                            invoiceType === 'cash_bill'
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-gray-100 text-gray-700'
                                        "
                                        class="py-2 rounded-lg font-semibold text-xs hover:opacity-90 transition-all flex flex-col items-center"
                                    >
                                        <span class="text-lg">🧾</span>
                                        <span>บิลเงินสด</span>
                                    </button>
                                    <button
                                        @click="invoiceType = 'tax_invoice'"
                                        :class="
                                            invoiceType === 'tax_invoice'
                                                ? 'bg-green-500 text-white'
                                                : 'bg-gray-100 text-gray-700'
                                        "
                                        class="py-2 rounded-lg font-semibold text-xs hover:opacity-90 transition-all flex flex-col items-center"
                                    >
                                        <span class="text-lg">📄</span>
                                        <span>ใบกำกับภาษี</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Tax Invoice Customer Form (Desktop) -->
                            <div
                                v-if="invoiceType === 'tax_invoice'"
                                class="border-t bg-green-50 px-4 py-3 flex-shrink-0"
                            >
                                <label
                                    class="block text-xs font-semibold text-green-800 mb-2"
                                    >ข้อมูลใบกำกับภาษี</label
                                >
                                <div class="space-y-2">
                                    <input
                                        v-model="
                                            taxInvoiceCustomer.company_name
                                        "
                                        type="text"
                                        class="w-full px-2 py-1.5 text-xs border border-green-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500"
                                        placeholder="ชื่อบริษัท/ชื่อ-นามสกุล *"
                                    />
                                    <textarea
                                        v-model="taxInvoiceCustomer.address"
                                        rows="2"
                                        class="w-full px-2 py-1.5 text-xs border border-green-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500"
                                        placeholder="ที่อยู่ *"
                                    ></textarea>
                                    <input
                                        v-model="taxInvoiceCustomer.tax_id"
                                        type="text"
                                        class="w-full px-2 py-1.5 text-xs border border-green-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500"
                                        placeholder="เลขประจำตัวผู้เสียภาษี (ไม่บังคับ)"
                                        maxlength="13"
                                    />
                                    <button
                                        v-if="selectedMainCustomer"
                                        @click="fillTaxInvoiceFromCustomer"
                                        class="w-full py-1.5 text-xs bg-green-600 text-white rounded hover:bg-green-700"
                                    >
                                        ดึงข้อมูลจากลูกค้าที่เลือก
                                    </button>
                                </div>
                            </div>

                            <!-- VAT Toggle -->
                            <div
                                class="border-t bg-white px-4 py-2.5 flex-shrink-0"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-semibold text-gray-700"
                                        >VAT 7%</span
                                    >
                                    <label
                                        class="relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            v-model="includeVAT"
                                            type="checkbox"
                                            class="sr-only peer"
                                        />
                                        <div
                                            class="w-10 h-5 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"
                                        ></div>
                                    </label>
                                </div>
                                <p
                                    v-if="
                                        invoiceType === 'tax_invoice' &&
                                        !includeVAT
                                    "
                                    class="text-xs text-orange-600 mt-1"
                                >
                                    ใบกำกับภาษียกเว้น VAT
                                </p>
                            </div>

                            <!-- Summary -->
                            <div
                                class="border-t bg-gray-50 px-4 py-3 flex-shrink-0"
                            >
                                <div class="space-y-1.5 mb-2">
                                    <div
                                        class="flex justify-between text-sm text-gray-700"
                                    >
                                        <span>ยอดรวม:</span>
                                        <span class="font-semibold"
                                            >฿{{ formatPrice(cartTotal) }}</span
                                        >
                                    </div>
                                    <div
                                        v-if="promotionDiscount > 0"
                                        class="flex justify-between text-sm text-green-600"
                                    >
                                        <span>ส่วนลด:</span>
                                        <span class="font-semibold"
                                            >-฿{{
                                                formatPrice(promotionDiscount)
                                            }}</span
                                        >
                                    </div>
                                    <!-- Shipping Fee -->
                                    <div class="flex justify-between items-center text-sm">
                                        <span>ค่าจัดส่ง:</span>
                                        <div class="flex items-center">
                                            <span class="mr-1">฿</span>
                                            <input
                                                v-model.number="shippingFee"
                                                type="number"
                                                min="0"
                                                step="1"
                                                class="w-20 px-2 py-1 text-right border border-gray-300 rounded text-sm"
                                                placeholder="0"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        v-if="includeVAT"
                                        class="flex justify-between text-sm text-blue-600"
                                    >
                                        <span>VAT (7%):</span>
                                        <span class="font-semibold"
                                            >฿{{ formatPrice(cartTax) }}</span
                                        >
                                    </div>
                                </div>
                                <div
                                    class="flex justify-between text-2xl font-bold border-t-2 border-gray-300 pt-2"
                                >
                                    <span class="text-gray-900">Total:</span>
                                    <span class="text-indigo-600"
                                        >฿{{
                                            formatPrice(cartGrandTotal)
                                        }}</span
                                    >
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div
                                class="border-t bg-white px-4 py-3 flex-shrink-0"
                            >
                                <label
                                    class="block text-xs font-semibold text-gray-700 mb-2"
                                    >วิธีชำระเงิน</label
                                >
                                <div class="grid grid-cols-2 gap-2 mb-2">
                                    <button
                                        @click="paymentMethod = 'cash'"
                                        :class="
                                            paymentMethod === 'cash'
                                                ? 'bg-indigo-500 text-white'
                                                : 'bg-gray-100 text-gray-700'
                                        "
                                        class="py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-all"
                                    >
                                        เงินสด
                                    </button>
                                    <button
                                        @click="paymentMethod = 'transfer'"
                                        :class="
                                            paymentMethod === 'transfer'
                                                ? 'bg-indigo-500 text-white'
                                                : 'bg-gray-100 text-gray-700'
                                        "
                                        class="py-2 rounded-lg font-semibold text-sm hover:opacity-90 transition-all"
                                    >
                                        โอนเงิน
                                    </button>
                                </div>

                                <!-- Cash Amount Input -->
                                <div v-if="paymentMethod === 'cash'">
                                    <label
                                        class="block text-xs text-gray-600 mb-1"
                                        >รับเงินมา</label
                                    >
                                    <input
                                        v-model.number="receivedAmount"
                                        type="number"
                                        step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                                        placeholder="0.00"
                                    />
                                    <div
                                        v-if="receivedAmount >= cartGrandTotal"
                                        class="mt-1.5 text-sm"
                                    >
                                        <span class="text-gray-600"
                                            >เงินทอน:</span
                                        >
                                        <span
                                            class="font-bold text-green-600 ml-1"
                                            >฿{{
                                                formatPrice(
                                                    receivedAmount -
                                                        cartGrandTotal
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <!-- Transfer Note -->
                                <div
                                    v-if="paymentMethod === 'transfer'"
                                    class="text-xs text-gray-600 mt-2"
                                >
                                    ชำระผ่านการโอนเงิน
                                </div>
                            </div>

                            <!-- Spacer -->
                            <div class="flex-1"></div>

                            <!-- Main Action Buttons -->
                            <div class="border-t bg-gray-50 p-3 flex-shrink-0">
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        @click="clearCart"
                                        :disabled="cartItems.length === 0"
                                        class="py-3 bg-red-100 hover:bg-red-200 disabled:bg-gray-100 disabled:text-gray-400 text-red-700 font-bold rounded-lg transition-colors"
                                    >
                                        ล้างตะกร้า
                                    </button>
                                    <button
                                        @click="confirmPayment"
                                        :disabled="!canProcessPayment"
                                        class="py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 disabled:from-gray-300 disabled:to-gray-300 text-white font-bold rounded-lg shadow-lg transition-all"
                                    >
                                        ชำระเงิน
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Products (7 columns) -->
                    <div class="lg:col-span-7 overflow-y-auto h-full">
                        <!-- Customer Selection Section -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">
                                ข้อมูลลูกค้า
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Customer Search -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                        >ค้นหาลูกค้า</label
                                    >
                                    <div class="relative">
                                        <input
                                            v-model="mainCustomerSearch"
                                            @input="searchMainCustomers"
                                            type="text"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500"
                                            placeholder="ค้นหาด้วยชื่อ, เบอร์โทร หรืุอ Line ID..."
                                        />
                                        <div
                                            v-if="mainCustomers.length > 0"
                                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto"
                                        >
                                            <div
                                                v-for="customer in mainCustomers"
                                                :key="customer.id"
                                                @click="
                                                    selectMainCustomer(customer)
                                                "
                                                class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                                            >
                                                <div class="font-medium">
                                                    {{
                                                        customer.name ||
                                                        customer.company_name
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{ customer.phone }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                >
                                                    วงเงิน: ฿{{
                                                        formatPrice(
                                                            customer.credit_limit
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selected Customer Info -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2"
                                            >ลูกค้าที่เลือก</label
                                        >
                                        <div
                                            v-if="selectedMainCustomer"
                                            class="p-3 bg-blue-50 rounded-lg border border-blue-200"
                                        >
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <div class="flex-1">
                                                    <div
                                                        class="font-medium text-blue-900"
                                                    >
                                                        {{
                                                            selectedMainCustomer.name ||
                                                            selectedMainCustomer.company_name
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-sm text-blue-700"
                                                    >
                                                        {{
                                                            selectedMainCustomer.phone
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-sm text-blue-700"
                                                    >
                                                        วงเงินคงเหลือ: ฿{{
                                                            formatPrice(
                                                                selectedMainCustomer.available_credit
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-xs text-blue-600 mt-1"
                                                    >
                                                        ประเภท:
                                                        {{
                                                            selectedMainCustomer.customer_type ===
                                                            "individual"
                                                                ? "บุคคลธรรดา"
                                                                : "นิติบุคคล"
                                                        }}
                                                    </div>
                                                </div>
                                                <button
                                                    @click="clearMainCustomer"
                                                    class="ml-2 text-blue-400 hover:text-blue-600"
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
                                                            d="M6 18L18 6M6 6l12 12"
                                                        ></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div
                                            v-else
                                            class="p-3 bg-gray-50 rounded-lg border border-gray-200"
                                        >
                                            <div
                                                class="text-gray-500 text-center"
                                            >
                                                <svg
                                                    class="mx-auto h-8 w-8 text-gray-400"
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
                                                <span class="text-sm"
                                                    >ยังไม่ได้เลือกลูกค้า</span
                                                >
                                                <div
                                                    class="text-xs text-gray-400 mt-1"
                                                >
                                                    การขายแบบเงินสด
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barcode Scanner Section -->
                        <div
                            class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-md p-4 mb-6"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="w-8 h-8 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                        ></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <input
                                        ref="barcodeInput"
                                        v-model="barcodeSearch"
                                        @keyup.enter="searchByBarcode"
                                        type="text"
                                        class="block w-full px-4 py-3 border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-white text-lg"
                                        placeholder="🔫 สแกน Barcode หรือพิมพ์รหัส Barcode แล้วกด Enter..."
                                        autofocus
                                    />
                                </div>
                                <button
                                    @click="searchByBarcode"
                                    class="px-4 py-3 bg-white text-green-600 rounded-lg font-semibold hover:bg-green-50 transition-colors"
                                >
                                    ค้นหา
                                </button>
                            </div>
                            <p
                                v-if="barcodeError"
                                class="mt-2 text-white text-sm"
                            >
                                {{ barcodeError }}
                            </p>
                        </div>

                        <!-- Search and Filter Bar -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <div
                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0"
                            >
                                <div class="flex-1 lg:mr-4">
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
                                            v-model="searchQuery"
                                            @input="searchProducts"
                                            type="text"
                                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                                            placeholder="ค้นหาสินค้า..."
                                        />
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <select
                                        v-model="selectedCategory"
                                        @change="searchProducts"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    >
                                        <option value="">
                                            หมวดหมู่ทั้งหมด
                                        </option>
                                        <option
                                            v-for="category in categories"
                                            :key="category.id"
                                            :value="category.id"
                                        >
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Product Grid -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <!-- Search Info Bar -->
                            <div
                                v-if="searchQuery || selectedCategory"
                                class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded"
                            >
                                <div class="flex items-center justify-between">
                                    <div
                                        class="flex items-center space-x-2 text-sm"
                                    >
                                        <svg
                                            class="w-4 h-4 text-blue-600"
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
                                        <span class="text-gray-700">
                                            <span v-if="searchQuery"
                                                >ค้นหา:
                                                <strong
                                                    >"{{ searchQuery }}"</strong
                                                ></span
                                            >
                                            <span
                                                v-if="
                                                    searchQuery &&
                                                    selectedCategory
                                                "
                                            >
                                                •
                                            </span>
                                            <span v-if="selectedCategory"
                                                >หมวดหมู่:
                                                <strong>{{
                                                    categories.find(
                                                        (c) =>
                                                            c.id ==
                                                            selectedCategory
                                                    )?.name
                                                }}</strong></span
                                            >
                                        </span>
                                    </div>
                                    <button
                                        @click="clearSearch"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    >
                                        ล้างการค้นหา
                                    </button>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div
                                v-if="loading"
                                class="flex flex-col items-center justify-center py-16"
                            >
                                <div
                                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500 mb-4"
                                ></div>
                                <p class="text-gray-500">กำลังค้นหาสินค้า...</p>
                            </div>

                            <!-- Empty State -->
                            <div
                                v-else-if="products.length === 0"
                                class="flex flex-col items-center justify-center py-16"
                            >
                                <svg
                                    class="w-16 h-16 text-gray-300 mb-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                    ></path>
                                </svg>
                                <p
                                    class="text-gray-500 text-lg font-medium mb-2"
                                >
                                    ไม่พบสินค้า
                                </p>
                                <p class="text-gray-400 text-sm">
                                    ลองค้นหาด้วยคำอื่นหรือเปลี่ยนหมวดหมู่
                                </p>
                            </div>

                            <!-- Product Grid -->
                            <div
                                v-else
                                class="grid grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4"
                            >
                                <div
                                    v-for="product in products"
                                    :key="product.id"
                                    class="group relative bg-white rounded-xl p-4 cursor-pointer border-2 border-gray-100 hover:border-primary-400 hover:shadow-lg transition-all duration-200"
                                    @click="addToCart(product)"
                                    :class="{
                                        'opacity-60 cursor-not-allowed':
                                            product.current_stock <= 0,
                                    }"
                                >
                                    <!-- Stock Badge -->
                                    <div class="absolute top-2 right-2 z-10">
                                        <span
                                            :class="
                                                stockStatusClass(
                                                    product.current_stock
                                                )
                                            "
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm"
                                        >
                                            {{ product.current_stock }} ชิ้น
                                        </span>
                                    </div>

                                    <!-- Product Image -->
                                    <div
                                        class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden relative"
                                    >
                                        <img
                                            v-if="product.image"
                                            :src="product.image"
                                            :alt="product.name"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                        />
                                        <svg
                                            v-else
                                            class="w-16 h-16 text-gray-300"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                            ></path>
                                        </svg>
                                        <!-- Add to Cart Icon Overlay -->
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 flex items-center justify-center transition-all duration-200"
                                        >
                                            <div
                                                class="transform scale-0 group-hover:scale-100 transition-transform duration-200"
                                            >
                                                <div
                                                    class="bg-primary-500 text-white rounded-full p-2"
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="space-y-1">
                                        <h3
                                            class="font-semibold text-gray-900 text-sm leading-tight line-clamp-2 min-h-[2.5rem]"
                                        >
                                            {{ product.name }}
                                        </h3>
                                        <p
                                            class="text-xs text-gray-500 flex items-center"
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
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                                ></path>
                                            </svg>
                                            {{ product.category }}
                                        </p>
                                        <div class="pt-2">
                                            <span
                                                class="text-xl font-bold text-primary-600"
                                                >฿{{
                                                    formatPrice(
                                                        product.selling_price
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div v-if="loading" class="text-center py-8">
                                <div
                                    class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"
                                ></div>
                                <p class="mt-2 text-gray-500">
                                    กำลังโหลดสินค้า...
                                </p>
                            </div>

                            <!-- No Products State -->
                            <div
                                v-else-if="products.length === 0"
                                class="text-center py-8 text-gray-500"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                    ></path>
                                </svg>
                                <p class="mt-2">ไม่พบสินค้า</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Modal -->
                <div
                    v-if="showPaymentModal"
                    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                >
                    <div
                        class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold">ชำระเงิน</h3>
                                <button
                                    @click="closePaymentModal"
                                    class="text-gray-400 hover:text-gray-600"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        ></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Invoice Type Selection -->
                            <div class="mb-6">
                                <h4 class="font-medium mb-3">ประเภทบิล</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <label
                                        class="flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                        :class="
                                            invoiceType === 'cash_bill'
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-gray-200 hover:border-gray-300'
                                        "
                                    >
                                        <input
                                            v-model="invoiceType"
                                            type="radio"
                                            value="cash_bill"
                                            class="sr-only"
                                        />
                                        <span class="text-3xl mb-2">🧾</span>
                                        <span class="font-medium text-sm"
                                            >บิลเงินสด</span
                                        >
                                        <span class="text-xs text-gray-500"
                                            >ใบเสร็จทั่วไป</span
                                        >
                                    </label>
                                    <label
                                        class="flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                        :class="
                                            invoiceType === 'tax_invoice'
                                                ? 'border-green-500 bg-green-50'
                                                : 'border-gray-200 hover:border-gray-300'
                                        "
                                    >
                                        <input
                                            v-model="invoiceType"
                                            type="radio"
                                            value="tax_invoice"
                                            class="sr-only"
                                        />
                                        <span class="text-3xl mb-2">📄</span>
                                        <span class="font-medium text-sm"
                                            >ใบกำกับภาษี</span
                                        >
                                        <span class="text-xs text-gray-500"
                                            >Tax Invoice</span
                                        >
                                    </label>
                                </div>
                            </div>

                            <!-- Tax Invoice Customer Form -->
                            <div
                                v-if="invoiceType === 'tax_invoice'"
                                class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200"
                            >
                                <h4 class="font-medium mb-3 text-green-800">
                                    ข้อมูลลูกค้าสำหรับใบกำกับภาษี
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                            >ชื่อ-นามสกุล / ชื่อบริษัท
                                            <span class="text-red-500"
                                                >*</span
                                            ></label
                                        >
                                        <input
                                            v-model="
                                                taxInvoiceCustomer.company_name
                                            "
                                            type="text"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500"
                                            placeholder="ชื่อบริษัท หรือ ชื่อ-นามสกุล"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                            >ที่อยู่
                                            <span class="text-red-500"
                                                >*</span
                                            ></label
                                        >
                                        <textarea
                                            v-model="taxInvoiceCustomer.address"
                                            rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500"
                                            placeholder="ที่อยู่สำหรับออกใบกำกับภาษี"
                                            required
                                        ></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >เลขประจำตัวผู้เสียภาษี</label
                                            >
                                            <input
                                                v-model="
                                                    taxInvoiceCustomer.tax_id
                                                "
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500"
                                                placeholder="13 หลัก (ไม่บังคับ)"
                                                maxlength="13"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >สาขา</label
                                            >
                                            <select
                                                v-model="
                                                    taxInvoiceCustomer.branch
                                                "
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500"
                                            >
                                                <option value="สำนักงานใหญ่">
                                                    สำนักงานใหญ่
                                                </option>
                                                <option value="สาขา">
                                                    สาขา
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                            >เบอร์โทร</label
                                        >
                                        <input
                                            v-model="taxInvoiceCustomer.phone"
                                            type="tel"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500"
                                            placeholder="เบอร์โทรศัพท์ (ไม่บังคับ)"
                                        />
                                    </div>
                                </div>
                                <!-- Auto-fill from existing customer -->
                                <div
                                    v-if="selectedMainCustomer"
                                    class="mt-3 pt-3 border-t border-green-200"
                                >
                                    <button
                                        @click="fillTaxInvoiceFromCustomer"
                                        type="button"
                                        class="text-sm text-green-600 hover:text-green-800 flex items-center"
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
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                            ></path>
                                        </svg>
                                        ดึงข้อมูลจากลูกค้าที่เลือก ({{
                                            selectedMainCustomer.name
                                        }})
                                    </button>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-medium mb-2">สรุปคำสั่งซื้อ</h4>

                                <!-- VAT Toggle for Payment Modal -->
                                <div
                                    class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <h5
                                                class="font-medium text-sm text-blue-800"
                                            >
                                                ภาษีมูลค่าเพิ่ม (VAT)
                                            </h5>
                                            <p class="text-xs text-blue-600">
                                                {{
                                                    invoiceType ===
                                                    "tax_invoice"
                                                        ? "ใบกำกับภาษีต้องมี VAT"
                                                        : "เลือกใช้หรือไม่ใช้ VAT 7%"
                                                }}
                                            </p>
                                        </div>
                                        <label
                                            class="relative inline-flex items-center cursor-pointer"
                                        >
                                            <input
                                                v-model="includeVAT"
                                                type="checkbox"
                                                class="sr-only peer"
                                                aria-label="เปิด/ปิด VAT"
                                                :disabled="
                                                    invoiceType ===
                                                    'tax_invoice'
                                                "
                                            />
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-50"
                                            ></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span>จำนวนรายการ:</span>
                                        <span
                                            >{{ cartItems.length }} รายการ</span
                                        >
                                    </div>
                                    <div class="flex justify-between">
                                        <span>ยอดรวม:</span>
                                        <span
                                            >฿{{ formatPrice(cartTotal) }}</span
                                        >
                                    </div>
                                    <div
                                        v-if="promotionDiscount > 0"
                                        class="flex justify-between text-green-600"
                                    >
                                        <span
                                            >ส่วนลด ({{
                                                appliedPromotion?.name
                                            }}):</span
                                        >
                                        <span
                                            >-฿{{
                                                formatPrice(promotionDiscount)
                                            }}</span
                                        >
                                    </div>
                                    <!-- Shipping Fee -->
                                    <div class="flex justify-between items-center">
                                        <span>ค่าจัดส่ง:</span>
                                        <div class="flex items-center">
                                            <span class="mr-1">฿</span>
                                            <input
                                                v-model.number="shippingFee"
                                                type="number"
                                                min="0"
                                                step="1"
                                                class="w-24 px-2 py-1 text-right border border-gray-300 rounded text-sm"
                                                placeholder="0"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        v-if="includeVAT"
                                        class="flex justify-between"
                                    >
                                        <span>ภาษี (7%):</span>
                                        <span>฿{{ formatPrice(cartTax) }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between font-bold text-lg border-t pt-1"
                                    >
                                        <span>รวมทั้งสิ้น:</span>
                                        <span class="text-primary-600"
                                            >฿{{
                                                formatPrice(cartGrandTotal)
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Promotions Section -->
                            <div class="mb-6">
                                <h4 class="font-medium mb-3">
                                    โปรโมชั่นที่ใช้ได้
                                </h4>

                                <!-- Promotion Dropdown -->
                                <div class="relative">
                                    <button
                                        @click="
                                            showPromotionDropdown =
                                                !showPromotionDropdown
                                        "
                                        class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        :class="{
                                            'border-green-500 bg-green-50':
                                                appliedPromotion,
                                            'border-gray-300':
                                                !appliedPromotion,
                                        }"
                                    >
                                        <div class="flex items-center">
                                            <div
                                                v-if="appliedPromotion"
                                                class="flex items-center"
                                            >
                                                <div class="flex-shrink-0 mr-3">
                                                    <div
                                                        v-if="
                                                            appliedPromotion.type ===
                                                            'percentage'
                                                        "
                                                        class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center"
                                                    >
                                                        <svg
                                                            class="w-3 h-3 text-blue-600"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                                                clip-rule="evenodd"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                    <div
                                                        v-else-if="
                                                            appliedPromotion.type ===
                                                            'fixed_amount'
                                                        "
                                                        class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center"
                                                    >
                                                        <svg
                                                            class="w-3 h-3 text-green-600"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path
                                                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
                                                            ></path>
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                                clip-rule="evenodd"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                    <div
                                                        v-else
                                                        class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center"
                                                    >
                                                        <svg
                                                            class="w-3 h-3 text-purple-600"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path
                                                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-medium text-sm text-gray-900"
                                                    >
                                                        {{
                                                            appliedPromotion.name
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-xs font-medium"
                                                        :class="
                                                            getPromotionColorClass(
                                                                appliedPromotion.type
                                                            )
                                                        "
                                                    >
                                                        {{
                                                            getPromotionLabel(
                                                                appliedPromotion
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="text-gray-500">
                                                เลือกโปรโมชั่น
                                            </div>
                                        </div>

                                        <svg
                                            class="w-5 h-5 text-gray-400 transition-transform"
                                            :class="{
                                                'rotate-180':
                                                    showPromotionDropdown,
                                            }"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            ></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div
                                        v-if="showPromotionDropdown"
                                        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                                    >
                                        <!-- No Promotion Option -->
                                        <div
                                            @click="
                                                removePromotion();
                                                showPromotionDropdown = false;
                                            "
                                            class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                            :class="{
                                                'bg-blue-50': !appliedPromotion,
                                            }"
                                        >
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 mr-3">
                                                    <div
                                                        class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center"
                                                    >
                                                        <svg
                                                            class="w-3 h-3 text-gray-500"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                clip-rule="evenodd"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-medium text-sm text-gray-900"
                                                    >
                                                        ไม่ใช้โปรโมชั่น
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500"
                                                    >
                                                        ราคาปกติ
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex-shrink-0 ml-auto"
                                                >
                                                    <svg
                                                        v-if="!appliedPromotion"
                                                        class="w-4 h-4 text-blue-500"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd"
                                                        ></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- No Promotions Available Message -->
                                        <div
                                            v-if="
                                                availablePromotions.length === 0
                                            "
                                            class="px-4 py-3 text-center text-gray-500"
                                        >
                                            <div
                                                class="flex items-center justify-center"
                                            >
                                                <svg
                                                    class="w-5 h-5 text-gray-400 mr-2"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                                <span class="text-sm"
                                                    >ไม่มีโปรโมชั่นที่ใช้ได้สำหรับสินค้าในตะกร้า</span
                                                >
                                            </div>
                                        </div>

                                        <!-- Promotion Options -->
                                        <div
                                            v-for="promotion in availablePromotions"
                                            :key="promotion.id"
                                            @click="
                                                applyPromotion(promotion);
                                                showPromotionDropdown = false;
                                            "
                                            class="px-4 py-3 hover:bg-gray-50 cursor-pointer"
                                            :class="{
                                                'bg-green-50':
                                                    appliedPromotion?.id ===
                                                    promotion.id,
                                            }"
                                        >
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 mr-3">
                                                    <div
                                                        v-if="
                                                            promotion.type ===
                                                            'percentage'
                                                        "
                                                        class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center"
                                                    >
                                                        <svg
                                                            class="w-3 h-3 text-blue-600"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                                                clip-rule="evenodd"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                    <div
                                                        v-else-if="
                                                            promotion.type ===
                                                            'fixed_amount'
                                                        "
                                                        class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center"
                                                    >
                                                        <svg
                                                            class="w-3 h-3 text-green-600"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path
                                                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
                                                            ></path>
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                                clip-rule="evenodd"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                    <div
                                                        v-else
                                                        class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center"
                                                    >
                                                        <svg
                                                            class="w-3 h-3 text-purple-600"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path
                                                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
                                                            ></path>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div class="flex-1">
                                                    <div
                                                        class="font-medium text-sm text-gray-900"
                                                    >
                                                        {{ promotion.name }}
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500"
                                                    >
                                                        {{
                                                            promotion.description
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-xs font-medium mt-1"
                                                        :class="
                                                            getPromotionColorClass(
                                                                promotion.type
                                                            )
                                                        "
                                                    >
                                                        {{
                                                            getPromotionLabel(
                                                                promotion
                                                            )
                                                        }}
                                                    </div>
                                                </div>

                                                <div class="flex-shrink-0 ml-2">
                                                    <svg
                                                        v-if="
                                                            appliedPromotion?.id ===
                                                            promotion.id
                                                        "
                                                        class="w-4 h-4 text-green-500"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd"
                                                        ></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Applied Promotion Summary -->
                                <div
                                    v-if="appliedPromotion"
                                    class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center">
                                            <svg
                                                class="w-4 h-4 text-green-600 mr-2"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"
                                                ></path>
                                            </svg>
                                            <span
                                                class="text-sm font-medium text-green-800"
                                                >โปรโมชั่นที่ใช้:
                                                {{
                                                    appliedPromotion.name
                                                }}</span
                                            >
                                        </div>
                                        <span
                                            class="text-sm font-bold text-green-600"
                                            >-฿{{
                                                formatPrice(promotionDiscount)
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="mb-6">
                                <h4 class="font-medium mb-3">
                                    วิธีการชำระเงิน
                                </h4>
                                <div class="space-y-2">
                                    <label
                                        class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                    >
                                        <input
                                            v-model="paymentMethod"
                                            type="radio"
                                            value="cash"
                                            class="mr-3"
                                        />
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-2"
                                                >💰</span
                                            >
                                            <span>เงินสด</span>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                        :class="{
                                            'opacity-50 cursor-not-allowed':
                                                !selectedMainCustomer,
                                        }"
                                    >
                                        <input
                                            v-model="paymentMethod"
                                            type="radio"
                                            value="customer_account"
                                            class="mr-3"
                                            :disabled="!selectedMainCustomer"
                                        />
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-2"
                                                >🏢</span
                                            >
                                            <span>เครดิต (บัญชีลูกค้า)</span>
                                        </div>
                                    </label>
                                    <div
                                        v-if="
                                            !selectedMainCustomer &&
                                            paymentMethod === 'customer_account'
                                        "
                                        class="text-sm text-red-600 ml-6"
                                    >
                                        กรุณาเลือกลูกค้าก่อนใช้การชำระแบบเครดิต
                                    </div>
                                </div>
                            </div>

                            <!-- Cash Payment Details -->
                            <div v-if="paymentMethod === 'cash'" class="mb-6">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >จำนวนเงินที่รับ</label
                                >
                                <input
                                    v-model.number="receivedAmount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    placeholder="0.00"
                                />
                                <div
                                    v-if="receivedAmount >= cartGrandTotal"
                                    class="mt-2 p-2 bg-green-50 rounded text-sm"
                                >
                                    <span class="text-green-800"
                                        >เงินทอน: ฿{{
                                            formatPrice(
                                                receivedAmount - cartGrandTotal
                                            )
                                        }}</span
                                    >
                                </div>
                                <div
                                    v-else-if="receivedAmount > 0"
                                    class="mt-2 p-2 bg-red-50 rounded text-sm"
                                >
                                    <span class="text-red-800"
                                        >เงินไม่เพียงพอ</span
                                    >
                                </div>
                            </div>

                            <!-- Customer Account Selection -->
                            <div
                                v-if="paymentMethod === 'customer_account'"
                                class="mb-6"
                            >
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >เลือกลูกค้า</label
                                >
                                <input
                                    v-model="customerSearch"
                                    @input="searchCustomers"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500"
                                    placeholder="ค้นหาลูกค้า..."
                                />
                                <div
                                    v-if="customers.length > 0"
                                    class="mt-2 max-h-40 overflow-y-auto border rounded-lg"
                                >
                                    <div
                                        v-for="customer in customers"
                                        :key="customer.id"
                                        @click="selectedMainCustomer = customer"
                                        class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                                        :class="{
                                            'bg-primary-50':
                                                selectedMainCustomer?.id ===
                                                customer.id,
                                        }"
                                    >
                                        <div class="font-medium">
                                            {{ customer.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ customer.company_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            วงเงินคงเหลือ: ฿{{
                                                formatPrice(
                                                    customer.available_credit
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-if="selectedMainCustomer"
                                    class="mt-2 p-3 bg-blue-50 rounded-lg"
                                >
                                    <div class="font-medium">
                                        {{ selectedMainCustomer.name }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        วงเงินคงเหลือ: ฿{{
                                            formatPrice(
                                                selectedMainCustomer.available_credit
                                            )
                                        }}
                                    </div>
                                    <div
                                        v-if="
                                            selectedMainCustomer.available_credit <
                                            cartGrandTotal
                                        "
                                        class="text-sm text-red-600 mt-1"
                                    >
                                        วงเงินไม่เพียงพอ
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3">
                                <button
                                    @click="closePaymentModal"
                                    class="flex-1 btn btn-outline"
                                >
                                    ยกเลิก
                                </button>
                                <button
                                    @click="confirmPayment"
                                    :disabled="!canProcessPayment"
                                    class="flex-1 btn btn-primary"
                                >
                                    ยืนยันการชำระเงิน
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// Reactive data
const searchQuery = ref("");
const selectedCategory = ref("");
const showMobileSearch = ref(false);
const showMobileCart = ref(false);
const showPaymentModal = ref(false);
const loading = ref(false);
const currentDateTime = ref("");

// Invoice Type Selection (บิลเงินสด / ใบกำกับภาษี)
const invoiceType = ref("cash_bill"); // 'cash_bill' or 'tax_invoice'
const showInvoiceTypeModal = ref(false);

// Tax Invoice Customer Info
const taxInvoiceCustomer = ref({
    name: "",
    company_name: "",
    address: "",
    tax_id: "",
    phone: "",
    branch: "สำนักงานใหญ่",
});

// Barcode Scanner
const barcodeInput = ref(null);
const barcodeSearch = ref("");
const barcodeError = ref("");

// Global Barcode Buffer (สำหรับยิง Barcode โดยไม่ต้องคลิกช่อง)
let barcodeBuffer = "";
let barcodeTimeout = null;

const handleGlobalKeydown = (event) => {
    // ไม่ทำงานถ้ากำลังพิมพ์ในช่อง input อื่น (ยกเว้น barcode input)
    const activeElement = document.activeElement;
    const isInputField =
        activeElement.tagName === "INPUT" ||
        activeElement.tagName === "TEXTAREA" ||
        activeElement.isContentEditable;

    // ถ้าอยู่ใน barcode input อยู่แล้ว ให้ทำงานปกติ
    if (activeElement === barcodeInput.value) {
        return;
    }

    // ถ้าอยู่ใน input อื่น (เช่น ค้นหา, จำนวน) ไม่ต้องจับ barcode
    if (isInputField) {
        return;
    }

    // ถ้ากด Enter และมี buffer ให้ค้นหา
    if (event.key === "Enter" && barcodeBuffer.length > 0) {
        event.preventDefault();
        barcodeSearch.value = barcodeBuffer;
        searchByBarcode();
        barcodeBuffer = "";
        return;
    }

    // จับเฉพาะตัวเลขและตัวอักษร (Barcode ปกติ)
    if (event.key.length === 1 && /[a-zA-Z0-9]/.test(event.key)) {
        event.preventDefault();
        barcodeBuffer += event.key;

        // Clear timeout เดิม
        if (barcodeTimeout) {
            clearTimeout(barcodeTimeout);
        }

        // ตั้ง timeout ใหม่ - ถ้าไม่มีการพิมพ์ต่อใน 100ms ให้ค้นหา
        // (Scanner ยิงเร็วมาก ถ้าพิมพ์มือจะช้ากว่านี้)
        barcodeTimeout = setTimeout(() => {
            if (barcodeBuffer.length >= 3) {
                // Barcode ต้องมีอย่างน้อย 3 ตัว
                barcodeSearch.value = barcodeBuffer;
                searchByBarcode();
            }
            barcodeBuffer = "";
        }, 100);
    }
};

// Products and categories
const products = ref([]);
const categories = ref([]);
const cartItems = ref([]);

// Promotion data
const allPromotions = ref([]);
const selectedPromotion = ref(null);
const appliedPromotion = ref(null);
const promotionDiscount = ref(0);
const showPromotionDropdown = ref(false);

// Computed: Available promotions based on cart items
const availablePromotions = computed(() => {
    if (cartItems.value.length === 0) return [];

    return allPromotions.value.filter((promo) => {
        // Check if promotion applies to any product in cart
        if (promo.applicable_products && promo.applicable_products.length > 0) {
            return cartItems.value.some((item) =>
                promo.applicable_products.includes(item.product_id)
            );
        }
        // If no specific products, promotion applies to all
        return true;
    });
});

// Payment data
const paymentMethod = ref("cash");
const receivedAmount = ref(0);
const customerSearch = ref("");
const customers = ref([]);
// Remove selectedCustomer since we're using selectedMainCustomer now

// Main customer selection (separate from payment modal)
const mainCustomerSearch = ref("");
const mainCustomers = ref([]);
const selectedMainCustomer = ref(null);

// VAT toggle
const includeVAT = ref(true);

// Shipping fee
const shippingFee = ref(0);

// Computed properties
// Note: filteredProducts removed because backend already filters the results
// We use products.value directly which contains filtered results from API

const categoryFilters = computed(() => {
    const filters = [
        {
            id: "",
            label: "ทั้งหมด",
            count: products.value.length,
            active: selectedCategory.value === "",
        },
    ];

    categories.value.forEach((category) => {
        const count = products.value.filter(
            (p) => p.category_id === category.id
        ).length;
        filters.push({
            id: category.id,
            label: category.name,
            count: count,
            active: selectedCategory.value == category.id,
        });
    });

    return filters;
});

const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
        const itemTotal = Number(item.price) * Number(item.quantity);
        return total + itemTotal;
    }, 0);
});

const cartTax = computed(() => {
    if (!includeVAT.value) return 0;
    const subtotal = cartTotal.value - promotionDiscount.value;
    return subtotal * 0.07;
});

const cartGrandTotal = computed(() => {
    const subtotal = cartTotal.value - promotionDiscount.value;
    const tax = includeVAT.value ? subtotal * 0.07 : 0;
    const shipping = shippingFee.value || 0;
    return subtotal + tax + shipping;
});

const canProcessPayment = computed(() => {
    if (cartItems.value.length === 0) return false;

    // Check tax invoice validation
    if (invoiceType.value === "tax_invoice") {
        if (
            !taxInvoiceCustomer.value.company_name?.trim() ||
            !taxInvoiceCustomer.value.address?.trim()
        ) {
            return false;
        }
    }

    if (paymentMethod.value === "cash") {
        return receivedAmount.value >= cartGrandTotal.value;
    }

    if (paymentMethod.value === "customer_account") {
        return (
            selectedMainCustomer.value &&
            selectedMainCustomer.value.available_credit >= cartGrandTotal.value
        );
    }

    return true;
});

// Methods
const formatPrice = (price) => {
    return new Intl.NumberFormat("th-TH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(price || 0);
};

const stockStatusClass = (stock) => {
    if (stock <= 0) return "bg-red-100 text-red-800";
    if (stock <= 10) return "bg-yellow-100 text-yellow-800";
    return "bg-green-100 text-green-800";
};

const searchProducts = async () => {
    if (searchQuery.value.length < 2 && !selectedCategory.value) {
        await loadProducts();
        return;
    }

    loading.value = true;
    try {
        // Build query params
        const params = new URLSearchParams();
        if (searchQuery.value) params.append("query", searchQuery.value);
        if (selectedCategory.value)
            params.append("category", selectedCategory.value);

        const response = await fetch(
            `/pos/search-products?${params.toString()}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        // Backend returns products array directly, not wrapped in { products: [] }
        products.value = Array.isArray(data) ? data : data.products || [];
    } catch (error) {
        console.error("Error searching products:", error);
        products.value = [];
    } finally {
        loading.value = false;
    }
};

const searchCustomers = async () => {
    if (customerSearch.value.length < 2) {
        customers.value = [];
        return;
    }

    try {
        const response = await fetch(
            `/pos/search-customers?query=${encodeURIComponent(
                customerSearch.value
            )}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        customers.value = data.customers || [];
    } catch (error) {
        console.error("Error searching customers:", error);
        customers.value = [];
    }
};

// Main customer search functions
const searchMainCustomers = async () => {
    if (mainCustomerSearch.value.length < 2) {
        mainCustomers.value = [];
        return;
    }

    try {
        const response = await fetch(
            `/pos/search-customers?query=${encodeURIComponent(
                mainCustomerSearch.value
            )}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        mainCustomers.value = data.customers || [];
    } catch (error) {
        console.error("Error searching main customers:", error);
        mainCustomers.value = [];
    }
};

const selectMainCustomer = (customer) => {
    selectedMainCustomer.value = customer;
    mainCustomerSearch.value = customer.name || customer.company_name;
    mainCustomers.value = [];
};

const clearMainCustomer = () => {
    selectedMainCustomer.value = null;
    mainCustomerSearch.value = "";
    mainCustomers.value = [];
};

// Barcode Scanner Functions
const searchByBarcode = async () => {
    if (!barcodeSearch.value.trim()) {
        barcodeError.value = "❌ กรุณาสแกนหรือกรอก Barcode";
        return;
    }

    barcodeError.value = "";

    try {
        const response = await fetch(
            `/pos/barcode/${encodeURIComponent(barcodeSearch.value.trim())}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            }
        );

        if (!response.ok) {
            if (response.status === 404) {
                barcodeError.value = `❌ ไม่พบสินค้าที่มี Barcode: ${barcodeSearch.value}`;
            } else {
                barcodeError.value = "❌ เกิดข้อผิดพลาดในการค้นหา";
            }
            return;
        }

        const product = await response.json();

        if (product && product.id) {
            addToCart(product);
            barcodeSearch.value = "";
            barcodeError.value = "";

            // Focus back to barcode input for next scan
            if (barcodeInput.value) {
                barcodeInput.value.focus();
            }
        } else {
            barcodeError.value = `❌ ไม่พบสินค้าที่มี Barcode: ${barcodeSearch.value}`;
        }
    } catch (error) {
        console.error("Error searching by barcode:", error);
        barcodeError.value = "❌ เกิดข้อผิดพลาดในการค้นหา";
    }
};

const loadProducts = async () => {
    loading.value = true;
    try {
        const response = await fetch("/pos/search-products", {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        products.value = Array.isArray(data) ? data : data.products || [];
    } catch (error) {
        console.error("Error loading products:", error);
        products.value = [];
    } finally {
        loading.value = false;
    }
};

const loadCategories = async () => {
    try {
        const response = await fetch("/api/categories", {
            method: "GET",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        categories.value = data.data || [];
    } catch (error) {
        console.error("Error loading categories:", error);
    }
};

const addToCart = (product) => {
    if (product.current_stock <= 0) return;

    const existingItem = cartItems.value.find(
        (item) => item.product_id === product.id
    );

    if (existingItem) {
        if (existingItem.quantity < product.current_stock) {
            existingItem.quantity++;
        }
    } else {
        cartItems.value.push({
            id: Date.now() + Math.random(),
            product_id: product.id,
            product_name: product.name,
            price: product.selling_price,
            quantity: 1,
            max_stock: product.current_stock,
        });
    }
};

const updateCartQuantity = ({ id, change }) => {
    const item = cartItems.value.find((item) => item.id === id);
    if (!item) return;

    const newQuantity = item.quantity + change;

    if (newQuantity <= 0) {
        removeFromCart(id);
    } else if (newQuantity <= item.max_stock) {
        item.quantity = newQuantity;
    }
};

const removeFromCart = (id) => {
    const index = cartItems.value.findIndex((item) => item.id === id);
    if (index > -1) {
        cartItems.value.splice(index, 1);
    }
};

const clearCart = () => {
    cartItems.value = [];
    // Reset promotion when clearing cart
    appliedPromotion.value = null;
    promotionDiscount.value = 0;
    // Reset shipping fee
    shippingFee.value = 0;
    // Clear selected customer
    selectedMainCustomer.value = null;
    mainCustomerSearch.value = "";
    mainCustomers.value = [];
};

const selectCategory = (filter) => {
    selectedCategory.value = filter.id;
    searchProducts();
};

const clearSearch = () => {
    searchQuery.value = "";
    selectedCategory.value = "";
    searchProducts();
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    paymentMethod.value = "cash";
    receivedAmount.value = 0;
    customerSearch.value = "";
    customers.value = [];
    invoiceType.value = "cash_bill";
    taxInvoiceCustomer.value = {
        name: "",
        company_name: "",
        address: "",
        tax_id: "",
        phone: "",
        branch: "สำนักงานใหญ่",
    };
};

// Fill tax invoice form from selected customer
const fillTaxInvoiceFromCustomer = () => {
    if (selectedMainCustomer.value) {
        taxInvoiceCustomer.value = {
            name: selectedMainCustomer.value.name || "",
            company_name:
                selectedMainCustomer.value.company_name ||
                selectedMainCustomer.value.name ||
                "",
            address: selectedMainCustomer.value.address || "",
            tax_id: selectedMainCustomer.value.tax_id || "",
            phone: selectedMainCustomer.value.phone || "",
            branch: "สำนักงานใหญ่",
        };
    }
};

// Validate tax invoice form
const isTaxInvoiceValid = computed(() => {
    if (invoiceType.value !== "tax_invoice") return true;
    return (
        taxInvoiceCustomer.value.company_name?.trim() &&
        taxInvoiceCustomer.value.address?.trim()
    );
});

const confirmPayment = async () => {
    // Validate tax invoice if selected
    if (invoiceType.value === "tax_invoice" && !isTaxInvoiceValid.value) {
        alert(
            "กรุณากรอกข้อมูลลูกค้าสำหรับใบกำกับภาษีให้ครบถ้วน (ชื่อ/บริษัท และ ที่อยู่)"
        );
        return;
    }

    try {
        const saleData = {
            items: cartItems.value.map((item) => ({
                product_id: item.product_id,
                quantity: item.quantity,
                unit_price: item.price,
            })),
            payment_method: paymentMethod.value,
            total_amount: cartGrandTotal.value,
            tax_amount: cartTax.value,
            customer_id: selectedMainCustomer.value?.id || null,
            received_amount:
                paymentMethod.value === "cash"
                    ? receivedAmount.value
                    : cartGrandTotal.value,
            promotion_id: appliedPromotion.value?.id || null,
            discount_amount: promotionDiscount.value,
            shipping_fee: shippingFee.value || 0,
            // Invoice type and tax invoice customer info
            invoice_type: invoiceType.value,
            tax_invoice_customer:
                invoiceType.value === "tax_invoice"
                    ? taxInvoiceCustomer.value
                    : null,
        };

        const response = await fetch("/pos/process-sale", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify(saleData),
        });

        if (!response.ok) {
            const error = await response.json();
            alert(
                "เกิดข้อผิดพลาด: " +
                    (error.message || error.error || "ไม่สามารถบันทึกการขายได้")
            );
            return;
        }

        const result = await response.json();

        if (result.success) {
            // Store invoice type before clearing
            const currentInvoiceType = invoiceType.value;

            // Clear cart and close modal
            clearCart();
            closePaymentModal();

            // Show success message with receipt option
            const invoiceLabel =
                currentInvoiceType === "tax_invoice"
                    ? "ใบกำกับภาษี"
                    : "บิลเงินสด";
            const printReceipt = confirm(
                `การขายสำเร็จ! หมายเลข${invoiceLabel}: ${result.sale_number}\n\nต้องการพิมพ์${invoiceLabel}หรือไม่?`
            );

            if (printReceipt) {
                // Open receipt in new window - use different template based on invoice type
                if (currentInvoiceType === "tax_invoice") {
                    // Tax invoice uses dot matrix template
                    window.open(
                        `/receipts/${result.sale.id}/print?type=tax_invoice`,
                        "_blank",
                        "width=900,height=700"
                    );
                } else {
                    // Cash bill uses thermal/a4 template
                    window.open(
                        `/receipts/${result.sale.id}/print`,
                        "_blank",
                        "width=800,height=600"
                    );
                }

                // Wait a moment then redirect to sales history
                setTimeout(() => {
                    router.visit("/sales");
                }, 1000);
            } else {
                // If not printing, redirect immediately
                router.visit("/sales");
            }
        } else {
            alert(
                "เกิดข้อผิดพลาด: " +
                    (result.message || "ไม่สามารถบันทึกการขายได้")
            );
        }
    } catch (error) {
        console.error("Error processing payment:", error);
        alert("เกิดข้อผิดพลาดในการประมวลผลการชำระเงิน");
    }
};

const generateReceipt = (sale) => {
    const receiptWindow = window.open("", "_blank", "width=300,height=600");

    const receiptHTML = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>ใบเสร็จ - ${sale.sale_number}</title>
      <style>
        body {
          font-family: 'Courier New', monospace;
          font-size: 12px;
          margin: 10px;
          line-height: 1.4;
        }
        .header {
          text-align: center;
          border-bottom: 1px dashed #000;
          padding-bottom: 10px;
          margin-bottom: 10px;
        }
        .shop-name {
          font-size: 16px;
          font-weight: bold;
          margin-bottom: 5px;
        }
        .receipt-info {
          margin-bottom: 10px;
          border-bottom: 1px dashed #000;
          padding-bottom: 10px;
        }
        .items {
          margin-bottom: 10px;
        }
        .item {
          display: flex;
          justify-content: space-between;
          margin-bottom: 3px;
        }
        .item-name {
          flex: 1;
          margin-right: 10px;
        }
        .item-qty-price {
          text-align: right;
          min-width: 80px;
        }
        .totals {
          border-top: 1px dashed #000;
          padding-top: 10px;
          margin-top: 10px;
        }
        .total-line {
          display: flex;
          justify-content: space-between;
          margin-bottom: 3px;
        }
        .grand-total {
          font-weight: bold;
          font-size: 14px;
          border-top: 1px solid #000;
          padding-top: 5px;
          margin-top: 5px;
        }
        .footer {
          text-align: center;
          margin-top: 20px;
          border-top: 1px dashed #000;
          padding-top: 10px;
          font-size: 10px;
        }
        @media print {
          body { margin: 0; }
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>
      <div class="header">
        <div class="shop-name">ร้านค้า POS System</div>
        <div>โทร: 02-xxx-xxxx</div>
        <div>ที่อยู่: 123 ถนนสุขุมวิท กรุงเทพฯ</div>
      </div>

      <div class="receipt-info">
        <div><strong>ใบเสร็จเลขที่:</strong> ${sale.sale_number}</div>
        <div><strong>วันที่:</strong> ${new Date(
            sale.created_at || Date.now()
        ).toLocaleString("th-TH")}</div>
        <div><strong>พนักงานขาย:</strong> ${
            sale.cashier?.username || "N/A"
        }</div>
        ${
            sale.customer
                ? `<div><strong>ลูกค้า:</strong> ${sale.customer.name}</div>`
                : ""
        }
      </div>

      <div class="items">
        ${
            sale.sale_items
                ?.map(
                    (item) => `
          <div class="item">
            <div class="item-name">${item.product?.name || "สินค้า"}</div>
            <div class="item-qty-price">${item.quantity} x ${parseFloat(
                        item.unit_price
                    ).toFixed(2)}</div>
          </div>
          <div class="item">
            <div class="item-name"></div>
            <div class="item-qty-price">${parseFloat(
                item.total_price || item.quantity * item.unit_price
            ).toFixed(2)}</div>
          </div>
        `
                )
                .join("") || ""
        }
      </div>

      <div class="totals">
        <div class="total-line">
          <span>ยอดรวม:</span>
          <span>฿${parseFloat(sale.subtotal || 0).toFixed(2)}</span>
        </div>
        ${
            sale.discount_amount > 0
                ? `
          <div class="total-line">
            <span>ส่วนลด:</span>
            <span>-฿${parseFloat(sale.discount_amount).toFixed(2)}</span>
          </div>
        `
                : ""
        }
        ${
            sale.tax_amount > 0
                ? `
          <div class="total-line">
            <span>ภาษี (7%):</span>
            <span>฿${parseFloat(sale.tax_amount).toFixed(2)}</span>
          </div>
        `
                : ""
        }
        <div class="total-line grand-total">
          <span>รวมทั้งสิ้น:</span>
          <span>฿${parseFloat(sale.total_amount).toFixed(2)}</span>
        </div>
        <div class="total-line">
          <span>รับเงิน:</span>
          <span>฿${parseFloat(
              sale.received_amount || sale.total_amount
          ).toFixed(2)}</span>
        </div>
        ${
            sale.change_amount > 0
                ? `
          <div class="total-line">
            <span>เงินทอน:</span>
            <span>฿${parseFloat(sale.change_amount).toFixed(2)}</span>
          </div>
        `
                : ""
        }
      </div>

      <div class="footer">
        <div>ขอบคุณที่ใช้บริการ</div>
        <div>โปรดเก็บใบเสร็จไว้เป็นหลักฐาน</div>
        <div class="no-print" style="margin-top: 20px;">
          <button onclick="window.print()">พิมพ์ใบเสร็จ</button>
          <button onclick="window.close()">ปิด</button>
        </div>
      </div>
    </body>
    </html>
  `;

    receiptWindow.document.write(receiptHTML);
    receiptWindow.document.close();

    // Auto print after a short delay
    setTimeout(() => {
        receiptWindow.print();
    }, 500);
};

const updateDateTime = () => {
    const now = new Date();
    currentDateTime.value = now.toLocaleString("th-TH", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
};

// Promotion methods
const loadPromotions = async () => {
    try {
        // Get product IDs from cart items
        const productIds = cartItems.value.map((item) => item.product_id);

        const response = await fetch("/pos/promotions/active", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
            },
            body: JSON.stringify({
                product_ids: productIds,
                cart_total: cartTotal.value,
            }),
        });

        if (response.ok) {
            const promotions = await response.json();
            allPromotions.value = promotions;
        } else {
            console.error("Failed to load promotions:", response.status);
        }
    } catch (error) {
        console.error("Error loading promotions:", error);
    }
};

const getPromotionLabel = (promotion) => {
    if (!promotion) return "โปรโมชั่นพิเศษ";

    let label = promotion.name || "โปรโมชั่น";

    // ใช้ทั้ง type และ discount_type เพื่อรองรับทั้ง 2 แบบ
    const promoType = promotion.type || promotion.discount_type;
    const promoValue = promotion.value || promotion.discount_value;

    if (promoType === "percentage" && promoValue) {
        label += ` (ลด ${promoValue}%)`;
    } else if (promoType === "fixed_amount" && promoValue) {
        label += ` (ลด ฿${promoValue})`;
    } else if (promoType === "buy_x_get_y") {
        const minQty = promotion.min_quantity || 1;
        const freeQty = promotion.free_quantity || 1;
        label += ` (ซื้อ ${minQty} แถม ${freeQty})`;
    }

    return label;
};

const getPromotionColorClass = (type) => {
    switch (type) {
        case "percentage":
            return "text-blue-600";
        case "fixed_amount":
            return "text-green-600";
        case "buy_x_get_y":
            return "text-purple-600";
        default:
            return "text-orange-600";
    }
};

const handlePromotionChange = () => {
    if (selectedPromotion.value) {
        // Find promotion object by ID
        const promotion = availablePromotions.value.find(
            (p) => p.id === selectedPromotion.value
        );
        if (promotion) {
            applyPromotion(promotion);
        }
    } else {
        removePromotion();
    }
};

const applyPromotion = async (promotion) => {
    if (!promotion) return;

    try {
        const response = await fetch("/pos/promotions/apply", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
            },
            body: JSON.stringify({
                promotion_id: promotion.id,
                cart_items: cartItems.value.map((item) => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    price: item.price,
                })),
            }),
        });

        if (response.ok) {
            const result = await response.json();
            appliedPromotion.value = promotion;
            promotionDiscount.value = result.discount || 0;
        } else {
            const error = await response.json();

            let errorMessage = "ไม่สามารถใช้โปรโมชั่นนี้ได้";
            if (error.error) {
                errorMessage += ": " + error.error;
            } else if (error.message) {
                errorMessage += ": " + error.message;
            }

            alert(errorMessage);
            selectedPromotion.value = null;
            appliedPromotion.value = null;
            promotionDiscount.value = 0;
        }
    } catch (error) {
        console.error("Error applying promotion:", error);
        alert("เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์");
        selectedPromotion.value = null;
        appliedPromotion.value = null;
        promotionDiscount.value = 0;
    }
};

const removePromotion = () => {
    appliedPromotion.value = null;
    promotionDiscount.value = 0;
};

// Watchers
watch(searchQuery, () => {
    if (searchQuery.value.length === 0) {
        loadProducts();
    }
});

// Watch cart items to reload promotions when cart changes
watch(
    cartItems,
    () => {
        loadPromotions();
    },
    { deep: true }
);

// Lifecycle
onMounted(async () => {
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // เพิ่ม Global Barcode Listener
    document.addEventListener("keydown", handleGlobalKeydown);

    await Promise.all([loadProducts(), loadCategories(), loadPromotions()]);
});

onUnmounted(() => {
    // ลบ Global Barcode Listener เมื่อออกจากหน้า
    document.removeEventListener("keydown", handleGlobalKeydown);
    if (barcodeTimeout) {
        clearTimeout(barcodeTimeout);
    }
});
</script>

<style scoped>
.product-card {
    transition: all 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-hover:hover {
    background-color: #f9fafb;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .product-card {
        padding: 12px;
    }

    .product-card h3 {
        font-size: 14px;
    }

    .product-card .aspect-square {
        height: 120px;
    }
}

/* Touch-friendly buttons */
@media (hover: none) and (pointer: coarse) {
    .product-card {
        min-height: 44px;
    }

    button {
        min-height: 44px;
        min-width: 44px;
    }
}
</style>
