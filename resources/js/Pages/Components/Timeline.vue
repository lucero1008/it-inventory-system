<script setup>
import dayjs from 'dayjs';
import { ref } from 'vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon, UserIcon, ExclamationTriangleIcon, ChevronLeftIcon, ChevronRightIcon, FunnelIcon } from '@heroicons/vue/24/solid'
import { Link } from '@inertiajs/vue3';

const date = ref();

defineProps({
  items: {
    type: Array,
    required: true,
  },
  pagination: {
    type: Object,
    required: false,
  },
  optionAction: {
    type: Array,
    required: true,
  }
});

const search = defineModel('search');
const perPage = defineModel('perPage');
const selectAction = defineModel('selectAction');


const getTimelineClasses = (action) => {
    const currentAction = action?.toLowerCase();
    switch (currentAction) {
        case 'login':
        case 'create':
            return 'timeline-success';
        case 'edit':
            return 'timeline-info';
        case 'logout':
        case 'expired':
            return 'timeline-warning';
        case 'delete':
            return 'timeline-danger';
        default:
            return 'timeline-default';
    }
}

const getTimelineIcon = (action) => {
    const currentAction = action?.toLowerCase();
    switch (currentAction) {
        case 'create':
            return PlusIcon;
        case 'edit':
            return PencilIcon;
        case 'expired':
            return ExclamationTriangleIcon;
        case 'delete':
            return TrashIcon;
        default:
            return UserIcon;
    }
}

const showFilters = ref(false);
</script>
<template>
        <div class="py-5 flex flex-col md:flex-row-reverse justify-between items-center gap-2 md:gap-0">
            <button type="button" class="w-full md:max-w-30 btn-info">
                Download
            </button>

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <div class="relative w-full">
                    <div class="data-table-search-input-icon">
                        <MagnifyingGlassIcon class="w-4 h-4"/>
                    </div>
                    <input type="text" class="data-table-search-input" placeholder="Search" v-model="search">
                </div>
                <div class="flex flex-row gap-2">
                    <select class="data-table-search-select min-w-35 w-full" v-model="selectAction">
                        <option value="" selected>All Action</option>
                        <option v-for="option in optionAction" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>

                    <button @click="showFilters = !showFilters" type="button" class="data-table-filter-btn">
                        <FunnelIcon class="w-3 h-3"/>
                    </button>
                </div>
                <div>

                </div>
            </div>
        </div>
<!-- v-if="showFilters"  -->
            <div class="border border-gray-200 shadow-md rounded-base p-5 flex flex-col sm:flex-row justify-between item-center gap-0 md:gap-4 mb-5">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="...">
                        <input type="date" class="data-table-normal-input">
                    </div>
                    <div class="...">
                        <input type="date" class="data-table-normal-input">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-2">
                    <div class="...">
                        <button type="button" class="w-full md:max-w-30 btn-info">
                            Apply
                        </button>
                    </div>
                    <div class="...">
                        <button type="button" class="w-full md:max-w-30 btn-secondary">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

        <div class="ml-2">
            <ol class="relative border-s border-default mb-3" v-for="(item, rowIndex) in items" :key="rowIndex">
                <li class="timeline-bg group">
                    <span :class="getTimelineClasses(item.action)" class="absolute -inset-s-4 -inset-y-0.5">
                        <component :is="getTimelineIcon(item.action)" class="w-3 h-3"/>
                    </span>
                    <div class="flex justify-between items-center">
                        <h3 class="timeline-title">{{ item['module'] }}</h3>
                        <time class="timeline-datetime">{{ dayjs(item.created_at).format('MMMM DD, YYYY h:mm A') }}</time>
                    </div>
                    <p class="timeline-details">{{ item['details'] }}</p>
                </li>
            </ol>

            <div v-if="items.length === 0">
                <p class="mt-2 text-xs">No Logs Found</p>
            </div>
        </div>


        <div class="flex items-center flex-column flex-wrap md:flex-row justify-between py-4">
        <div class="data-table-pagination-left-side">
            <span>Showing</span>
            <div>
                <select class="data-table-pagination-select-perpage"
                    v-model="perPage">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            of <span class="data-table-pagination-total">{{ pagination?.total || 0 }}</span> enties
        </div>

        <ul class="inline-flex gap-2 text-sm">
            <template v-for="(link, index) in pagination?.links" :key="index">
                <li v-if="index === 0 || index === pagination.links.length - 1">

                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="data-table-pagination-btn-enable"
                    >
                        <ChevronLeftIcon v-if="index === 0" class="w-3 h-3"/>
                        <ChevronRightIcon v-else class="w-3 h-3"/>
                    </Link>

                    <span
                        v-else
                        class="data-table-pagination-btn-disable"
                    >
                        <ChevronLeftIcon v-if="index === 0" class="w-3 h-3"/>
                        <ChevronRightIcon v-else class="w-3 h-3"/>
                    </span>

                </li>
            </template>
        </ul>
    </div>


</template>
