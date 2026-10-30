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
    rows: {
        type: [String, Number],
        default: '3'
    },
    placeholder: {
        type: String,
        default: ''
    },
    modelValue: {
        type: [String, Number],
        default: ''
    },
    error: {
        type: String,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="mb-0">
        <label
            :class="{ 'form-control-label-error': error, 'form-control-label': !error }"
            :for="id"
        >
            {{ label }}
        </label>

        <textarea
            :class="{ 'form-control-error': error, 'form-control': !error, 'bg-gray-100/50 cursor-not-allowed': disabled}"
            :id="id"
            :rows="rows"
            :placeholder="placeholder"
            :value="modelValue"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        </textarea>

        <p v-if="error" class="form-control-error-msg">{{ error }}</p>
    </div>
</template>
