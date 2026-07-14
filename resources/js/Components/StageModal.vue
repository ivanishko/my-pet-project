<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ isEdit ? 'Редактирование этапа' : 'Создание этапа' }}
            </h2>

            <form @submit.prevent="submit" class="mt-4 space-y-4">
                <!-- Тип этапа -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">
                        Тип этапа *
                    </label>
                    <select
                        id="type"
                        v-model="form.type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option
                            v-for="(label, value) in availableTypes"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>
                    <div v-if="errors.type" class="text-red-500 text-sm mt-1">
                        {{ errors.type }}
                    </div>
                    <p v-if="!isEdit && !hasChampionship" class="text-xs text-gray-500 mt-1">
                        Примечание: "Чемпионат" можно создать только один раз
                    </p>
                </div>

                <!-- Название (автоматически) -->
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-sm text-gray-600">
                        Название этапа:
                        <span class="font-medium text-gray-900">{{ generatedName }}</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Название генерируется автоматически на основе выбранного типа
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
                        rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                </div>

                <!-- Информация о типе -->
                <div class="bg-blue-50 p-3 rounded text-sm text-blue-800">
                    <p v-if="form.type === 'championship'">
                        Чемпионат: Каждая команда играет с каждой. Победитель определяется по сумме очков.
                    </p>
                    <p v-else-if="form.type === 'group'">
                        Групповой этап: Команды разделяются на группы. В каждой группе команды играют друг с другом.
                    </p>
                    <p v-else-if="form.type === 'playoff'">
                        Плей-офф: Команды играют на вылет. Проигравший покидает турнир.
                    </p>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="close">
                        Отмена
                    </SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Сохранение...' : (isEdit ? 'Обновить' : 'Создать') }}
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
        seasonId: {
            type: Number,
            required: true
        },
        stage: {
            type: Object,
            default: null
        },
        isEdit: {
            type: Boolean,
            default: false
        },
        existingStages: {
            type: Array,
            default: () => []
        }
    });

    const emit = defineEmits(['save', 'close']);

    const typeLabels = {
        championship: 'Чемпионат',
        group: 'Групповой этап',
        playoff: 'Плей-офф'
    };

    const form = ref({
        type: props.stage?.type || 'championship',
        description: props.stage?.description || '',
    });

    const errors = ref({});

    // Проверяем, есть ли уже чемпионат в сезоне
    const hasChampionship = computed(() => {
        if (props.isEdit && props.stage?.type === 'championship') {
            // Если редактируем существующий чемпионат, то он не считается
            return props.existingStages.some(s => s.type === 'championship' && s.id !== props.stage.id);
        }
        return props.existingStages.some(s => s.type === 'championship');
    });

    // Доступные типы для выбора
    const availableTypes = computed(() => {
        if (props.isEdit) {
            // При редактировании показываем все типы
            return typeLabels;
        }

        // При создании исключаем чемпионат, если он уже есть
        if (hasChampionship.value) {
            const { championship, ...rest } = typeLabels;
            return rest;
        }

        return typeLabels;
    });

    const generatedName = computed(() => {
        return typeLabels[form.value.type] || form.value.type;
    });

    watch(() => props.show, (newShow) => {
        if (newShow && props.stage) {
            form.value = {
                type: props.stage.type || 'championship',
                description: props.stage.description || '',
            };
        } else if (!newShow) {
            form.value = {
                type: 'championship',
                description: '',
            };
            errors.value = {};
        }
    }, { immediate: true });

    // Если чемпионат уже есть, автоматически выбираем другой тип
    watch(hasChampionship, (newVal) => {
        if (newVal && !props.isEdit && form.value.type === 'championship') {
            form.value.type = 'group';
        }
    }, { immediate: true });

    const submit = () => {
        emit('save', {
            ...form.value,
            season_id: props.seasonId
        });
    };

    const close = () => {
        emit('close');
    };
</script>
