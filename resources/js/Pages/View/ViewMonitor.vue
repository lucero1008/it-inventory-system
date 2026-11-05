<script setup>
import dayjs from 'dayjs';
import { XMarkIcon, TvIcon } from '@heroicons/vue/24/solid'
import { router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

import Navbar from '../Components/Navbar.vue';
import CustomTable from '../Components/CustomTable.vue';
import Badge from '../Components/Badge.vue';

const props = defineProps({
    viewMonitor: {
        type: Object,
        default: null
    },
    assetLogs: {
        type: Object,
        default: null
    },
    returnUrl: {
        type: String,
        default: '/monitor'
    },
    filters: Object,
});

const tableHeaders = [
  { label: 'ID', key: 'id', enabaleSort: false },
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

    router.get(`/monitor/view/${props.viewMonitor.id}`, params, {
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
                            <p class="main-card-description">Track, monitor, and manage all company-issued monitor hardware assets here.</p>
                        </div>
                        <Link :href="returnUrl" class="main-card-close-icon">
                            <XMarkIcon class="w-5 h-5"/>
                            <span class="sr-only">Close modal</span>
                        </Link>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6 items-stretch w-full mt-6">
                        <div class="view-icon">
                            <div class="text-gray-400 dark:text-zinc-500">
                                <TvIcon class="w-15 h-15"/>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col gap-6">
                            <div>
                                <h3 class="view-brand-model">{{ viewMonitor.brand }} {{ viewMonitor.model }}</h3>
                                <p class="view-specs">
                                    <span>Size:</span> {{ viewMonitor.size }} inches
                                </p>
                            </div>

                            <div class="view-others">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-1 md:gap-6 w-full">
                                    <ul class="space-y-1.5">
                                        <li class="view-other-details">
                                            <span>Asset Tag:</span> {{ viewMonitor.asset_tag }}
                                        </li>
                                        <li class="view-other-details">
                                            <span>Serial Number:</span> {{ viewMonitor.serial_number }}
                                        </li>
                                        <li class="view-other-details">
                                            <span>Date Transfer:</span> {{ dayjs(viewMonitor.date_of_asset_transfer).format('MMMM DD, YYYY') }}
                                        </li>
                                    </ul>


                                    <ul class="space-y-1.5">
                                        <li class="view-other-details flex items-center gap-2">
                                            <span>Status:</span>
                                            <Badge :status="viewMonitor.status"/>
                                        </li>
                                        <li class="view-other-details">
                                            <span>Current User:</span> {{ viewMonitor.current_user }} - {{ viewMonitor.department }}
                                        </li>
                                        <li class="view-other-details">
                                            <span>Remark:</span> {{ viewMonitor.remarks ? viewMonitor.remarks : "n/a" }}
                                        </li>
                                    </ul>
                                </div>
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

                        <template #id="{ item }">
                            {{ (assetLogs.from || 1) + assetLogs.data.indexOf(item) }}
                        </template>

                        <template #details="{ item }">
                            <span class="w-full md:whitespace-normal md:break-all ml-11 sm:ml-8 md:ml-0">{{ item.details }}</span>
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
