<template>
    <GuestLayout>
    <div class="relative min-h-screen bg-gray-100">

        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">

            <!-- Заголовок -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Последние публикации</h1>
                <p class="mt-2 text-gray-600">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </p>
            </div>

            <!-- Список постов -->
            <div v-if="posts.length > 0" class="grid gap-6">
                <div
                        v-for="post in posts"
                        :key="post.id"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <Link :href="route('posts.show', post.id)" class="block">
                        <h3 class="text-xl font-semibold mb-2">{{ post.title }}</h3>
                    </Link>
                    <p class="text-gray-600">{{ post.content.substring(0, 150) }}{{ post.content.length > 150 ? '...' : '' }}</p>
                    <div class="mt-4 flex items-center justify-between">
            <span class="text-sm text-gray-500">
              Автор: {{ post.user.name }}
            </span>
                        <span class="text-sm text-gray-500">
              {{ new Date(post.created_at).toLocaleDateString() }}
            </span>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-12">
                <p class="text-gray-500 text-lg">Пока нет публикаций</p>
            </div>

            <!-- Кнопки авторизации -->
            <div v-if="canLogin && !$page.props.auth.user" class="mt-8 text-center">
                <Link
                        :href="route('login')"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition mr-4"
                >
                Войти
                </Link>
                <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition"
                >
                Регистрация
                </Link>
            </div>
        </div>
    </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

    import { Link } from '@inertiajs/vue3';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        posts: Array,
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
    });
</script>