<?php
/**
 * @var array $block
 * @var bool  $is_preview  true = affiché dans l'éditeur Gutenberg
 * @var int   $post_id     ID du post courant
 */

// get_field() lit les valeurs À CHAQUE appel = toujours à jour, jamais figé
$post_id = $block->context['postId'] ?? get_the_ID();

// Source unique : on lit les champs définis dans la config ACF du bloc (fields.php)
// => pas de liste codée en dur, le template utilise exactement la config du bloc.
$field_group = acf_get_field_group('group_testimonial_block');
$fields      = $field_group ? acf_get_fields($field_group) : [];

$data = [];
foreach ($fields as $field) {
    $data[$field['name']] = get_field($field['name'], $post_id);
}

$author_name = $data['author_name'] ?? '';
$quote       = $data['quote'] ?? '';
$photo       = $data['author_photo'] ?? null;
$is_preview  = defined('REST_REQUEST') && REST_REQUEST;

$class_name = 'testimonial-block';
if (!empty($block->attributes['className'])) {
    $class_name .= ' ' . $block->attributes['className'];
}
?>
<div class="<?php echo esc_attr($class_name); ?>">
    <?php if ($photo) : ?>
        <img src="<?php echo esc_url($photo['sizes']['medium'] ?? $photo['url']); ?>"
             alt="<?php echo esc_attr($photo['alt']); ?>">
    <?php endif; ?>

    <?php if ($quote) : ?>
        <blockquote><?php echo esc_html($quote);?></blockquote>
    <?php else : ?>
        <?php if ($is_preview) : ?>
            <p><em>Ajoutez une citation dans la sidebar →</em></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($author_name) : ?>
        <p class="author"><strong><?php echo esc_html($author_name); ?></strong></p>
    <?php endif; ?>
</div>