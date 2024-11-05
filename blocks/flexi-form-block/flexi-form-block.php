<?php

/**
 * Plugin Name:       Flexi Gallery Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       flexi-gallery-block
 *
 * @package CreateBlock
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_flexi_form_block_block_init()
{
	register_block_type(__DIR__ . '/build', array(
		'render_callback' => 'render_flexi_form_block',
	));
}
add_action('init', 'create_block_flexi_form_block_block_init');


function render_flexi_form_block($args)
{

	$shortcode = "";
	// Build the shortcode with all attributes
	if (isset($args['form_class'])) {

		if (isset($args['enable_ajax']) && '1' == $args['enable_ajax']) {
			$enable_ajax = "true";
		} else {
			$enable_ajax = "false";
		}

		if (isset($args['flexi_type']) && 'plain' == $args['flexi_type']) {
			$flexi_type = '';
		} else {
			$flexi_type = 'type="' . $args['flexi_type'] . '"';
		}

		$shortcode .= '[flexi-form class="' . $args['form_class'] . '" title="' . $args['form_title'] . '" name="' . sanitize_title_with_dashes($args['form_title']) . '" ajax="' . $enable_ajax . '" ' . $flexi_type . ']';

		$shortcode .= '[flexi-form-tag type="post_title" class="fl-input" title="' . $args['title_label'] . '" value="" placeholder="' . $args['title_placeholder'] . '" required="true"]';

		if (isset($args['enable_category']) && '1' == $args['enable_category']) {
			$shortcode .= '[flexi-form-tag type="category" title="' . $args['category_label'] . '" id="' . $args['cat'] . '"]';
		}

		if (isset($args['enable_tag']) && '1' == $args['enable_tag']) {
			$shortcode .= '[flexi-form-tag type="tag" title="' . $args['tag_label'] . '"]';
		}

		if (isset($args['enable_desp']) && '1' == $args['enable_desp']) {
			$shortcode .= '[flexi-form-tag type="article" class="fl-textarea" title="' . $args['desp_label'] . '" placeholder="' . $args['desp_placeholder'] . '"]';
		}

		if (isset($args['enable_file']) && '1' == $args['enable_file']) {
			if (isset($args['enable_bulk_file']) && '1' == $args['enable_bulk_file']) {
				$shortcode .= '[flexi-form-tag type="file_multiple" title="' . $args['file_label'] . '" class="flexi_drag_file" multiple="true" required="true"]';
			} else {
				$shortcode .= '[flexi-form-tag type="file" title="' . $args['file_label'] . '" required="true"]';
			}
		}

		if (isset($args['enable_url']) && '1' == $args['enable_url']) {
			$shortcode .= '[flexi-form-tag type="video_url" title="' . $args['url_label'] . '" value="" placeholder="eg. https://www.youtube.com/watch?v=uqyVWtWFQkY" required="true"]';
		}

		if (isset($args['enable_security']) && '1' == $args['enable_security']) {
			$shortcode .= '[flexi-form-tag type="captcha" title="Security"]';
		}

		$shortcode .= '[flexi-form-tag type="submit" name="flexi_submit_button" value="' . $args['button_label'] . '"]';

		$shortcode .= '[/flexi-form]';
	}

	// Output the shortcode with do_shortcode to render it properly
	ob_start();
	echo do_shortcode(wp_kses_post($shortcode));

	// Display an admin preview if in REST context
	if (defined('REST_REQUEST') && REST_REQUEST) {
		echo "<hr><div style='clear:both;border: 1px solid #999; background: #eee'>";
		echo "<ul><li>Preview is for reference and may not view same.
  <li>Below shortcode generated for this page</ul>";
		echo '' . wp_kses_post($shortcode) . '</div>';
		wp_enqueue_style('flexi_min', plugin_dir_url(__FILE__) . 'css/flexi-public-min.css', array(), FLEXI_VERSION, 'all');
		wp_enqueue_style('flexi', plugin_dir_url(__FILE__) . 'css/flexi-public.css', array(), FLEXI_VERSION, 'all');
	}

	return ob_get_clean();
}