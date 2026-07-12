<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Сезон: {{ season.name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Турнир: {{ season.tournament?.name || 'Не указан' }}
                        <span v-if="teamsCount !== undefined" class="ml-4">
                            Команд в сезоне: {{ teamsCount }}
                        </span>
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
                            <div v-if="$page.props.auth.user && teamsCount >= 2" class="flex space-x-2">
                                <button
                                    v-if="!isEditing"
                                    @click="startEditing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                                >
                                    Редактировать расписание
                                </button>
                                <template v-else>
                                    <button
                                        @click="addNewRound"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                                    >
                                        {{ createRoundButtonText }}
                                    </button>
                                    <button
                                        v-if="rounds.length > 0"
                                        @click="saveSchedule"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                                    >
                                        Сохранить расписание
                                    </button>
                                    <button
                                        @click="cancelEditing"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
                                    >
                                        Отмена
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Информация о командах -->
                        <div v-if="teamsCount < 2" class="bg-yellow-50 p-4 rounded-lg mb-4">
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

                        <!-- Существующее расписание (только просмотр) -->
                        <div v-if="!isEditing && existingRounds.length > 0" class="space-y-4">
                            <div
                                v-for="round in existingRounds"
                                :key="round.id"
                                class="border rounded-lg p-4"
                            >
                                <h4 class="font-semibold text-gray-800 mb-3">
                                    {{ round.name }}
                                </h4>
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

                        <!-- Редактирование расписания -->
                        <div v-else-if="isEditing">
                            <div v-if="rounds.length === 0" class="text-center py-8">
                                <p class="text-gray-500">Расписание еще не создано</p>
                                <p class="text-sm text-gray-400 mt-1">
                                    Нажмите кнопку "{{ createRoundButtonText }}" чтобы начать
                                </p>
                            </div>

                            <div v-else class="space-y-6">
                                <div
                                    v-for="(round, index) in rounds"
                                    :key="index"
                                    class="border rounded-lg p-4"
                                >
                                    <h4 class="font-semibold text-gray-800 mb-3">
                                        {{ round.name }}
                                    </h4>

                                    <!-- Форма добавления матча для последнего тура -->
                                    <div v-if="index === rounds.length - 1" class="bg-gray-50 p-4 rounded-lg">
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
                                                        v-for="team in season.teams"
                                                        :key="team.id"
                                                        :value="team.id"
                                                        :disabled="team.id === newGame.away_team_id"
                                                    >
                                                        {{ team.name }}
                                                    </option>
                                                </select>
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
                                                        v-for="team in season.teams"
                                                        :key="team.id"
                                                        :value="team.id"
                                                        :disabled="team.id === newGame.home_team_id"
                                                    >
                                                        {{ team.name }}
                                                    </option>
                                                </select>
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

                                        <div class="mt-3 flex justify-end">
                                            <button
                                                @click="addGameToRound"
                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                                                :disabled="!newGame.home_team_id || !newGame.away_team_id"
                                            >
                                                Добавить матч
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Список матчей в туре -->
                                    <div v-if="round.games && round.games.length > 0" class="mt-3 space-y-2">
                                        <div
                                            v-for="(game, gameIndex) in round.games"
                                            :key="gameIndex"
                                            class="bg-gray-50 p-3 rounded flex justify-between items-center"
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
                                                <div v-if="game.home_score !== null && game.away_score !== null" class="font-bold text-lg">
                                                    {{ game.home_score }} - {{ game.away_score }}
                                                </div>
                                                <div v-else class="text-gray-400 text-sm">
                                                    Не сыграна
                                                </div>
                                                <button
                                                    @click="removeGameFromRound(index, gameIndex)"
                                                    class="text-red-500 hover:text-red-700 text-sm"
                                                >
                                                    Удалить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-sm text-gray-400 mt-2">
                                        Матчей пока нет
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Если расписания нет -->
                        <div v-else-if="!isEditing && existingRounds.length === 0" class="text-center py-8">
                            <p class="text-gray-500">Расписание еще не создано</p>
                            <button
                                v-if="$page.props.auth.user && teamsCount >= 2"
                                @click="startEditing"
                                class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Создать расписание
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref, computed, watch } from 'vue';

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

    // Состояние для локального расписания
    const rounds = ref([]);
    const newGame = ref({
        home_team_id: '',
        away_team_id: '',
        start_time: '',
        venue: '',
        home_score: null,
        away_score: null
    });
    const isEditing = ref(false);

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

    // Статусы игр
    const gameStatusLabels = {
        scheduled: 'Запланирована',
        in_progress: 'В процессе',
        completed: 'Завершена',
        postponed: 'Перенесена',
        cancelled: 'Отменена'
    };

    const gameStatusClasses = {
        scheduled: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-green-100 text-green-800',
        postponed: 'bg-orange-100 text-orange-800',
        cancelled: 'bg-red-100 text-red-800'
    };

    // Текст кнопки создания тура
    const createRoundButtonText = computed(() => {
        const count = rounds.value.length;
        return count === 0 ? 'Создать 1 тур' : `Создать ${count + 1} тур`;
    });

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

    // Редактирование
    const startEditing = () => {
        // Копируем существующее расписание для редактирования
        rounds.value = props.existingRounds.map(round => ({
            id: round.id,
            name: round.name,
            games: round.games.map(game => ({
                id: game.id,
                home_team_id: game.home_team_id,
                away_team_id: game.away_team_id,
                start_time: game.start_time,
                venue: game.venue,
                home_score: game.home_score,
                away_score: game.away_score,
                status: game.status || 'scheduled'
            }))
        }));
        isEditing.value = true;
    };

    const cancelEditing = () => {
        rounds.value = [];
        isEditing.value = false;
    };

    // Добавить новый тур
    const addNewRound = () => {
        const roundNumber = rounds.value.length + 1;
        rounds.value.push({
            name: `${roundNumber} тур`,
            games: []
        });
        // Очищаем форму
        newGame.value = {
            home_team_id: '',
            away_team_id: '',
            start_time: '',
            venue: '',
            home_score: null,
            away_score: null
        };
    };

    // Добавить матч в последний тур
    const addGameToRound = () => {
        if (rounds.value.length === 0) return;
        if (!newGame.value.home_team_id || !newGame.value.away_team_id) {
            alert('Выберите обе команды');
            return;
        }

        const lastRound = rounds.value[rounds.value.length - 1];
        lastRound.games.push({
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

    // Удалить матч из тура
    const removeGameFromRound = (roundIndex, gameIndex) => {
        rounds.value[roundIndex].games.splice(gameIndex, 1);
    };


    // Сохранить расписание
    const saveSchedule = () => {
        if (rounds.value.length === 0) {
            alert('Нет туров для сохранения');
            return;
        }

        // Проверяем, что в каждом туре есть матчи
        const emptyRounds = rounds.value.filter(r => r.games.length === 0);
        if (emptyRounds.length > 0) {
            alert(`В турах ${emptyRounds.map(r => r.name).join(', ')} нет матчей. Добавьте матчи или удалите пустые туры.`);
            return;
        }

        if (!confirm('Сохранить расписание?')) return;

        // Подготавливаем данные для отправки
        const data = {
            season_id: props.season.id,
            rounds: rounds.value.map(round => ({
                name: round.name,
                games: round.games.map(game => ({
                    home_team_id: game.home_team_id,
                    away_team_id: game.away_team_id,
                    start_time: game.start_time || null,
                    venue: game.venue || null,
                    home_score: game.home_score !== null ? parseInt(game.home_score) : null,
                    away_score: game.away_score !== null ? parseInt(game.away_score) : null,
                    status: 'scheduled'
                }))
            }))
        };

        router.post(route('seasons.schedule.store'), data, {
            onSuccess: () => {
                rounds.value = [];
                isEditing.value = false;
                router.reload();
            },
            onError: (errors) => {
                alert('Ошибка при сохранении: ' + JSON.stringify(errors));
            }
        });
    };
</script>
