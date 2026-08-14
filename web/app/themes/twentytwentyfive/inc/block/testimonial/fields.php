<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$testimonial = new FieldsBuilder('testimonial_block');

$testimonial
    ->addText('author_name', ['label' => 'Nom'])
    ->addTextarea('quote', ['label' => 'Citation'])
    ->addImage('author_photo', ['label' => 'Photo', 'return_format' => 'array'])
    ->setLocation('post_type', '==', 'project');

add_action('acf/init', function () use ($testimonial) {
    acf_add_local_field_group($testimonial->build());
});