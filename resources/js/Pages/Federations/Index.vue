<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Федераций
                </h2>
                <button
                    v-if="$page.props.auth.user"
                    @click="openCreateModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Создать федерацию
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Сообщения -->
                        <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ status }}
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
                                        <div class="flex-1">
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
                                            <a v-if="federation.website" :href="federation.website" target="_blank" class="text-blue-500 hover:underline">
                                                Сайт
                                            </a>
                                            <a v-if="federation.email" :href="`mailto:${federation.email}`" class="text-blue-500 hover:underline">
                                                Email
                                            </a>
                                            <a v-if="federation.phone" :href="`tel:${federation.phone}`" class="text-blue-500 hover:underline">
                                                Телефон
                                            </a>
                                        </div>

                                        <div v-if="$page.props.auth.user" class="flex space-x-2">
                                            <button
                                                @click="openEditModal(federation)"
                                                class="text-blue-500 hover:text-blue-700"
                                            >
                                                Редактировать
                                            </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно создания/редактирования федерации -->
        <FederationModal
            :show="showModal"
            :federation="currentFederation"
            :is-edit="isEditMode"
            @save="handleSave"
            @close="closeModal"
        />

        <!-- Модальное окно подтверждения удаления -->
        <ConfirmationModal
            :show="showDeleteConfirmation"
            @confirmed="deleteFederation"
            @close="closeDeleteConfirmation"
        >
            Вы действительно хотите удалить федерацию "{{ federationToDelete?.name }}"?
        </ConfirmationModal>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Link, router } from '@inertiajs/vue3';
    import FederationModal from '@/Components/FederationModal.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref } from 'vue';

    const props = defineProps({
        federations: {
            type: Array,
            required: true
        },
        status: {
            type: String,
            default: null
        }
    });

    const showModal = ref(false);
    const showDeleteConfirmation = ref(false);
    const currentFederation = ref(null);
    const isEditMode = ref(false);
    const federationToDelete = ref(null);

    const truncateText = (text, length) => {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
    };

    const openCreateModal = () => {
        currentFederation.value = {
            name: '',
            description: '',
            logo: null,
            website: '',
            email: '',
            phone: ''
        };
        isEditMode.value = false;
        showModal.value = true;
    };

    const openEditModal = (federation) => {
        currentFederation.value = { ...federation };
        isEditMode.value = true;
        showModal.value = true;
    };

    const handleSave = (data) => {
        if (isEditMode.value) {
            router.put(route('federations.update', currentFederation.value.id), data, {
                onSuccess: () => {
                    closeModal();
                }
            });
        } else {
            router.post(route('federations.store'), data, {
                onSuccess: () => {
                    closeModal();
                }
            });
        }
    };

    const closeModal = () => {
        showModal.value = false;
        currentFederation.value = null;
        isEditMode.value = false;
    };

    const confirmDelete = (federation) => {
        federationToDelete.value = federation;
        showDeleteConfirmation.value = true;
    };

    const deleteFederation = () => {
        router.delete(route('federations.destroy', federationToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
                federationToDelete.value = null;
            }
        });
    };

    const closeDeleteConfirmation = () => {
        showDeleteConfirmation.value = false;
        federationToDelete.value = null;
    };
</script>
