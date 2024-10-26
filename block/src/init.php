<?php

/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function flexi_block_cgb_block_assets()
{
    // phpcs:ignore
    // Register block styles for both frontend + backend.
    wp_register_style(
        'flexi_block-cgb-style-css', // Handle.
        plugins_url('dist/blocks.style.build.css?ver=' . FLEXI_VERSION, dirname(__FILE__)), // Block style CSS.
        is_admin() ? array('wp-editor') : null, // Dependency to include the CSS after it.
        null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
    );

    // Register block editor script for backend.
    wp_register_script(
        'flexi_block-cgb-block-js', // Handle.
        plugins_url('/dist/blocks.build.js?ver=' . FLEXI_VERSION, dirname(__FILE__)), // Block.build.js: We register the block here. Built with Webpack.
        array('wp-data', 'wp-compose', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components', 'wp-api-fetch'), // Dependencies, defined above.
        null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
        true // Enqueue the script in the footer.
    );

    // Register block editor styles for backend.
    wp_register_style(
        'flexi_block-cgb-block-editor-css', // Handle.
        plugins_url('dist/blocks.editor.build.css?ver=' . FLEXI_VERSION, dirname(__FILE__)), // Block editor CSS.
        array('wp-edit-blocks'), // Dependency to include the CSS after it.
        null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
    );

    // WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
    wp_localize_script(
        'flexi_block-cgb-block-js',
        'cgbGlobal', // Array containing dynamic data for a JS Global.
        [
            'pluginDirPath' => plugin_dir_path(__DIR__),
            'pluginDirUrl'  => plugin_dir_url(__DIR__),
            // Add more data here that you want to access from `cgbGlobal` object.
        ]
    );
}

// Hook: Block assets.
add_action('init', 'flexi_block_cgb_block_assets');

//Flexi own category for guten block

function flexi_block_categories($categories, $post)
{
    /*
if ('post' !== $post->post_type) {
return $categories;
}
 */
    return array_merge(
        $categories,
        array(
            array(
                'slug'  => 'flexi',
                'title' => __('Flexi Plugin', 'flexi'),
                'icon'  => 'playlist-video',
            ),
        )
    );
}
add_filter('block_categories_all', 'flexi_block_categories', 10, 2);
