<script setup>
import dayjs from 'dayjs';
import { XMarkIcon } from '@heroicons/vue/24/solid'
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

import Navbar from '../Components/Navbar.vue';
import FormInput from '../Components/FormInput.vue';
import FormSelect from '../Components/FormSelect.vue';
import ConfirmationModal from '../Components/ConfirmationModal.vue';

const props = defineProps({
    selectedUser: {
        type: Object,
        default: null
    },
    returnUrl: {
        type: String,
        default: '/users'
    }
});

const form = useForm({
  id: props.selectedUser ? props.selectedUser.id : null,
  name: props.selectedUser ? props.selectedUser.name : null,
  username: props.selectedUser ? props.selectedUser.username : null,
  usertype: props.selectedUser ? props.selectedUser.usertype : '',
  status: props.selectedUser ? props.selectedUser.status : 'Active',
  password: '',
  password_confirmation: '',
  created_at: props.selectedUser ? props.selectedUser.created_at : '',
  updated_at: props.selectedUser ? props.selectedUser.updated_at : 'N/A',
});

const resetForm = () => {
  form.id = props.selectedUser ? props.selectedUser.id : null;
  form.name = props.selectedUser ? props.selectedUser.name : null;
  form.username = props.selectedUser ? props.selectedUser.username : null;
  form.usertype = props.selectedUser ? props.selectedUser.usertype : '';
  form.status = props.selectedUser ? props.selectedUser.status : 'Active',
  form.password = '';
  form.password_confirmation = '';
  form.created_at = props.selectedUser ? props.selectedUser.created_at : '';
  form.updated_at = props.selectedUser ? props.selectedUser.updated_at : 'N/A';
  form.errors = '';
};

const isModalOpen = ref(false);
const modalConfig = ref({
  title: '',
  actionType: 'create',
  confirmText: 'Yes',
  message: '',
  onConfirm: null
});

const openCreateModal = () => {
  modalConfig.value = {
    title: 'Confirmation',
    actionType: 'create',
    confirmText: 'Yes, Add',
    message: 'Are you sure you want to add this user?',
    onConfirm: () => {
      form.post('/users/store', {
        onSuccess: () => {
          isModalOpen.value = false;
          form.reset();
          form.clearErrors();
        },
        onError: () => {
          isModalOpen.value = false;
        }
      });
    }
  };
  isModalOpen.value = true;
};

const openEditModal = () => {
   modalConfig.value = {
    title: 'Confirmation',
    actionType: 'edit',
    confirmText: 'Yes, Update',
    message: 'Are you sure you want to update?',
    onConfirm: () => {
      form.put(`/users/update/${form.id}`, {
        onSuccess: () => {
          isModalOpen.value = false;
          form.reset();
          form.clearErrors();
        },
        onError: () => {
          isModalOpen.value = false;
        }
      });
    }
  };
  isModalOpen.value = true;
};

const handleSaveClick = () => {
  if (form.id) {
    openEditModal();
  } else {
    openCreateModal();
  }
};

const statusOptions = [
    'Active',
    'Inactive'
]

const typeOptions = [
    'Admin',
    'Staff',
    'Technician'
]

</script>
<template>
    <Navbar/>
    <div class="main-bg">
        <div class="main-section">
            <div class="main-card">
                <div class="sm:px-8 sm:py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="main-card-title">{{ form.id ? 'Edit Users' : 'Add New User' }}</h1>
                            <p class="main-card-description">Manage your system users, roles, and status configuration here.</p>
                        </div>
                        <Link :href="returnUrl" class="main-card-close-icon">
                            <XMarkIcon class="w-5 h-5"/>
                            <span class="sr-only">Close modal</span>
                        </Link>
                    </div>

                        <div class="main-card-following-section">
                            <div class="mb-2">
                                <span class="text-[13px] font-semibold">Ensure that these requirements are met:</span>
                                <ul class="text-[12px] mt-1 list-disc list-outside ps-6">
                                    <li>Password must be at least 6 characters.</li>
                                    <li>At least one lowercase and uppercase character.</li>
                                    <li>At least one special character (eg. @$!%*#?&).</li>
                                </ul>
                            </div>
                            <div>
                                <span class="text-[13px] font-semibold">Role Descriptions:</span>
                                <ul class="text-[12px] mt-1 list-disc list-outside ps-6">
                                    <li>
                                        <span class="font-semibold">Admin: </span>
                                        Manages the entire system and user management.
                                    </li>
                                    <li>
                                        <span class="font-semibold">Staff: </span>
                                        Responsible for encoding and monitoring inventory assets.
                                    </li>
                                    <li>
                                        <span class="font-semibold">Technician: </span>
                                        Responsible for updating hardware status and maintenance.
                                    </li>
                                </ul>
                            </div>
                        </div>

                    <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <FormInput
                                label="Name"
                                id="name"
                                placeholder="Enter name"
                                v-model="form.name"
                                :error="form.errors.name"
                            />

                            <FormInput
                                label="Username"
                                id="username"
                                placeholder="Enter username"
                                v-model="form.username"
                                :error="form.errors.username"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                            <FormSelect
                                label="User Type"
                                id="usertype"
                                v-model="form.usertype"
                                placeholder="Choose user type..."
                                :options="typeOptions"
                                :hidePlaceHolder=false
                                :error="form.errors.usertype"
                            />

                            <FormSelect
                                label="Status"
                                id="status"
                                v-model="form.status"
                                :options="statusOptions"
                                :error="form.errors.status"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <FormInput
                                label="Password"
                                id="password"
                                type="password"
                                placeholder="Enter password"
                                v-model="form.password"
                                :error="form.errors.password"
                            />

                            <FormInput
                                label="Confirm Password"
                                id="password_confirmation"
                                type="password"
                                placeholder="Confirm password"
                                v-model="form.password_confirmation"
                                :error="form.errors.password_confirmation"
                            />
                        </div>

                        <div v-if="form.id" class="main-card-date-add-update mt-2">
                            <p>Date added: {{ dayjs(form.created_at).format('MMMM DD, YYYY h:mm A') }}</p>
                            <p>Date updated: {{ dayjs(form.updated_at).format('MMMM DD, YYYY h:mm A') }}</p>
                        </div>

                        <div class="mt-4 mb-4 grid grid-cols-1 gap-3 md:flex md:justify-end md:col-span-2 md:justify-self-end">
                            <button
                                type="button"
                                @click="handleSaveClick" class="btn-info w-auto md:w-40 ">
                                {{ form.id ? 'Update' : 'Save' }}
                            </button>
                            <button type="button" @click="resetForm" class="w-auto md:w-40 btn-secondary">Reset</button>
                            <Link :href="returnUrl" class="w-full btn-dark block sm:hidden">
                                Close
                            </Link>
                        </div>
                    </form>

                    <ConfirmationModal
                        :show="isModalOpen"
                        :title="modalConfig.title"
                        :actionType="modalConfig.actionType"
                        :confirmText="modalConfig.confirmText"
                        @close="isModalOpen = false"
                        @confirm="modalConfig.onConfirm"
                    >
                        {{ modalConfig.message }}
                    </ConfirmationModal>

                </div>
            </div>
        </div>
    </div>
</template>
