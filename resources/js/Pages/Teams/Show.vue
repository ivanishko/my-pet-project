<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Команда: {{ team.name }}
                </h2>
                <div class="flex space-x-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('teams.edit', team.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Редактировать
                    </Link>
                    <Link
                        :href="route('teams.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Назад к списку
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-start">
                            <div v-if="team.logo" class="w-32 h-32 rounded-full overflow-hidden mr-6 flex-shrink-0">
                                <img :src="`/storage/${team.logo}`" :alt="team.name" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ team.name }}</h1>
                                <p class="text-gray-600 mb-4">{{ team.city }}</p>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <span>
                                        <span class="font-medium">Федерация:</span>
                                        <Link :href="route('federations.show', team.federation.id)" class="text-blue-600 hover:underline ml-1">
                                            {{ team.federation.name }}
                                        </Link>
                                    </span>
                                    <span>
                                        <span class="font-medium">Статус:</span>
                                        <span :class="statusClasses[team.status]" class="px-2 py-1 rounded text-xs ml-1">
                                            {{ statusLabels[team.status] }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="team.description" class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Описание</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ team.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';

    const props = defineProps({
        team: {
            type: Object,
            required: true
        }
    });

    const statusLabels = {
        active: 'Активная',
        inactive: 'Неактивная'
    };

    const statusClasses = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800'
    };
</script>
