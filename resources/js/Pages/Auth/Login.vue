<script setup>
import { useForm } from '@inertiajs/vue3'

import FlashAlert from '../Components/FlashAlert.vue';
import FormInput from '../Components/FormInput.vue';

const form = useForm({
  username: '',
  password: '',
})

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
};
</script>
<template>
  <div class="login-main">
    <div class="pb-5">
        <h1 class="login-title">IT Asset Inventory</h1>
    </div>
    <div class="login-card">
        <FlashAlert/>
        <form @submit.prevent="submit" class="mt-5">
            <div class="mb-4">
                <FormInput
                    label="Username"
                    id="username"
                    placeholder="Enter your username"
                    v-model="form.username"
                    :error="form.errors.username"
                />
            </div>
            <div class="mb-6">
                <FormInput
                    label="Password"
                    id="password"
                    type="password"
                    placeholder="Enter your password"
                    v-model="form.password"
                    :error="form.errors.password"
                />
            </div>
            <button type="submit"
                    :class="{ 'w-full btn-disabled': form.processing, 'w-full btn-info': !form.processing }"
                    :disabled="form.processing"
                    >
                    {{ form.processing ? 'Logging in...' : 'Login' }}
            </button>
        </form>
    <p class="login-card-footer">
        &copy;2026 IT Inventory. All rights reserved.
    </p>
    </div>
  </div>
</template>
