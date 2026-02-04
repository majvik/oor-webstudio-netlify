<?php
/**
 * Регистрация Custom Post Types
 */

// Custom Post Type: Артисты
function oor_register_artist_post_type() {
    register_post_type('artist', [
        'labels' => [
            'name' => 'Артисты',
            'singular_name' => 'Артист',
            'add_new' => 'Добавить артиста',
            'add_new_item' => 'Добавить нового артиста',
            'edit_item' => 'Редактировать артиста',
            'new_item' => 'Новый артист',
            'view_item' => 'Просмотреть артиста',
            'search_items' => 'Искать артистов',
            'not_found' => 'Артисты не найдены',
            'not_found_in_trash' => 'В корзине артистов не найдено',
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'artists'],
        'supports' => ['title', 'thumbnail'],
        'menu_icon' => 'dashicons-microphone',
        'show_in_rest' => false, // Отключаем Gutenberg
    ]);
}
add_action('init', 'oor_register_artist_post_type');

// Custom Post Type: События
function oor_register_event_post_type() {
    register_post_type('event', [
        'labels' => [
            'name' => 'События',
            'singular_name' => 'Событие',
            'add_new' => 'Добавить событие',
            'add_new_item' => 'Добавить новое событие',
            'edit_item' => 'Редактировать событие',
            'new_item' => 'Новое событие',
            'view_item' => 'Просмотреть событие',
            'search_items' => 'Искать события',
            'not_found' => 'События не найдены',
            'not_found_in_trash' => 'В корзине событий не найдено',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'events'],
        'supports' => ['title', 'thumbnail'],
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_rest' => false,
    ]);
}
add_action('init', 'oor_register_event_post_type');
