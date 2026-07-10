<template>
    <GuestLayout>
        <Head :title="tournament.name" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ tournament.name }}
                </h2>
                <div class="flex space-x-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('tournaments.edit', tournament.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Редактировать
                    </Link>
                    <Link
                        :href="route('tournaments.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Назад
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Основная информация -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                                    {{ tournament.name }}
                                </h1>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 mb-4">
                                    <span class="flex items-center">
                                        Федерация:
                                        <Link
                                            :href="route('federations.show', tournament.federation.id)"
                                            class="ml-1 text-blue-600 hover:underline"
                                        >
                                            {{ tournament.federation.name }}
                                        </Link>
                                    </span>
                                    <span>
                                        Место: {{ tournament.location }}
                                    </span>
                                    <span>
                                        <span :class="statusClasses[tournament.status]" class="px-2 py-1 rounded text-xs">
                                            {{ statusLabels[tournament.status] }}
                                        </span>
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-500">
                                    <span>
                                        Тип: {{ typeLabels[tournament.type] }}
                                    </span>
                                    <span>
                                        Создан: {{ formatDate(tournament.created_at) }}
                                    </span>
                                    <span v-if="tournament.updated_at !== tournament.created_at">
                                        Обновлен: {{ formatDate(tournament.updated_at) }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="tournament.image" class="ml-6 flex-shrink-0">
                                <img
                                    :src="`/storage/${tournament.image}`"
                                    :alt="tournament.name"
                                    class="w-32 h-32 object-cover rounded-lg"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Описание</h3>
                        <div class="prose max-w-none">
                            <p v-if="tournament.description" class="text-gray-700 whitespace-pre-line">
                                {{ tournament.description }}
                            </p>
                            <p v-else class="text-gray-400 italic">
                                Описание отсутствует
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Сезоны -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Сезоны ({{ seasonsCount }})
                            </h3>
                            <button
                                v-if="$page.props.auth.user"
                                @click="openCreateSeasonModal"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                            >
                                Добавить сезон
                            </button>
                        </div>

                        <div v-if="tournament.seasons && tournament.seasons.length > 0" class="space-y-4">
                            <div
                                v-for="season in tournament.seasons"
                                :key="season.id"
                                class="border rounded-lg p-4 hover:shadow-md transition"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">
                                            <Link
                                                :href="route('seasons.show', season.id)"
                                                class="hover:text-blue-600 transition"
                                            >
                                                {{ season.name }}
                                            </Link>
                                        </h4>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ season.description }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-2 text-sm text-gray-500">
                                            <span>
                                                {{ formatDate(season.start_date) }}
                                                {{ season.end_date ? ` - ${formatDate(season.end_date)}` : '' }}
                                            </span>
                                            <span>
                                                <span :class="seasonStatusClasses[season.status]" class="px-2 py-1 rounded text-xs">
                                                    {{ seasonStatusLabels[season.status] }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div v-if="$page.props.auth.user" class="flex space-x-2">
                                        <Link
                                            :href="route('seasons.edit', season.id)"
                                            class="text-blue-500 hover:text-blue-700 text-sm"
                                        >
                                            Редактировать
                                        </Link>
                                        <button
                                            @click="confirmDeleteSeason(season)"
                                            class="text-red-500 hover:text-red-700 text-sm"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">Сезонов пока нет</p>
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('seasons.create', { tournament_id: tournament.id })"
                                class="mt-2 inline-block text-blue-500 hover:text-blue-700"
                            >
                                Создать первый сезон
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Кнопки управления внизу -->
                <div v-if="$page.props.auth.user" class="mt-6 flex justify-end space-x-3">
                    <Link
                        :href="route('tournaments.edit', tournament.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Редактировать турнир
                    </Link>
                    <button
                        @click="confirmDelete"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                        :disabled="tournament.seasons && tournament.seasons.length > 0"
                    >
                        Удалить турнир
                    </button>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления турнира -->
        <ConfirmationModal
            :show="showDeleteConfirmation"
            @confirmed="deleteTournament"
            @close="closeDeleteConfirmation"
        >
            <template #title>
                Удаление турнира
            </template>
            <p>Вы действительно хотите удалить турнир "{{ tournament.name }}"?</p>
            <p v-if="tournament.seasons && tournament.seasons.length > 0" class="text-red-500 mt-2">
                У этого турнира есть сезоны! Удаление невозможно.
            </p>
        </ConfirmationModal>

        <!-- Модальное окно подтверждения удаления сезона -->
        <ConfirmationModal
            :show="showDeleteSeasonConfirmation"
            @confirmed="deleteSeason"
            @close="closeDeleteSeasonConfirmation"
        >
            <template #title>
                Удаление сезона
            </template>
            <p>Вы действительно хотите удалить сезон "{{ seasonToDelete?.name }}"?</p>
        </ConfirmationModal>

        <SeasonModal
            :show="showSeasonModal"
            :tournament-id="tournament.id"
            @save="handleSeasonSave"
            @close="closeSeasonModal"
        />

    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import SeasonModal from '@/Components/SeasonModal.vue'; // <-- ДОБАВЬТЕ ЭТУ СТРОКУ
    import { ref } from 'vue';

    const props = defineProps({
        tournament: {
            type: Object,
            required: true
        },
        seasonsCount: {
            type: Number,
            default: 0
        },
        status: {
            type: String,
            default: null
        }
    });

    const showDeleteConfirmation = ref(false);
    const showDeleteSeasonConfirmation = ref(false);
    const seasonToDelete = ref(null);

    // Статусы турниров
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

    // Типы турниров
    const typeLabels = {
        individual: 'Индивидуальный',
        team: 'Командный',
        mixed: 'Смешанный'
    };

    // Статусы сезонов
    const seasonStatusLabels = {
        upcoming: 'Предстоящий',
        active: 'Активный',
        completed: 'Завершен'
    };

    const seasonStatusClasses = {
        upcoming: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        completed: 'bg-gray-100 text-gray-800'
    };

    // Форматирование даты
    const formatDate = (dateString) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString('ru-RU', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // Удаление турнира
    const confirmDelete = () => {
        if (props.tournament.seasons && props.tournament.seasons.length > 0) {
            return;
        }
        showDeleteConfirmation.value = true;
    };

    const deleteTournament = () => {
        router.delete(route('tournaments.destroy', props.tournament.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
            }
        });
    };

    const closeDeleteConfirmation = () => {
        showDeleteConfirmation.value = false;
    };

    // Удаление сезона
    const confirmDeleteSeason = (season) => {
        seasonToDelete.value = season;
        showDeleteSeasonConfirmation.value = true;
    };

    const deleteSeason = () => {
        router.delete(route('seasons.destroy', seasonToDelete.value.id), {
            onSuccess: () => {
                showDeleteSeasonConfirmation.value = false;
                seasonToDelete.value = null;
                router.reload();
            }
        });
    };

    const closeDeleteSeasonConfirmation = () => {
        showDeleteSeasonConfirmation.value = false;
        seasonToDelete.value = null;
    };

    const showSeasonModal = ref(false);

    const openCreateSeasonModal = () => {
        showSeasonModal.value = true;
    };

    const handleSeasonSave = (data) => {
        router.post(route('seasons.store'), data, {
            onSuccess: () => {
                closeSeasonModal();
                router.reload();
            }
        });
    };

    const closeSeasonModal = () => {
        showSeasonModal.value = false;
    };
</script>

<style scoped>
    .prose {
        line-height: 1.8;
    }
</style>
