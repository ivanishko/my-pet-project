<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue3-toastify';

const props = defineProps({
    federations: Array
});

// Состояния для модальных окон
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Текущая выбранная федерация
const currentFederation = ref({
    id: null,
    name: '',
    description: ''
});

// Форма для новой федерации
const newFederation = ref({
    name: '',
    description: ''
});

// Открытие модального окна добавления
const openAddModal = () => {
    newFederation.value = { name: '', description: '' };
    showAddModal.value = true;
};

// Открытие модального окна редактирования
const openEditModal = (federation) => {
    currentFederation.value = { ...federation };
    showEditModal.value = true;
};

// Открытие модального окна удаления
const openDeleteModal = (federation) => {
    currentFederation.value = { ...federation };
    showDeleteModal.value = true;
};

// Создание новой федерации
const createFederation = () => {
    router.post(route('federations.store'), newFederation.value, {
        onSuccess: () => {
            // toast.success('Федерация успешно создана!');
            showAddModal.value = false;
            newFederation.value = { name: '', description: '' };
        },
        onError: (errors) => {
            console.error(errors, 'Ошибка при создании федерации');
        }
    });
};

// Обновление федерации
const updateFederation = () => {
    router.put(route('federations.update', currentFederation.value.id), currentFederation.value, {
        onSuccess: () => {
            // toast.success('Федерация успешно обновлена!');
            showEditModal.value = false;
        },
        onError: (errors) => {
            console.error('Ошибка при обновлении федерации');
        }
    });
};

// Удаление федерации
const confirmDelete = () => {
    router.delete(route('federations.destroy', currentFederation.value.id), {
        onSuccess: () => {
            // toast.success('Федерация успешно удалена!');
            showDeleteModal.value = false;
        },
        onError: () => {
            console.error('Ошибка при удалении федерации');
        }
    });
};
</script>

<template>
    <Head title="Федерации" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Управление федерациями</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Кнопка добавления -->
                        <button
                            @click="openAddModal"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-6"
                        >
                            Добавить федерацию
                        </button>

                        <!-- Список федераций -->
                        <div v-if="federations.length > 0" class="space-y-4">
                            <div
                                v-for="federation in federations"
                                :key="federation.id"
                                class="p-4 border rounded-lg shadow-sm hover:shadow-md transition-shadow"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg">{{ federation.name }}</h3>
                                        <p class="text-gray-600 mt-1">{{ federation.description }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            @click="openEditModal(federation)"
                                            class="text-blue-500 hover:text-blue-700 px-3 py-1 border border-blue-500 rounded hover:bg-blue-50 transition-colors"
                                        >
                                            Редактировать
                                        </button>
                                        <button
                                            @click="openDeleteModal(federation)"
                                            class="text-red-500 hover:text-red-700 px-3 py-1 border border-red-500 rounded hover:bg-red-50 transition-colors"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 py-4">
                            Нет доступных федераций
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно добавления -->
        <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Добавить федерацию</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-1">Название</label>
                            <input
                                v-model="newFederation.name"
                                type="text"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Описание</label>
                            <textarea
                                v-model="newFederation.description"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            @click="showAddModal = false"
                            class="px-4 py-2 border rounded hover:bg-gray-100 transition-colors"
                        >
                            Отмена
                        </button>
                        <button
                            @click="createFederation"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                        >
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно редактирования -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Редактировать федерацию</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-1">Название</label>
                            <input
                                v-model="currentFederation.name"
                                type="text"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Описание</label>
                            <textarea
                                v-model="currentFederation.description"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            @click="showEditModal = false"
                            class="px-4 py-2 border rounded hover:bg-gray-100 transition-colors"
                        >
                            Отмена
                        </button>
                        <button
                            @click="updateFederation"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                        >
                            Обновить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно удаления -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Подтверждение удаления</h3>
                    <p>Вы уверены, что хотите удалить федерацию "{{ currentFederation.name }}"?</p>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 border rounded hover:bg-gray-100 transition-colors"
                        >
                            Отмена
                        </button>
                        <button
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
                        >
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
