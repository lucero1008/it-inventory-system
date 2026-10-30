<script setup>
import dayjs from 'dayjs';
import { XMarkIcon } from '@heroicons/vue/24/solid'
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

import Navbar from '../Components/Navbar.vue';
import FormInput from '../Components/FormInput.vue';
import FormTextArea from '../Components/FormTextArea.vue';
import FormSelect from '../Components/FormSelect.vue';
import ConfirmationModal from '../Components/ConfirmationModal.vue';

const props = defineProps({
    selectedMonitor: {
        type: Object,
        default: null
    },
    returnUrl: {
        type: String,
        default: '/monitor'
    },
    isView: {
        type: Boolean,
        default: false
    }
});

const form = useForm({
  id: props.selectedMonitor ? props.selectedMonitor.id : null,
  asset_tag: props.selectedMonitor ? props.selectedMonitor.asset_tag : null,
  brand: props.selectedMonitor ? props.selectedMonitor.brand : '',
  model: props.selectedMonitor ? props.selectedMonitor.model : null,
  serial_number: props.selectedMonitor ? props.selectedMonitor.serial_number : null,
  size: props.selectedMonitor ? props.selectedMonitor.size : '',
  status: props.selectedMonitor ? props.selectedMonitor.status : 'Available',
  current_user: props.selectedMonitor ? props.selectedMonitor.current_user : '',
  department: props.selectedMonitor ? props.selectedMonitor.department : '',
  remarks: props.selectedMonitor ? props.selectedMonitor.remarks : null,
  date_of_asset_transfer: props.selectedMonitor ? props.selectedMonitor.date_of_asset_transfer : new Date().toISOString().split('T')[0],
  created_at: props.selectedMonitor ? props.selectedMonitor.created_at : '',
  updated_at: props.selectedMonitor ? props.selectedMonitor.updated_at : 'N/A',
});

