<?php

$style_base_color = flexi_get_option('flexi_style_base_color', 'flexi_app_style_settings', '');
$style_text_color = flexi_get_option('flexi_style_text_color', 'flexi_app_style_settings', '');
$style_title = flexi_get_option('flexi_style_heading', 'flexi_app_style_settings', 'fl-is-4 fl-mb-1');
$style_button = flexi_get_option('flexi_style_button', 'flexi_app_style_settings', 'fl-button');

//Displays Toolbar
$toolbar = new Flexi_Gallery_Toolbar();
if (false == $clear) {
?>
<?php echo wp_kses_post($toolbar->label()); ?>

<?php
}
//Display tags

if ($show_tag) {
    $style_tag = flexi_get_option('flexi_style_tag', 'flexi_app_style_settings', 'fl-tag');

    echo wp_kses_post(flexi_generate_tags($tags_array, $style_tag, 'filter_tag')) . "<div style='clear:both;'></div>";
}
//var_dump($evalue);
$sidebar_widget = flexi_get_option('enable_gallery_widget', 'flexi_image_layout_settings', 0);

if ($sidebar_widget == 1 && false == $clear && is_active_sidebar('flexi-gallery-sidebar') && is_flexi_page('primary_page', 'flexi_image_layout_settings')) {

    echo '<div class="fl-columns"><div class="fl-column fl-is-three-quarters">';
}

?>

<?php
//Attach header gallery based based on layout selection
$header_file = FLEXI_PLUGIN_DIR . 'public/partials/layout/gallery/' . $layout . '/header.php';
if (file_exists($header_file)) {
    require $header_file;
}

//Turn off pagination at guten block editor
if (defined('REST_REQUEST') && REST_REQUEST) {
    $navigation = "off";
}
?>