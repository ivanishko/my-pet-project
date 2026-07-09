<template>
    <GuestLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Федераций
                </h2>
                <a
                    v-if="$page.props.auth.user"
                    :href="route('federations.create')"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Создать федерацию!
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Сообщения об успехе/ошибке -->
                        <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ status }}
                        </div>
                        <div v-if="error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {{ error }}
                        </div>

                        <!-- Список федераций -->
                        <div v-if="federations.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="federation in federations"
                                :key="federation.id"
                                class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition"
                            >
                                <div class="p-6">
                                    <div class="flex items-center mb-4">
                                        <div v-if="federation.logo" class="w-12 h-12 rounded-full overflow-hidden mr-3">
                                            <img :src="`/storage/${federation.logo}`" :alt="federation.name" class="w-full h-full object-cover" />
                                        </div>
                                        <div v-else class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-gray-500 text-xl font-bold">
                                                {{ federation.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <Link
                                                :href="route('federations.show', federation.id)"
                                                class="text-xl font-semibold hover:text-blue-600 transition"
                                            >
                                                {{ federation.name }}
                                            </Link>
                                            <p class="text-sm text-gray-500">
                                                Турниров: {{ federation.tournaments_count || 0 }}
                                            </p>
                                        </div>
                                    </div>

                                    <p class="text-gray-600 text-sm mb-4">
                                        {{ truncateText(federation.description, 100) }}
                                    </p>

                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center space-x-2">
                                            <span v-if="federation.website">
                                                <a :href="federation.website" target="_blank" class="text-blue-500 hover:underline">
                                                    Сайт
                                                </a>
                                            </span>
                                            <span v-if="federation.email">
                                                <a :href="`mailto:${federation.email}`" class="text-blue-500 hover:underline">
                                                    Почта
                                                </a>
                                            </span>
                                            <span v-if="federation.phone">
                                                <a :href="`tel:${federation.phone}`" class="text-blue-500 hover:underline">
                                                    Телефон
                                                </a>
                                            </span>
                                        </div>

                                        <!-- Кнопки управления (только для авторизованных) -->
                                        <div v-if="$page.props.auth.user" class="flex space-x-2">
                                            <Link
                                                :href="route('federations.edit', federation.id)"
                                                class="text-blue-500 hover:text-blue-700"
                                            >
                                                Править️
                                            </Link>
                                            <button
                                                @click="confirmDelete(federation)"
                                                class="text-red-500 hover:text-red-700"
                                            >
                                                Удалить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <p class="text-gray-500 text-lg">Федераций пока нет</p>
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('federations.create')"
                                class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Создать первую федерацию
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления -->
        <ConfirmationModal
            :show="showDeleteConfirmation"
            @confirmed="deleteFederation"
            @close="closeDeleteConfirmation"
        >
            Вы действительно хотите удалить федерацию "{{ federationToDelete?.name }}"?
            <p v-if="federationToDelete?.tournaments_count > 0" class="text-red-500 mt-2">
                ⚠️ У этой федерации есть турниры! Удаление невозможно.
            </p>
        </ConfirmationModal>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Link, usePage } from '@inertiajs/vue3';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';

    const props = defineProps({
        federations: {
            type: Array,
            required: true
        },
        status: {
            type: String,
            default: null
        },
        error: {
            type: String,
            default: null
        }
    });

    const showDeleteConfirmation = ref(false);
    const federationToDelete = ref(null);

    const truncateText = (text, length) => {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
    };

    const confirmDelete = (federation) => {
        federationToDelete.value = federation;
        showDeleteConfirmation.value = true;
    };

    const deleteFederation = () => {
        if (federationToDelete.value.tournaments_count > 0) {
            // Нельзя удалить федерацию с турнирами
            showDeleteConfirmation.value = false;
            return;
        }

        router.delete(route('federations.destroy', federationToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
                federationToDelete.value = null;
            },
            onError: (errors) => {
                console.error('Ошибка при удалении:', errors);
            }
        });
    };

    const closeDeleteConfirmation = () => {
        showDeleteConfirmation.value = false;
        federationToDelete.value = null;
    };
</script>
