<?php

/**
 * Standalone gallery with shortcode [flexi-standalone]
 * Also used on media replace on edit screen at frontend
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */
class Flexi_Standalone_Gallery
{

    public function __construct()
    {
        add_shortcode('flexi-standalone', array($this, 'flexi_standalone'));
    }

    public function flexi_standalone($params)
    {
        $put = '';
        ob_start();

        if (isset($params['id'])) {
            $id = esc_attr($params['id']);
        } else {
            if (isset($_REQUEST['id'])) {
                $id = sanitize_text_field($_REQUEST['id']);
            } else {
                $id = get_query_var('id', 0);
            }
        }
        $flexi_post = get_post($id);
        // flexi_log($id);

        if ($flexi_post && 0 != $id && $flexi_post->post_author == get_current_user_id()) {

            if (isset($_REQUEST['id'])) {

                if (isset($_REQUEST['id']) && isset($_REQUEST['manage'])) {
                    // Update primary image form
?>
<style>
#flexi_form {
    display: none;
}
</style>

<!-- Image loader -->
<div id='flexi_loader_internal' style='display: none;'>
    <img src="<?php echo esc_url(FLEXI_PLUGIN_URL . '/public/images/loading.gif'); ?>">
</div>
<div class="flexi_response_internal"></div>


<div id="flexi_form_internal">

    <?php
                        if (sanitize_text_field($_REQUEST['manage']) == 'media') {
                        ?>
    <div id='flexi_ajax_refresh_loader' style='display: none;'>
        <img src="<?php echo esc_url(FLEXI_PLUGIN_URL . '/public/images/loading.gif'); ?>">
    </div>
    <div id="flexi_ajax_refresh_content">
        <div id="flexi_thumb_image"> <a href="<?php echo esc_url(get_permalink($id)); ?>"> <img id="flexi_medium_image"
                    src="<?php echo wp_kses_post(flexi_image_src('medium', $flexi_post)); ?>"></a></div>
    </div>

    <?php
                            $nonce = wp_create_nonce('flexi_ajax_refresh');
                            echo '<a href="#" class="flexi_css_button" id="flexi_ajax_refresh" data-nonce="' . $nonce . '" data-id="' . esc_attr($id) . '" data-method_name="primary_image" data-param1="medium" data-param2="" data-param3="" title="Refresh"></a>';
                            ?>

    <hr>
    <form id="flexi-request-form-update-primary" class="flexi_ajax_update_image" method="post"
        enctype="multipart/form-data" action="<?php echo admin_url("/admin-ajax.php"); ?>">
        <fieldset>
            <legend><?php echo __('Select new image to replace', 'flexi'); ?></legend>
            <input type="file" name="user-submitted-image[]" accept="image/*" value="" id="file" class="" required="">
            <?php
                                    wp_nonce_field('flexi-nonce', 'flexi-nonce', false);
                                    echo '<input type="hidden" name="flexi_id" value="' . esc_attr($id) . '">';
                                    echo '<input type="hidden" name="upload_type" value="flexi">';
                                    echo '<input type="hidden" name="type" value="primary">';
                                    echo '<input type="hidden" name="action" value="flexi_ajax_update_image">';
                                    ?>
            <input type="submit" name="submit" value="<?php echo __('Replace image', 'flexi'); ?>" id="submit" class="">
        </fieldset>
    </form>

    <?php

                            $enable_addon = flexi_get_option('enable_standalone_button', 'flexi_standalone_settings', 1);
                            if ('1' == $enable_addon) {
                                $link = flexi_get_button_url($id, false, 'edit_standalone_page', 'flexi_standalone_settings');
                                $link = add_query_arg('manage', 'standalone', $link);
                                $link = add_query_arg('id', $id, $link);
                                $button_label = flexi_get_option('standalone_button_label', 'flexi_standalone_settings', 'Add Sub-Gallery');
                                // echo "<hr><a class='fl-button' href='" . $link . "'>" . $button_label . "</a><br><br>";
                                echo '<hr><a href=' . esc_url($link) . ' class="fl-button fl-is-medium">
                <span class="fl-icon"><i class="fas fa-folder-plus"></i></span>
                <span>' . esc_attr($button_label) . '</span></a>';
                            }
                        } else {

                            // Update sub-gallery
                            ?>
    <div id='flexi_ajax_refresh_loader' style='display: none;'>
        <img src="<?php echo esc_url(FLEXI_PLUGIN_URL . '/public/images/loading.gif'); ?>">
    </div>
    <div id="flexi_ajax_refresh_content">
        <div id="flexi_thumb_image"> <?php flexi_standalone_gallery($id, 'thumbnail', 75, 75, true); ?>
        </div>
    </div>

    <?php
                            $nonce = wp_create_nonce('flexi_ajax_refresh');
                            echo '<a href="#" class="flexi_css_button" id="flexi_ajax_refresh" data-nonce="' . $nonce . '" data-id="' . esc_attr($id) . '" data-method_name="standalone" data-param1="" data-param2="" data-param3="" title="Refresh"></a>';
                            ?>

    <hr>
    <form id="flexi-request-form-update-primary" class="flexi_ajax_update_image flexi_form_style" method="post"
        enctype="multipart/form-data" action="<?php echo admin_url("/admin-ajax.php"); ?>">


        <input type="file" name="user-submitted-image[]" accept="image/*" value="" id="file" class="" required="">


        <?php
                                wp_nonce_field('flexi-nonce', 'flexi-nonce', false);
                                echo '<input type="hidden" name="flexi_id" value="' . esc_attr($id) . '">';
                                echo '<input type="hidden" name="upload_type" value="flexi">';
                                echo '<input type="hidden" name="type" value="standalone">';
                                echo '<input type="hidden" name="action" value="flexi_ajax_update_image">';
                                ?>
        <input type="submit" name="submit" value="Add image" id="submit" class="">
    </form>

    <?php
                            $link = flexi_get_button_url($id, false, 'edit_standalone_page', 'flexi_standalone_settings');
                            $link = add_query_arg('manage', 'media', $link);
                            $link = add_query_arg('id', $id, $link);

                            ?>
    <br>

    <a class="fl-button" href="<?php echo esc_url($link); ?>"><?php echo __('Go back', 'flexi'); ?></a>


    <?php
                        }
                        ?>
</div>



<?php
                } else {
                    $popup = flexi_get_option('lightbox_switch', 'flexi_detail_settings', 1);
                    if ('1' == $popup) {
                        $popup = flexi_get_option('popup_style', 'flexi_detail_settings', 'on');
                    }
                    $data = flexi_image_data('thumbnail', $flexi_post->ID, $popup);
                    echo '<div class="' . $data['popup'] . ' flexi_effect">';
                    echo '<a class="" ' . $data['extra'] . ' href="' . esc_url($data['url']) . '" data-caption="' . sanitize_title($data['title']) . '" data-src="' . esc_url($data['src']) . '" border="0">';
                    echo '<div class="flexi-image-wrapper-thumb"><img src="' . esc_url(flexi_image_src('thumbnail', $flexi_post)) . '"></div></a></div>';

                    if (isset($params['edit'])) {
                        if ($params['edit'] == 'true') {
                            // Update primary image link below image at edit page
                            $link = flexi_get_button_url($id, false, 'edit_standalone_page', 'flexi_standalone_settings');
                            $link = add_query_arg('manage', 'media', $link);
                            $link = add_query_arg('id', $id, $link);
                            // echo '<a href="' . esc_url($link) . '" class="flexi_css_button"><span class="flexi_css_button-icon"></span><span class="flexi_css_button-text">' . __("Manage Media", "flexi") . '</span></a>';
                            echo '<a href="' . esc_url($link) . '" class="fl-button">
              <span class="fl-icon"><i class="fas fa-grip-horizontal"></i></span>
              <span>' . __('Manage Media..', 'flexi') . '</span>
            </a>';
                        }
                    }

                ?>

<script>
jQuery('[data-fancybox-trigger').fancybox({
    selector: '.flexi_show_popup_on a:visible',
    thumbs: {
        autoStart: true
    },
    protect: true,

});
</script>
<?php
                }
            } else {

                ?>

<div class="fl-columns fl-is-centered fl-is-vcentered">
    <div class="fl-column fl-is-narrow">
        <div class="flexi-image-wrapper_large"><img id="flexi_large_image"
                src="<?php echo wp_kses_post(flexi_image_src('large', $flexi_post)); ?>"></div>
    </div>
</div>

<div class="fl-columns fl-is-centered fl-is-vcentered">
    <div class="fl-column fl-is-narrow">
        <?php
                        echo flexi_standalone_gallery($id, 'thumbnail', 75, 75);
                        ?>
    </div>
</div>
<?php
            }
        } else {
            echo '<div id="flexi_no_record" class="flexi_alert-box flexi_error">' . __('Wrong ID', 'flexi') . '</div>';
        }

        $put = ob_get_clean();
        return $put;
    }
}
$standalone = new Flexi_Standalone_Gallery();