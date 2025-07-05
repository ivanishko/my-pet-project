<template>
    <GuestLayout>
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Заголовок как ссылка -->
                <h3 class="text-xl font-semibold mb-2 transition">
                    {{ post.title }}
                </h3>
                <!-- Краткое содержание -->
                <p class="text-gray-600 mb-4">{{ truncatedContent }}</p>

                <!-- Автор и дата -->
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>Автор: {{ post.user.name }}</span>
                    <span>{{ formattedDate }}</span>
                </div>
            </div>
        </div>
    </GuestLayout>

</template>

<script setup>
    import { Link } from '@inertiajs/vue3';

    import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

    const props = defineProps({
        post: {
            type: Object,
            required: true
        }
    });

    const truncatedContent = computed(() => {
        return props.post.content.length > 200
            ? props.post.content.substring(0, 200) + '...'
            : props.post.content;
    });

    const formattedDate = computed(() => {
        return new Date(props.post.created_at).toLocaleDateString();
    });
</script>