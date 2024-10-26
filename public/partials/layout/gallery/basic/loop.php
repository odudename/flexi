<?php
$data = flexi_image_data('thumbnail', get_the_ID(), $popup);
$style_text_color = flexi_get_option('flexi_style_text_color', 'flexi_app_style_settings', '');
echo '<div class="flexi_responsive flexi_gallery_child" id="flexi_' . get_the_ID() . '"  data-tags="' . esc_attr($tags) . '">';
echo '<div class="flexi_gallery_grid">';
?>

<div class="flexi-list-small">

    <?php

    echo '<div class="flexi-image-wrapper flexi-list-sub ' . esc_attr($data['popup']) . ' flexi_effect" id="' . esc_attr($hover_effect) . '">';
    echo '<a ' . sanitize_text_field($data['extra']) . ' href="' . esc_url($data['url']) . '" data-caption="' . sanitize_title($data['title']) . '" data-src="' . esc_url($data['src']) . '" border="0">';
    echo '<img src="' . esc_url(flexi_image_src('medium', $post)) . '">';
    ?>
    <div id="flexi_info" class="<?php echo esc_attr($hover_caption); ?>">
        <div class="flexi_title"><?php echo esc_attr($data['title']); ?></div>
        <div class="flexi_p"><?php echo wpautop(wp_kses_post(flexi_excerpt())); ?></div>
    </div>
    <div class="flexi_figcaption"><?php echo esc_attr($data['title']); ?></div>
    <?php
    echo '</a>';
    echo '</div>';
    ?>


    <div class="flexi_details  <?php echo esc_attr($style_text_color); ?>"
        style="<?php echo flexi_evalue_toggle('title', $evalue); ?>">
        <?php echo esc_attr($data['title']); ?>
    </div>

</div>
</div>
</div>
<div class="godude-desc flexi_desc_<?php echo get_the_ID(); ?>">
    <p><?php echo wp_kses_post(flexi_custom_field_loop($post, 'popup', 1, false)); ?></p>
    <p><?php echo wpautop(wp_kses_post(flexi_excerpt())); ?></p>
</div>