<script setup>
import { PencilIcon, TrashIcon, EyeIcon } from '@heroicons/vue/24/solid'
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

import Navbar from '../Components/Navbar.vue';
import CustomTable from '../Components/CustomTable.vue';
import FlashAlert from '../Components/FlashAlert.vue';
import ConfirmationModal from '../Components/ConfirmationModal.vue';

const addNewPrinter = () => {
    router.visit('/printer/action');
}

const editPrinter = (item) => {
    router.visit(`/printer/action/${item.id}`);
}

const viewPrinter = (item) => {
    router.visit(`/printer/view/${item.id}`);
}

const props = defineProps({
  printer: Object,
  filters: Object,
});

const tableHeaders = [
  { label: 'ID', key: 'id', enabaleSort: true },
  { label: 'Brand & Model', key: 'brand_model', enabaleSort: false },
  { label: 'Serial Number', key: 'serial_number', enabaleSort: false },
  { label: 'Type', key: 'type', enabaleSort: false },
  { label: 'Status', key: 'status', enabaleSort: true },
];

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.perPage || 10);
const select = ref(props.filters.select || '');
const sort = ref(props.filters.sort || 'id');
const direction = ref(props.filters.direction || 'desc');

const fetchData = () => {
    const params = {};

    if (search.value) params.search = search.value;
    if (perPage.value) params.perPage = perPage.value;
    if (select.value) params.select = select.value;
    if (sort.value && typeof sort.value === 'string') {
        params.sort = sort.value;
    }
    if (direction.value) params.direction = direction.value;

    router.get('/printer', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};


const handleSort = (column) => {
    if (sort.value === column) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value = column;
        direction.value = 'asc';
    }
    fetchData();
};

watch([search, select, perPage], debounce(fetchData, 300));

const getStatusClass = (status) => {
    const currentStatus = status?.toLowerCase();
    switch (currentStatus) {
        case 'available':
            return 'badge-success';
        case 'deployed':
        case 'in use':
            return 'badge-info';
        case 'for repair':
            return 'badge-warning';
        case 'for disposal':
        case 'disposed':
            return 'badge-danger';
        case 'for testing':
            return 'badge-grey';
        case 'undefined':
            return 'badge-alternative';
        default:
            return 'badge-default';
    }
};

const isModalOpen = ref(false);
const modalConfig = ref({
  title: '',
  actionType: 'create',
  confirmText: 'Yes',
  message: '',
  onConfirm: null
});

const openRemoveModal = (item) => {
   modalConfig.value = {
    title: 'Confirmation',
    actionType: 'delete',
    confirmText: 'Yes, Remove',
    message: 'Are you sure you want to remove?',
    onConfirm: () => {
      router.delete(`/printer/delete/${item.id}`, {
        onSuccess: () => {
          isModalOpen.value = false;
        },
        onError: () => {
          isModalOpen.value = false;
        }
      });
    }
  };
  isModalOpen.value = true;
};

const statusOptions = [
    'Available',
    'In Use',
    'For Repair',
    'For Disposal',
    'Disposed',
    'For Testing',
    'Undefined'
]
</script>
<template>
    <Navbar/>
    <div class="main-bg">
        <div class="main-section">
            <div class="main-card">
                <div class="sm:px-8 sm:py-4">
                    <h1 class="main-card-title">Printer List</h1>
                    <p class="main-card-description">Track, monitor, and manage all supplier or company-issued printer assets here.</p>

                    <div class="mt-15 sm:mt-0">
                        <FlashAlert/>
                    </div>
                    <CustomTable
                        :headers="tableHeaders"
                        :items="printer.data"
                        :pagination="printer"
                        :options="statusOptions"
                        optionsPlaceHolder="All Statuses"
                        v-model:search="search"
                        v-model:perPage="perPage"
                        v-model:select="select"
                        @sort="handleSort"
                        @addUserBtn="addNewPrinter"
                    >
                        <template #brand_model="{ item }">
                            <span>{{ item.brand }} {{ item.model }}</span>
                        </template>

                        <template #type="{ item }">
                            <span>{{ item.printer_type }} - {{ item.ownership }}</span>
                        </template>

                        <template #status="{ item }">
                            <span :class="getStatusClass(item.status)">
                                {{ item.status }}
                            </span>
                        </template>

                        <template #actions="{ item }">
                            <button @click="viewPrinter(item)" class="btn-icon-secondary">
                                <EyeIcon class="w-3 h-3"/>
                            </button>
                            <button @click="editPrinter(item)" class="btn-icon-info">
                                <PencilIcon class="w-3 h-3"/>
                            </button>
                            <button @click="openRemoveModal(item)" class="btn-icon-danger">
                                <TrashIcon class="w-3 h-3"/>
                            </button>
                        </template>
                    </CustomTable>

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
