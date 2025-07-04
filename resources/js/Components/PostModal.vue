<template>
    <Modal :show="modelValue" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ isEdit ? 'Edit Post' : 'Create Post' }}
            </h2>

            <div class="mt-4">
                <InputLabel for="title" value="Title" />
                <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full"
                />
                <InputError :message="errors.title" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="content" value="Content" />
                <textarea
                        id="content"
                        v-model="form.content"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="4"
                ></textarea>
                <InputError :message="errors.content" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')">
                    Cancel
                </SecondaryButton>
                <PrimaryButton class="ml-3" @click="submit">
                    Save
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
    import Modal from '@/Components/Modal.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import { reactive, watch } from 'vue';

    const props = defineProps({
        modelValue: Boolean,
        post: Object,
        isEdit: Boolean
    });

    const emit = defineEmits(['update:modelValue', 'save', 'close']);

    const form = reactive({
        title: '',
        content: ''
    });

    const errors = reactive({
        title: '',
        content: ''
    });

    watch(() => props.post, (newPost) => {
        if (newPost) {
            form.title = newPost.title;
            form.content = newPost.content;
        }
    }, { immediate: true });

    const submit = () => {
        // Simple validation
        let isValid = true;

        if (!form.title.trim()) {
            errors.title = 'Title is required';
            isValid = false;
        } else {
            errors.title = '';
        }

        if (!form.content.trim()) {
            errors.content = 'Content is required';
            isValid = false;
        } else {
            errors.content = '';
        }

        if (isValid) {
            emit('save', { ...form });
        }
    };
</script>