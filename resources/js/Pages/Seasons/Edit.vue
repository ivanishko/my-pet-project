<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Редактирование сезона: {{ season.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Выбор турнира -->
                            <div>
                                <label for="tournament_id" class="block text-sm font-medium text-gray-700">
                                    Турнир *
                                </label>
                                <select
                                    id="tournament_id"
                                    v-model="form.tournament_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Выберите турнир</option>
                                    <option
                                        v-for="tournament in tournaments"
                                        :key="tournament.id"
                                        :value="tournament.id"
                                    >
                                        {{ tournament.name }} ({{ tournament.federation?.name || 'Без федерации' }})
                                    </option>
                                </select>
                                <div v-if="errors.tournament_id" class="text-red-500 text-sm mt-1">
                                    {{ errors.tournament_id }}
                                </div>
                            </div>

                            <!-- Текущее название (информация) -->
                            <div class="bg-gray-50 p-3 rounded">
                                <p class="text-sm text-gray-600">
                                    Текущее название: <span class="font-medium">{{ season.name }}</span>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Новое название будет сгенерировано автоматически на основе дат
                                </p>
                                <p v-if="generatedName" class="mt-1 font-medium text-blue-600">
                                    Предварительный просмотр: {{ generatedName }}
                                </p>
                            </div>

                            <!-- Описание -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Описание
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                            </div>

                            <!-- Дата начала -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">
                                    Дата начала *
                                </label>
                                <input
                                    id="start_date"
                                    v-model="form.start_date"
                                    type="date"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                    @change="generateNamePreview"
                                />
                                <div v-if="errors.start_date" class="text-red-500 text-sm mt-1">
                                    {{ errors.start_date }}
                                </div>
                            </div>

                            <!-- Дата окончания -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">
                                    Дата окончания *
                                </label>
                                <input
                                    id="end_date"
                                    v-model="form.end_date"
                                    type="date"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                    @change="generateNamePreview"
                                />
                                <div v-if="errors.end_date" class="text-red-500 text-sm mt-1">
                                    {{ errors.end_date }}
                                </div>
                            </div>

                            <!-- Информация о длительности -->
                            <div v-if="durationInfo" :class="durationClass" class="p-3 rounded">
                                <p class="text-sm">
                                    {{ durationInfo }}
                                </p>
                            </div>

                            <!-- Текущий статус -->
                            <div class="bg-gray-50 p-3 rounded">
                                <p class="text-sm text-gray-600">
                                    Текущий статус:
                                    <span :class="statusClasses[season.status]" class="px-2 py-1 rounded text-xs ml-1">
                                        {{ statusLabels[season.status] }}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Статус будет обновлен автоматически на основе новых дат
                                </p>
                            </div>

                            <!-- Кнопки -->
                            <div class="flex items-center justify-end space-x-3">
                                <Link
                                    :href="route('tournaments.show', season.tournament_id)"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    Отмена
                                </Link>
                                <button
                                    type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Сохранение...' : 'Обновить' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import { ref, computed, watch, onMounted } from 'vue';

    const props = defineProps({
        season: {
            type: Object,
            required: true
        },
        tournaments: {
            type: Array,
            required: true
        },
        status: {
            type: String,
            default: null
        }
    });

    // Функция для преобразования даты в формат YYYY-MM-DD
    const formatDateForInput = (date) => {
        if (!date) return '';
        // Если дата уже в формате YYYY-MM-DD, возвращаем как есть
        if (typeof date === 'string' && date.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return date;
        }
        // Иначе парсим и форматируем
        try {
            const d = new Date(date);
            if (isNaN(d.getTime())) return '';
            return d.toISOString().split('T')[0];
        } catch (e) {
            return '';
        }
    };

    // Инициализация формы с правильными значениями дат
    const form = useForm({
        tournament_id: props.season.tournament_id || '',
        description: props.season.description || '',
        start_date: formatDateForInput(props.season.start_date),
        end_date: formatDateForInput(props.season.end_date),
    });

    const errors = ref({});
    const generatedName = ref(props.season.name || '');

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

    // Информация о длительности
    const durationInfo = computed(() => {
        if (!form.start_date || !form.end_date) return '';

        const start = new Date(form.start_date);
        const end = new Date(form.end_date);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 366) {
            return 'Сезон не может длиться более одного года!';
        }

        return `Длительность: ${diffDays} дней`;
    });

    const durationClass = computed(() => {
        if (!form.start_date || !form.end_date) return '';

        const start = new Date(form.start_date);
        const end = new Date(form.end_date);
        const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24));

        return diffDays > 366 ? 'bg-red-50 text-red-800' : 'bg-blue-50 text-blue-800';
    });

    // Генерация названия
    const generateNamePreview = () => {
        if (!form.start_date || !form.end_date) {
            generatedName.value = '';
            return;
        }

        const start = new Date(form.start_date);
        const end = new Date(form.end_date);
        const startYear = start.getFullYear();
        const endYear = end.getFullYear();

        // Проверка на валидность
        if (end < start) {
            generatedName.value = 'Ошибка: дата окончания раньше даты начала';
            return;
        }

        if (startYear === endYear) {
            generatedName.value = String(startYear);
        } else {
            generatedName.value = `${startYear} - ${endYear}`;
        }
    };

    // Следим за изменением дат
    watch([() => form.start_date, () => form.end_date], () => {
        generateNamePreview();
    }, { immediate: true });

    // При монтировании компонента генерируем название
    onMounted(() => {
        generateNamePreview();
    });

    const submit = () => {
        // Дополнительная проверка перед отправкой
        if (form.start_date && form.end_date) {
            const start = new Date(form.start_date);
            const end = new Date(form.end_date);

            if (end < start) {
                errors.value = { end_date: 'Дата окончания не может быть раньше даты начала' };
                return;
            }

            const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24));
            if (diffDays > 366) {
                errors.value = { end_date: 'Сезон не может длиться более одного года' };
                return;
            }
        }

        form.put(route('seasons.update', props.season.id), {
            onError: (error) => {
                errors.value = error;
            },
            onSuccess: () => {
                errors.value = {};
            }
        });
    };
</script>
