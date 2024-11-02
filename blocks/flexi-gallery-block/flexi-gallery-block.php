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
function create_block_flexi_gallery_block_block_init()
{
	register_block_type(__DIR__ . '/build', array(
		'render_callback' => 'flexi_render_gallery_block',
	));
}
add_action('init', 'create_block_flexi_gallery_block_block_init');


function flexi_render_gallery_block($attributes)
{
	ob_start();
?>
	<div class="custom-gallery" data-columns="<?php echo esc_attr($attributes['columns']); ?>">
		<p>Gallery Block: <?php echo esc_attr($attributes['columns']); ?> columns</p>
	</div>
<?php
	return ob_get_clean();
}
