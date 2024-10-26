<?php
//Attach footer of gallery based based on layout selection
$footer_file = FLEXI_PLUGIN_DIR . 'public/partials/layout/category/' . $layout . '/footer.php';
if (file_exists($footer_file)) {
  require $footer_file;
}
$sidebar_widget = flexi_get_option('enable_gallery_widget', 'flexi_image_layout_settings', 0);
if ($sidebar_widget == 1 && is_active_sidebar('flexi-gallery-sidebar') &&  is_flexi_page('category_page', 'flexi_categories_settings')) {

  echo "</div><div class='fl-column'><div class='flexi_gallery_sidebar'>";
  dynamic_sidebar('flexi-gallery-sidebar');
  echo "</div></div></div>";
}
?>
<style>
:root {
    --flexi_category_padding: <?php echo esc_attr($padding);
    ?>;
}
</style>