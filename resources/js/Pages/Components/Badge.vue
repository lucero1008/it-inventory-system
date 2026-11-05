<script setup>

defineProps({
    status: {
        Object: String,
        required: true
    }
});

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
    <span :class="getStatusClass(status)">
        {{ status }}
    </span>
</template>
