<template>
  <AppLayout title="จัดการผู้ใช้งาน">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        จัดการผู้ใช้งาน
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">จัดการผู้ใช้งาน</h1>
              <Link :href="route('users.create')" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
                    style="background-color: #6B7B47; border-color: #6B7B47;"
                    onmouseover="this.style.backgroundColor='#8A9B5A'"
                    onmouseout="this.style.backgroundColor='#6B7B47'">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                เพิ่มผู้ใช้ใหม่
              </Link>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <input
                  v-model="form.search"
                  type="text"
                  placeholder="ค้นหาผู้ใช้งาน..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                  style="border-color: #E2E8F0;"
                  onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                  onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                  @input="search"
                />
              </div>
              <div>
                <select v-model="form.role" @change="search" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                        style="border-color: #E2E8F0;"
                        onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                        onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'">
                  <option value="">ทุกบทบาท</option>
                  <option value="admin">ผู้ดูแลระบบ</option>
                  <option value="manager">ผู้จัดการ</option>
                  <option value="cashier">พนักงานขาย</option>
                </select>
              </div>
              <div>
                <select v-model="form.status" @change="search" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                        style="border-color: #E2E8F0;"
                        onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                        onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'">
                  <option value="">ทุกสถานะ</option>
                  <option value="active">ใช้งาน</option>
                  <option value="inactive">ไม่ใช้งาน</option>
                </select>
              </div>
              <div>
                <button @click="clearFilters" class="w-full px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 ease-in-out">
                  ล้างตัวกรอง
                </button>
              </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ผู้ใช้งาน
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      บทบาท
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      สถานะ
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      เข้าสู่ระบบล่าสุด
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      การดำเนินการ
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-sm font-medium text-gray-700">
                            {{ user.first_name.charAt(0) }}{{ user.last_name.charAt(0) }}
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ user.first_name }} {{ user.last_name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ user.email }}
                          </div>
                          <div class="text-xs text-gray-400">
                            @{{ user.username }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getRoleClass(user.role)">
                        {{ getRoleText(user.role) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ user.is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ user.last_login_at ? formatDate(user.last_login_at) : 'ไม่เคยเข้าสู่ระบบ' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <Link :href="route('users.show', user.id)" 
                            class="mr-3 transition ease-in-out duration-150"
                            style="color: #6B7B47;"
                            onmouseover="this.style.color='#8A9B5A'"
                            onmouseout="this.style.color='#6B7B47'">
                        ดู
                      </Link>
                      <Link :href="route('users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                        แก้ไข
                      </Link>
                      <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900">
                        ลบ
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6" v-if="users.links.length > 3">
              <nav class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                  <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    ก่อนหน้า
                  </Link>
                  <Link v-if="users.next_page_url" :href="users.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    ถัดไป
                  </Link>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <p class="text-sm text-gray-700">
                      แสดง {{ users.from }} ถึง {{ users.to }} จาก {{ users.total }} รายการ
                    </p>
                  </div>
                  <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                      <template v-for="(link, index) in users.links">
                        <Link v-if="link.url" :key="'link-' + index" :href="link.url" :class="[
                          'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                          link.active ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                          link.active ? { backgroundColor: '#6B7B47', borderColor: '#6B7B47' } : {}
                        ]">
                          {{ link.label }}
                        </Link>
                        <span v-else :key="'span-' + index" :class="[
                          'relative inline-flex items-center px-2 py-2 border text-sm font-medium bg-white border-gray-300 text-gray-500'
                        ]">
                          {{ link.label }}
                        </span>
                      </template>
                    </nav>
                  </div>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import { debounce } from 'lodash'

export default {
  components: {
    AppLayout,
    Head,
    Link,
  },
  props: {
    users: Object,
    filters: Object,
  },
  setup(props) {
    const form = reactive({
      search: props.filters.search || '',
      role: props.filters.role || '',
      status: props.filters.status || '',
    })

    const search = debounce(() => {
      router.get(route('users.index'), form, {
        preserveState: true,
        replace: true,
      })
    }, 300)

    const clearFilters = () => {
      form.search = ''
      form.role = ''
      form.status = ''
      search()
    }

    const getRoleClass = (role) => {
      const classes = {
        admin: 'bg-red-100 text-red-800',
        manager: 'bg-yellow-100 text-yellow-800',
        cashier: 'bg-green-100 text-green-800',
      }
      return classes[role] || 'bg-gray-100 text-gray-800'
    }

    const getRoleText = (role) => {
      const roles = {
        admin: 'ผู้ดูแลระบบ',
        manager: 'ผู้จัดการ',
        cashier: 'พนักงานขาย',
      }
      return roles[role] || role
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    }

    const deleteUser = (user) => {
      if (confirm(`คุณแน่ใจหรือไม่ที่จะลบ ${user.first_name} ${user.last_name}?`)) {
        router.delete(route('users.destroy', user.id))
      }
    }

    return {
      form,
      search,
      clearFilters,
      getRoleClass,
      getRoleText,
      formatDate,
      deleteUser,
    }
  },
}
</script>