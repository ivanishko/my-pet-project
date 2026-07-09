<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Создание турнира
            </h2>

            <form @submit.prevent="submit" class="mt-4 space-y-4">
                <!-- Выбор федерации -->
                <div>
                    <label for="federation_id" class="block text-sm font-medium text-gray-700">
                        Федерация *
                    </label>
                    <select
                        id="federation_id"
                        v-model="form.federation_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Выберите федерацию</option>
                        <option
                            v-for="federation in federations"
                            :key="federation.id"
                            :value="federation.id"
                        >
                            {{ federation.name }}
                        </option>
                    </select>
                    <div v-if="errors.federation_id" class="text-red-500 text-sm mt-1">
                        {{ errors.federation_id }}
                    </div>
                </div>

                <!-- Название турнира -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Название турнира *
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                    <div v-if="errors.name" class="text-red-500 text-sm mt-1">
                        {{ errors.name }}
                    </div>
                </div>

                <!-- Описание -->
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

                <!-- Местоположение -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">
                        Местоположение *
                    </label>
                    <input
                        id="location"
                        v-model="form.location"
                        type="text"
                        placeholder="Город, место проведения"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                    <div v-if="errors.location" class="text-red-500 text-sm mt-1">
                        {{ errors.location }}
                    </div>
                </div>

                <!-- Тип турнира -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">
                        Тип турнира *
                    </label>
                    <select
                        id="type"
                        v-model="form.type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="individual">Индивидуальный</option>
                        <option value="team">Командный</option>
                        <option value="mixed">Смешанный</option>
                    </select>
                    <div v-if="errors.type" class="text-red-500 text-sm mt-1">
                        {{ errors.type }}
                    </div>
                </div>

                <!-- Статус -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                        Статус
                    </label>
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="active">Активный</option>
                        <option value="inactive">Неактивный</option>
                        <option value="completed">Завершен</option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="close">
                        Отмена
                    </SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Сохранение...' : 'Создать' }}
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
        federations: {
            type: Array,
            required: true
        },
        preselectedFederationId: {
            type: Number,
            default: null
        }
    });

    const emit = defineEmits(['save', 'close']);

    const form = ref({
        federation_id: '',
        name: '',
        description: '',
        location: '',
        type: 'individual',
        status: 'active'
    });

    const errors = ref({});

    watch(() => props.show, (newShow) => {
        if (newShow && props.preselectedFederationId) {
            form.value.federation_id = props.preselectedFederationId;
        }
        if (!newShow) {
            // Сбрасываем форму при закрытии
            form.value = {
                federation_id: '',
                name: '',
                description: '',
                location: '',
                type: 'individual',
                status: 'active'
            };
            errors.value = {};
        }
    }, { immediate: true });

    const submit = () => {
        emit('save', form.value);
    };

    const close = () => {
        emit('close');
    };
</script>
