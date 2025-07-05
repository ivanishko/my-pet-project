<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    federation: Object,
    status: String,
});

const form = useForm({
    name: props.federation.name,
    description: props.federation.description,
});

const submit = () => {
    form.put(route('federations.update', props.federation.id));
};
</script>

<template>
    <Head title="Редактирование федерации" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Редактирование федерации</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Уведомления -->
                <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ status }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2">Название</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                >
                                <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2">Описание</label>
                                <textarea
                                    v-model="form.description"
                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    rows="3"
                                ></textarea>
                            </div>

                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('federations.index')"
                                    class="px-4 py-2 border rounded hover:bg-gray-100 transition-colors"
                                >
                                    Назад
                                </Link>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Сохранение...</span>
                                    <span v-else>Сохранить</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
