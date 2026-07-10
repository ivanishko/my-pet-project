<script setup>
    import { ref, computed } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import { Link, usePage } from '@inertiajs/vue3';
    import { Head } from '@inertiajs/vue3';
    const showingNavigationDropdown = ref(false);
    const page = usePage();

    // Computed свойства для удобства
    const user = computed(() => page.props.auth.user);
    const isAuthenticated = computed(() => !!user.value);

    defineProps({
        title: {
            type: String,
            default: 'Главная'
        }
    });
</script>

<template>
    <Head :title="title" />
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Левая часть: Логотип и навигация -->
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink
                                    :href="route('posts.index')"
                                    :active="route().current('posts.index')"
                                >
                                    Записи
                                </NavLink>
                                <NavLink
                                    :href="route('seasons.index')"
                                    :active="route().current('seasons.index')"
                                >
                                    Текущие турниры
                                </NavLink>
                                <NavLink
                                    v-if="isAuthenticated"
                                    :href="route('federations.index')"
                                    :active="route().current('federations.index')"
                                >
                                    Федерации
                                </NavLink>
                                <NavLink
                                    v-if="isAuthenticated"
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Дашбоард
                                </NavLink>
                            </div>
                        </div>

                        <!-- Правая часть: Авторизация или профиль -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Если пользователь НЕ авторизован - показываем кнопки Войти и Регистрация -->
                            <div v-if="!isAuthenticated" class="flex items-center gap-4">
                                <Link
                                    :href="route('login')"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                                >
                                    Войти
                                </Link>
                                <Link
                                    :href="route('register')"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
                                >
                                    Регистрация
                                </Link>
                            </div>

                            <!-- Если пользователь авторизован - показываем дропдаун с профилем -->
                            <div v-else class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ user.name }}
                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Профиль
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Выйти
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Гамбургер (мобильное меню) -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu (мобильное меню) -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <!-- Навигационные ссылки для мобильной версии -->
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('posts.index')"
                            :active="route().current('posts.index')"
                        >
                            Записи
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('federations.index')"
                            :active="route().current('federations.index')"
                        >
                            Федерации
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="isAuthenticated"
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Дашбоард
                        </ResponsiveNavLink>
                    </div>

                    <!-- Для НЕавторизованных - ссылки на вход и регистрацию -->
                    <div v-if="!isAuthenticated" class="pt-4 pb-1 border-t border-gray-200">
                        <div class="space-y-1">
                            <ResponsiveNavLink :href="route('login')">
                                Войти
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('register')">
                                Регистрация
                            </ResponsiveNavLink>
                        </div>
                    </div>

                    <!-- Для авторизованных - профиль и выход -->
                    <div v-else class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Профиль
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Выйти
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
