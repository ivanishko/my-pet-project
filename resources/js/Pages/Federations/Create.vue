<template>
    <GuestLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Создание федерации
            </h2>
        </template>

        <div class="py-12">

            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Название -->
                            <div>

                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Название *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                                <div v-if="errors.name" class="text-red-500 text-sm mt-1">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <!-- Описание -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Описание
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                            </div>

                            <!-- Логотип -->
                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700">
                                    Логотип
                                </label>
                                <input
                                    id="logo"
                                    type="file"
                                    @change="handleFileUpload"
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <div v-if="form.logoPreview" class="mt-2">
                                    <img :src="form.logoPreview" alt="Preview" class="w-32 h-32 object-cover rounded" />
                                </div>
                                <div v-if="errors.logo" class="text-red-500 text-sm mt-1">
                                    {{ errors.logo }}
                                </div>
                            </div>

                            <!-- Сайт -->
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700">
                                    Веб-сайт
                                </label>
                                <input
                                    id="website"
                                    v-model="form.website"
                                    type="url"
                                    placeholder="https://example.com"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <div v-if="errors.website" class="text-red-500 text-sm mt-1">
                                    {{ errors.website }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="info@example.com"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <div v-if="errors.email" class="text-red-500 text-sm mt-1">
                                    {{ errors.email }}
                                </div>
                            </div>

                            <!-- Телефон -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Телефон
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="+7 (999) 123-45-67"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <div v-if="errors.phone" class="text-red-500 text-sm mt-1">
                                    {{ errors.phone }}
                                </div>
                            </div>

                            <!-- Кнопки -->
                            <div class="flex items-center justify-end space-x-3">
                                <Link
                                    :href="route('federations.index')"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    Отмена
                                </Link>
                                <button
                                    type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Сохранение...' : 'Создать' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Link } from '@inertiajs/vue3';
    import { useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const form = useForm({
        name: '',
        description: '',
        logo: null,
        logoPreview: null,
        website: '',
        email: '',
        phone: '',
    });

    const errors = ref({});

    const handleFileUpload = (event) => {
        const file = event.target.files[0];
        if (file) {
            form.logo = file;
            // Создаем превью
            const reader = new FileReader();
            reader.onload = (e) => {
                form.logoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    };

    const submit = () => {
        console.log('form', form);
        form.post(route('federations.store'), {
            onError: (error) => {
                errors.value = error;
            },
            onSuccess: () => {
                form.reset('logo', 'logoPreview');
            },
        });
    };
</script>
