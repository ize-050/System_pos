<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: #2D3748;">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Profile Information -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium" style="color: #2D3748;">Profile Information</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Update your account's profile information and email address.
                            </p>
                        </header>

                        <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium" style="color: #2D3748;">Update Password</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Ensure your account is using a long, random password to stay secure.
                            </p>
                        </header>

                        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="current_password" value="Current Password" />
                                <TextInput
                                    id="current_password"
                                    ref="currentPasswordInput"
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    autocomplete="current-password"
                                />
                                <InputError :message="passwordForm.errors.current_password" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="password" value="New Password" />
                                <TextInput
                                    id="password"
                                    ref="passwordInput"
                                    v-model="passwordForm.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    autocomplete="new-password"
                                />
                                <InputError :message="passwordForm.errors.password" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" value="Confirm Password" />
                                <TextInput
                                    id="password_confirmation"
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    autocomplete="new-password"
                                />
                                <InputError :message="passwordForm.errors.password_confirmation" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="passwordForm.processing">Save</PrimaryButton>
                                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                                    <p v-if="passwordForm.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium" style="color: #2D3748;">Delete Account</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Once your account is deleted, all of its resources and data will be permanently deleted.
                            </p>
                        </header>

                        <DangerButton @click="confirmUserDeletion" class="mt-6">Delete Account</DangerButton>

                        <Modal :show="confirmingUserDeletion" @close="closeModal">
                            <div class="p-6">
                                <h2 class="text-lg font-medium" style="color: #2D3748;">
                                    Are you sure you want to delete your account?
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    Once your account is deleted, all of its resources and data will be permanently deleted.
                                    Please enter your password to confirm you would like to permanently delete your account.
                                </p>

                                <div class="mt-6">
                                    <InputLabel for="password" value="Password" class="sr-only" />
                                    <TextInput
                                        id="password"
                                        ref="passwordInput"
                                        v-model="deleteForm.password"
                                        type="password"
                                        class="mt-1 block w-3/4"
                                        placeholder="Password"
                                        @keyup.enter="deleteUser"
                                    />
                                    <InputError :message="deleteForm.errors.password" class="mt-2" />
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                                    <DangerButton class="ml-3" :disabled="deleteForm.processing" @click="deleteUser">
                                        Delete Account
                                    </DangerButton>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DangerButton from '@/Components/DangerButton.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    user: Object,
})

const confirmingUserDeletion = ref(false)
const passwordInput = ref(null)
const currentPasswordInput = ref(null)

const form = useForm({
    name: props.user.name,
    email: props.user.email,
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const deleteForm = useForm({
    password: '',
})

const updateProfile = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.name) {
                form.reset('name')
            }
            if (form.errors.email) {
                form.reset('email')
            }
        }
    })
}

const updatePassword = () => {
    passwordForm.patch(route('profile.password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation')
                passwordInput.value.focus()
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password')
                currentPasswordInput.value.focus()
            }
        },
    })
}

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true
}

const deleteUser = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => deleteForm.reset(),
    })
}

const closeModal = () => {
    confirmingUserDeletion.value = false
    deleteForm.reset()
}
</script>