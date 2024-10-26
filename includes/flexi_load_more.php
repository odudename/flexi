<?php
// Load more content ajax call
add_action('wp_ajax_flexi_load_more', 'flexi_load_more');
add_action('wp_ajax_nopriv_flexi_load_more', 'flexi_load_more');

function flexi_load_more()
{
	global $wp_query;
	global $post;
	$paged         = sanitize_text_field($_REQUEST['max_paged']);
	$layout        = sanitize_text_field($_REQUEST['gallery_layout']);
	$popup         = sanitize_text_field($_REQUEST['popup']);
	$album         = sanitize_text_field($_REQUEST['album']);
	$search        = sanitize_text_field($_REQUEST['search']);
	$postsperpage  = sanitize_text_field($_REQUEST['postsperpage']);
	$orderby       = sanitize_text_field($_REQUEST['orderby']);
	$user          = sanitize_text_field($_REQUEST['user']);
	$keyword       = sanitize_text_field($_REQUEST['keyword']);
	$padding       = sanitize_text_field($_REQUEST['padding']);
	$hover_effect  = sanitize_text_field($_REQUEST['hover_effect']);
	$php_field     = sanitize_text_field($_REQUEST['php_field']);
	$hover_caption = sanitize_text_field($_REQUEST['hover_caption']);
	$evalue        = sanitize_text_field($_REQUEST['evalue']);
	$column        = sanitize_text_field($_REQUEST['column']);
	$attach        = sanitize_text_field($_REQUEST['attach']);
	$attach_id     = sanitize_text_field($_REQUEST['attach_id']);
	$filter        = sanitize_text_field($_REQUEST['filter']);
	$post_status   = sanitize_text_field($_REQUEST['post_status']);
	ob_start();

	// A default response holder, which will have data for sending back to our js file
	$response = array(
		'error' => false,
		'msg'   => 'No Message',
		'count' => '0',
	);

	// var_dump($response);

	if (is_user_logged_in()) {

		$current_user = wp_get_current_user();
		$cur_user     = $current_user->user_login;
		if ($cur_user == $user) {
			// $post_status = array('draft', 'publish', 'pending');
		}
	}

	if ('' != $album && '' != $keyword) {
		$relation = 'AND';
	} else {
		$relation = 'OR';
	}

	if ('' != $album || '' != $keyword) {
		$args = array(
			'post_type'      => 'flexi',
			'paged'          => $paged,
			's'              => flexi_get_param_value('keyword', $search),
			'posts_per_page' => $postsperpage,
			'orderby'        => $orderby,
			'post_status'    => explode(',', $post_status),
			'order'          => 'DESC',
			'author'         => $user,
			'tax_query'      => array(
				'relation' => $relation,
				array(
					'taxonomy' => 'flexi_category',
					'field'    => 'slug',
					'terms'    => explode(',', $album),
					// 'terms'    => array( 'mobile', 'sports' ),
					// 'include_children' => 0 //It will not include post of sub categories
				),

				array(
					'taxonomy' => 'flexi_tag',
					'field'    => 'slug',
					'terms'    => explode(',', $keyword),
					// 'terms'    => array( 'mobile', 'sports' ),
				),

			),
		);
	} else {
		$args = array(
			'post_type'      => 'flexi',
			's'              => flexi_get_param_value('keyword', $search),
			'paged'          => $paged,
			'posts_per_page' => $postsperpage,
			'author'         => $user,
			'post_status'    => explode(',', $post_status),
			'orderby'        => $orderby,
			'order'          => 'DESC',

		);
	}

	$args['meta_query'] = array('compare' => 'AND');

	// If filter is used as parameter image,url,video
	if ('' != $filter) {
		$filter_array = array(
			'key'     => 'flexi_type',
			'value'   => $filter,
			'compare' => '=',
		);

		array_push($args['meta_query'], $filter_array);
	}

	// flexi_log($args);
	// flexi_log("-----------------");
	// Add meta query for attach page
	if ('true' == $attach && '' != $attach_id) {

		$attach_array = array(
			'key'     => 'flexi_attach_at',
			'value'   => $attach_id,
			'compare' => '=',
		);

		array_push($args['meta_query'], $attach_array);
	}

	// Query based on Custom fields
	for ($z = 1; $z <= 30; $z++) {
		$param_value = flexi_get_param_value('flexi_field_' . $z, $search);
		// If search used in URL
		if ($param_value != '') {
			$attach_array = array(
				'key'     => 'flexi_field_' . $z,
				'value'   => explode('..', $param_value),
				'compare' => 'IN',
			);

			array_push($args['meta_query'], $attach_array);
		} else {
			if (isset($params['flexi_field_' . $z])) {

				$attach_array = array(
					'key'     => 'flexi_field_' . $z,
					'value'   => explode('..', $params['flexi_field_' . $z]),
					'compare' => 'IN',
				);

				array_push($args['meta_query'], $attach_array);
			}
		}
	}

	$query = new WP_Query($args);

	$put = '';

	// flexi_log($args);
	// echo "----";
	$count = 0;
	while ($query->have_posts()) :
		$query->the_post();
		$tags = flexi_get_taxonomy_raw($post->ID, 'flexi_tag');
		$count++;
		if ('' != $layout) {
			require FLEXI_PLUGIN_DIR . 'public/partials/layout/gallery/' . $layout . '/loop.php';
		}

	endwhile;

	$put = ob_get_clean();
	// $response['msg'] = "hii";
	$response['msg']   = $put;
	$response['count'] = $count;
	echo wp_json_encode($response);
	wp_reset_postdata();
	die();
}