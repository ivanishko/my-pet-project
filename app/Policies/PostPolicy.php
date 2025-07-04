<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(?User $user)
    {
        return true; // Просмотр списка доступен всем
    }

    public function view(?User $user, Post $post)
    {
        return true; // Просмотр конкретного поста доступен всем
    }

    public function create(User $user)
    {
        return $user !== null; // Создание только для авторизованных
    }

    public function update(User $user, Post $post)
    {
        return $user->id === $post->user_id; // Редактирование только для автора
    }

    public function delete(User $user, Post $post)
    {
        return $user->id === $post->user_id; // Удаление только для автора
    }
}