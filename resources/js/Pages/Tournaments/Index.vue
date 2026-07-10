<template>
    <GuestLayout>
        <Head :title="'Турниры'" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Все турниры
                </h2>
                <button
                    v-if="$page.props.auth.user"
                    @click="openCreateModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Создать турнир
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

                        <!-- Список турниров -->
                        <div v-if="tournaments.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="tournament in tournaments"
                                :key="tournament.id"
                                class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition"
                            >
                                <div class="p-6">
                                    <Link
                                        :href="route('tournaments.show', tournament.id)"
                                        class="block text-xl font-semibold hover:text-blue-600 transition mb-2"
                                    >
                                        {{ tournament.name }}
                                    </Link>
                                    <p class="text-gray-600 text-sm mb-4">
                                        {{ truncateText(tournament.description, 100) }}
                                    </p>
                                    <div class="text-sm text-gray-500 space-y-1">
                                        <div>Место: {{ tournament.location }}</div>
                                        <div>Федерация: {{ tournament.federation?.name || 'Без федерации' }}</div>
                                        <div>
                                            <span :class="statusClasses[tournament.status]" class="px-2 py-1 rounded text-xs">
                                                {{ statusLabels[tournament.status] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-if="$page.props.auth.user" class="mt-4 flex space-x-2">
                                        <Link
                                            :href="route('tournaments.edit', tournament.id)"
                                            class="text-blue-500 hover:text-blue-700"
                                        >
                                            Редактировать
                                        </Link>
                                        <button
                                            @click="confirmDelete(tournament)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <p class="text-gray-500 text-lg">Турниров пока нет</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно создания турнира -->
        <TournamentModal
            :show="showModal"
            :federations="federations"
            @save="handleSave"
            @close="closeModal"
        />

        <!-- Модальное окно подтверждения удаления -->
        <ConfirmationModal
            :show="showDeleteConfirmation"
            @confirmed="deleteTournament"
            @close="closeDeleteConfirmation"
        >
            Вы действительно хотите удалить турнир "{{ tournamentToDelete?.name }}"?
        </ConfirmationModal>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import TournamentModal from '@/Components/TournamentModal.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref } from 'vue';

    const props = defineProps({
        tournaments: {
            type: Array,
            required: true
        },
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
    const tournamentToDelete = ref(null);

    const statusLabels = {
        active: 'Активный',
        inactive: 'Неактивный',
        completed: 'Завершен'
    };

    const statusClasses = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        completed: 'bg-blue-100 text-blue-800'
    };

    const truncateText = (text, length) => {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
    };

    const openCreateModal = () => {
        showModal.value = true;
    };

    const handleSave = (data) => {
        router.post(route('tournaments.store'), data, {
            onSuccess: () => {
                closeModal();
            }
        });
    };

    const closeModal = () => {
        showModal.value = false;
    };

    const confirmDelete = (tournament) => {
        tournamentToDelete.value = tournament;
        showDeleteConfirmation.value = true;
    };

    const deleteTournament = () => {
        router.delete(route('tournaments.destroy', tournamentToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
                tournamentToDelete.value = null;
            }
        });
    };

    const closeDeleteConfirmation = () => {
        showDeleteConfirmation.value = false;
        tournamentToDelete.value = null;
    };
</script>
