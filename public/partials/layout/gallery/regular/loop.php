<?php
$data = flexi_image_data('thumbnail', get_the_ID(), $popup);
// echo '<div id="flexi_main_loop">';
echo '<div class="flexi_responsive flexi_gallery_child" id="flexi_' . get_the_ID() . '"  data-tags="' . esc_attr($tags) . '">';
echo '<div class="flexi_gallery_grid flexi_padding flexi_effect ' . esc_attr($data['popup']) . '" id="' . esc_attr($hover_effect) . '">';
echo '<div class="flexi-image-wrapper" style="border: 1px solid #eee">';
// echo '<a class="" href="' . esc_url($data['url']) . '" data-caption="' . esc_attr($data['title']) . '" border="0">';
echo '<a ' . sanitize_text_field($data['extra']) . ' href="' . esc_url_raw($data['url']) . '" data-caption="' . esc_attr($data['title']) . '" data-src="' . esc_url_raw($data['src']) . '" border="0">';
echo '<img src="' . esc_url_raw(flexi_image_src('medium', $post)) . '" class="flexi_type_' . esc_attr($data['type']) . '">';
?>


<div id="flexi_info" class="<?php echo esc_attr($hover_caption); ?>">
    <div class="flexi_title"><?php echo esc_attr($data['title']); ?></div>
    <div class="flexi_p"><?php echo wp_kses_post(flexi_excerpt()); ?></div>
</div>
<div class="flexi_figcaption"><?php echo esc_attr($data['title']); ?></div>
<?php
echo '</a>';
echo '</div>';
echo '</div>';
echo '</div>';
// echo "</div>";
?>
<div class="godude-desc flexi_desc_<?php echo get_the_ID(); ?>">
    <p><?php echo wp_kses_post(flexi_custom_field_loop($post, 'popup', 1, false)); ?></p>
    <p><?php echo wpautop(wp_kses_post(flexi_excerpt())); ?></p>
    <p><?php echo wp_kses_post(flexi_show_icon_grid()); ?></p>
</div>