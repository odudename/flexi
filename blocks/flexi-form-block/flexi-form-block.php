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


function render_flexi_form_block($attributes)
{

	// Build the shortcode with all attributes
	$shortcode = 'flexi-fffffff';
	$shortcode .= '---';

	// Output the shortcode with do_shortcode to render it properly
	ob_start();
	//echo $shortcode . "<hr>";

	// Display an admin preview if in REST context
	if (defined('REST_REQUEST') && REST_REQUEST) {
		echo "<small><div style='clear:both;border: 1px solid #999; background: #eee'>";
		echo "<ul><li>Save & view frontend for actual output</li>";
		echo "<li>This page only displays the shortcode which will be rendered.</li>";
		echo "<li>Some settings may not work on specific layouts.</li></ul>";
		//	echo '[' . esc_html($shortcode) . ']</div></small>';
	} else {
		//	echo do_shortcode('[' . $shortcode . ']');
		echo "tet";
	}

	return ob_get_clean();
}