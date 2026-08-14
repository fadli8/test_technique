<?php
use StoutLogic\AcfBuilder\FieldsBuilder;
$builder = new FieldsBuilder('post_groupage_type');

$builder->addText('client_name', [
        'label' => 'Nom du client',
    ])
    ->addSelect('project_category', [
        'label' => 'Catégorie',
        'choices' => [
            'web' => 'Web',
            'branding' => 'Branding',
            'mobile' => 'Mobile',
        ]
    ])
    ->addGallery('project_gallery', [
        'label' => 'Galerie photos',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => [],
        'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
        ],
        'return_format' => 'array',
        'min' => '',
        'max' => '',
        'insert' => 'append',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
    ])
    ->addUrl('project_url', [
        'label' => 'Lien externe',
    ])
    ->addTextarea('project_summary', [
        'label' => 'Description courte',
    ])
    ->setLocation('post_type', '==', 'project');


    add_action('acf/init', function () use ($builder) {
    acf_add_local_field_group($builder->build());
});