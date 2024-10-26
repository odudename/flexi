<?php

/**
 * Search bar shortcode
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes/search
 */

class Flexi_Gallery_Search
{
    public function __construct()
    {
        add_shortcode('flexi-search', array($this, 'flexi_gallery_search'));
    }

    public function flexi_gallery_search($params)
    {

        $put = "";
        ob_start();

        extract(shortcode_atts(array(
            'placeholder' => __('My search', 'flexi'),
            'label' => 'Search',
            'input_class' => 'fl-input',
            'button_class' => 'fl-button fl-is-info',
            'pattern' => '',
        ), $params));

        // Search
        if (isset($_GET['search'])) {
            $search = sanitize_text_field($_GET['search']);
            // $search = $_GET['search'];
        } else {
            $search = '';
        }

        $search_value = flexi_get_param_value('keyword', $search);
        if (empty($search_value)) {
            $search_value = '';
        }

?>
<div class="fl-column fl-has-text-right">
    <form action="<?php echo flexi_get_button_url('', false, 'primary_page', 'flexi_image_layout_settings'); ?>"
        id="theForm" onkeydown="return event.key != 'Enter';">

        <div class="fl-field fl-is-grouped">
            <p class="fl-control fl-is-expanded">
                <input id="search_value" class="<?php echo esc_attr($input_class); ?>" name="search" type="text"
                    placeholder="<?php echo esc_attr($placeholder); ?>" pattern="<?php echo $pattern; ?>"
                    value="<?php echo $search_value; ?>">
                <input type="hidden" id="search_url"
                    value="<?php echo esc_url(flexi_get_button_url('', false, 'primary_page', 'flexi_image_layout_settings')); ?>">

            </p>
            <p class="fl-control">
                <a id="flexi_search" class="<?php echo esc_attr($button_class); ?>">
                    <?php echo esc_attr($label) ?>
                </a>
            </p>
        </div>

    </form>
</div>
<?php
        $put = ob_get_clean();
        return $put;
    }
}
$gallery_search = new Flexi_Gallery_Search();