<template>

    <GuestLayout>
        <Head :title="`Редактирование турнира ${ tournament.name }`" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Редактирование турнира: {{ tournament.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Выбор федерации -->
                            <div>
                                <label for="federation_id" class="block text-sm font-medium text-gray-700">
                                    Федерация *
                                </label>
                                <select
                                    id="federation_id"
                                    v-model="form.federation_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Выберите федерацию</option>
                                    <option
                                        v-for="federation in federations"
                                        :key="federation.id"
                                        :value="federation.id"
                                    >
                                        {{ federation.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.federation_id" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.federation_id }}
                                </div>
                            </div>

                            <!-- Название турнира -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Название турнира *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                    required
                                />
                                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.name }}
                                </div>
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

                            <!-- Местоположение -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">
                                    Местоположение *
                                </label>
                                <input
                                    id="location"
                                    v-model="form.location"
                                    type="text"
                                    placeholder="Город, место проведения"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.location }"
                                    required
                                />
                                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.location }}
                                </div>
                            </div>

                            <!-- Тип турнира -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">
                                    Тип турнира *
                                </label>
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="individual">Индивидуальный</option>
                                    <option value="team">Командный</option>
                                    <option value="mixed">Смешанный</option>
                                </select>
                                <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.type }}
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
                                    <option value="active">Активный</option>
                                    <option value="inactive">Неактивный</option>
                                    <option value="completed">Завершен</option>
                                </select>
                            </div>

                            <!-- Кнопки -->
                            <div class="flex items-center justify-end space-x-3">
                                <Link
                                    :href="route('tournaments.index')"
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
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';

    const props = defineProps({
        tournament: {
            type: Object,
            required: true
        },
        federations: {
            type: Array,
            required: true
        }
    });

    const form = useForm({
        federation_id: props.tournament.federation_id || '',
        name: props.tournament.name || '',
        description: props.tournament.description || '',
        location: props.tournament.location || '',
        type: props.tournament.type || 'individual',
        status: props.tournament.status || 'active',
    });

    const submit = () => {
        form.put(route('tournaments.update', props.tournament.id), {
            onSuccess: () => {
                // Можно добавить уведомление
            },
            onError: (errors) => {
                console.error('Ошибка при обновлении:', errors);
            }
        });
    };
</script>
