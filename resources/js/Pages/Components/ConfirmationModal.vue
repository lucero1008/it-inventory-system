<script setup>
import { XMarkIcon } from '@heroicons/vue/24/solid'
import { computed } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirmation',
  },
  actionType: {
    type: String,
    default: 'create',
  },
  confirmText: {
    type: String,
    default: 'Yes, Submit'
  }
});

defineEmits(['close', 'confirm']);

const confirmButtonClass = computed(() => {
    switch(props.actionType) {
    case 'edit':
      return 'w-full btn-info';
    case 'delete':
      return 'w-full btn-danger';
    case 'create':
    default:
      return 'w-full btn-success';
  }
});
</script>
<template>
    <div v-if="show" class="modal-c">
        <div class="relative p-4 w-full max-w-100 max-h-full">
            <!-- Modal content -->
            <div class="modal-c-content">
                <!-- Modal header -->
                <div class="modal-c-header">
                    <h1>{{ title }}</h1>
                    <button type="button" @click="$emit('close')" class="modal-c-btn-close">
                        <XMarkIcon class="w-5 h-5"/>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="modal-c-body">
                    <p><slot></slot></p>
                </div>
                <!-- Modal footer -->
                <div class="modal-c-footer">
                    <button type="button"
                            @click="$emit('confirm')"
                            :class="confirmButtonClass"
                            >
                            {{ confirmText }}
                    </button>
                    <button type="button"
                            @click="$emit('close')"
                            class="w-full btn-secondary">
                            No, Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
