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
    selectedDevice: {
        type: Object,
        default: null
    },
    returnUrl: {
        type: String,
        default: '/device'
    },
    isView: {
        type: Boolean,
        default: false
    }
});

const form = useForm({
  id: props.selectedDevice ? props.selectedDevice.id : null,
  asset_tag: props.selectedDevice ? props.selectedDevice.asset_tag : null,
  device_type: props.selectedDevice ? props.selectedDevice.device_type : 'Desktop',
  brand: props.selectedDevice ? props.selectedDevice.brand : '',
  model: props.selectedDevice ? props.selectedDevice.model : null,
  serial_number: props.selectedDevice ? props.selectedDevice.serial_number : null,
  processor: props.selectedDevice ? props.selectedDevice.processor : null,
  ram: props.selectedDevice ? props.selectedDevice.ram : '',
  storage: props.selectedDevice ? props.selectedDevice.storage : '',
  status: props.selectedDevice ? props.selectedDevice.status : 'Available',
  current_user: props.selectedDevice ? props.selectedDevice.current_user : '',
  department: props.selectedDevice ? props.selectedDevice.department : '',
  remarks: props.selectedDevice ? props.selectedDevice.remarks : null,
  date_of_asset_transfer: props.selectedDevice ? props.selectedDevice.date_of_asset_transfer : new Date().toISOString().split('T')[0],
  created_at: props.selectedDevice ? props.selectedDevice.created_at : '',
  updated_at: props.selectedDevice ? props.selectedDevice.updated_at : 'N/A',
});

const resetForm = () => {
  form.id = props.selectedDevice ? props.selectedDevice.id : null;
  form.asset_tag = props.selectedDevice ? props.selectedDevice.asset_tag : null;
  form.device_type = props.selectedDevice ? props.selectedDevice.device_type : 'Desktop';
  form.brand = props.selectedDevice ? props.selectedDevice.brand : '';
  form.model = props.selectedDevice ? props.selectedDevice.model : null;
  form.serial_number = props.selectedDevice ? props.selectedDevice.serial_number : null;
  form.processor = props.selectedDevice ? props.selectedDevice.processor : null;
  form.ram = props.selectedDevice ? props.selectedDevice.ram : '';
  form.storage = props.selectedDevice ? props.selectedDevice.storage : '';
  form.status = props.selectedDevice ? props.selectedDevice.status : 'Available';
  form.current_user = props.selectedDevice ? props.selectedDevice.current_user : '';
  form.department = props.selectedDevice ? props.selectedDevice.department : '';
  form.remarks = props.selectedDevice ? props.selectedDevice.remarks : null;
  form.date_of_asset_transfer = props.selectedDevice ? props.selectedDevice.date_of_asset_transfer : new Date().toISOString().split('T')[0];
  form.created_at = props.selectedDevice ? props.selectedDevice.created_at : '';
  form.updated_at = props.selectedDevice ? props.selectedDevice.updated_at : 'N/A';
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
    message: 'Are you sure you want to add this device?',
    onConfirm: () => {
      form.post('/device/store', {
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
      form.put(`/device/update/${form.id}`, {
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

const ramOptions = [
    '512MB DDR1',
    '1GB DDR1',
    '1GB DDR2',
    '2GB DDR2',
    '4GB DDR2',
    '4GB DDR3',
    '4GB DDR4',
    '8GB DDR3',
    '8GB DDR4',
    '8GB DDR5',
    '16GB DDR4',
    '16GB DDR5',
    '32GB DDR4',
    '32GB DDR5',
    '64GB DDR4',
    '64GB DDR5'
];

const storageOptions = [
    '128GB SSD',
    '256GB SSD',
    '512GB SSD',
    '1TB SSD',
    '2TB SSD',
    '60GB HDD',
    '120GB HDD',
    '150GB HDD',
    '180GB HDD',
    '250GB HDD',
    '500GB HDD',
    '1TB HDD',
    '2TB HDD',
];

const statusOptions = [
    'Available',
    'In Use',
    'For Repair',
    'For Disposal',
    'Disposed',
    'For Testing',
    'Undefined'
]

const brandOptions = [
    'HP',
    'Dell',
    'Lenovo',
    'Asus',
    'Acer',
    'Other'
]

const typeOptions = [
    'Desktop',
    'Laptop'
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
                            <h1 class="main-card-title">
                                {{ isView ? 'View Device Details' : (form.id ? 'Edit Device' : 'Add New Device') }}
                            </h1>
                            <p class="main-card-description">Track, monitor, and manage all company-issued desktop and laptop hardware assets here.</p>
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
                                label="Device Type"
                                id="device_type"
                                v-model="form.device_type"
                                placeholder="Choose Device Type..."
                                :options="typeOptions"
                                :hidePlaceHolder=false
                                :error="form.errors.device_type"
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

                            <FormInput
                                label="Processor"
                                id="processor"
                                placeholder="Enter processor"
                                v-model="form.processor"
                                :error="form.errors.processor"
                                :disabled="isView"
                            />

                            <FormSelect
                                label="RAM (Memory)"
                                id="ram"
                                v-model="form.ram"
                                placeholder="Choose RAM (Memory)..."
                                :options="ramOptions"
                                :hidePlaceHolder=false
                                :error="form.errors.ram"
                                :disabled="isView"
                            />

                            <FormSelect
                                label="Storage"
                                id="storage"
                                v-model="form.storage"
                                placeholder="Choose Storage..."
                                :options="storageOptions"
                                :hidePlaceHolder=false
                                :error="form.errors.storage"
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
