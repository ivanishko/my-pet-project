<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                <slot name="title">Confirm Action</slot>
            </h2>

            <div class="mt-4">
                <slot></slot>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="close" class="mr-2">
                    Cancel
                </SecondaryButton>
                <DangerButton @click="confirm">
                    <slot name="confirm-button">Confirm</slot>
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
    import Modal from '@/Components/Modal.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import { ref, watch } from 'vue';

    const props = defineProps({
        show: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['confirmed', 'close']);

    const show = ref(props.show);

    watch(() => props.show, (value) => {
        show.value = value;
    });

    const confirm = () => {
        emit('confirmed');
        close();
    };

    const close = () => {
        show.value = false;
        emit('close');
    };
</script>