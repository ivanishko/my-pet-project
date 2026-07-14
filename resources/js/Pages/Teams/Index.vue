<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ federation ? `Команды ${federation.name}` : 'Все команды' }}
                </h2>
                <button
                    v-if="$page.props.auth.user"
                    @click="openCreateModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Создать команду
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ status }}
                        </div>

                        <div v-if="teams.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="team in teams"
                                :key="team.id"
                                class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition"
                            >
                                <div class="p-6">
                                    <div class="flex items-center mb-4">
                                        <div v-if="team.logo" class="w-12 h-12 rounded-full overflow-hidden mr-3">
                                            <img :src="`/storage/${team.logo}`" :alt="team.name" class="w-full h-full object-cover" />
                                        </div>
                                        <div v-else class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-gray-500 text-xl font-bold">
                                                {{ team.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <Link
                                                :href="route('teams.show', team.id)"
                                                class="text-xl font-semibold hover:text-blue-600 transition"
                                            >
                                                {{ team.name }}
                                            </Link>
                                            <p class="text-sm text-gray-500">{{ team.city }}</p>
                                        </div>
                                    </div>

                                    <p class="text-gray-600 text-sm mb-4">
                                        {{ truncateText(team.description, 100) }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <span :class="statusClasses[team.status]" class="px-2 py-1 rounded text-xs">
                                            {{ statusLabels[team.status] }}
                                        </span>
                                        <div v-if="$page.props.auth.user" class="flex space-x-2">
                                            <Link
                                                :href="route('teams.edit', team.id)"
                                                class="text-blue-500 hover:text-blue-700"
                                            >
                                                Редактировать
                                            </Link>
                                            <button
                                                @click="confirmDelete(team)"
                                                class="text-red-500 hover:text-red-700"
                                            >
                                                Удалить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12">
                            <p class="text-gray-500 text-lg">Команд пока нет</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <TeamModal
            :show="showModal"
            :federations="federations"
            :federation-id="federation?.id"
            @save="handleSave"
            @close="closeModal"
        />

        <ConfirmationModal
            :show="showDeleteConfirmation"
            @confirmed="deleteTeam"
            @close="closeDeleteConfirmation"
        >
            Вы действительно хотите удалить команду "{{ teamToDelete?.name }}"?
        </ConfirmationModal>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Link, router } from '@inertiajs/vue3';
    import TeamModal from '@/Components/TeamModal.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref } from 'vue';

    const props = defineProps({
        teams: {
            type: Array,
            required: true
        },
        federation: {
            type: Object,
            default: null
        },
        federations: {
            type: Array,
            default: () => []
        },
        status: {
            type: String,
            default: null
        }
    });

    const showModal = ref(false);
    const showDeleteConfirmation = ref(false);
    const teamToDelete = ref(null);

    const statusLabels = {
        active: 'Активная',
        inactive: 'Неактивная'
    };

    const statusClasses = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800'
    };

    const truncateText = (text, length) => {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
    };

    const openCreateModal = () => {
        showModal.value = true;
    };

    const handleSave = (data) => {
        router.post(route('teams.store'), data, {
            onSuccess: () => {
                closeModal();
            }
        });
    };

    const closeModal = () => {
        showModal.value = false;
    };

    const confirmDelete = (team) => {
        teamToDelete.value = team;
        showDeleteConfirmation.value = true;
    };

    const deleteTeam = () => {
        router.delete(route('teams.destroy', teamToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
                teamToDelete.value = null;
            }
        });
    };

    const closeDeleteConfirmation = () => {
        showDeleteConfirmation.value = false;
        teamToDelete.value = null;
    };
</script>
