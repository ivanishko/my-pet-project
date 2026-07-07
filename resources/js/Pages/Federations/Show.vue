<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  federation: Object,
  status: String,
});
</script>

<template>
  <Head :title="federation.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Федерация: {{ federation.name }}
        </h2>
        <div class="flex space-x-2">
          <Link
              :href="route('federations.edit', federation.id)"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Редактировать
          </Link>
          <Link
              :href="route('federations.index')"
              class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100"
          >
            Назад к списку
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Уведомления -->
        <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
          {{ status }}
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Информация о федерации -->
            <div class="mb-8">
              <h3 class="text-lg font-bold mb-2">Основная информация</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-gray-500">Название:</p>
                  <p class="font-medium">{{ federation.name }}</p>
                </div>
                <div>
                  <p class="text-gray-500">Дата создания:</p>
                  <p class="font-medium">{{ new Date(federation.created_at).toLocaleDateString() }}</p>
                </div>
              </div>
              <div class="mt-4">
                <p class="text-gray-500">Описание:</p>
                <p class="whitespace-pre-line">{{ federation.description || 'Нет описания' }}</p>
              </div>
            </div>

            <!-- Список турниров -->
            <div>
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Турниры федерации</h3>
                <Link
                    :href="route('federations.tournaments.create', federation.id)"
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                >
                  Добавить турнир
                </Link>
              </div>

              <div v-if="federation.tournaments.length > 0" class="space-y-4">
                <div
                    v-for="tournament in federation.tournaments"
                    :key="tournament.id"
                    class="p-4 border rounded-lg hover:shadow-md transition-shadow"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h4 class="font-bold text-lg">
                        <Link
                            :href="route('federations.tournaments.edit', [federation.id, tournament.id])"
                            class="text-blue-500 hover:text-blue-700"
                        >
                          {{ tournament.name }}
                        </Link>
                      </h4>
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
    </div>
  </AuthenticatedLayout>
</template>
