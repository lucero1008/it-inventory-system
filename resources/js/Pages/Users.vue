<script setup>
import { PencilIcon } from '@heroicons/vue/24/solid'
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

import Navbar from './Components/Navbar.vue';
import CustomTable from './Components/CustomTable.vue';
import FlashAlert from './Components/FlashAlert.vue';

const addNewUser = () => {
    router.visit('/users/action');
}

const editUser = (item) => {
    router.visit(`/users/action/${item.id}`);
}

const props = defineProps({
  users: Object,
  filters: Object,
});

const tableHeaders = [
  { label: 'ID', key: 'id', enabaleSort: true },
  { label: 'Full Name', key: 'name', enabaleSort: false },
  { label: 'Username', key: 'username', enabaleSort: false },
  { label: 'User Type', key: 'usertype', enabaleSort: false },
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

    router.get('/users', params, {
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

const optionStatus = [
    'Active',
    'Inactive',
]

</script>

<template>
    <Navbar/>
    <div class="main-bg">
        <div class="main-section">
            <div class="main-card">
                <div class="sm:px-8 sm:py-4">
                    <h1 class="main-card-title">User List</h1>
                    <p class="main-card-description">Manage your system users, roles, and status configuration here.</p>
                    <FlashAlert/>
                    <CustomTable
                        :headers="tableHeaders"
                        :items="users.data"
                        :pagination="users"
                        :options="optionStatus"
                        optionsPlaceHolder="All Statuses"
                        v-model:search="search"
                        v-model:perPage="perPage"
                        v-model:select="select"
                        @sort="handleSort"
                        @addUserBtn="addNewUser"
                    >
                        <template #status="{ item }">
                            <span :class="item.status === 'Active' ? 'badge-success' : 'badge-danger'">
                                {{ item.status }}
                            </span>
                        </template>

                        <template #actions="{ item }">
                            <button @click="editUser(item)" class="btn-icon-info">
                                <PencilIcon class="w-3 h-3"/>
                            </button>
                        </template>
                    </CustomTable>
                </div>
            </div>

        </div>
    </div>
</template>
