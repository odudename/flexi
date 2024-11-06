<?php

/**
 * FFMPEG library support
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes/addon
 */
class Flexi_Addon_FFMPEG
{

	private $help = ' <a style="text-decoration: none;" href="https://odude.com/flexi/docs/flexi-gallery/information/ffmpeg-video-encoding/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';

	public function __construct()
	{
		add_filter('flexi_settings_sections', array($this, 'add_section'));
		add_filter('flexi_settings_fields', array($this, 'add_extension'));
		add_filter('flexi_settings_fields', array($this, 'add_fields'));
		add_action('flexi_activated', array($this, 'set_value'));
		add_action('flexi_submit_complete', array($this, 'generate_thumbnail'), 10, 1);
	}

	// Add Section tab
	public function add_section($new)
	{

		$enable_addon = flexi_get_option('enable_ffmpeg', 'flexi_extension', 0);
		if ('1' == $enable_addon) {

			function flexi_ffmpeg_report()
			{
				$ffmpegpath = flexi_get_option('ffmpeg_path', 'flexi_ffmpeg_setting', '/usr/local/bin');
				$command    = $ffmpegpath . ' -version';
				$out        = array();
				$msg        = '<code>' . @shell_exec($command) . '</code>';
				return $msg;
				// echo @shell_exec($command);
			}

			$sections = array(
				array(
					'id'          => 'flexi_ffmpeg_setting',
					'title'       => 'FFMPEG ' . __('settings', 'flexi'),
					'description' => $this->help . ' <b><a href="https://ffmpeg.org/">FFMPEG</a></b> PHP ' . __('extension must be installed on your server.<br><b>shell_exec</b> should be enabled by PHP or purchase <a href="https://odude.com/flexi/product/flexi-library-ffmpeg/">FFMPEG- Flexi Library</a><br>This will only get applied to newly submitted video files.<br>Processing time based on video file sizes.<br>Thumbnail are based on media settings, medium size<br>Animated video results poor quality. Install Flexi-PRO for higher resolution.<hr>FFMPEG Library required purchase of flexi-pro<hr>' . flexi_ffmpeg_report() . '<hr>This library requires a working FFMpeg install. You will need both FFMpeg and FFProbe binaries to use it.', 'flexi'),
					'tab'         => 'general',
				),
			);
			$new      = array_merge($new, $sections);
		}
		return $new;
	}

	// add_filter flexi_settings_tabs
	public function add_tabs($new)
	{
		$tabs = array();
		$new  = array_merge($tabs, $new);
		return $new;
	}

	// Add enable/disable option at extension tab
	public function add_extension($new)
	{

		$enable_addon = flexi_get_option('enable_ffmpeg', 'flexi_extension', 0);
		if ('1' == $enable_addon) {

			$description = ' <a style="text-decoration: none;" href="' . admin_url('admin.php?page=flexi_settings&tab=general&section=flexi_ffmpeg_setting') . '"><span class="dashicons dashicons-admin-tools"></span></a>';
		} else {
			$description = '';
		}

		$fields = array(
			'flexi_extension' => array(
				array(
					'name'              => 'enable_ffmpeg',
					'label'             => __('Video', 'flexi') . ' FFMPEG ' . __('encoding', 'flexi'),
					'description'       => __('This will generate thumbnail for video files like mp4,3gp,mov. Your server must have ffmpeg installed.', 'flexi') . ' ' . $this->help . ' ' . $description,
					'type'              => 'checkbox',
					'sanitize_callback' => 'intval',

				),
			),
		);
		$new = array_merge_recursive($new, $fields);

		return $new;
	}

	// Add section fields
	public function add_fields($new)
	{
		$enable_addon = flexi_get_option('enable_ffmpeg', 'flexi_extension', 0);
		if ('1' == $enable_addon) {
			$fields = array(
				'flexi_ffmpeg_setting' => array(
					array(
						'name'              => 'ffmpeg_path',
						'label'             => __('FFMPEG folder path', 'flexi'),
						'description'       => __("This should be the folder where FFMPEG installed on your server. Eg. /usr/local/bin or /usr/bin/ffmpeg or E:\\ffmpeg\\bin\\ffmpeg.exe", 'flexi'),
						'type'              => 'text',
						'size'              => 'large',
						'sanitize_callback' => 'sanitize_text_field',
					),

					array(
						'name'              => 'ffmpeg_processor',
						'label'             => __('FFMPEG Processor', 'flexi'),
						'description'       => '',
						'type'              => 'radio',
						'options'           => array(
							'exec'    => __('shell_exec command', 'flexi'),
							'library' => __('FFMPEG - Flexi Library', 'flexi'),
						),
						'sanitize_callback' => 'sanitize_key',
					),

					array(
						'name'              => 'video_thumbnail',
						'label'             => __('Video Thumbnail', 'flexi'),
						'description'       => '',
						'type'              => 'radio',
						'options'           => array(
							'static'   => __('Static image', 'flexi'),
							'animated' => __('Animated 3 second image', 'flexi'),
							'none'     => __('Dynamic file icon', 'flexi'),
						),
						'sanitize_callback' => 'sanitize_key',
					),

				),
			);
			$new = array_merge_recursive($new, $fields);
		}
		return $new;
	}

