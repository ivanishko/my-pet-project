<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  tournament: Object,
  status: String,
});
</script>

<template>
  <Head :title="`Сезоны - ${tournament.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Сезоны турнира "{{ tournament.name }}"
        </h2>
        <Link
            :href="route('tournaments.seasons.create', tournament.id)"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Добавить сезон
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
          {{ status }}
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div v-if="tournament?.seasons?.length > 0" class="space-y-4">
              <div
                  v-for="season in tournament.seasons"
                  :key="season.id"
                  class="p-4 border rounded-lg hover:shadow-md transition-shadow"
                  :class="{ 'bg-blue-50': season.is_current }"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="font-bold text-lg">
                      Сезон {{ season.name }}
                      <span v-if="season.is_current" class="ml-2 text-sm bg-green-500 text-white px-2 py-1 rounded">
                                                Текущий
                                            </span>
                    </h3>
                    <div class="mt-2 text-sm text-gray-500">
                      <p>Период: {{ season.start_date }} - {{ season.end_date }}</p>
                    </div>
                  </div>
                  <div class="flex space-x-2">
                    <Link
                        :href="route('tournaments.seasons.edit', [tournament.id, season.id])"
                        class="text-blue-500 hover:text-blue-700 px-3 py-1 border border-blue-500 rounded hover:bg-blue-50"
                    >
                      Редактировать
                    </Link>
                    <Link
                        method="delete"
                        :href="route('tournaments.seasons.destroy', [tournament.id, season.id])"
                        as="button"
                        class="text-red-500 hover:text-red-700 px-3 py-1 border border-red-500 rounded hover:bg-red-50"
                        preserve-scroll
                    >
                      Удалить
                    </Link>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-gray-500 py-4">
              Нет сезонов для этого турнира
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