const resetForm = () => {
  form.id = props.selectedMonitor ? props.selectedMonitor.id : null;
  form.asset_tag = props.selectedMonitor ? props.selectedMonitor.asset_tag : null;
  form.brand = props.selectedMonitor ? props.selectedMonitor.brand : '';
  form.model = props.selectedMonitor ? props.selectedMonitor.model : null;
  form.serial_number = props.selectedMonitor ? props.selectedMonitor.serial_number : null;
  form.size = props.selectedMonitor ? props.selectedMonitor.size : '';
  form.status = props.selectedMonitor ? props.selectedMonitor.status : 'Available';
  form.current_user = props.selectedMonitor ? props.selectedMonitor.current_user : '';
  form.department = props.selectedMonitor ? props.selectedMonitor.department : '';
  form.remarks = props.selectedMonitor ? props.selectedMonitor.remarks : null;
  form.date_of_asset_transfer = props.selectedMonitor ? props.selectedMonitor.date_of_asset_transfer : new Date().toISOString().split('T')[0];
  form.created_at = props.selectedMonitor ? props.selectedMonitor.created_at : '';
  form.updated_at = props.selectedMonitor ? props.selectedMonitor.updated_at : 'N/A';
  form.errors = '';
}

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
    message: 'Are you sure you want to add this monitor?',
    onConfirm: () => {
      form.post('/monitor/store', {
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
      form.put(`/monitor/update/${form.id}`, {
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

const brandOptions = [
    'HP',
    'Dell',
    'Lenovo',
    'Samsung',
    'Philips',
    'ViewSonic',
    'Asus',
    'AOC',
    'BenQ',
    'Eizo',
    'Other'
]

const statusOptions = [
    'Available',
    'In Use',
    'For Disposal',
    'Disposed',
    'For Testing',
    'Undefined'
]

const sizeOptions = [
  '19',
  '21',
  '21.5',
  '22',
  '24',
  '25',
  '27',
  '28',
  '29',
  '32',
  '34',
  '38',
  '42',
  '43',
  '45',
  '49',
  '57'
];
</script>

<template>
    <Navbar/>

    <div class="main-bg">
        <div class="main-section">
            <div class="main-card">
                <div class="sm:px-8 sm:py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="main-card-title">
                                {{ isView ? 'View Monitor Details' : (form.id ? 'Edit Monitor' : 'Add New Monitor') }}
                            </h1>
                            <p class="main-card-description">Track, monitor, and manage all company-issued monitor hardware assets here.</p>
                        </div>
                        <Link :href="returnUrl" class="main-card-close-icon">
                            <XMarkIcon class="w-5 h-5"/>
                            <span class="sr-only">Close modal</span>
                        </Link>
                    </div>
                    <form class="mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <FormInput
                                label="Asset Tag"
                                id="asset_tag"
                                placeholder="Enter asset tag"
                                v-model="form.asset_tag"
                                :error="form.errors.asset_tag"
                                :disabled="isView"
                            />

                            <FormSelect
                                label="Brand"
                                id="brand"
                                v-model="form.brand"
                                placeholder="Choose Brand..."
                                :options="brandOptions"
                                :hidePlaceHolder=false
                                :error="form.errors.brand"
                                :disabled="isView"
                            />

                            <FormInput
                                label="Model"
                                id="model"
                                placeholder="Enter model"
                                v-model="form.model"
                                :error="form.errors.model"
                                :disabled="isView"
                            />

                            <FormInput
                                label="Serial Number"
                                id="serial_number"
                                placeholder="Enter serial number"
                                v-model="form.serial_number"
                                :error="form.errors.serial_number"
                                :disabled="isView"
                            />

                            <FormSelect
                                label="Size"
                                id="size"
                                v-model="form.size"
                                placeholder="Choose Size (inches)..."
                                :options="sizeOptions"
                                :hidePlaceHolder=false
                                :error="form.errors.size"
                                :disabled="isView"
                            />

                            <FormInput
                                label="Current User"
                                id="current_user"
                                placeholder="Enter current user"
                                v-model="form.current_user"
                                :error="form.errors.current_user"
                                :disabled="isView"
                            />

                            <FormInput
                                label="Department"
                                id="department"
                                placeholder="Enter department"
                                v-model="form.department"
                                :error="form.errors.department"
                                :disabled="isView"
                            />

                            <FormSelect
                                label="Status"
                                id="status"
                                v-model="form.status"
                                placeholder="Choose Status..."
                                :options="statusOptions"
                                :hidePlaceHolder=false
                                :error="form.errors.status"
                                :disabled="isView"
                            />

                            <FormInput
                                label="Date of Asset Transfer"
                                id="date_of_asset_transfer"
                                type="date"
                                v-model="form.date_of_asset_transfer"
                                :error="form.errors.date_of_asset_transfer"
                                :disabled="isView"
                            />
                        </div>
                        <FormTextArea
                            label="Remark"
                            id="remarks"
                            rows="6"
                            placeholder="Add remark here..."
                            v-model="form.remarks"
                            :error="form.errors.remarks"
                            :disabled="isView"
                            class="mt-4"
                        />

                        <div v-if="form.id" class="main-card-date-add-update">
                            <p>Date added: {{ dayjs(form.created_at).format('MMMM DD, YYYY h:mm A') }}</p>
                            <p>Date updated: {{ dayjs(form.updated_at).format('MMMM DD, YYYY h:mm A') }}</p>
                        </div>

                        <div class="mt-4 mb-4 grid grid-cols-1 gap-3 md:flex md:justify-end md:col-span-2 md:justify-self-end">
                            <button
                                v-if="!isView"
                                @click="handleSaveClick"
                                type="button"
                                class="btn-info w-auto md:w-40 ">
                                {{ form.id ? 'Update' : 'Save' }}
                            </button>
                            <button v-if="!isView" type="button" @click="resetForm" class="w-auto md:w-40 btn-secondary">Reset</button>
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
