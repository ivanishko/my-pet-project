<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  tournament: Object,
});

const form = useForm({
  start_date: '',
  end_date: '',
  is_current: false,
});

const submit = () => {
  form.post(route('tournaments.seasons.store', props.tournament.id));
};
</script>

<template>
  <Head title="Добавление сезона" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Добавление сезона для "{{ tournament.name }}"
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-gray-700 mb-1">Дата начала *</label>
                  <input
                      v-model="form.start_date"
                      type="date"
                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                  >
                  <p v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">
                    {{ form.errors.start_date }}
                  </p>
                </div>

                <div>
                  <label class="block text-gray-700 mb-1">Дата окончания *</label>
                  <input
                      v-model="form.end_date"
                      type="date"
                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                  >
                  <p v-if="form.errors.end_date" class="text-red-500 text-sm mt-1">
                    {{ form.errors.end_date }}
                  </p>
                </div>
              </div>

              <div class="mb-4">
                <label class="inline-flex items-center">
                  <input
                      v-model="form.is_current"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  >
                  <span class="ml-2">Сделать текущим сезоном</span>
                </label>
              </div>

              <div class="flex items-center justify-end space-x-4">
                <Link
                    :href="route('tournaments.seasons.index', tournament.id)"
                    class="px-4 py-2 border rounded hover:bg-gray-100"
                >
                  Отмена
                </Link>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    :disabled="form.processing"
                >
                  <span v-if="form.processing">Создание...</span>
                  <span v-else>Создать сезон</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
