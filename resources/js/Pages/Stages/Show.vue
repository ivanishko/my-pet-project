<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Этап: {{ stage.name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Сезон: {{ stage.season?.name || 'Не указан' }}
                        <span v-if="stage.season?.teams" class="ml-4">
                            Команд: {{ stage.season.teams.length }}
                        </span>
                    </p>
                </div>
                <div class="flex space-x-2">

                    <Link
                        :href="route('seasons.show', stage.season_id)"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                        Назад к сезону
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Сообщения -->
                <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ status }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Информация об этапе -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Тип</p>
                            </div>
                            <div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Порядок</p>
                                <p class="font-medium">#{{ stage.order + 1 }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Туров</p>
                                <p class="font-medium">{{ roundsCount || 0 }}</p>
                            </div>
                        </div>
                        <div v-if="stage.description" class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-500">Описание</p>
                            <p class="text-gray-700">{{ stage.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Информация о командах -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Команды сезона ({{ stage.season?.teams?.length || 0 }})
                        </h3>
                        <div v-if="stage.season?.teams && stage.season.teams.length > 0" class="flex flex-wrap gap-2">
                            <span
                                v-for="team in stage.season.teams"
                                :key="team.id"
                                class="px-3 py-1 bg-gray-100 rounded-full text-sm"
                            >
                                {{ team.name }}
                            </span>
                        </div>
                        <div v-else class="text-gray-500 text-sm">
                            Команды не добавлены. Добавьте команды на странице сезона.
                        </div>
                    </div>
                </div>

                <!-- Туры и игры -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Туры и игры ({{ roundsCount || 0 }} туров)
                            </h3>
                            <button
                                v-if="$page.props.auth.user && stage.season?.teams?.length >= 2"
                                @click="openCreateRoundModal"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                            >
                                Добавить тур
                            </button>
                        </div>

                        <!-- Список туров -->
                        <div v-if="stage.rounds && stage.rounds.length > 0" class="space-y-4">
                            <div
                                v-for="round in stage.rounds"
                                :key="round.id"
                                class="border rounded-lg p-4 hover:shadow-md transition"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <h4 class="font-semibold text-gray-800">
                                                {{ round.name }}
                                            </h4>
                                            <span :class="roundStatusClasses[round.status]" class="px-2 py-0.5 rounded text-xs">
                                                {{ roundStatusLabels[round.status] }}
                                            </span>
                                            <span class="text-sm text-gray-400">
                                                {{ round.games?.length || 0 }} игр
                                            </span>
                                        </div>
                                        <p v-if="round.description" class="text-sm text-gray-600 mt-1">
                                            {{ round.description }}
                                        </p>
                                    </div>
                                    <div v-if="$page.props.auth.user" class="flex space-x-2 ml-4">
                                        <button
                                            @click="openEditRoundModal(round)"
                                            class="text-blue-500 hover:text-blue-700 text-sm"
                                        >
                                            Редактировать
                                        </button>
                                        <button
                                            @click="openAddGameModal(round)"
                                            class="text-green-500 hover:text-green-700 text-sm"
                                        >
                                            Добавить игру
                                        </button>
                                        <button
                                            @click="confirmDeleteRound(round)"
                                            class="text-red-500 hover:text-red-700 text-sm"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>

                                <!-- Игры тура -->
                                <div v-if="round.games && round.games.length > 0" class="mt-3 space-y-2">
                                    <div
                                        v-for="game in round.games"
                                        :key="game.id"
                                        class="bg-gray-50 p-3 rounded flex justify-between items-center hover:bg-gray-100 transition"
                                    >
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">{{ game.home_team?.name || '?' }}</span>
                                                <span class="text-gray-500 text-sm">vs</span>
                                                <span class="font-medium">{{ game.away_team?.name || '?' }}</span>
                                            </div>
                                            <span v-if="game.venue" class="text-sm text-gray-400">
                                                {{ game.venue }}
                                            </span>
                                            <span v-if="game.start_time" class="text-sm text-gray-400">
                                                {{ formatDateTime(game.start_time) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div v-if="game.home_score !== null && game.away_score !== null" class="font-bold text-lg">
                                                {{ game.home_score }} - {{ game.away_score }}
                                            </div>
                                            <div v-else class="text-gray-400 text-sm">
                                                Не сыграна
                                            </div>
                                            <span :class="gameStatusClasses[game.status]" class="px-2 py-0.5 rounded text-xs">
                                                {{ gameStatusLabels[game.status] }}
                                            </span>
                                            <button
                                                v-if="$page.props.auth.user"
                                                @click="openEditGameModal(game)"
                                                class="text-blue-500 hover:text-blue-700 text-sm"
                                            >
                                                Редактировать
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-400 mt-2">
                                    Игр пока нет
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">Туров пока нет</p>
                            <p class="text-sm text-gray-400 mt-1">
                                Добавьте тур вручную с помощью кнопки "Добавить тур"
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Кнопка удаления этапа -->
                <div v-if="$page.props.auth.user" class="mt-6 flex justify-end">
                    <button
                        @click="confirmDeleteStage"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Удалить этап
                    </button>
                </div>
            </div>
        </div>

        <!-- Модальное окно создания тура -->
        <RoundModal
            :show="showRoundModal"
            :stage-id="stage.id"
            :is-edit="isEditRound"
            :round="editingRound"
            @save="handleRoundSave"
            @close="closeRoundModal"
        />

        <!-- Модальное окно добавления игры -->
        <GameModal
            :show="showGameModal"
            :round-id="currentRound?.id"
            :game="editingGame"
            :teams="stage.season?.teams || []"
            :is-edit="isEditGame"
            @save="handleGameSave"
            @close="closeGameModal"
        />

        <!-- Модальное окно редактирования игры -->
        <GameModal
            :show="showEditGameModal"
            :round-id="editingGame?.round_id"
            :game="editingGame"
            :teams="stage.season?.teams || []"
            :is-edit="true"
            @save="handleGameUpdate"
            @close="closeEditGameModal"
        />

        <!-- Модальное окно подтверждения удаления тура -->
        <ConfirmationModal
            :show="showDeleteRoundConfirmation"
            @confirmed="deleteRound"
            @close="showDeleteRoundConfirmation = false"
        >
            <template #title>
                Удаление тура
            </template>
            <p>Вы действительно хотите удалить тур <strong>"{{ roundToDelete?.name }}"</strong>?</p>
            <p class="text-red-500 text-sm mt-2">Все игры в этом туре также будут удалены.</p>
        </ConfirmationModal>

        <!-- Модальное окно подтверждения удаления этапа -->
        <ConfirmationModal
            :show="showDeleteStageConfirmation"
            @confirmed="deleteStage"
            @close="showDeleteStageConfirmation = false"
        >
            <template #title>
                Удаление этапа
            </template>
            <p>Вы действительно хотите удалить этап <strong>"{{ stage.name }}"</strong>?</p>
            <p class="text-red-500 text-sm mt-2">Все туры и игры в этом этапе также будут удалены.</p>
        </ConfirmationModal>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import RoundModal from '@/Components/RoundModal.vue';
    import GameModal from '@/Components/GameModal.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref } from 'vue';

    const props = defineProps({
        stage: {
            type: Object,
            required: true
        },
        roundsCount: {
            type: Number,
            default: 0
        },
        status: {
            type: String,
            default: null
        }
    });

    // Состояния модальных окон
    const showRoundModal = ref(false);

    const showDeleteRoundConfirmation = ref(false);
    const showDeleteStageConfirmation = ref(false);

    // Редактируемые объекты
    const editingRound = ref(null);

    const isEditRound = ref(false);
    const roundToDelete = ref(null);

    const showGameModal = ref(false);
    const showEditGameModal = ref(false);
    const currentRound = ref(null);
    const editingGame = ref(null);
    const isEditGame = ref(false);

    // Открытие модального окна добавления игры
    const openAddGameModal = (round) => {
        currentRound.value = round;
        editingGame.value = null;
        isEditGame.value = false;
        showGameModal.value = true;
    };

    // Обработка сохранения игры
    const handleGameSave = (data) => {
        router.post(route('rounds.games.store', currentRound.value.id), data, {
            onSuccess: () => {
                showGameModal.value = false;
                currentRound.value = null;
                router.reload();
            }
        });
    };

    // Открытие модального окна редактирования игры
    const openEditGameModal = (game) => {
        editingGame.value = game;
        isEditGame.value = true;
        showEditGameModal.value = true;
    };

    // Обработка обновления игры
    const handleGameUpdate = (data) => {
        router.put(route('games.update', editingGame.value.id), data, {
            onSuccess: () => {
                showEditGameModal.value = false;
                editingGame.value = null;
                router.reload();
            }
        });
    };

    // Закрытие модальных окон
    const closeGameModal = () => {
        showGameModal.value = false;
        currentRound.value = null;
    };

    const closeEditGameModal = () => {
        showEditGameModal.value = false;
        editingGame.value = null;
    };
</script>
