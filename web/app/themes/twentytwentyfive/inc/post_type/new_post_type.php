<?php
add_action('init', function () {
    register_post_type('project',[
        'labels' => [
            'name'               => __('Projets'),
            'singular_name'      => __('Projet'),
            'add_new'            => __('Ajouter'),
            'add_new_item'       => __('Ajouter un projet'),
            'edit_item'          => __('Modifier le projet'),
            'new_item'           => __('Nouveau projet'),
            'view_item'          => __('Voir le projet'),
            'all_items'          => __('Tous les projets'),
            'search_items'       => __('Rechercher un projet'),
            'not_found'          => __('Aucun projet trouvé'),
        ],
        'public'        => true,
        'has_archive'   => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-portfolio',
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt'],
        'rewrite'       => ['slug' => 'projets'],
        ]);
});