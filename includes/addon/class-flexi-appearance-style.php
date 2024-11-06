<?php

/**
 * Gallery appearance settings
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes/addon
 */
class Flexi_Addon_Appearance_Style
{
	private $help = ' <a style="text-decoration: none;" href="https://odude.com/flexi/docs/flexi-gallery/tutorial/appearance-css-style/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';

	public function __construct()
	{

		add_filter('flexi_settings_sections', array($this, 'add_section'));
		add_filter('flexi_settings_fields', array($this, 'add_fields'));
	}

	// add_filter flexi_settings_tabs
	public function add_tabs($new)
	{
		$tabs = array();
		$new  = array_merge($tabs, $new);
		return $new;
	}

	// Add Section title
	public function add_section($new)
	{

		$sections = array(
			array(
				'id'          => 'flexi_app_style_settings',
				'title'       => __('Appearance CSS Style', 'flexi'),
				'description' => __('Change colors, fonts of flexi elements using your own css style classes.<br>Form colors can abe achieved by using class attribute in shortcode.', 'flexi') . ' ' . $this->help,
				'tab'         => 'general',
			),
		);
		$new      = array_merge($new, $sections);

		return $new;
	}

	// Add section fields
	public function add_fields($new)
	{

		$fields = array(
			'flexi_app_style_settings' => array(
				array(
					'name'              => 'flexi_style_text_color',
					'label'             => __('Text color', 'flexi'),
					'description'       => __('fl-has-text-black fl-has-text-danger fl-has-text-success', 'flexi'),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_key',
				),
				array(
					'name'              => 'flexi_style_base_color',
					'label'             => __('Base/container color', 'flexi'),
					'description'       => __('fl-has-background-black fl-has-background-white', 'flexi'),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_key',
				),

				array(
					'name'        => 'flexi_style_heading',
					'type'        => 'text',
					'label'       => __('Gallery title heading', 'flexi'),
					'description' => 'fl-is-4 fl-mb-1 fl-has-text-success',
				),
				array(
					'name'        => 'flexi_style_tag',
					'type'        => 'text',
					'label'       => __('Gallery filter tag style', 'flexi'),
					'description' => 'fl-is-medium',
				),
				array(
					'name'        => 'flexi_style_button',
					'type'        => 'text',
					'label'       => __('Flexi regular buttons', 'flexi'),
					'description' => 'fl-button fl-is-small fl-is-info',
				),

				array(
					'name'        => 'flexi_style_icon_grid',
					'type'        => 'text',
					'label'       => __('Icon grid buttons', 'flexi'),
					'description' => 'fl-is-small flexi_css_button',
				),

				array(
					'name'        => 'flexi_style_common_toolbar',
					'type'        => 'text',
					'label'       => __('Common toolbar buttons', 'flexi'),
					'description' => 'fl-is-light',
				),

			),
		);
		$new = array_merge($new, $fields);

		return $new;
	}
}

// Ultimate Member: Setting at Flexi & Tab at profile page
$appstyle = new Flexi_Addon_Appearance_Style();