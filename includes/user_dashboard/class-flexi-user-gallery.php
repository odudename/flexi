<?php
/**
 * Frontend user dashboard container
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes/dashboard
 */
class Flexi_User_Dashboard_Gallery
{
    public function __construct()
    {
        add_action('flexi_user_dashboard', array($this, 'flexi_user_gallery'));
    }

    public function flexi_user_gallery()
    {

        $layout = flexi_get_option('gallery_layout', 'flexi_user_dashboard_settings', 'portfolio');
        $postsperpage = flexi_get_option('perpage', 'flexi_user_dashboard_settings', 10);
        $column = flexi_get_option('column', 'flexi_user_dashboard_settings', 2);

        if (isset($_GET['tab'])) {
            $tab_arg = sanitize_text_field($_GET['tab']);
        } else {
            $tab_arg = "public";
        }

        if ($tab_arg == "public") {
            $shortcode = '[flexi-gallery user="show_mine" column="' . esc_attr($column) . '" perpage="' . esc_attr($postsperpage) . '" layout="' . esc_attr($layout) . '" popup="off"]';
            echo do_shortcode(wp_kses_post($shortcode));
        } else if ($tab_arg == "private") {
            $shortcode = '[flexi-gallery user="show_mine" column="' . esc_attr($column) . '" perpage="' . esc_attr($postsperpage) . '" layout="' . esc_attr($layout) . '" popup="off" post_status="draft,pending"]';
            echo do_shortcode(wp_kses_post($shortcode));
        } else {
            echo '';
        }
    }
}
$user_dashboard = new Flexi_User_Dashboard_Gallery();