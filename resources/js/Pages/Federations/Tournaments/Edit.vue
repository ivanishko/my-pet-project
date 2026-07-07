<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  federation: Object,
  tournament: Object,
});

const form = useForm({
  name: props.tournament?.name ?? '',
  description: props.tournament?.description ?? '',
  start_date: props.tournament?.start_date ?? '',
  end_date: props.tournament?.end_date ?? '',
  location: props.tournament?.location ?? '',
});

const submit = () => {
  props.tournament
      ? form.put(route('federations.tournaments.update', [props.federation.id, props.tournament.id]))
      : form.post(route('federations.tournaments.store', props.federation.id));
};
</script>

<template>
  <Head :title="`${tournament ? 'Редактирование' : 'Создание'} турнира`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ tournament ? 'Редактирование' : 'Создание' }} турнира для "{{ federation.name }}"
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-gray-700 mb-1">Название *</label>
                  <input
                      v-model="form.name"
                      type="text"
                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      :class="{ 'border-red-500': form.errors.name }"
                      required
                  >
                  <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                    {{ form.errors.name }}
                  </p>
                </div>

                <div>
                  <label class="block text-gray-700 mb-1">Место проведения *</label>
                  <input
                      v-model="form.location"
                      type="text"
                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      :class="{ 'border-red-500': form.errors.location }"
                      required
                  >
                  <p v-if="form.errors.location" class="text-red-500 text-sm mt-1">
                    {{ form.errors.location }}
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <label class="block text-gray-700 mb-1">Дата начала *</label>
                  <input
                      v-model="form.start_date"
                      type="date"
                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      :class="{ 'border-red-500': form.errors.start_date }"
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
                      :class="{ 'border-red-500': form.errors.end_date }"
                      required
                  >
                  <p v-if="form.errors.end_date" class="text-red-500 text-sm mt-1">
                    {{ form.errors.end_date }}
                  </p>
                </div>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 mb-1">Описание</label>
                <textarea
                    v-model="form.description"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="4"
                ></textarea>
              </div>

              <div class="flex items-center justify-end space-x-4">
                <Link
                    :href="route('federations.tournaments.index', federation.id)"
                    class="px-4 py-2 border rounded hover:bg-gray-100"
                >
                  Отмена
                </Link>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    :disabled="form.processing"
                >
                  <span v-if="form.processing">Сохранение...</span>
                  <span v-else>{{ tournament ? 'Обновить' : 'Создать' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
