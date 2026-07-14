<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Команды сезона: {{ season.name }}
                </h2>
                <Link
                    :href="route('seasons.show', season.id)"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    Назад к сезону
                </Link>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Список команд в сезоне -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">
                                Команды в сезоне ({{ teams.length }})
                            </h3>

                            <div v-if="teams.length > 0" class="space-y-2">
                                <div
                                    v-for="team in teams"
                                    :key="team.id"
                                    class="flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50"
                                >
                                    <div class="flex items-center">
                                        <div v-if="team.logo" class="w-8 h-8 rounded-full overflow-hidden mr-3">
                                            <img :src="`/storage/${team.logo}`" :alt="team.name" class="w-full h-full object-cover" />
                                        </div>
                                        <div v-else class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-gray-500 text-sm font-bold">
                                                {{ team.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ team.name }}</p>
                                            <p class="text-sm text-gray-500">{{ team.city }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ team.federation?.name || 'Без федерации' }}
                                            </p>
                                        </div>
                                    </div>
                                    <button
                                        @click="removeTeam(team)"
                                        class="text-red-500 hover:text-red-700 text-sm"
                                    >
                                        Удалить
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                В сезоне пока нет команд
                            </div>
                        </div>
                    </div>

                    <!-- Доступные команды для добавления -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">
                                Доступные команды ({{ availableTeams.length }})
                            </h3>

                            <!-- Команды федерации -->
                            <div v-if="federationTeams.length > 0" class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Команды вашей федерации</span>
                                </h4>
                                <div class="space-y-2">
                                    <div
                                        v-for="team in federationTeams"
                                        :key="team.id"
                                        class="flex justify-between items-center p-3 border border-blue-200 bg-blue-50 rounded-lg hover:bg-blue-100"
                                    >
                                        <div class="flex items-center">
                                            <div v-if="team.logo" class="w-8 h-8 rounded-full overflow-hidden mr-3">
                                                <img :src="`/storage/${team.logo}`" :alt="team.name" class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-gray-500 text-sm font-bold">
                                                    {{ team.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium">{{ team.name }}</p>
                                                <p class="text-sm text-gray-500">{{ team.city }}</p>
                                            </div>
                                        </div>
                                        <button
                                            @click="addTeam(team)"
                                            class="text-green-500 hover:text-green-700 text-sm font-medium"
                                        >
                                            Добавить
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Команды других федераций -->
                            <div v-if="otherTeams.length > 0">
                                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Команды других федераций</span>
                                </h4>
                                <div class="space-y-2">
                                    <div
                                        v-for="team in otherTeams"
                                        :key="team.id"
                                        class="flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50"
                                    >
                                        <div class="flex items-center">
                                            <div v-if="team.logo" class="w-8 h-8 rounded-full overflow-hidden mr-3">
                                                <img :src="`/storage/${team.logo}`" :alt="team.name" class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-gray-500 text-sm font-bold">
                                                    {{ team.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium">{{ team.name }}</p>
                                                <p class="text-sm text-gray-500">{{ team.city }}</p>
                                                <p class="text-xs text-gray-400">
                                                    {{ team.federation?.name || 'Без федерации' }}
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            @click="addTeam(team)"
                                            class="text-green-500 hover:text-green-700 text-sm"
                                        >
                                            Добавить
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="availableTeams.length === 0" class="text-center py-8 text-gray-500">
                                Все доступные команды уже добавлены
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопка для массового добавления -->
                <div v-if="availableTeams.length > 0" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                        <span class="text-sm text-gray-600">
                            Доступно для добавления: {{ availableTeams.length }} команд
                            <span v-if="federationTeams.length > 0" class="text-blue-600">
                                ({{ federationTeams.length }} из вашей федерации)
                            </span>
                        </span>
                        <button
                            @click="addAllTeams"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Добавить все команды
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления -->
        <ConfirmationModal
            :show="showDeleteConfirmation"
            @confirmed="confirmRemoveTeam"
            @close="showDeleteConfirmation = false"
        >
            Вы действительно хотите удалить команду "{{ teamToRemove?.name }}" из сезона?
        </ConfirmationModal>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Link, router } from '@inertiajs/vue3';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref, computed } from 'vue';

    const props = defineProps({
        season: {
            type: Object,
            required: true
        },
        teams: {
            type: Array,
            required: true
        },
        allTeams: {
            type: Array,
            required: true
        },
        federationId: {
            type: Number,
            required: true
        },
        status: {
            type: String,
            default: null
        }
    });

    const showDeleteConfirmation = ref(false);
    const teamToRemove = ref(null);

    // Доступные команды (которые еще не добавлены)
    const availableTeams = computed(() => {
        const teamIds = props.teams.map(t => t.id);
        return props.allTeams.filter(t => !teamIds.includes(t.id));
    });

    // Команды федерации
    const federationTeams = computed(() => {
        return availableTeams.value.filter(t => t.federation_id === props.federationId);
    });

    // Команды других федераций
    const otherTeams = computed(() => {
        return availableTeams.value.filter(t => t.federation_id !== props.federationId);
    });

    const addTeam = (team) => {
        router.post(route('seasons.teams.store'), {
            season_id: props.season.id,
            team_id: team.id
        }, {
            onSuccess: () => {
                // Страница обновится автоматически
            }
        });
    };

    const removeTeam = (team) => {
        teamToRemove.value = team;
        showDeleteConfirmation.value = true;
    };

    const confirmRemoveTeam = () => {
        router.delete(route('seasons.teams.destroy', [props.season.id, teamToRemove.value.id]), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
                teamToRemove.value = null;
            }
        });
    };

    const addAllTeams = () => {
        if (availableTeams.value.length === 0) return;

        const teamIds = availableTeams.value.map(t => t.id);

        router.post(route('seasons.teams.store.multiple'), {
            season_id: props.season.id,
            team_ids: teamIds
        }, {
            onSuccess: () => {
                // Страница обновится автоматически
            }
        });
    };
</script>
