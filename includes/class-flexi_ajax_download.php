<?php
/**
 * Ajax download button to download images
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */
class flexi_download_post
{

    public function __construct()
    {
        add_filter('wp_kses_allowed_html', array($this, "kses_filter_allowed_html"), 10, 2);
    }

    public function kses_filter_allowed_html($allowed, $context)
    {
        if (is_array($context)) {
            return $allowed;
        }

        if ($context === 'post') {
            $allowed['a']['download'] = true;
            //$allowed['table']['data-*'] = true;
            // ... keep on doing these for each HTML entity you want to allow data- attributes on
        }

        return $allowed;
    }
    public function flexi_ajax_download()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], 'flexi_ajax_download')) {
            exit('No naughty business please');
        }
        $post_id = sanitize_text_field($_REQUEST['post_id']);

        $post_author_id = get_post_field('post_author', $post_id);

        if (get_current_user_id() == $post_author_id) {
            // check the user before proceed to download
            $data = true;
        } else {
            $data = false;
        }

        if (false === $data) {
            $result['type'] = 'error';
        } else {
            $result['type'] = 'success';
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            echo wp_json_encode($result);
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

        die();
    }

    // Used in ajax call, force users to login before any action.
    public function flexi_my_must_login()
    {
        echo __('Login Please !', 'flexi');
        die();
    }

    // Adds download/trash icon in flexi icon container.
    public function flexi_add_icon_grid_download($icon)
    {
        global $post;
        $download_flexi_icon = flexi_get_option('download_flexi_icon', 'flexi_icon_settings', 1);
        $nonce = wp_create_nonce('flexi_ajax_download');
        // $link  = admin_url('admin-ajax.php?action=flexi_ajax_download&post_id=' . $post->ID . '&nonce=' . $nonce);

        $extra_icon = array();

        if (flexi_get_type($post) != 'url') {
            // if (isset($options['show_trash_icon'])) {
            if ('1' == $download_flexi_icon) {
                $url = flexi_file_src($post, $url = true);

                $extra_icon = array(
                    array('far fa-save', __('Download', 'flexi'), $url, $url, $post->ID, 'fl-button fl-is-small flexi_ajax_download flexi_css_button', 'download'),

                );
            }
            // }
        }

        // combine the two arrays
        if (is_array($extra_icon) && is_array($icon)) {
            $icon = array_merge($extra_icon, $icon);
        }

        return $icon;
    }
}