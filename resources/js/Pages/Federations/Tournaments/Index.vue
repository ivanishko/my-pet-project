<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  federation: Object,
  tournaments: Array,
  status: String,
});
</script>

<template>
  <Head :title="`Турниры - ${federation.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Турниры федерации "{{ federation.name }}"
        </h2>
        <Link
            :href="route('federations.tournaments.create', federation.id)"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Добавить турнир
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
            <div v-if="tournaments.length > 0" class="space-y-4">
              <div
                  v-for="tournament in tournaments"
                  :key="tournament.id"
                  class="p-4 border rounded-lg hover:shadow-md transition-shadow"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="font-bold text-lg">{{ tournament.name }}</h3>
                    <p class="text-gray-600 mt-1">{{ tournament.description }}</p>
                    <div class="mt-2 text-sm text-gray-500">
                      <p>Даты: {{ tournament.start_date }} - {{ tournament.end_date }}</p>
                      <p>Место: {{ tournament.location }}</p>
                    </div>
                  </div>
                  <div class="flex space-x-2">
                    <Link
                        :href="route('federations.tournaments.edit', [federation.id, tournament.id])"
                        class="text-blue-500 hover:text-blue-700 px-3 py-1 border border-blue-500 rounded hover:bg-blue-50"
                    >
                      Редактировать
                    </Link>
                    <Link
                        method="delete"
                        :href="route('federations.tournaments.destroy', [federation.id, tournament.id])"
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
              Нет турниров для этой федерации
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
