<script setup>
import { ref } from 'vue';
import { ChevronDownIcon, ChevronUpIcon, ChevronUpDownIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { Link } from '@inertiajs/vue3';

defineProps({
  headers: {
    type: Array,
    required: true,
  },
  items: {
    type: Array,
    required: true,
  },
  pagination: {
    type: Object,
    required: false,
  },
  options: {
    type: Array,
    required: true,
  },
  optionsPlaceHolder: {
    type: String,
    default: '',
  },
  isSelectHidden: {
    type: Boolean,
    default: false,
  }
});

const search = defineModel('search');
const perPage = defineModel('perPage');
const select = defineModel('select');

const emit = defineEmits(['sort', 'addUserBtn']);

const handleAddNew = () => {
    emit('addUserBtn');
}

const handleSort = (key) => {
    emit('sort', key);
};

const activeRowIndex = ref(0);
const toggleRow = (index) => {
    activeRowIndex.value = activeRowIndex.value === index ? null : index;
};
</script>
<template>
    <div class="py-5 flex flex-col md:flex-row-reverse justify-between items-center gap-2 md:gap-0">
            <button type="button" @click="handleAddNew" class="w-full md:max-w-30 btn-info">
                Add New
            </button>

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <div class="relative w-full">
                    <div class="data-table-search-input-icon">
                        <MagnifyingGlassIcon class="w-4 h-4"/>
                    </div>
                        <input type="text" class="data-table-search-input" placeholder="Search" v-model="search">
                </div>
                <div>
                    <select class="data-table-search-select min-w-35 w-full" :hidden="isSelectHidden" v-model="select">
                        <option value="" selected>{{ optionsPlaceHolder }}</option>
                        <option v-for="option in options" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>
            </div>
    </div>

    <div class="data-table-section rounded-sm md:border md:border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="data-table-thead">
                <tr>
                    <th v-for="(header, index) in headers"
                        :key="index"
                        scope="col"
                        :class="[
                            'py-3',
                            header.key === 'id' ? 'md:px-2 ' : 'px-6'
                        ]"
                    >
                        <div class="flex items-center dark:text-slate-100">
                        {{ header.label }}
                            <button v-if="header.enabaleSort" class="cursor-pointer hover:text-gray-900" @click="handleSort(header.key)">
                                <ChevronUpDownIcon class="w-4 h-4 ms-1" />
                            </button>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
                <tbody class="text-[12px] block md:table-row-group">
                    <tr v-for="(item, rowIndex) in items" :key="rowIndex" class="data-table-tbody-tr">
                        <template v-for="(header, colIndex) in headers" :key="colIndex">
                            <template v-if="header.key !== 'id' || activeRowIndex === rowIndex">
                                <td :class="[
                                        colIndex === 1 ? 'flex justify-between items-center' : (activeRowIndex === rowIndex ? 'flex justify-between items-center' : 'hidden md:table-cell'),
                                        'px-0 md:px-6 py-2 md:table-cell last:border-0'
                                    ]"
                                >
                                    <div v-if="colIndex === 1" class="w-0 items-center md:hidden">
                                        <button @click="toggleRow(rowIndex)"
                                            :class="activeRowIndex === rowIndex ? 'data-table-collapse-btn-mobile-active' : 'data-table-collapse-btn-mobile'">
                                            <ChevronUpIcon v-if="activeRowIndex === rowIndex" class="w-3 h-3" />
                                            <ChevronDownIcon v-else class="w-3 h-3" />
                                        </button>
                                    </div>

                                    <div class="flex flex-1 items-center ml-13 md:ml-0 md:block">
                                        <span class="data-table-header-label-mobile">{{ header.label }}</span>
                                        <slot :name="header.key" :item="item">
                                            {{ item[header.key] }}
                                        </slot>
                                    </div>
                                </td>
                            </template>

                            <template v-else-if="header.key === 'id'">
                                <td class="hidden md:table-cell px-0 md:px-6 py-2 last:border-0">
                                    <slot :name="header.key" :item="item">
                                        {{ item[header.key] }}
                                    </slot>
                                </td>
                            </template>
                        </template>

                        <td :class="activeRowIndex === rowIndex ? 'flex flex-1 items-center ml-13 md:ml-0' : 'hidden md:table-cell'"
                            class="px-0 md:px-6 py-2">
                            <span class="data-table-header-label-mobile">Action</span>
                            <slot name="actions" :item="item"></slot>
                        </td>
                    </tr>

                    <tr v-if="items.length === 0" class="block md:table-row">
                        <td :colspan="headers.length + 1"
                            class="data-table-no-data-found-msg">
                            No data found!
                        </td>
                    </tr>
                </tbody>
        </table>

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
