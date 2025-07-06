<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Posts
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Кнопка создания поста -->
                        <button
                                v-if="$page.props.auth.user"
                                @click="openCreateModal"
                                class="mb-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Create Post
                        </button>

                        <div v-if="posts.length > 0">
                            <PostList :posts="posts" @edit="openEditModal" @delete="openDeleteModal" />
                        </div>
                        <div v-else class="p-4 text-gray-500">
                            Постов нет
                        </div>

                        <!-- Модальное окно для создания/редактирования -->
                        <PostModal
                                :show="showPostModal"
                                :post="currentPost"
                                :is-edit="isEditMode"
                                @save="handleSave"
                                @close="closePostModal"
                        />

                        <!-- Модальное окно подтверждения удаления -->
                        <ConfirmationModal
                                :show="showDeleteConfirmation"
                                @confirmed="confirmDelete"
                                @close="closeDeleteConfirmation"
                        >
                            Are you sure you want to delete this post?
                        </ConfirmationModal>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PostList from '@/Components/PostList.vue';
    import PostModal from '@/Components/PostModal.vue';
    import ConfirmationModal from '@/Components/ConfirmationModal.vue';
    import { ref, onMounted } from 'vue';
    import { router } from '@inertiajs/vue3';
    import axios from 'axios';
    import GuestLayout from '@/Layouts/GuestLayout.vue';

    // Данные постов
    const posts = ref([]);
    const isLoading = ref(true);
    const error = ref(null);

    // Состояние модальных окон
    const showPostModal = ref(false);
    const showDeleteConfirmation = ref(false);

    // Текущий пост для редактирования/удаления
    const currentPost = ref(null);
    const isEditMode = ref(false);
    const postToDeleteId = ref(null);

    // Загрузка постов
    onMounted(() => {
        fetchPosts();
    });

    const fetchPosts = () => {
        router.get('/posts', {}, {
            preserveState: true,
            onSuccess: (page) => {
                posts.value = page.props.posts || [];
            },
            onError: (errors) => {
                console.error('Ошибка при загрузке постов:', errors);
                posts.value = [];
            }
        });
    };

    // Управление модальными окнами
    const openCreateModal = () => {
        currentPost.value = { title: '', content: '' };
        isEditMode.value = false;
        showPostModal.value = true;
    };

    const openEditModal = (post) => {
        currentPost.value = { ...post };
        isEditMode.value = true;
        showPostModal.value = true;
    };

    const openDeleteModal = (post) => {
        postToDeleteId.value = post.id;
        showDeleteConfirmation.value = true;
    };

    const closePostModal = () => {
        showPostModal.value = false;
    };

    const closeDeleteConfirmation = () => {
        showDeleteConfirmation.value = false;
    };

    // Обработка сохранения
    const handleSave = async (postData) => {
        try {
            const url = isEditMode.value
                ? `/posts/${currentPost.value.id}`
                : '/posts';

            const method = isEditMode.value ? 'put' : 'post';

            const response = await axios[method](url, postData);

            if (response.status === 201 || response.status === 200) {
                fetchPosts();
                closePostModal();
            }
        } catch (error) {
            console.error('Error saving post:', error);

            // Показываем более подробную ошибку
            if (error.response) {
                console.error('Response data:', error.response.data);
                alert(`Error: ${error.response.data.message || 'Unknown error'}`);
            } else {
                alert('Network error or server is not responding');
            }
        }
    };

    // Подтверждение удаления
    const confirmDelete = async () => {
        try {
            await axios.delete(`/posts/${postToDeleteId.value}`);
            fetchPosts();
            closeDeleteConfirmation();
        } catch (error) {
            console.error('Error deleting post:', error);
        }
    };
</script>