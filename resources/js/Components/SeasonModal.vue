<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Создание сезона
            </h2>

            <form @submit.prevent="submit" class="mt-4 space-y-4">
                <!-- Название (автоматически) -->
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-sm text-gray-600">Название будет сгенерировано автоматически на основе дат</p>
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
                        rows="3"
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
                <div v-if="durationInfo" class="bg-yellow-50 p-3 rounded">
                    <p class="text-sm text-yellow-800">
                        {{ durationInfo }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="close">
                        Отмена
                    </SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Сохранение...' : 'Создать' }}
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
        tournamentId: {
            type: Number,
            required: true
        }
    });

    const emit = defineEmits(['save', 'close']);

    const form = ref({
        description: '',
        start_date: '',
        end_date: ''
    });

    const errors = ref({});
    const generatedName = ref('');

    const durationInfo = computed(() => {
        if (!form.value.start_date || !form.value.end_date) return '';

        const start = new Date(form.value.start_date);
        const end = new Date(form.value.end_date);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 366) {
            return '⚠️ Сезон не может длиться более одного года';
        }

        return `Длительность: ${diffDays} дней`;
    });

    const generateNamePreview = () => {
        if (!form.value.start_date || !form.value.end_date) {
            generatedName.value = '';
            return;
        }

        const start = new Date(form.value.start_date);
        const end = new Date(form.value.end_date);
        const startYear = start.getFullYear();
        const endYear = end.getFullYear();

        if (startYear === endYear) {
            generatedName.value = String(startYear);
        } else {
            generatedName.value = `${startYear} - ${endYear}`;
        }
    };

    watch(() => props.show, (newShow) => {
        if (newShow) {
            // Устанавливаем дату начала на сегодня
            const today = new Date().toISOString().split('T')[0];
            form.value.start_date = today;

            // Устанавливаем дату окончания на +1 месяц
            const future = new Date();
            future.setMonth(future.getMonth() + 1);
            form.value.end_date = future.toISOString().split('T')[0];

            generateNamePreview();
            errors.value = {};
        }
    }, { immediate: true });

    const submit = () => {
        emit('save', {
            ...form.value,
            tournament_id: props.tournamentId
        });
    };

    const close = () => {
        emit('close');
    };
</script>
