<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <GuestLayout>
        <Head :title="'Дашборд'" />
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Дашбоард</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="$page.props.auth.user" class="p-6 text-gray-900">Вы авторизовались как <b>{{ $page.props.auth.user.name }}</b>!</div>
                    <div v-else>Авторизуйтесь</div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
