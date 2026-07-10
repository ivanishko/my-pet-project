<template>
    <GuestLayout>
        <Head :title="`${season.tournament.name} ${season.name}`" />
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Сезон: {{ season.name }}
                </h2>
                <div class="flex space-x-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('seasons.edit', season.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Редактировать
                    </Link>
                    <Link
                        :href="route('seasons.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Назад к списку
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
                                    {{ season.tournament.name }} {{ season.name }}
                                </h1>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 mb-4">
                                    <span v-if="season.tournament">
                                        Турнир:
                                        <Link
                                            :href="route('tournaments.show', season.tournament.id)"
                                            class="text-blue-600 hover:underline"
                                        >
                                            {{ season.tournament.name }}
                                        </Link>
                                    </span>
                                    <span v-if="season.tournament?.federation">
                                        Федерация:
                                        <Link
                                            :href="route('federations.show', season.tournament.federation.id)"
                                            class="text-blue-600 hover:underline"
                                        >
                                            {{ season.tournament.federation.name }}
                                        </Link>
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-500">
                                    <span>
                                        <span class="font-medium">Начало:</span> {{ formatDate(season.start_date) }}
                                    </span>
                                    <span>
                                        <span class="font-medium">Окончание:</span> {{ formatDate(season.end_date) }}
                                    </span>
                                    <span>
                                        <span :class="statusClasses[season.status]" class="px-2 py-1 rounded text-xs">
                                            {{ statusLabels[season.status] }}
                                        </span>
                                    </span>
                                    <span>
                                        <span class="font-medium">Длительность:</span> {{ durationDays }} дней
                                    </span>
                                </div>

                                <div class="mt-4 text-sm text-gray-500">
                                    <span class="font-medium">Создан:</span> {{ formatDateTime(season.created_at) }}
                                    <span v-if="season.updated_at !== season.created_at" class="ml-4">
                                        <span class="font-medium">Обновлен:</span> {{ formatDateTime(season.updated_at) }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="season.is_current" class="ml-6 flex-shrink-0">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    Текущий сезон
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Описание</h3>
                        <div class="prose max-w-none">
                            <p v-if="season.description" class="text-gray-700 whitespace-pre-line">
                                {{ season.description }}
                            </p>
                            <p v-else class="text-gray-400 italic">
                                Описание отсутствует
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Информация о турнире -->
                <div v-if="season.tournament" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Информация о турнире
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Название турнира</p>
                                <Link
                                    :href="route('tournaments.show', season.tournament.id)"
                                    class="font-medium text-blue-600 hover:underline"
                                >
                                    {{ season.tournament.name }}
                                </Link>
                            </div>
                            <div v-if="season.tournament.federation">
                                <p class="text-sm text-gray-500">Федерация</p>
                                <Link
                                    :href="route('federations.show', season.tournament.federation.id)"
                                    class="font-medium text-blue-600 hover:underline"
                                >
                                    {{ season.tournament.federation.name }}
                                </Link>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Местоположение</p>
                                <p class="font-medium">{{ season.tournament.location || 'Не указано' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Тип турнира</p>
                                <p class="font-medium">{{ tournamentTypeLabels[season.tournament.type] || 'Не указан' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Статус турнира</p>
                                <span :class="tournamentStatusClasses[season.tournament.status]" class="px-2 py-1 rounded text-xs">
                                    {{ tournamentStatusLabels[season.tournament.status] || 'Не указан' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки управления -->
                <div v-if="$page.props.auth.user" class="mt-6 flex justify-end space-x-3">
                    <Link
                        :href="route('seasons.edit', season.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Редактировать сезон
                    </Link>
                    <button
                        @click="confirmDelete"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Удалить сезон
                    </button>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления -->
        <ConfirmationModal
            :show="showDeleteConfirmation"
            @confirmed="deleteSeason"
            @close="closeDeleteConfirmation"
        >
            <template #title>
                Удаление сезона
            </template>
            <p>Вы действительно хотите удалить сезон <strong>"{{ season.name }}"</strong>?</p>
        </ConfirmationModal>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref, computed } from 'vue';

    const props = defineProps({
        season: {
            type: Object,
            required: true
        },
        status: {
            type: String,
            default: null
        }
    });

    const showDeleteConfirmation = ref(false);

    // Статусы сезонов
    const statusLabels = {
        upcoming: 'Предстоящий',
        active: 'Активный',
        completed: 'Завершен'
    };

    const statusClasses = {
        upcoming: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        completed: 'bg-gray-100 text-gray-800'
    };

    // Типы турниров
    const tournamentTypeLabels = {
        individual: 'Индивидуальный',
        team: 'Командный',
        mixed: 'Смешанный'
    };

    // Статусы турниров
    const tournamentStatusLabels = {
        active: 'Активный',
        inactive: 'Неактивный',
        completed: 'Завершен'
    };

    const tournamentStatusClasses = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        completed: 'bg-blue-100 text-blue-800'
    };

    // Длительность сезона в днях
    const durationDays = computed(() => {
        if (!props.season.start_date || !props.season.end_date) return 0;
        const start = new Date(props.season.start_date);
        const end = new Date(props.season.end_date);
        const diffTime = Math.abs(end - start);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    });

    const formatDate = (dateString) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString('ru-RU', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const formatDateTime = (dateString) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString('ru-RU', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const confirmDelete = () => {
        showDeleteConfirmation.value = true;
    };

    const deleteSeason = () => {
        router.delete(route('seasons.destroy', props.season.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
            }
        });
    };

    const closeDeleteConfirmation = () => {
        showDeleteConfirmation.value = false;
    };
</script>

<style scoped>
    .prose {
        line-height: 1.8;
    }
</style>
