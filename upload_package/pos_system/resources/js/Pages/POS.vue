<template>
  <AppLayout title="ระบบขายหน้าร้าน (POS)">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          ระบบขายหน้าร้าน (POS)
        </h2>
        <div class="hidden md:flex items-center space-x-4">
          <span class="text-sm text-gray-600">พนักงาน: {{ $page.props.auth.user.name }}</span>
          <span class="text-sm text-gray-600">{{ currentDateTime }}</span>
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
              <button @click="showMobileSearch = !showMobileSearch" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
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
              >
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
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                {{ filter.label }} ({{ filter.count }})
              </button>
            </div>
          </div>

          <!-- Mobile Product List -->
          <div class="space-y-3 mb-20">
            <div
              v-for="product in filteredProducts"
              :key="product.id"
              class="bg-white rounded-lg shadow-md p-4 cursor-pointer hover:shadow-lg transition-shadow"
              @click="addToCart(product)"
            >
              <div class="flex items-center space-x-3">
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                  <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover rounded-lg">
                  <svg v-else class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                </div>
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900">{{ product.name }}</h4>
                  <p class="text-sm text-gray-500">{{ product.category }}</p>
                  <div class="flex items-center justify-between mt-1">
                    <span class="text-lg font-bold text-primary-600">฿{{ formatPrice(product.selling_price) }}</span>
                    <span :class="stockStatusClass(product.current_stock)" class="px-2 py-1 rounded-full text-xs font-medium">
                      คงเหลือ {{ product.current_stock }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile Cart Summary (Fixed Bottom) -->
          <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 z-50">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm text-gray-600">{{ cartItems.length }} รายการ</span>
              <span class="text-lg font-bold text-primary-600">฿{{ formatPrice(cartGrandTotal) }}</span>
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
          <div v-if="showMobileCart" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end">
            <div class="bg-white w-full max-h-[80vh] rounded-t-xl overflow-hidden">
              <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-semibold">ตะกร้าสินค้า</h3>
                  <button @click="showMobileCart = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="p-4 overflow-y-auto max-h-96">
                <div v-if="cartItems.length === 0" class="text-center py-8 text-gray-500">
                  ไม่มีสินค้าในตะกร้า
                </div>
                <div v-else class="space-y-3">
                  <div v-for="item in cartItems" :key="item.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                      <h4 class="font-medium">{{ item.product_name }}</h4>
                      <p class="text-sm text-gray-500">฿{{ formatPrice(item.price) }} x {{ item.quantity }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <button @click="updateCartQuantity({ id: item.id, change: -1 })" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                      </button>
                      <span class="w-8 text-center">{{ item.quantity }}</span>
                      <button @click="updateCartQuantity({ id: item.id, change: 1 })" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                      </button>
                      <button @click="removeFromCart(item.id)" class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="p-4 border-t border-gray-200">
                <!-- VAT Toggle for Mobile -->
                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="font-medium text-sm text-blue-800">ภาษีมูลค่าเพิ่ม (VAT)</h4>
                      <p class="text-xs text-blue-600">เลือกใช้หรืุอไม่ใช้ VAT 7%</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input v-model="includeVAT" type="checkbox" class="sr-only peer" aria-label="เปิด/ปิด VAT">
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                  </div>
                </div>

                <div class="space-y-2 mb-4">
                  <div class="flex justify-between text-sm">
                    <span>ยอดรวม:</span>
                    <span>฿{{ formatPrice(cartTotal) }}</span>
                  </div>
                  <div v-if="includeVAT" class="flex justify-between text-sm">
                    <span>ภาษี (7%):</span>
                    <span>฿{{ formatPrice(cartTax) }}</span>
                  </div>
                  <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>รวมทั้งสิ้น:</span>
                    <span class="text-primary-600">฿{{ formatPrice(cartGrandTotal) }}</span>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button @click="clearCart" class="flex-1 btn btn-outline">ล้างตะกร้า</button>
                  <button @click="showPaymentModal = true; showMobileCart = false" class="flex-1 btn btn-primary">ชำระเงิน</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden lg:grid lg:grid-cols-12 lg:gap-6">
          <!-- Product Section (8 columns) -->
          <div class="lg:col-span-8">
            <!-- Customer Selection Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
              <h3 class="text-lg font-semibold mb-4">ข้อมูลลูกค้า</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Customer Search -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหาลูกค้า</label>
                  <div class="relative">
                    <input
                      v-model="mainCustomerSearch"
                      @input="searchMainCustomers"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500"
                      placeholder="ค้นหาด้วยชื่อ, เบอร์โทร หรืุอ Line ID..."
                    >
                    <div v-if="mainCustomers.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                      <div
                        v-for="customer in mainCustomers"
                        :key="customer.id"
                        @click="selectMainCustomer(customer)"
                        class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                      >
                        <div class="font-medium">{{ customer.name || customer.company_name }}</div>
                        <div class="text-sm text-gray-500">{{ customer.phone }}</div>
                        <div class="text-sm text-gray-500">วงเงิน: ฿{{ formatPrice(customer.credit_limit) }}</div>
                      </div>
                    </div>
                  </div>

                  <!-- Selected Customer Info -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ลูกค้าที่เลือก</label>
                    <div v-if="selectedMainCustomer" class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                      <div class="flex items-center justify-between">
                        <div class="flex-1">
                          <div class="font-medium text-blue-900">{{ selectedMainCustomer.name || selectedMainCustomer.company_name }}</div>
                          <div class="text-sm text-blue-700">{{ selectedMainCustomer.phone }}</div>
                          <div class="text-sm text-blue-700">
                            วงเงินคงเหลือ: ฿{{ formatPrice(selectedMainCustomer.available_credit) }}
                          </div>
                          <div class="text-xs text-blue-600 mt-1">
                            ประเภท: {{ selectedMainCustomer.customer_type === 'individual' ? 'บุคคลธรรดา' : 'นิติบุคคล' }}
                          </div>
                        </div>
                        <button
                          @click="clearMainCustomer"
                          class="ml-2 text-blue-400 hover:text-blue-600"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                    <div v-else class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                      <div class="text-gray-500 text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm">ยังไม่ได้เลือกลูกค้า</span>
                        <div class="text-xs text-gray-400 mt-1">การขายแบบเงินสด</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div class="flex-1 lg:mr-4">
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                    </div>
                    <input
                      v-model="searchQuery"
                      @input="searchProducts"
                      type="text"
                      class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="ค้นหาสินค้า..."
                    >
                  </div>
                </div>
                <div class="flex space-x-2">
                  <select v-model="selectedCategory" @change="searchProducts" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">หมวดหมู่ทั้งหมด</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Product Grid -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="grid grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                <div
                  v-for="product in products"
                  :key="product.id"
                  class="product-card bg-gray-50 rounded-lg p-4 cursor-pointer hover:shadow-md transition-all duration-200 card-hover"
                  @click="addToCart(product)"
                  :class="{ 'opacity-50 cursor-not-allowed': product.current_stock <= 0 }"
                >
                  <div class="aspect-square bg-gray-200 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                    <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                    <svg v-else class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                  </div>
                  <h3 class="font-medium text-gray-900 text-sm mb-1 line-clamp-2">{{ product.name }}</h3>
                  <p class="text-xs text-gray-500 mb-2">{{ product.category }}</p>
                  <div class="flex items-center justify-between">
                    <span class="text-lg font-bold text-primary-600">฿{{ formatPrice(product.selling_price) }}</span>
                    <span :class="stockStatusClass(product.current_stock)" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ product.current_stock }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Loading State -->
              <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                <p class="mt-2 text-gray-500">กำลังโหลดสินค้า...</p>
              </div>

              <!-- No Products State -->
              <div v-else-if="products.length === 0" class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="mt-2">ไม่พบสินค้า</p>
              </div>
            </div>
          </div>

          <!-- Cart Section (4 columns) -->
          <div class="lg:col-span-4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
              <h3 class="text-lg font-semibold mb-4">ตะกร้าสินค้า</h3>

              <!-- Cart Items -->
              <div class="space-y-3 mb-6 max-h-96 overflow-y-auto">
                <div v-if="cartItems.length === 0" class="text-center py-8 text-gray-500">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v5a2 2 0 01-2 2H9a2 2 0 01-2-2v-5m6-5V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2"></path>
                  </svg>
                  <p class="mt-2">ไม่มีสินค้าในตะกร้า</p>
                </div>

                <div v-for="item in cartItems" :key="item.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div class="flex-1">
                    <h4 class="font-medium text-sm">{{ item.product_name }}</h4>
                    <p class="text-sm text-gray-500">฿{{ formatPrice(item.price) }}</p>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button @click="updateCartQuantity({ id: item.id, change: -1 })" class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                      </svg>
                    </button>
                    <span class="w-8 text-center text-sm">{{ item.quantity }}</span>
                    <button @click="updateCartQuantity({ id: item.id, change: 1 })" class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                    </button>
                    <button @click="removeFromCart(item.id)" class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Cart Summary -->
              <div class="border-t pt-4">
                <!-- VAT Toggle Section -->
                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="font-medium text-sm text-blue-800">ภาษีมูลค่าเพิ่ม (VAT)</h4>
                      <p class="text-xs text-blue-600">เลือกใช้หรืุอไม่ใช้ VAT 7%</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input v-model="includeVAT" type="checkbox" class="sr-only peer" aria-label="เปิด/ปิด VAT">
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                  </div>
                </div>




                <div class="space-y-2 mb-4">
                  <div class="flex justify-between text-sm">
                    <span>ยอดรวม:</span>
                    <span>฿{{ formatPrice(cartTotal) }}</span>
                  </div>
                  <div v-if="promotionDiscount > 0" class="flex justify-between text-sm text-green-600">
                    <span>ส่วนลด ({{ appliedPromotion?.name }}):</span>
                    <span>-฿{{ formatPrice(promotionDiscount) }}</span>
                  </div>
                  <div v-if="includeVAT" class="flex justify-between text-sm">
                    <span>ภาษี (7%):</span>
                    <span>฿{{ formatPrice(cartTax) }}</span>
                  </div>
                  <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>รวมทั้งสิ้น:</span>
                    <span class="text-primary-600">฿{{ formatPrice(cartGrandTotal) }}</span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                  <button
                    @click="clearCart"
                    :disabled="cartItems.length === 0"
                    class="w-full btn btn-outline"
                  >
                    ล้างตะกร้า
                  </button>
                  <button
                    @click="showPaymentModal = true"
                    :disabled="cartItems.length === 0"
                    class="w-full btn btn-primary"
                  >
                    ชำระเงิน
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
          <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold">ชำระเงิน</h3>
                <button @click="closePaymentModal" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>

              <!-- Order Summary -->
              <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium mb-2">สรุปคำสั่งซื้อ</h4>

                <!-- VAT Toggle for Payment Modal -->
                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                  <div class="flex items-center justify-between">
                    <div>
                      <h5 class="font-medium text-sm text-blue-800">ภาษีมูลค่าเพิ่ม (VAT)</h5>
                      <p class="text-xs text-blue-600">เลือกใช้หรืุอไม่ใช้ VAT 7%</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input v-model="includeVAT" type="checkbox" class="sr-only peer" aria-label="เปิด/ปิด VAT">
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                  </div>
                </div>

                <div class="space-y-1 text-sm">
                  <div class="flex justify-between">
                    <span>จำนวนรายการ:</span>
                    <span>{{ cartItems.length }} รายการ</span>
                  </div>
                  <div class="flex justify-between">
                    <span>ยอดรวม:</span>
                    <span>฿{{ formatPrice(cartTotal) }}</span>
                  </div>
                  <div v-if="promotionDiscount > 0" class="flex justify-between text-green-600">
                    <span>ส่วนลด ({{ appliedPromotion?.name }}):</span>
                    <span>-฿{{ formatPrice(promotionDiscount) }}</span>
                  </div>
                  <div v-if="includeVAT" class="flex justify-between">
                    <span>ภาษี (7%):</span>
                    <span>฿{{ formatPrice(cartTax) }}</span>
                  </div>
                  <div class="flex justify-between font-bold text-lg border-t pt-1">
                    <span>รวมทั้งสิ้น:</span>
                    <span class="text-primary-600">฿{{ formatPrice(cartGrandTotal) }}</span>
                  </div>
                </div>
              </div>

              <!-- Promotions Section -->
              <div v-if="availablePromotions.length > 0" class="mb-6">
                <h4 class="font-medium mb-3">โปรโมชั่นที่ใช้ได้</h4>
                <div class="space-y-2">
                  <div
                    v-for="promotion in availablePromotions"
                    :key="promotion.id"
                    @click="applyPromotion(promotion)"
                    class="p-3 border rounded-lg cursor-pointer transition-colors"
                    :class="{
                      'border-green-500 bg-green-50': appliedPromotion?.id === promotion.id,
                      'border-gray-200 hover:border-primary-300 hover:bg-primary-50': appliedPromotion?.id !== promotion.id
                    }"
                  >
                    <div class="flex items-center">
                      <div class="flex-shrink-0 mr-3">
                        <div v-if="promotion.type === 'percentage'" class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                          <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                          </svg>
                        </div>
                        <div v-else-if="promotion.type === 'fixed_amount'" class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                          <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                          </svg>
                        </div>
                        <div v-else class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                          <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                          </svg>
                        </div>
                      </div>

                      <div class="flex-1">
                        <div class="font-medium text-sm text-gray-900">{{ promotion.name }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ promotion.description }}</div>
                        <div class="text-xs font-medium mt-1" :class="getPromotionColorClass(promotion.type)">
                          {{ getPromotionLabel(promotion) }}
                        </div>
                      </div>

                      <div class="flex-shrink-0 ml-2">
                        <svg v-if="appliedPromotion?.id === promotion.id" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- Remove Promotion Button -->
                  <div v-if="appliedPromotion" class="mt-2">
                    <button
                      @click="removePromotion"
                      class="text-sm text-red-600 hover:text-red-800 underline"
                    >
                      ลบโปรโมชั่น
                    </button>
                  </div>
                </div>
              </div>

              <!-- Payment Method Selection -->
              <div class="mb-6">
                <h4 class="font-medium mb-3">วิธีการชำระเงิน</h4>
                <div class="space-y-2">
                  <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input v-model="paymentMethod" type="radio" value="cash" class="mr-3">
                    <div class="flex items-center">
                      <span class="text-2xl mr-2">💰</span>
                      <span>เงินสด</span>
                    </div>
                  </label>
                  <label
                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                    :class="{ 'opacity-50 cursor-not-allowed': !selectedMainCustomer }"
                  >
                    <input
                      v-model="paymentMethod"
                      type="radio"
                      value="customer_account"
                      class="mr-3"
                      :disabled="!selectedMainCustomer"
                    >
                    <div class="flex items-center">
                      <span class="text-2xl mr-2">🏢</span>
                      <span>เครดิต (บัญชีลูกค้า)</span>
                    </div>
                  </label>
                  <div v-if="!selectedMainCustomer && paymentMethod === 'customer_account'" class="text-sm text-red-600 ml-6">
                    กรุณาเลือกลูกค้าก่อนใช้การชำระแบบเครดิต
                  </div>
                </div>
              </div>

              <!-- Cash Payment Details -->
              <div v-if="paymentMethod === 'cash'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">จำนวนเงินที่รับ</label>
                <input
                  v-model.number="receivedAmount"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500"
                  placeholder="0.00"
                >
                <div v-if="receivedAmount >= cartGrandTotal" class="mt-2 p-2 bg-green-50 rounded text-sm">
                  <span class="text-green-800">เงินทอน: ฿{{ formatPrice(receivedAmount - cartGrandTotal) }}</span>
                </div>
                <div v-else-if="receivedAmount > 0" class="mt-2 p-2 bg-red-50 rounded text-sm">
                  <span class="text-red-800">เงินไม่เพียงพอ</span>
                </div>
              </div>

              <!-- Customer Account Selection -->
              <div v-if="paymentMethod === 'customer_account'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">เลือกลูกค้า</label>
                <input
                  v-model="customerSearch"
                  @input="searchCustomers"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-primary-500"
                  placeholder="ค้นหาลูกค้า..."
                >
                <div v-if="customers.length > 0" class="mt-2 max-h-40 overflow-y-auto border rounded-lg">
                  <div
                    v-for="customer in customers"
                    :key="customer.id"
                    @click="selectedMainCustomer = customer"
                    class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                    :class="{ 'bg-primary-50': selectedMainCustomer?.id === customer.id }"
                  >
                    <div class="font-medium">{{ customer.name }}</div>
                    <div class="text-sm text-gray-500">{{ customer.company_name }}</div>
                    <div class="text-sm text-gray-500">วงเงินคงเหลือ: ฿{{ formatPrice(customer.available_credit) }}</div>
                  </div>
                </div>
                <div v-if="selectedMainCustomer" class="mt-2 p-3 bg-blue-50 rounded-lg">
                  <div class="font-medium">{{ selectedMainCustomer.name }}</div>
                  <div class="text-sm text-gray-600">วงเงินคงเหลือ: ฿{{ formatPrice(selectedMainCustomer.available_credit) }}</div>
                  <div v-if="selectedMainCustomer.available_credit < cartGrandTotal" class="text-sm text-red-600 mt-1">
                    วงเงินไม่เพียงพอ
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex space-x-3">
                <button @click="closePaymentModal" class="flex-1 btn btn-outline">ยกเลิก</button>
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
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Reactive data
const searchQuery = ref('')
const selectedCategory = ref('')
const showMobileSearch = ref(false)
const showMobileCart = ref(false)
const showPaymentModal = ref(false)
const loading = ref(false)
const currentDateTime = ref('')

