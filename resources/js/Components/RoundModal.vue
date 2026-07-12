<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ isEdit ? 'Редактирование тура' : 'Создание тура' }}
            </h2>

            <form @submit.prevent="submit" class="mt-4 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Название тура *
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Тур 1, 1/8 финала и т.д."
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                    <div v-if="errors.name" class="text-red-500 text-sm mt-1">
                        {{ errors.name }}
                    </div>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">
                        Тип тура *
                    </label>
                    <select
                        id="type"
                        v-model="form.type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="group">Групповой тур</option>
                        <option value="playoff">Плей-офф раунд</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Описание
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                </div>

                <div v-if="isEdit">
                    <label for="status" class="block text-sm font-medium text-gray-700">
                        Статус
                    </label>
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="upcoming">Предстоящий</option>
                        <option value="active">Активный</option>
                        <option value="completed">Завершен</option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="close">Отмена</SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Сохранение...' : (isEdit ? 'Обновить' : 'Создать') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
    import Modal from '@/Components/Modal.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import { ref, watch } from 'vue';

    const props = defineProps({
        show: Boolean,
        stageId: {
            type: Number,
            required: true
        },
        round: {
            type: Object,
            default: null
        },
        isEdit: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['save', 'close']);

    const form = ref({
        name: '',
        type: 'group',
        description: '',
        status: 'upcoming',
    });

    const errors = ref({});

    watch(() => props.show, (newShow) => {
        if (newShow && props.round) {
            form.value = {
                name: props.round.name || '',
                type: props.round.type || 'group',
                description: props.round.description || '',
                status: props.round.status || 'upcoming',
            };
        } else if (!newShow) {
            form.value = {
                name: '',
                type: 'group',
                description: '',
                status: 'upcoming',
            };
            errors.value = {};
        }
    }, { immediate: true });

    const submit = () => {
        emit('save', {
            ...form.value,
            stage_id: props.stageId
        });
    };

    const close = () => {
        emit('close');
    };
</script>
