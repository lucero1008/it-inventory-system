<script setup>
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

import FlashAlert from './Components/FlashAlert.vue';
import Navbar from './Components/Navbar.vue';
import Timeline from './Components/Timeline.vue';

const props = defineProps({
  logs: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.perPage || 10);
const selectAction = ref(props.filters.selectAction || '');

const fetchData = () => {
    const params = {};

    if (search.value) params.search = search.value;
    if (perPage.value) params.perPage = perPage.value;
    if (selectAction.value) params.selectAction = selectAction.value;

    router.get('/logs', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch([search, selectAction, perPage], debounce(fetchData, 300));

const optionAction = [
    'Login',
    'Logout',
    'Expired',
    'Create',
    'Edit',
    'Delete',
]

</script>
<template>
    <Navbar/>
    <div class="main-bg">
        <div class="main-section">
            <div class="main-card">
                <div class="sm:px-8 sm:py-4">
                    <h1 class="main-card-title">Activity Logs</h1>
                    <p class="main-card-description">Track all system activities, user changes, and status updates here.</p>
                    <FlashAlert/>

                    <Timeline
                      :items="logs.data"
                      :pagination="logs"
                      :optionAction="optionAction"
                       v-model:search="search"
                       v-model:perPage="perPage"
                       v-model:selectAction="selectAction"
                    >
                    </Timeline>
                </div>
            </div>
        </div>
    </div>
</template>
