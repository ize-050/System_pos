<template>
  <div>
    <Head title="Create User" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-900">Create New User</h1>
              <Link :href="route('users.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Users
              </Link>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div>
                  <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                  <input
                    id="username"
                    v-model="form.username"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.username }"
                    required
                  />
                  <p v-if="errors.username" class="mt-1 text-sm text-red-600">{{ errors.username }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.email }"
                    required
                  />
                  <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                </div>

                <!-- First Name -->
                <div>
                  <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                  <input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.first_name }"
                    required
                  />
                  <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}</p>
                </div>

                <!-- Last Name -->
                <div>
                  <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                  <input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.last_name }"
                    required
                  />
                  <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.phone }"
                  />
                  <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                </div>

                <!-- Role -->
                <div>
                  <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                  <select
                    id="role"
                    v-model="form.role"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.role }"
                    required
                  >
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="cashier">Cashier</option>
                  </select>
                  <p v-if="errors.role" class="mt-1 text-sm text-red-600">{{ errors.role }}</p>
                </div>

                <!-- Password -->
                <div>
                  <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.password }"
                    required
                  />
                  <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
                </div>

                <!-- Confirm Password -->
                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                  <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none transition ease-in-out duration-150"
                    style="border-color: #E2E8F0;"
                    onfocus="this.style.borderColor='#6B7B47'; this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                    :class="{ 'border-red-500': errors.password_confirmation }"
                    required
                  />
                  <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</p>
                </div>
              </div>

              <!-- Status -->
              <div class="flex items-center">
                <input
                  id="is_active"
                  v-model="form.is_active"
                  type="checkbox"
                  class="h-4 w-4 border-gray-300 rounded transition ease-in-out duration-150"
                    style="color: #6B7B47; border-color: #E2E8F0;"
                    onfocus="this.style.boxShadow='0 0 0 3px rgba(107, 123, 71, 0.2)'"
                    onblur="this.style.boxShadow='none'"
                />
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                  Active User
                </label>
              </div>

              <!-- Submit Buttons -->
              <div class="flex justify-end space-x-3">
                <Link :href="route('users.index')" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                  Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150"
                    style="background-color: #6B7B47; border-color: #6B7B47;"
                    onmouseover="this.style.backgroundColor='#8A9B5A'"
                    onmouseout="this.style.backgroundColor='#6B7B47'"
                  >
                  <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ processing ? 'Creating...' : 'Create User' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link,
  },
  props: {
    errors: Object,
  },
  setup() {
    const { data: form, post, processing } = useForm({
      username: '',
      email: '',
      first_name: '',
      last_name: '',
      phone: '',
      role: '',
      password: '',
      password_confirmation: '',
      is_active: true,
    })

    const submit = () => {
      post(route('users.store'))
    }

    return {
      form,
      processing,
      submit,
    }
  },
}
</script>