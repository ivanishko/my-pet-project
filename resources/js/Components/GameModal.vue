<template>
    <Modal :show="show" @close="close">
        <div class="p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-medium text-gray-900">
                {{ isEdit ? 'Редактирование игры' : 'Добавление игры' }}
            </h2>

            <form @submit.prevent="submit" class="mt-4 space-y-4">
                <!-- Выбор команды хозяев -->
                <div>
                    <label for="home_team_id" class="block text-sm font-medium text-gray-700">
                        Команда хозяев *
                    </label>
                    <select
                        id="home_team_id"
                        v-model="form.home_team_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Выберите команду</option>
                        <option
                            v-for="team in availableTeams"
                            :key="team.id"
                            :value="team.id"
                            :disabled="team.id === form.away_team_id"
                        >
                            {{ team.name }}
                        </option>
                    </select>
                    <div v-if="errors.home_team_id" class="text-red-500 text-sm mt-1">
                        {{ errors.home_team_id }}
                    </div>
                </div>

                <!-- Выбор команды гостей -->
                <div>
                    <label for="away_team_id" class="block text-sm font-medium text-gray-700">
                        Команда гостей *
                    </label>
                    <select
                        id="away_team_id"
                        v-model="form.away_team_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Выберите команду</option>
                        <option
                            v-for="team in availableTeams"
                            :key="team.id"
                            :value="team.id"
                            :disabled="team.id === form.home_team_id"
                        >
                            {{ team.name }}
                        </option>
                    </select>
                    <div v-if="errors.away_team_id" class="text-red-500 text-sm mt-1">
                        {{ errors.away_team_id }}
                    </div>
                </div>

                <!-- Дата и время -->
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">
                        Дата и время начала
                    </label>
                    <input
                        id="start_time"
                        v-model="form.start_time"
                        type="datetime-local"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <!-- Место проведения -->
                <div>
                    <label for="venue" class="block text-sm font-medium text-gray-700">
                        Место проведения
                    </label>
                    <input
                        id="venue"
                        v-model="form.venue"
                        type="text"
                        placeholder="Стадион, арена"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <!-- Счет -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="home_score" class="block text-sm font-medium text-gray-700">
                            Голы хозяев
                        </label>
                        <input
                            id="home_score"
                            v-model.number="form.home_score"
                            type="number"
                            min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label for="away_score" class="block text-sm font-medium text-gray-700">
                            Голы гостей
                        </label>
                        <input
                            id="away_score"
                            v-model.number="form.away_score"
                            type="number"
                            min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
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
                        <option value="scheduled">Запланирована</option>
                        <option value="in_progress">В процессе</option>
                        <option value="completed">Завершена</option>
                        <option value="postponed">Перенесена</option>
                        <option value="cancelled">Отменена</option>
                    </select>
                </div>

                <div v-if="form.status === 'completed' && (form.home_score === null || form.away_score === null)" class="bg-yellow-50 p-3 rounded text-sm text-yellow-800">
                    Для завершения игры укажите счет.
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="close">Отмена</SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Сохранение...' : (isEdit ? 'Обновить' : 'Добавить') }}
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
    import { ref, watch, computed } from 'vue';

    const props = defineProps({
        show: Boolean,
        roundId: {
            type: Number,
            required: true
        },
        game: {
            type: Object,
            default: null
        },
        teams: {
            type: Array,
            default: () => []
        },
        isEdit: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['save', 'close']);

    const form = ref({
        home_team_id: '',
        away_team_id: '',
        start_time: '',
        venue: '',
        home_score: null,
        away_score: null,
        status: 'scheduled',
    });

    const errors = ref({});

    const availableTeams = computed(() => {
        return props.teams || [];
    });

    watch(() => props.show, (newShow) => {
        if (newShow && props.game) {
            form.value = {
                home_team_id: props.game.home_team_id || '',
                away_team_id: props.game.away_team_id || '',
                start_time: props.game.start_time ? new Date(props.game.start_time).toISOString().slice(0, 16) : '',
                venue: props.game.venue || '',
                home_score: props.game.home_score,
                away_score: props.game.away_score,
                status: props.game.status || 'scheduled',
            };
        } else if (!newShow) {
            form.value = {
                home_team_id: '',
                away_team_id: '',
                start_time: '',
                venue: '',
                home_score: null,
                away_score: null,
                status: 'scheduled',
            };
            errors.value = {};
        }
    }, { immediate: true });

    const submit = () => {
        // Проверка что выбраны разные команды
        if (form.value.home_team_id && form.value.away_team_id &&
            form.value.home_team_id === form.value.away_team_id) {
            errors.value = { home_team_id: 'Команды должны быть разными' };
            return;
        }

        emit('save', {
            ...form.value,
            home_score: form.value.home_score !== null && form.value.home_score !== '' ? parseInt(form.value.home_score) : null,
            away_score: form.value.away_score !== null && form.value.away_score !== '' ? parseInt(form.value.away_score) : null,
            round_id: props.roundId
        });
    };

    const close = () => {
        emit('close');
    };
</script>
