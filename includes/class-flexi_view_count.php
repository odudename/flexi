<?php

/**
 * Display view count on gallery page
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */
class flexi_view_count
{
	public function __construct()
	{
		// add_action('flexi_module_grid', array($this, 'display_view_count'));
		add_filter('flexi_settings_fields', array($this, 'add_fields'));
		add_filter('flexi_addon_gallery_all', array($this, 'display_view_count'), 9, 4);
		add_filter('flexi_addon_gallery_portfolio', array($this, 'display_view_count'), 9, 4);
	}

	public function display_view_count($container, $evalue = '', $id = '')
	{
		//flexi_log('count page ' . $evalue . ' -- ' . $id);
		$enable = flexi_get_option('evalue_count', 'flexi_image_layout_settings', 1);
		$this->increase_count($id, 'flexi_view_count');
		//If page is detail page
		if ($evalue == '') {
			$evalue .= 'count:on';
		}

		$toggle = flexi_evalue_toggle('count', $evalue);
		//flexi_log("The value of count is: " . 	$toggle . ' for evalue: ' . $evalue);

		if ($enable == 1 && $toggle == "") {
			$extra_icon = array();
			$div = '<div class="fl-button fl-is-small">
        <span class="fl-icon fl-is-small"><i class="far fa-eye"></i></span>
        <span>' . $this->get_view_count($id, 'flexi_view_count') . '</span></div>';
			$extra_icon = array(
				array('fl-field fl-has-addons', $div),

			);

			// combine the two arrays
			if (is_array($extra_icon) && is_array($container)) {
				$container = array_merge($extra_icon, $container);
			}
		}

		return $container;
	}

	// Total number of like & unlike
	public function get_view_count($id, $key)
	{
		$count = get_post_meta($id, $key, true);
		return $count;
	}

	// Increase like
	public function increase_count($post_id, $key)
	{

		$count = (int) get_post_meta($post_id, $key, true);
		$count++;
		update_post_meta($post_id, $key, $count);
	}

	// enable/disable option at Gallery -> Gallery Settings
	public function add_fields($new)
	{

		$fields = array(
			'flexi_image_layout_settings' => array(

				array(
					'name' => 'evalue_count',
					'label' => __('Display view count', 'flexi') . ' (evalue)',
					'description' => __('Counts number of page viewed. Counts only those where count view is shown.', 'flexi'),
					'type' => 'checkbox',
					'sanitize_callback' => 'intval',
				),
			),
		);
		$new = array_merge_recursive($new, $fields);

		return $new;
	}
}

$flexi_view_count = new flexi_view_count();