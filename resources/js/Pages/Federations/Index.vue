<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const federations = ref([]);
const newFederation = ref({
  name: '',
  description: ''
});

const fetchFederations = async () => {
  try {
    const response = await axios.get('/federations');
    federations.value = response.data;
  } catch (error) {
    console.error('Error fetching federations:', error);
  }
};

const createFederation = async () => {
  try {
    await axios.post('/federations', newFederation.value);
    newFederation.value = { name: '', description: '' };
    await fetchFederations();
  } catch (error) {
    console.error('Error creating federation:', error);
  }
};

const deleteFederation = async (id) => {
  try {
    await axios.delete(`/federations/${id}`);
    await fetchFederations();
  } catch (error) {
    console.error('Error deleting federation:', error);
  }
};

onMounted(fetchFederations);
</script>

<template>
  <Head title="Federations" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Federations</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-medium mb-4">Create New Federation</h3>

            <div class="mb-6">
              <input v-model="newFederation.name" placeholder="Name" class="border p-2 rounded mr-2" />
              <input v-model="newFederation.description" placeholder="Description" class="border p-2 rounded mr-2" />
              <button @click="createFederation" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
            </div>

            <h3 class="text-lg font-medium mb-4">Federations List</h3>
            <ul>
              <li v-for="federation in federations" :key="federation.id" class="mb-4 p-4 border rounded flex justify-between items-center">
                <div>
                  <h4 class="font-bold">{{ federation.name }}</h4>
                  <p>{{ federation.description }}</p>
                </div>
                <button @click="deleteFederation(federation.id)" class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
