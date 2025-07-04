<template>
    <div>
        <div v-for="post in posts" :key="post.id" class="mb-6 p-4 border rounded-lg">
            <h3 class="text-xl font-bold">{{ post.title }}</h3>
            <p class="text-gray-600 mb-2">{{ post.content }}</p>
            <p class="text-sm text-gray-500">Автор: {{ post.user.name }}</p>

            <!-- Кнопки только для авторизованного автора поста -->
            <div v-if="true" class="mt-2">
                <button @click="$emit('edit', post)" class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-sm">
                    Edit
                </button>
                <button @click="$emit('delete', post)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm">
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    defineProps({
        posts: {
            type: Array,
            required: true
        }
    });

    defineEmits(['edit', 'delete']);
</script>