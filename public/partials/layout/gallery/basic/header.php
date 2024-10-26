<style>
.flexi_basic_width_<?php echo esc_attr($width); ?> .flexi-image-wrapper {
  width: <?php echo esc_attr($width); ?>px;
  height: <?php echo esc_attr($height); ?>px;
}

.flexi_basic_width_<?php echo esc_attr($width); ?> .flexi-list-sub {
    width: <?php echo esc_attr($width); ?>px;
  height: <?php echo esc_attr($height); ?>px;
}

@media only screen and (max-width: 768px) {
  .flexi_basic_width_<?php echo esc_attr($width); ?> .flexi-list-sub {
    width: 100%;
  }
}

.flexi_basic_width_<?php echo esc_attr($width); ?> .flexi-list-sub > a {
    height: <?php echo esc_attr($height); ?>px;
}

    </style>
<div id="flexi_gallery" class="flexi_basic_width_<?php echo esc_attr($width); ?>">
<div id="flexi_main_loop">
