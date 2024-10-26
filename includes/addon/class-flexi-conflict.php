<?php
/**
 * Settings to turn off some of the extension if same library is loaded twice by other plugins
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes/addon
 */
class Flexi_Addon_Conflict {
	private $help = ' <a style="text-decoration: none;" href="https://odude.com/docs/flexi-gallery/troubleshooting/conflict-fixes/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';

	public function __construct() {

		add_filter( 'flexi_settings_sections', array( $this, 'add_section' ) );
		add_filter( 'flexi_settings_fields', array( $this, 'add_extension' ) );
		add_filter( 'flexi_settings_fields', array( $this, 'add_fields' ) );
	}

	// add_filter flexi_settings_tabs
	public function add_tabs( $new ) {
		$tabs = array();
		$new  = array_merge( $tabs, $new );
		return $new;
	}

	// Add Section title & description
	public function add_section( $new ) {
		$enable_addon = flexi_get_option( 'enable_conflict_fixes', 'flexi_extension', 0 );
		if ( '1' == $enable_addon ) {
			$sections = array(
				array(
					'id'          => 'flexi_conflict_settings',
					'title'       => __( 'Conflict & Fixes', 'flexi' ),
					'description' => __( 'Try to fix the conflicts occurred with other plugins & theme.', 'flexi' ) . ' ' . $this->help,
					'tab'         => 'general',
				),
			);
			$new      = array_merge( $new, $sections );
		}
		return $new;
	}

	// Add enable/disable option at extension tab
	public function add_extension( $new ) {
		$enable_addon = flexi_get_option( 'enable_conflict_fixes', 'flexi_extension', 0 );
		if ( '1' == $enable_addon ) {

			$description = ' <a style="text-decoration: none;" href="' . admin_url( 'admin.php?page=flexi_settings&tab=general&section=flexi_conflict_settings' ) . '"><span class="dashicons dashicons-admin-tools"></span></a>';
		} else {
			$description = '';
		}

		$fields = array(
			'flexi_extension' => array(
				array(
					'name'              => 'enable_conflict_fixes',
					'label'             => __( 'Conflict & Fixes', 'flexi' ),
					'description'       => __( 'Try to fix the conflicts occurred with other plugins & theme.', 'flexi' ) . ' ' . $this->help . ' ' . $description,
					'type'              => 'checkbox',
					'sanitize_callback' => 'intval',

				),
			),
		);
		$new = array_merge_recursive( $new, $fields );

		return $new;
	}

	// Add section fields
	public function add_fields( $new ) {
		$enable_addon = flexi_get_option( 'enable_conflict_fixes', 'flexi_extension', 0 );
		if ( '1' == $enable_addon ) {
			$fields = array(
				'flexi_conflict_settings' => array(

					array(
						'name'              => 'conflict_disable_fancybox',
						'label'             => __( 'Disable FancyBox Lightbox', 'flexi' ),
						'description'       => __( 'Disable it, <br>if fancyBox v3.5.7 is used by other plugins & theme. So that, only one instance of it is available.<br>If you are not using lightbox at all.', 'flexi' ),
						'type'              => 'checkbox',
						'sanitize_callback' => 'intval',
					),
					array(
						'name'              => 'conflict_disable_godude',
						'label'             => __( 'Disable godude Lightbox', 'flexi' ),
						'description'       => __( 'Disable it, <br>if godude.js is used by other plugins & theme. So that, only one instance of it is available.', 'flexi' ),
						'type'              => 'checkbox',
						'sanitize_callback' => 'intval',
					),
					array(
						'name'              => 'conflict_disable_fontawesome',
						'label'             => __( 'Disable Font Awesome Free 5.3.1', 'flexi' ),
						'description'       => __( 'Disable it, <br>if fontawesome.js is used by other plugins & theme.', 'flexi' ) . ' https://fontawesome.com/',
						'type'              => 'checkbox',
						'sanitize_callback' => 'intval',
					),

				),
			);
			$new = array_merge( $new, $fields );
		}
		return $new;
	}
}

// Ultimate Member: Setting at Flexi & Tab at profile page
$conflict = new Flexi_Addon_Conflict();