// Products and categories
const products = ref([])
const categories = ref([])
const cartItems = ref([])

// Promotion data
const availablePromotions = ref([])
const appliedPromotion = ref(null)
const promotionDiscount = ref(0)
const showPromotionDropdown = ref(false)

// Payment data
const paymentMethod = ref('cash')
const receivedAmount = ref(0)
const customerSearch = ref('')
const customers = ref([])
// Remove selectedCustomer since we're using selectedMainCustomer now

// Main customer selection (separate from payment modal)
const mainCustomerSearch = ref('')
const mainCustomers = ref([])
const selectedMainCustomer = ref(null)

// VAT toggle
const includeVAT = ref(true)

// Computed properties
const filteredProducts = computed(() => {
  let filtered = products.value

  if (searchQuery.value) {
    filtered = filtered.filter(product =>
      product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      product.sku.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (selectedCategory.value) {
    filtered = filtered.filter(product => product.category_id == selectedCategory.value)
  }

  return filtered
})

const categoryFilters = computed(() => {
  const filters = [
    { id: '', label: 'ทั้งหมด', count: products.value.length, active: selectedCategory.value === '' }
  ]

  categories.value.forEach(category => {
    const count = products.value.filter(p => p.category_id === category.id).length
    filters.push({
      id: category.id,
      label: category.name,
      count: count,
      active: selectedCategory.value == category.id
    })
  })

  return filters
})

const cartTotal = computed(() => {
  return cartItems.value.reduce((total, item) => total + (item.price * item.quantity), 0)
})

const cartTax = computed(() => {
  return includeVAT.value ? cartTotal.value * 0.07 : 0
})

const cartGrandTotal = computed(() => {
  const subtotal = cartTotal.value - promotionDiscount.value
  return subtotal + (includeVAT.value ? subtotal * 0.07 : 0)
})

const canProcessPayment = computed(() => {
  if (cartItems.value.length === 0) return false

  if (paymentMethod.value === 'cash') {
    return receivedAmount.value >= cartGrandTotal.value
  }

  if (paymentMethod.value === 'customer_account') {
    return selectedMainCustomer.value && selectedMainCustomer.value.available_credit >= cartGrandTotal.value
  }

  return true
})

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('th-TH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price || 0)
}

const stockStatusClass = (stock) => {
  if (stock <= 0) return 'bg-red-100 text-red-800'
  if (stock <= 10) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const searchProducts = async () => {
  if (searchQuery.value.length < 2 && !selectedCategory.value) {
    await loadProducts()
    return
  }

  loading.value = true
  try {
    const response = await fetch(`/pos/search-products?query=${encodeURIComponent(searchQuery.value)}&category=${selectedCategory.value}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    products.value = data.products || []
  } catch (error) {
    console.error('Error searching products:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

const searchCustomers = async () => {
  if (customerSearch.value.length < 2) {
    customers.value = []
    return
  }

  try {
    const response = await fetch(`/pos/search-customers?query=${encodeURIComponent(customerSearch.value)}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    customers.value = data.customers || []
  } catch (error) {
    console.error('Error searching customers:', error)
    customers.value = []
  }
}

// Main customer search functions
const searchMainCustomers = async () => {
  if (mainCustomerSearch.value.length < 2) {
    mainCustomers.value = []
    return
  }

  try {
    const response = await fetch(`/pos/search-customers?query=${encodeURIComponent(mainCustomerSearch.value)}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    mainCustomers.value = data.customers || []
  } catch (error) {
    console.error('Error searching main customers:', error)
    mainCustomers.value = []
  }
}

const selectMainCustomer = (customer) => {
  selectedMainCustomer.value = customer
  mainCustomerSearch.value = customer.name || customer.company_name
  mainCustomers.value = []
}

const clearMainCustomer = () => {
  selectedMainCustomer.value = null
  mainCustomerSearch.value = ''
  mainCustomers.value = []
}

const loadProducts = async () => {
  loading.value = true
  try {
    const response = await fetch('/pos/search-products', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    products.value = data || []
  } catch (error) {
    console.error('Error loading products:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await fetch('/api/categories', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    categories.value = data.data || []
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

const addToCart = (product) => {
  if (product.current_stock <= 0) return

  const existingItem = cartItems.value.find(item => item.product_id === product.id)

  if (existingItem) {
    if (existingItem.quantity < product.current_stock) {
      existingItem.quantity++
    }
  } else {
    cartItems.value.push({
      id: Date.now() + Math.random(),
      product_id: product.id,
      product_name: product.name,
      price: product.selling_price,
      quantity: 1,
      max_stock: product.current_stock
    })
  }
}

const updateCartQuantity = ({ id, change }) => {
  const item = cartItems.value.find(item => item.id === id)
  if (!item) return

  const newQuantity = item.quantity + change

  if (newQuantity <= 0) {
    removeFromCart(id)
  } else if (newQuantity <= item.max_stock) {
    item.quantity = newQuantity
  }
}

const removeFromCart = (id) => {
  const index = cartItems.value.findIndex(item => item.id === id)
  if (index > -1) {
    cartItems.value.splice(index, 1)
  }
}

const clearCart = () => {
  cartItems.value = []
  // Reset promotion when clearing cart
  appliedPromotion.value = null
  promotionDiscount.value = 0
}

const selectCategory = (filter) => {
  selectedCategory.value = filter.id
  searchProducts()
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  paymentMethod.value = 'cash'
  receivedAmount.value = 0
  customerSearch.value = ''
  customers.value = []
  // Remove selectedCustomer since we're using selectedMainCustomer now
}

const confirmPayment = async () => {
  try {
    const saleData = {
      items: cartItems.value.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        unit_price: item.price
      })),
      payment_method: paymentMethod.value,
      total_amount: cartGrandTotal.value,
      tax_amount: cartTax.value,
      customer_id: selectedMainCustomer.value?.id || null,
      received_amount: paymentMethod.value === 'cash' ? receivedAmount.value : cartGrandTotal.value,
      promotion_id: appliedPromotion.value?.id || null,
      discount_amount: promotionDiscount.value
    }

    const response = await fetch('/pos/process-sale', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(saleData)
    })

    const result = await response.json()

    if (result.success) {
      // Clear cart and close modal
      clearCart()
      closePaymentModal()

      // Show success message with receipt option
      const printReceipt = confirm(`การขายสำเร็จ! หมายเลขใบเสร็จ: ${result.sale_number}\n\nต้องการพิมพ์ใบเสร็จหรืุอไม่?`)

      if (printReceipt) {
        generateReceipt(result.sale)
      }

      // Reload products and promotions to update stock and usage
      await Promise.all([
        loadProducts(),
        loadPromotions()
      ])
    } else {
      alert('เกิดข้อผิดพลาด: ' + (result.message || 'ไม่สามารถบันทึกการขายได้'))
    }
  } catch (error) {
    console.error('Error processing payment:', error)
    alert('เกิดข้อผิดพลาดในการประมวลผลการชำระเงิน')
  }
}

const generateReceipt = (sale) => {
  const receiptWindow = window.open('', '_blank', 'width=300,height=600')

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
        <div><strong>วันที่:</strong> ${new Date(sale.created_at || Date.now()).toLocaleString('th-TH')}</div>
        <div><strong>พนักงานขาย:</strong> ${sale.cashier?.username || 'N/A'}</div>
        ${sale.customer ? `<div><strong>ลูกค้า:</strong> ${sale.customer.name}</div>` : ''}
      </div>

      <div class="items">
        ${sale.sale_items?.map(item => `
          <div class="item">
            <div class="item-name">${item.product?.name || 'สินค้า'}</div>
            <div class="item-qty-price">${item.quantity} x ${parseFloat(item.unit_price).toFixed(2)}</div>
          </div>
          <div class="item">
            <div class="item-name"></div>
            <div class="item-qty-price">${parseFloat(item.total_price || item.quantity * item.unit_price).toFixed(2)}</div>
          </div>
        `).join('') || ''}
      </div>

      <div class="totals">
        <div class="total-line">
          <span>ยอดรวม:</span>
          <span>฿${parseFloat(sale.subtotal || 0).toFixed(2)}</span>
        </div>
        ${sale.discount_amount > 0 ? `
          <div class="total-line">
            <span>ส่วนลด:</span>
            <span>-฿${parseFloat(sale.discount_amount).toFixed(2)}</span>
          </div>
        ` : ''}
        ${sale.tax_amount > 0 ? `
          <div class="total-line">
            <span>ภาษี (7%):</span>
            <span>฿${parseFloat(sale.tax_amount).toFixed(2)}</span>
          </div>
        ` : ''}
        <div class="total-line grand-total">
          <span>รวมทั้งสิ้น:</span>
          <span>฿${parseFloat(sale.total_amount).toFixed(2)}</span>
        </div>
        <div class="total-line">
          <span>รับเงิน:</span>
          <span>฿${parseFloat(sale.received_amount || sale.total_amount).toFixed(2)}</span>
        </div>
        ${sale.change_amount > 0 ? `
          <div class="total-line">
            <span>เงินทอน:</span>
            <span>฿${parseFloat(sale.change_amount).toFixed(2)}</span>
          </div>
        ` : ''}
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
  `

  receiptWindow.document.write(receiptHTML)
  receiptWindow.document.close()

  // Auto print after a short delay
  setTimeout(() => {
    receiptWindow.print()
  }, 500)
}

const updateDateTime = () => {
  const now = new Date()
  currentDateTime.value = now.toLocaleString('th-TH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

// Promotion methods
const loadPromotions = async () => {
  try {
    // Get product IDs from cart items
    const productIds = cartItems.value.map(item => item.product_id)

    const response = await fetch('/pos/promotions/active', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        product_ids: productIds
      })
    })

    if (response.ok) {
      const promotions = await response.json()
      availablePromotions.value = promotions
    } else {
      console.error('Failed to load promotions:', response.status)
    }
  } catch (error) {
    console.error('Error loading promotions:', error)
  }
}

const getPromotionLabel = (promotion) => {
  switch (promotion.type) {
    case 'percentage':
      return `ลด ${promotion.value}%`
    case 'fixed_amount':
      return `ลด ฿${formatPrice(promotion.value)}`
    case 'buy_x_get_y':
      return `ซื้อ ${promotion.min_quantity} แถม ${promotion.free_quantity}`
    default:
      return 'โปรโมชั่นพิเศษ'
  }
}

const getPromotionColorClass = (type) => {
  switch (type) {
    case 'percentage':
      return 'text-blue-600'
    case 'fixed_amount':
      return 'text-green-600'
    case 'buy_x_get_y':
      return 'text-purple-600'
    default:
      return 'text-orange-600'
  }
}

const applyPromotion = async (promotion) => {
  try {
    const response = await fetch('/pos/promotions/apply', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        promotion_id: promotion.id,
        cart_items: cartItems.value.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity,
          price: item.price
        }))
      })
    })

    if (response.ok) {
      const result = await response.json()
      appliedPromotion.value = promotion
      promotionDiscount.value = result.discount
    } else {
      const error = await response.json()
      alert('ไม่สามารถใช้โปรโมชั่นนี้ได้: ' + (error.error || 'เงื่อนไขไม่ตรงตามกำหนด'))
    }
  } catch (error) {
    console.error('Error applying promotion:', error)
    alert('เกิดข้อผิดพลาดในการใช้โปรโมชั่น')
  }
}

const removePromotion = () => {
  appliedPromotion.value = null
  promotionDiscount.value = 0
}

// Watchers
watch(searchQuery, () => {
  if (searchQuery.value.length === 0) {
    loadProducts()
  }
})

// Watch cart items to reload promotions when cart changes
watch(cartItems, () => {
  loadPromotions()
}, { deep: true })

// Lifecycle
onMounted(async () => {
  updateDateTime()
  setInterval(updateDateTime, 1000)

  await Promise.all([
    loadProducts(),
    loadCategories(),
    loadPromotions()
  ])
})
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
