<script setup>
import dayjs from 'dayjs';
import { XMarkIcon, ComputerDesktopIcon, BookmarkIcon } from '@heroicons/vue/24/solid'
import { router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

import Navbar from '../Components/Navbar.vue';
import CustomTable from '../Components/CustomTable.vue';

const props = defineProps({
    viewDevice: {
        type: Object,
        default: null
    },
    assetLogs: {
        type: Object,
        default: null
    },
    returnUrl: {
        type: String,
        default: '/device'
    },
    filters: Object,
});

const tableHeaders = [
  { label: 'ID', key: 'id', enabaleSort: true },
  { label: 'Description', key: 'details', enabaleSort: false },
  { label: 'Update By', key: 'user_id', enabaleSort: true },
  { label: 'Date', key: 'created_at', enabaleSort: true },
];

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.perPage || 10);
const sort = ref(props.filters.sort || 'id');
const direction = ref(props.filters.direction || 'desc');

const fetchData = () => {
    const params = {};

    if (search.value) params.search = search.value;
    if (perPage.value) params.perPage = perPage.value;
    if (sort.value && typeof sort.value === 'string') {
        params.sort = sort.value;
    }
    if (direction.value) params.direction = direction.value;

    router.get(`/device/view/${props.viewDevice.id}`, params, {
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

watch([search, perPage], debounce(fetchData, 300));

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
                                View Details
                            </h1>
                            <p class="main-card-description">Track, monitor, and manage all company-issued desktop and laptop hardware assets here.</p>
                        </div>
                        <Link :href="returnUrl" class="main-card-close-icon">
                            <XMarkIcon class="w-5 h-5"/>
                            <span class="sr-only">Close modal</span>
                        </Link>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6 items-stretch w-full mt-6">
                        <div class="view-icon">
                            <div class="text-gray-400 dark:text-zinc-500">
                                <ComputerDesktopIcon class="w-15 h-15"/>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col gap-6">
                            <div>
                                <h3 class="view-brand-model">{{ viewDevice.brand }} {{ viewDevice.model }}</h3>
                                <p class="view-specs">
                                    <span>Processor:</span> {{ viewDevice.processor }}, <span>Memory:</span> {{ viewDevice.ram }}, <span>Storage:</span> {{ viewDevice.storage }}
                                </p>
                            </div>

                            <div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-1 md:gap-6 w-full">
                                    <ul class="space-y-1.5">
                                        <li class="view-other-details">
                                            <span>Asset Tag:</span> {{ viewDevice.asset_tag }}
                                        </li>
                                        <li class="view-other-details">
                                            <span>Serial Number:</span> {{ viewDevice.serial_number }}
                                        </li>
                                        <li class="view-other-details">
                                            <span>Type of Device:</span> {{ viewDevice.type }}
                                        </li>
                                    </ul>


                                    <ul class="space-y-1.5">
                                        <li class="view-other-details flex items-center gap-2">
                                            <span>Status:</span>
                                            <span :class="getStatusClass(viewDevice.status)">
                                                {{ viewDevice.status }}
                                            </span>
                                        </li>
                                        <li class="view-other-details">
                                            <span>Current User:</span> {{ viewDevice.current_user }} | {{ viewDevice.department }}
                                        </li>
                                        <li class="view-other-details">
                                            <span>Date Transfer:</span> {{ dayjs(viewDevice.date_of_asset_transfer).format('MMMM DD, YYYY h:mm A') }}
                                        </li>
                                    </ul>
                                </div>
                                <p class="view-other-details mt-1">
                                    <span>Remark:</span> {{ viewDevice.remarks ? viewDevice.remarks : "n/a" }}
                                </p>
                            </div>


                        </div>
                    </div>

                    <div class="mt-6 md:mt-0">
                        <h4 class="data-table-alternative-title block md:hidden">Asset History</h4>

                        <CustomTable
                            :headers="tableHeaders"
                            :items="assetLogs.data"
                            :pagination="assetLogs"
                            :options="[]"
                            optionsPlaceHolder="Nothing"
                            alternativeTitle="Asset History"
                            :isSelectHidden=true
                            :isActionHidden=true
                            :isAddBtnHidden=true
                            v-model:search="search"
                            v-model:perPage="perPage"
                            @sort="handleSort"
                        >

                        <template #details="{ item }">
                            <span class="w-full whitespace-normal break-all">{{ item.details }}</span>
                        </template>

                        <template #user_id="{ item }">
                            {{ item.user.username }}
                        </template>

                        <template #created_at="{ item }">
                            {{ dayjs(item.created_at).format('MMMM DD, YYYY h:mm A') }}
                        </template>

                        </CustomTable>
                    </div>


                </div>
            </div>
        </div>
    </div>
</template>
