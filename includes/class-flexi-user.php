<?php
/**
 * Handle Flexi memeber or users
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */

class Flexi_User {
	public function __construct() {
		//Add custom query vars
		add_filter('query_vars', array($this, 'add_query_vars_filter'));
	}

	public function add_query_vars_filter($vars) {
		$vars[] = "flexi_user";
		return $vars;
	}

	public function flexi_add_user_profile_icon($icon) {
		global $post;
		$link = get_permalink(flexi_get_option('primary_page', 'flexi_image_layout_settings', 0));
		$author = get_user_by('id', get_the_author_meta('ID'));
		if ($author) {
			$link = add_query_arg("flexi_user", $author->user_login, $link);

			$extra_icon = array();
			$user_flexi_icon = flexi_get_option('user_flexi_icon', 'flexi_icon_settings', 1);
			$my_gallery = flexi_get_option('my_gallery', 'flexi_user_dashboard_settings', 0);
			$current_page_id = get_queried_object_id();

			//Hide profile icon in usre dashboard page
			if ("1" == $user_flexi_icon && $my_gallery != $current_page_id) {
				$extra_icon = array(
					array("far fa-user", __('Profile', 'flexi'), $link, '#', $post->ID, 'fl-is-small flexi_css_button'),

				);
			}
			// combine the two arrays
			if (is_array($extra_icon) && is_array($icon)) {
				$icon = array_merge($extra_icon, $icon);
			}
		}

		return $icon;
	}
}
