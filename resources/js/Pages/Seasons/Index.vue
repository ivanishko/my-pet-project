<template>
    <GuestLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Все сезоны
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Сообщения -->
                        <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ status }}
                        </div>

                        <!-- Список сезонов -->
                        <div v-if="seasons.length > 0" class="space-y-4">
                            <div
                                v-for="season in seasons"
                                :key="season.id"
                                class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition p-4"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <Link
                                            :href="route('seasons.show', season.id)"
                                            class="text-xl font-semibold hover:text-blue-600 transition"
                                        >
                                            {{ season.tournament?.name}} {{ season.name }}
                                        </Link>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <p>Федерация: {{ season.tournament?.federation?.name || 'Не указана' }}</p>
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
                                            <span>
                                                {{ formatDate(season.start_date) }} - {{formatDate(season.end_date) }}
                                            </span>
                                            <span>
                                                <span :class="statusClasses[season.status]" class="px-2 py-1 rounded text-xs">
                                                    {{ statusLabels[season.status] }}
                                                </span>
                                            </span>
                                        </div>
                                        <p v-if="season.description" class="mt-2 text-gray-600 text-sm">
                                            {{ season.description }}
                                        </p>
                                    </div>
                                    <div v-if="$page.props.auth.user" class="flex space-x-2 ml-4">
                                        <Link
                                            :href="route('seasons.edit', season.id)"
                                            class="text-blue-500 hover:text-blue-700 text-sm"
                                        >
                                            Редактировать
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <p class="text-gray-500 text-lg">Сезонов пока нет</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';

    const props = defineProps({
        seasons: {
            type: Array,
            required: true
        },
        status: {
            type: String,
            default: null
        }
    });

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

    const formatDate = (dateString) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString('ru-RU', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };
</script>
