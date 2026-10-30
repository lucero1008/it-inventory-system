<script setup>
defineProps({
    label: {
        type: String,
        required: true
    },
    id: {
        type: String,
        required: true
    },
    modelValue: {
        type: [String, Number],
        default: ''
    },
    options: {
        type: Array,
        required: true
    },
    error: {
        type: String,
        default: null
    },
    placeholder: {
        type: String,
        default: 'Choose...'
    },
    hidePlaceHolder: {
        type: Boolean,
        default: true
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="relative z-0 w-full group mb-0">
        <label
            :class="{ 'form-control-label-error': error, 'form-control-label': !error }"
            :for="id"
        >
            {{ label }}
        </label>

        <select
            :id="id"
            :class="{ 'form-control-error': error, 'form-control': !error, 'bg-gray-100/50 cursor-not-allowed': disabled }"
            :value="modelValue"
            :disabled="disabled"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="" disabled selected :hidden="hidePlaceHolder">{{ placeholder }}</option>
            <option v-for="option in options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>

        <p v-if="error" class="form-control-error-msg">{{ error }}</p>
    </div>
</template>
