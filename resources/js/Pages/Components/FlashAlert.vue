<script setup>
import { XMarkIcon } from '@heroicons/vue/24/solid'
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const successMessage = computed(() => page.props.flash?.success);
const errorMessage = computed(() => page.props.flash?.error);
const warningMessage = computed(() => page.props.flash?.warning);
const infoMessage = computed(() => page.props.flash?.info);
</script>

<template>
    <div v-if="successMessage" class="flex sm:items-center p-4 text-sm text-fg-success-strong rounded-base bg-success-soft mt-15 sm:mt-0" role="alert">
        <div class="ms-2 text-xs">
            {{ successMessage }}
        </div>
        <button type="button"
                @click="$page.props.flash.success = null"
                class="ms-auto -mx-1.5 -my-1.5 rounded focus:ring-2 focus:ring-success-medium hover:bg-success-soft inline-flex items-center justify-center h-8 w-8 cursor-default">
            <span class="sr-only">Close</span>
            <XMarkIcon class="w-4 h-4"/>
        </button>
    </div>

    <div v-if="errorMessage" class="flex sm:items-center p-4 text-sm text-fg-danger-strong rounded-base bg-danger-soft mt-15 sm:mt-0" role="alert">
        <div class="ms-2 text-xs">
            {{ successMessage }}
        </div>
        <button type="button"
                @click="$page.props.flash.error = null"
                class="ms-auto -mx-1.5 -my-1.5 rounded focus:ring-2 focus:ring-danger-medium hover:bg-danger-soft inline-flex items-center justify-center h-8 w-8 cursor-default">
            <span class="sr-only">Close</span>
            <XMarkIcon class="w-4 h-4"/>
        </button>
    </div>

    <div v-if="warningMessage" class="flex sm:items-center p-4 mb-4 text-sm text-fg-warning rounded-base bg-warning-soft mt-15 sm:mt-0" role="alert">
        <div class="ms-2 text-xs">
            {{ warningMessage }}
        </div>
        <button type="button"
                @click="$page.props.flash.warning = null"
                class="ms-auto -mx-1.5 -my-1.5 rounded focus:ring-2 focus:ring-warning-medium hover:bg-warning-soft inline-flex items-center justify-center h-8 w-8 cursor-default">
            <span class="sr-only">Close</span>
            <XMarkIcon class="w-4 h-4"/>
        </button>
    </div>

    <div v-if="infoMessage" class="flex sm:items-center p-4 text-sm text-fg-brand-strong rounded-base bg-brand-softer mt-15 sm:mt-0" role="alert">
        <div class="ms-2 text-xs">
            {{ infoMessage }}
        </div>
        <button type="button"
                @click="$page.props.flash.infoMessage = null"
                class="ms-auto -mx-1.5 -my-1.5 rounded focus:ring-2 focus:ring-brand-medium hover:bg-brand-soft inline-flex items-center justify-center h-8 w-8 cursor-default">
            <span class="sr-only">Close</span>
            <XMarkIcon class="w-4 h-4"/>
        </button>
    </div>

    <div :class="[
        (successMessage || errorMessage || warningMessage || infoMessage) ? '' : 'mt-8 md:mt-0'
    ]"></div>
</template>
