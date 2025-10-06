<template>
  <div>
    <Head title="User Details" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
              <div class="flex space-x-3">
                <Link :href="route('users.edit', user.id)" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150"
                    style="background-color: #6B7B47; border-color: #6B7B47;"
                    onmouseover="this.style.backgroundColor='#8A9B5A'"
                    onmouseout="this.style.backgroundColor='#6B7B47'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit User
                </Link>
                <Link :href="route('users.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Back to Users
                </Link>
              </div>
            </div>

            <!-- User Profile Card -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
              <div class="flex items-center space-x-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                  <div class="h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                    {{ user.first_name.charAt(0) }}{{ user.last_name.charAt(0) }}
                  </div>
                </div>
                
                <!-- Basic Info -->
                <div class="flex-1">
                  <h2 class="text-2xl font-bold text-gray-900">{{ user.first_name }} {{ user.last_name }}</h2>
                  <p class="text-gray-600">@{{ user.username }}</p>
                  <p class="text-gray-600">{{ user.email }}</p>
                  <div class="mt-2 flex items-center space-x-4">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getRoleClass(user.role)">
                      {{ user.role }}
                    </span>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      {{ user.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- User Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Personal Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                    <dd class="text-sm text-gray-900">{{ user.first_name }} {{ user.last_name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                    <dd class="text-sm text-gray-900">{{ user.email }}</dd>
                  </div>
                  <div v-if="user.phone">
                    <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                    <dd class="text-sm text-gray-900">{{ user.phone }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Username</dt>
                    <dd class="text-sm text-gray-900">@{{ user.username }}</dd>
                  </div>
                </dl>
              </div>

              <!-- Account Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="text-sm text-gray-900">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getRoleClass(user.role)">
                        {{ user.role }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="text-sm text-gray-900">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ user.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(user.created_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(user.updated_at) }}</dd>
                  </div>
                  <div v-if="user.last_login_at">
                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(user.last_login_at) }}</dd>
                  </div>
                  <div v-if="user.email_verified_at">
                    <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                    <dd class="text-sm text-gray-900">
                      <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Verified
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Activity Log (if available) -->
            <div v-if="user.activities && user.activities.length > 0" class="mt-6">
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                  <div v-for="activity in user.activities.slice(0, 5)" :key="activity.id" class="flex items-center space-x-3 text-sm">
                    <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div class="flex-1">
                      <span class="text-gray-900">{{ activity.description }}</span>
                      <span class="text-gray-500 ml-2">{{ formatDate(activity.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end space-x-3">
              <button @click="deleteUser" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete User
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link,
  },
  props: {
    user: Object,
  },
  setup(props) {
    const getRoleClass = (role) => {
      const classes = {
        admin: 'bg-red-100 text-red-800',
        manager: 'bg-yellow-100 text-yellow-800',
        cashier: 'bg-green-100 text-green-800',
      }
      return classes[role] || 'bg-gray-100 text-gray-800'
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    }

    const deleteUser = () => {
      if (confirm(`Are you sure you want to delete ${props.user.first_name} ${props.user.last_name}? This action cannot be undone.`)) {
        router.delete(route('users.destroy', props.user.id))
      }
    }

    return {
      getRoleClass,
      formatDate,
      deleteUser,
    }
  },
}
</script>