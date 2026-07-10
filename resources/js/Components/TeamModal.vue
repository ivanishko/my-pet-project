<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Создание команды
            </h2>

            <form @submit.prevent="submit" class="mt-4 space-y-4">
                <!-- Выбор федерации -->
                <div v-if="!federationId">
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

                <!-- Название команды -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Название команды *
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

                <!-- Город -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">
                        Город *
                    </label>
                    <input
                        id="city"
                        v-model="form.city"
                        type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                    <div v-if="errors.city" class="text-red-500 text-sm mt-1">
                        {{ errors.city }}
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

                <!-- Логотип -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">
                        Логотип
                    </label>
                    <input
                        id="logo"
                        type="file"
                        @change="handleFileUpload"
                        accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    />
                    <div v-if="form.logoPreview" class="mt-2">
                        <img :src="form.logoPreview" alt="Preview" class="w-32 h-32 object-cover rounded" />
                    </div>
                    <div v-if="errors.logo" class="text-red-500 text-sm mt-1">
                        {{ errors.logo }}
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
                        <option value="active">Активная</option>
                        <option value="inactive">Неактивная</option>
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
            default: () => []
        },
        federationId: {
            type: Number,
            default: null
        }
    });

    const emit = defineEmits(['save', 'close']);

    const form = ref({
        federation_id: props.federationId || '',
        name: '',
        city: '',
        description: '',
        status: 'active',
        logo: null,
        logoPreview: null,
    });

    const errors = ref({});

    watch(() => props.show, (newShow) => {
        if (newShow && props.federationId) {
            form.value.federation_id = props.federationId;
        }
        if (!newShow) {
            form.value = {
                federation_id: props.federationId || '',
                name: '',
                city: '',
                description: '',
                status: 'active',
                logo: null,
                logoPreview: null,
            };
            errors.value = {};
        }
    }, { immediate: true });

    const handleFileUpload = (event) => {
        const file = event.target.files[0];
        if (file) {
            form.value.logo = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                form.value.logoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    };

    const submit = () => {
        emit('save', form.value);
    };

    const close = () => {
        emit('close');
    };
</script>
