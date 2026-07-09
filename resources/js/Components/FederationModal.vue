<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ isEdit ? 'Редактирование федерации' : 'Создание федерации' }}
            </h2>

            <form @submit.prevent="submit" class="mt-4 space-y-4">
                <!-- Название -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Название *
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

                <!-- Сайт -->
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">
                        Веб-сайт
                    </label>
                    <input
                        id="website"
                        v-model="form.website"
                        type="url"
                        placeholder="https://example.com"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="info@example.com"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <!-- Телефон -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Телефон
                    </label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        placeholder="+7 (999) 123-45-67"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="close">
                        Отмена
                    </SecondaryButton>
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
        federation: Object,
        isEdit: Boolean
    });

    const emit = defineEmits(['save', 'close']);

    const form = ref({
        name: '',
        description: '',
        website: '',
        email: '',
        phone: ''
    });

    const errors = ref({});

    watch(() => props.federation, (newFederation) => {
        if (newFederation) {
            form.value = {
                name: newFederation.name || '',
                description: newFederation.description || '',
                website: newFederation.website || '',
                email: newFederation.email || '',
                phone: newFederation.phone || ''
            };
        }
    }, { immediate: true });

    const submit = () => {
        emit('save', form.value);
    };

    const close = () => {
        emit('close');
    };
</script>