	public function set_value()
	{
		// Set default location of elements
		flexi_get_option('ffmpeg_path', 'flexi_ffmpeg_setting', '/usr/local/bin');
		flexi_get_option('video_thumbnail', 'flexi_ffmpeg_setting', 'animated');
	}

	// Generate thumbnail for video
	public function generate_thumbnail($post_id)
	{
		$enable_addon = flexi_get_option('enable_ffmpeg', 'flexi_extension', 0);
		if ('1' == $enable_addon) {

			$flexi_post       = get_post($post_id);
			$info             = new Flexi_Post_Info();
			$type             = $info->post_meta($post_id, 'flexi_type', '');
			$ffmpeg_processor = flexi_get_option('ffmpeg_processor', 'flexi_ffmpeg_setting', 'exec');

			// Execute only if it is video media type. Not valid for youtube/vimeo urls
			if ('video' == $type) {

				$video = $info->media_path($post_id, false);
				$this->flexi_ffmpeg($video, $post_id, $ffmpeg_processor);
			}
		}
		return true;
	}

	// FFMPEG generate thumbnails
	public function flexi_ffmpeg($video, $post_id, $ffmpeg_processor)
	{
		$flexi_post = get_post($post_id);

		if (!function_exists('wp_generate_attachment_metadata')) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		$upload_dir_paths = wp_upload_dir();
		$ffmpegpath       = flexi_get_option('ffmpeg_path', 'flexi_ffmpeg_setting', '/usr/local/bin');
		$palette          = $upload_dir_paths['path'] . '/' . $post_id . '_palette.png';
		$image_name       = $post_id . '_thumbnail.gif';
		$input            = $video;
		$output           = $upload_dir_paths['path'] . '/' . $image_name; // Create image file name

		if ($this->make_jpg($input, $output, $ffmpegpath, $palette, $ffmpeg_processor)) {

			$image_data       = file_get_contents($output); // Get image data
			$unique_file_name = wp_unique_filename($upload_dir_paths['path'], $image_name); // Generate unique name

			// Create the image  file on the server
			file_put_contents($output, $image_data);

			// Check image file type
			$wp_filetype = wp_check_filetype($output, null);

			// Set attachment data
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name($image_name),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			// Create the attachment
			$attach_id = wp_insert_attachment($attachment, $output, $post_id);
			// Define attachment metadata
			$attach_data = wp_generate_attachment_metadata($attach_id, $output);

			// Assign metadata to attachment
			wp_update_attachment_metadata($attach_id, $attach_data);

			add_post_meta($post_id, 'flexi_image_id', $attach_id);
			add_post_meta($post_id, 'flexi_image', wp_get_attachment_url($attach_id));

			// echo 'success';

		} else {
			// echo 'bah!';
		}
	}

	public function make_jpg($input, $output, $ffmpegpath, $palette, $ffmpeg_processor, $fromdurasec = '05')
	{

		if (!file_exists($input)) {
			return false;
		}

		$m_width = flexi_get_option('m_width', 'flexi_media_settings', 300);

		$video_thumbnail = flexi_get_option('video_thumbnail', 'flexi_ffmpeg_setting', 'animated');
		if ('static' == $video_thumbnail) {
			if ('library' == $ffmpeg_processor) {
				if (function_exists('flexi_ffmpeg_video_static')) {
					flexi_ffmpeg_video_static($ffmpegpath, $input, $palette, $m_width, $output, $ffmpeg_processor);
				}
			} else {

				$command = $ffmpegpath . ' -i ' . $input . ' -an -ss 00:00:' . $fromdurasec . ' -r 1 -vframes 1 -f mjpeg -y -vf "scale=' . $m_width . ':-1" ' . $output;
				flexi_log($command);
				@shell_exec($command);
			}
		} elseif ('animated' == $video_thumbnail) {

			if (is_flexi_pro()) {
				if (function_exists('flexi_ffmpeg_video_gif')) {
					flexi_ffmpeg_video_gif($ffmpegpath, $input, $palette, $m_width, $output, $ffmpeg_processor);
				}
			} else {
				$command = "$ffmpegpath -i $input -ss 00:00:03 -t 00:00:08 -async 1 -vf fps=5,scale=$m_width:-1,smartblur=ls=-0.5 $output";
				@shell_exec($command);
			}
		} else {
			// It will set default icons
		}


		// image size based on media setting
		// $command = "$ffmpegpath -i $input -ss 00:00:03 -t 00:00:08 -async 1 -s $size $output";

		// Low quality
		// $command = "$ffmpegpath -i $input -ss 00:00:03 -t 00:00:08 -async 1 -vf fps=5,scale=$m_width:-1,smartblur=ls=-0.5 $output";

		if (!file_exists($output)) {
			return false;
		}

		if (filesize($output) == 0) {
			return false;
		}

		return true;
	}
}

// FFMPEG settings
$conflict = new Flexi_Addon_FFMPEG();