<template>
    <GuestLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Турнир: {{ season.tournament?.name || 'Не указан' }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Сезон: {{ season.name }}
                        <span v-if="existingRounds.length > 0" class="ml-4">
                            Туров: {{ existingRounds.length }}
                        </span>
                    </p>
                </div>
                <div class="flex space-x-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('seasons.teams.index', season.id)"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                        Управление командами
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('seasons.edit', season.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                        Редактировать
                    </Link>
                    <Link
                        :href="route('seasons.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                        Назад
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

                <!-- Основная информация -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Статус</p>
                                <span :class="seasonStatusClasses[season.status]" class="px-2 py-1 rounded text-xs">
                                    {{ seasonStatusLabels[season.status] }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Начало</p>
                                <p class="font-medium">{{ formatDate(season.start_date) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Окончание</p>
                                <p class="font-medium">{{ formatDate(season.end_date) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Команд</p>
                                <p class="font-medium">{{ teamsCount }}</p>
                            </div>
                        </div>
                        <div v-if="season.description" class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-500">Описание</p>
                            <p class="text-gray-700">{{ season.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Расписание -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Расписание
                            </h3>
                        </div>

                        <!-- Информация о командах -->
                        <div v-if="teamsCount < 2 && $page.props.auth.user" class="bg-yellow-50 p-4 rounded-lg mb-4">
                            <p class="text-yellow-800">
                                Для создания расписания необходимо минимум 2 команды в сезоне.
                            </p>
                            <Link
                                :href="route('seasons.teams.index', season.id)"
                                class="text-blue-600 hover:text-blue-800 text-sm"
                            >
                                Управление командами →
                            </Link>
                        </div>

                        <!-- Существующие туры -->
                        <div v-if="existingRounds.length > 0" class="space-y-4">
                            <div
                                v-for="(round, index) in existingRounds"
                                :key="round.id"
                                class="border rounded-lg p-4"
                            >
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-gray-800">
                                        {{ round.name }}
                                    </h4>
                                    <div class="flex space-x-2">
                                        <span class="text-sm text-gray-400">
                                            {{ round.games?.length || 0 }} матчей
                                        </span>
                                        <button
                                            v-if="round.games && round.games.length === 0 && $page.props.auth.user"
                                            @click="confirmDeleteRound(round.id)"
                                            class="text-red-500 hover:text-red-700 text-sm"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>

                                <!-- Матчи тура -->
                                <div v-if="round.games && round.games.length > 0" class="space-y-2">
                                    <div
                                        v-for="game in round.games"
                                        :key="game.id"
                                        class="bg-gray-50 p-3 rounded flex justify-between items-center"
                                    >
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">{{ game.home_team?.name || 'Неизвестно' }}</span>
                                                <span class="text-gray-500 text-sm">vs</span>
                                                <span class="font-medium">{{ game.away_team?.name || 'Неизвестно' }}</span>
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
                                                - : -
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-400">
                                    Матчей нет
                                </div>
                            </div>
                        </div>

                        <!-- Форма создания нового тура -->
                        <div v-if="isCreatingRound" class="border-2 border-blue-300 rounded-lg p-4 mt-4 bg-blue-50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-semibold text-gray-800">
                                    {{ newRoundName }}
                                </h4>
                                <button
                                    @click="cancelRoundCreation"
                                    class="text-gray-500 hover:text-gray-700 text-sm"
                                >
                                    Отменить
                                </button>
                            </div>

                            <!-- Форма добавления матча -->
                            <div class="bg-white p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Дата и время
                                        </label>
                                        <input
                                            v-model="newGame.start_time"
                                            type="datetime-local"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Место проведения
                                        </label>
                                        <input
                                            v-model="newGame.venue"
                                            type="text"
                                            placeholder="Стадион"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Команда хозяев
                                        </label>
                                        <select
                                            v-model="newGame.home_team_id"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">Выберите команду</option>
                                            <option
                                                v-for="team in availableTeams"
                                                :key="team.id"
                                                :value="team.id"
                                                :disabled="team.id === newGame.away_team_id"
                                            >
                                                {{ team.name }}
                                                <span v-if="isTeamUsed(team.id)" class="text-gray-400 text-xs">
                                                    (уже в матче)
                                                </span>
                                            </option>
                                        </select>
                                        <p v-if="availableTeams.length === 0" class="text-sm text-yellow-600 mt-1">
                                            Все команды уже добавлены в матчи
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Команда гостей
                                        </label>
                                        <select
                                            v-model="newGame.away_team_id"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">Выберите команду</option>
                                            <option
                                                v-for="team in availableTeams"
                                                :key="team.id"
                                                :value="team.id"
                                                :disabled="team.id === newGame.home_team_id"
                                            >
                                                {{ team.name }}
                                                <span v-if="isTeamUsed(team.id)" class="text-gray-400 text-xs">
                                                    (уже в матче)
                                                </span>
                                            </option>
                                        </select>
                                        <p v-if="availableTeams.length === 0" class="text-sm text-yellow-600 mt-1">
                                            Все команды уже добавлены в матчи
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Голы хозяев
                                        </label>
                                        <input
                                            v-model.number="newGame.home_score"
                                            type="number"
                                            min="0"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Голы гостей
                                        </label>
                                        <input
                                            v-model.number="newGame.away_score"
                                            type="number"
                                            min="0"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>

                                <div class="mt-3 flex justify-between items-center">
                                    <button
                                        @click="addGameToRound"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                                        :disabled="!newGame.home_team_id || !newGame.away_team_id || availableTeams.length === 0"
                                    >
                                        Добавить матч
                                    </button>
                                    <span class="text-sm text-gray-500">
                                        Добавлено матчей: {{ tempGames.length }}
                                        <span v-if="availableTeams.length === 0" class="text-yellow-600 ml-2">
                                            (все команды использованы)
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- Список добавленных матчей -->
                            <div v-if="tempGames.length > 0" class="mt-4 space-y-2">
                                <div
                                    v-for="(game, gameIndex) in tempGames"
                                    :key="gameIndex"
                                    class="bg-white p-3 rounded flex justify-between items-center"
                                >
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium">{{ getTeamName(game.home_team_id) }}</span>
                                            <span class="text-gray-500 text-sm">vs</span>
                                            <span class="font-medium">{{ getTeamName(game.away_team_id) }}</span>
                                        </div>
                                        <span v-if="game.venue" class="text-sm text-gray-400">
                                            {{ game.venue }}
                                        </span>
                                        <span v-if="game.start_time" class="text-sm text-gray-400">
                                            {{ formatDateTime(game.start_time) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div v-if="game.home_score !== null && game.away_score !== null" class="font-bold">
                                            {{ game.home_score }} - {{ game.away_score }}
                                        </div>
                                        <div v-else class="text-gray-400 text-sm">
                                            - : -
                                        </div>
                                        <button
                                            @click="removeGameFromTemp(gameIndex)"
                                            class="text-red-500 hover:text-red-700 text-sm"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Кнопка сохранения тура -->
                            <div class="mt-4 flex justify-end">
                                <button
                                    @click="saveRound"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded"
                                    :disabled="tempGames.length === 0"
                                >
                                    Сохранить тур
                                </button>
                                <span v-if="tempGames.length === 0" class="text-gray-400 text-sm ml-3 self-center">
                                    Добавьте хотя бы один матч
                                </span>
                            </div>
                        </div>

                        <!-- Если расписания нет и не создается новый тур -->
                        <div v-else-if="existingRounds.length === 0 && !isCreatingRound" class="text-center py-8">
                            <p class="text-gray-500">Расписание еще не создано</p>
                            <p class="text-sm text-gray-400 mt-1">
                                Нажмите кнопку "{{ createRoundButtonText }}" чтобы начать
                            </p>
                        </div>
                        <div class="pt-4">
                            <button
                                v-if="$page.props.auth.user && teamsCount >= 2 && !isCreatingRound"
                                @click="createNewRound"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                            >
                                {{ createRoundButtonText }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
            <p class="text-gray-500 text-sm mt-2">Это действие нельзя отменить.</p>
        </ConfirmationModal>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    const props = defineProps({
        season: {
            type: Object,
            required: true
        },
        teamsCount: {
            type: Number,
            default: 0
        },
        existingRounds: {
            type: Array,
            default: () => []
        },
        status: {
            type: String,
            default: null
        }
    });

    // Состояние для создания тура
    const isCreatingRound = ref(false);
    const tempGames = ref([]);
    const newGame = ref({
        home_team_id: '',
        away_team_id: '',
        start_time: '',
        venue: '',
        home_score: null,
        away_score: null
    });

    // Состояние для удаления тура
    const showDeleteRoundConfirmation = ref(false);
    const roundToDelete = ref(null);

    // Статусы сезона
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

    // Текст кнопки создания тура
    const createRoundButtonText = computed(() => {
        const count = props.existingRounds.length;
        return count === 0 ? 'Создать 1 тур' : `Создать ${count + 1} тур`;
    });

    // Название нового тура
    const newRoundName = computed(() => {
        const count = props.existingRounds.length + 1;
        return `${count} тур`;
    });

    // Получить список команд, которые уже использованы в матчах
    const usedTeamIds = computed(() => {
        const used = new Set();
        tempGames.value.forEach(game => {
            if (game.home_team_id) used.add(parseInt(game.home_team_id));
            if (game.away_team_id) used.add(parseInt(game.away_team_id));
        });
        return used;
    });

    // Доступные команды (не использованные в матчах)
    const availableTeams = computed(() => {
        if (!props.season.teams) return [];
        return props.season.teams.filter(team => !usedTeamIds.value.has(team.id));
    });

    // Проверка, используется ли команда
    const isTeamUsed = (teamId) => {
        return usedTeamIds.value.has(parseInt(teamId));
    };

    // Получить имя команды по ID
    const getTeamName = (teamId) => {
        const team = props.season.teams?.find(t => t.id === teamId);
        return team?.name || 'Неизвестная команда';
    };

    // Форматирование даты
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
        const date = new Date(dateString);
        return date.toLocaleString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // Создать новый тур (показываем форму)
    const createNewRound = () => {
        isCreatingRound.value = true;
        tempGames.value = [];
        newGame.value = {
            home_team_id: '',
            away_team_id: '',
            start_time: '',
            venue: '',
            home_score: null,
            away_score: null
        };
    };

    // Отмена создания тура
    const cancelRoundCreation = () => {
        isCreatingRound.value = false;
        tempGames.value = [];
    };

    // Добавить матч в текущий тур
    const addGameToRound = () => {
        if (!newGame.value.home_team_id || !newGame.value.away_team_id) {
            alert('Выберите обе команды');
            return;
        }

        // Проверяем, не используются ли команды уже
        if (usedTeamIds.value.has(parseInt(newGame.value.home_team_id))) {
            alert('Команда хозяев уже добавлена в другой матч');
            return;
        }
        if (usedTeamIds.value.has(parseInt(newGame.value.away_team_id))) {
            alert('Команда гостей уже добавлена в другой матч');
            return;
        }

        tempGames.value.push({
            home_team_id: newGame.value.home_team_id,
            away_team_id: newGame.value.away_team_id,
            start_time: newGame.value.start_time,
            venue: newGame.value.venue,
            home_score: newGame.value.home_score,
            away_score: newGame.value.away_score,
            status: 'scheduled'
        });

        // Очищаем форму
        newGame.value.home_team_id = '';
        newGame.value.away_team_id = '';
        newGame.value.home_score = null;
        newGame.value.away_score = null;
    };

    // Удалить матч из временного списка
    const removeGameFromTemp = (index) => {
        tempGames.value.splice(index, 1);
    };

    // Сохранить тур с матчами
    const saveRound = () => {
        if (tempGames.value.length === 0) {
            alert('Добавьте хотя бы один матч');
            return;
        }

        // Проверяем, что все команды в матчах уникальны
        const allTeamIds = [];
        tempGames.value.forEach(game => {
            allTeamIds.push(parseInt(game.home_team_id));
            allTeamIds.push(parseInt(game.away_team_id));
        });
        const uniqueTeamIds = new Set(allTeamIds);
        if (uniqueTeamIds.size !== allTeamIds.length) {
            alert('Каждая команда может участвовать только в одном матче за тур');
            return;
        }

        const roundNumber = props.existingRounds.length + 1;

        const data = {
            season_id: props.season.id,
            name: `${roundNumber} тур`,
            games: tempGames.value.map(game => ({
                home_team_id: game.home_team_id,
                away_team_id: game.away_team_id,
                start_time: game.start_time || null,
                venue: game.venue || null,
                home_score: game.home_score !== null ? parseInt(game.home_score) : null,
                away_score: game.away_score !== null ? parseInt(game.away_score) : null,
                status: 'scheduled'
            }))
        };

        router.post(route('seasons.rounds.store'), data, {
            onSuccess: () => {
                isCreatingRound.value = false;
                tempGames.value = [];
                router.reload();
            },
            onError: (errors) => {
                alert('Ошибка при сохранении: ' + JSON.stringify(errors));
            }
        });
    };

    // Удаление тура
    const confirmDeleteRound = (roundId) => {
        const round = props.existingRounds.find(r => r.id === roundId);
        if (round.games && round.games.length > 0) {
            alert('Невозможно удалить тур, в котором уже есть матчи.');
            return;
        }
        roundToDelete.value = round;
        showDeleteRoundConfirmation.value = true;
    };

    const deleteRound = () => {
        if (!roundToDelete.value) return;

        router.delete(route('rounds.destroy', roundToDelete.value.id), {
            onSuccess: () => {
                showDeleteRoundConfirmation.value = false;
                roundToDelete.value = null;
                router.reload();
            },
            onError: (errors) => {
                alert('Ошибка при удалении тура: ' + JSON.stringify(errors));
                showDeleteRoundConfirmation.value = false;
            }
        });
    };
</script>
