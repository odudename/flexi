<style>
  .flexi_basic_width_category_<?php echo esc_attr($width); ?>.flexi-image-wrapper {
    width: <?php echo esc_attr($width); ?>px;
    height: <?php echo esc_attr($height); ?>px;
  }

  .flexi_basic_width_category_<?php echo esc_attr($width); ?>.flexi-list-sub_category {
    width: <?php echo esc_attr($width); ?>px;
    height: <?php echo esc_attr($height); ?>px;
  }

  @media only screen and (max-width: 768px) {
    .flexi_basic_width_category_<?php echo esc_attr($width); ?>.flexi-list-sub_category {
      width: 100%;
    }
  }

  .flexi_basic_width_category_<?php echo esc_attr($width); ?>.flexi-list-sub_category>a {
    height: <?php echo esc_attr($height); ?>px;
  }
</style>

<?php
$primary_page_link = get_permalink(flexi_get_option('primary_page', 'flexi_image_layout_settings', 0));
$category_page_link = get_permalink(flexi_get_option('category_page', 'flexi_categories_settings', 0));
?>
<div id="flexi_gallery" class="flexi_basic_width_category_<?php echo esc_attr($width); ?>">
  <div id="flexi_main_loop">