<?php
$data = flexi_image_data('thumbnail', get_the_ID(), $popup);
?>
<div class="flexi_gallery_child flexi_padding" id="flexi_<?php echo get_the_ID(); ?>" style="position: relative;"
    data-tags="<?php echo esc_attr($tags); ?>">
    <div class="flexi_masonry-item">
        <div class="flexi_effect <?php echo esc_attr($data['popup']); ?>" id="<?php echo esc_attr($hover_effect); ?>">

            <a
                <?php echo sanitize_text_field($data['extra']) . ' href="' . esc_url($data['url']) . '" data-src="' . esc_url($data['src']) . '" data-caption="' . sanitize_title($data['title']) . '" border="0"'; ?>>
                <img class="flexi-fit_cover" src="<?php echo esc_url(flexi_image_src('medium', $post)); ?>">

                <div id="flexi_info" class="<?php echo esc_attr($hover_caption); ?>">
                    <div class="flexi_title"><?php echo esc_attr($data['title']); ?></div>
                    <div class="flexi_p"><?php wpautop(wp_kses_post(flexi_excerpt())); ?></div>
                </div>
                <div class="flexi_figcaption"><?php echo esc_attr($data['title']); ?></div>
            </a>

        </div>
    </div>
</div>
<div style="display: none;" id="flexi_inline_<?php echo get_the_ID(); ?>">
    <h2><?php echo esc_attr($data['title']); ?></h2>
</div>
<div class="godude-desc flexi_desc_<?php echo get_the_ID(); ?>">
    <p><?php echo wp_kses_post(flexi_custom_field_loop($post, 'popup', 1, false)); ?></p>
    <p><?php echo wpautop(wp_kses_post(flexi_excerpt())); ?></p>
</div>