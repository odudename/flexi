<style>
.flexi_user_width_<?php echo esc_attr( $width ); ?> .flexi-image-wrapper {
  width: <?php echo esc_attr( $width ); ?>px;
  height: <?php echo esc_attr( $height ); ?>px;
}

@media only screen and (max-width: 768px) {
  .flexi_user_width_<?php echo esc_attr( $width ); ?> .flexi-image-wrapper  {
    width: 100%;
    border: 0px solid #eee;
  }
}

	</style>

<div id="flexi_gallery" class="flexi_user_width_<?php echo esc_attr( $width ); ?>">
<div id="flexi_main_loop">
