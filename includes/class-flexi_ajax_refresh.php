<?php

/**
 * Add more post on click on Load More button
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */
class flexi_ajax_refresh
{

    public function __construct()
    {
        add_action('wp_ajax_flexi_ajax_refresh', array($this, 'flexi_ajax_refresh'));
        add_action('wp_ajax_nopriv_flexi_ajax_refresh', array($this, 'flexi_ajax_refresh'));
    }

    public function flexi_ajax_refresh()
    {
        $id = sanitize_text_field($_REQUEST['id']);
        $param1 = sanitize_text_field($_REQUEST['param1']);
        $param2 = sanitize_text_field($_REQUEST['param2']);
        $param3 = sanitize_text_field($_REQUEST['param3']);
        $method_name = sanitize_text_field($_REQUEST['method_name']);

        $response = array(
            'error' => false,
            'msg' => 'No Message',
            'count' => '0',
        );

        // ******************** */
        // Run the function as mentioned on $method_name

        $msg = $this->$method_name($id, $param1, $param2, $param3);

        // ******************** */

        $response['msg'] = $msg;
        echo wp_json_encode($response);
        die();
    }
    public function allowed_html($html)
    {
        $allowed_html = array('style' => array());
        return wp_kses($html, $allowed_html);
    }
    public function standalone($id, $param1, $param2, $param3)
    {
        ob_start();
        echo '<div id="flexi_thumb_image">' . flexi_standalone_gallery($id, 'thumbnail', 75, 75, true) . '</div>';
        $put = ob_get_clean();
        return $put;
    }
    public function primary_image($id, $param1, $param2, $param3)
    {
        ob_start();
        $put = ob_get_clean();

        $flexi_post = get_post($id);

        if ($flexi_post && 0 != $id) {
            echo '<a href="' . esc_url(get_permalink($id)) . '" ><img id="flexi_medium_image" src="' . esc_url(flexi_image_src($param1, $flexi_post)) . '"></a>';
        } else {
            return '';
        }
        $put = ob_get_clean();
        return $put;
    }
}

$refresh = new flexi_ajax_refresh();