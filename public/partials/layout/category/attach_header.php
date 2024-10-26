<?php
$sidebar_widget = flexi_get_option('enable_gallery_widget', 'flexi_image_layout_settings', 0);

if ($sidebar_widget == 1 && is_active_sidebar('flexi-gallery-sidebar') &&  is_flexi_page('category_page', 'flexi_categories_settings')) {

    echo '<div class="fl-columns"><div class="fl-column fl-is-three-quarters">';
}


//Attach header gallery based based on layout selection
$header_file = FLEXI_PLUGIN_DIR . 'public/partials/layout/category/' . esc_attr($layout) . '/header.php';
if (file_exists($header_file)) {
    require $header_file;
}

$style_text_color = flexi_get_option('flexi_style_text_color', 'flexi_app_style_settings', '');
$style_button = flexi_get_option('flexi_style_button', 'flexi_app_style_settings', 'fl-button');