<?php
/**
 * Theme class
 * 
 */
namespace HomeViet;

class Theme {
	use \HomeViet\Singleton;

	protected function __construct() {
		add_action('after_switch_theme', [$this, 'theme_activation']);
		add_action('switch_theme', [$this, 'theme_deactivation']);
		add_action('fw_init', [$this, 'setup_theme'] );
		add_action('fw_init', [$this, 'includes'] );
		add_action('fw_init', [$this, 'hooks'] );
	}

	public static function setting() {
		return \HomeViet\Setting::get_instance();
	}

	public function hooks() {
		//$this->hooks_authentication();
		$this->hooks_custom_types();
		$this->hooks_template();
		$this->hooks_assets();
		$this->hooks_shortcode();
		$this->hooks_head();
		$this->hooks_header();
		$this->hooks_footer();
		//$this->hooks_widget();
		$this->hooks_ajax();
		$this->hooks_background_process();

	}

	private function hooks_background_process() {
		//add_action( 'delete_texture_images_process', ['\HomeViet\Background_Process', 'delete_texture_images_process'] );
	}

	private function hooks_ajax() {

		//add_action( 'wp_ajax_texture_detail', ['\HomeViet\Ajax', 'texture_detail'] );
		add_action( 'wp_ajax_texture_upload', ['\HomeViet\Ajax', 'texture_upload'] );
		add_action( 'wp_ajax_occupation_selection_save', ['\HomeViet\Ajax', 'occupation_selection_save'] );
		add_action( 'wp_ajax_get_edit_contractor_form', ['\HomeViet\Ajax', 'get_edit_contractor_form'] );
		add_action( 'wp_ajax_update_contractor', ['\HomeViet\Ajax', 'update_contractor'] );
		add_action( 'wp_ajax_get_contractor_info', ['\HomeViet\Ajax', 'get_contractor_info'] );
		add_action( 'wp_ajax_update_contractor_sort', ['\HomeViet\Ajax', 'update_contractor_sort'] );
		add_action( 'wp_ajax_sort_contractors', ['\HomeViet\Ajax', 'sort_contractors'] );
		add_action( 'wp_ajax_add_to_project', ['\HomeViet\Ajax', 'add_to_project'] );
		add_action( 'wp_ajax_remove_from_project', ['\HomeViet\Ajax', 'remove_from_project'] );

		add_action( 'wp_ajax_add_to_local_project', ['\HomeViet\Ajax', 'add_to_local_project'] );
		add_action( 'wp_ajax_remove_from_local_project', ['\HomeViet\Ajax', 'remove_from_local_project'] );

		add_action( 'wp_ajax_texture_download', ['\HomeViet\Ajax', 'texture_download'] );
		add_action( 'wp_ajax_nopriv_texture_download', ['\HomeViet\Ajax', 'texture_download'] );
	}

	private function hooks_shortcode() {
		//add_shortcode( 'user_texture_rating_block', ['\HomeViet\Shortcode', 'user_texture_rating_block'] );
		add_shortcode( 'user_info_block', ['\HomeViet\Shortcode', 'user_info_block'] );
		add_shortcode( 'texture_upload_button', ['\HomeViet\Shortcode', 'texture_upload_button'] );
		add_shortcode( 'texture_edit_button', ['\HomeViet\Shortcode', 'texture_edit_button'] );
	}

	private function hooks_assets() {
		//add_action('wp_enqueue_scripts', ['\HomeViet\Assets', 'enqueue_page_builder_scripts_for_tax'], 10);
		add_action('wp_enqueue_scripts', ['\HomeViet\Assets', 'enqueue_styles'], 50);
		add_action('wp_enqueue_scripts', ['\HomeViet\Assets', 'enqueue_scripts'], 50);
	}

	private function hooks_head() {
		add_action('wp_head', ['\HomeViet\Template_Tags', 'head_scripts'], 50);
		//add_action('wp_head', ['\HomeViet\Template_Tags', 'head_youtube_scripts'], 10);
		add_action('wp_head', ['\HomeViet\Template_Tags', 'noindex'], 10);
		add_filter('pre_get_document_title', ['\HomeViet\Template_Tags', 'get_single_texture_document_title'], 10);
	}

	private function hooks_header() {
		add_action('wp_body_open', ['\HomeViet\Template_Tags', 'body_open_custom_code'], 5);
		add_action('template_redirect', ['\HomeViet\Template_Tags', 'header_html'], 10 );
	}

	private function hooks_footer() {
		add_action('template_redirect', ['\HomeViet\Template_Tags', 'footer_html'], 10);
		add_action('wp_footer', ['\HomeViet\Template_Tags', 'modals'], 50);
		add_action('wp_footer', ['\HomeViet\Template_Tags', 'footer_custom_scripts'], 100);
		
	}

	private function hooks_widget() {
		add_action('widgets_init', ['\HomeViet\Widgets', 'register_sidebars']);
	}

	private function hooks_template() {
		//add_action('get_template_part_parts/contractors', ['\HomeViet\Template_Tags', 'extract_template_args'], 10, 3);
	}

	private function hooks_custom_types() {
		if ( is_admin() ) {
			add_action( 'admin_menu', ['\HomeViet\Custom_Types', '_admin_action_rename_post_menu' ], 99 );
			add_filter( 'parent_file', ['\HomeViet\Custom_Types', 'admin_menu_highlight'] );
		}
		add_action( 'init', ['\HomeViet\Custom_Types', '_theme_action_change_default'], 0 );
		add_action( 'init', ['\HomeViet\Custom_Types', '_theme_action_register_taxonomy'], 10 );
		add_action( 'init', ['\HomeViet\Custom_Types', '_theme_action_register_custom_type'], 10 );

		//add_filter( 'init', ['\HomeViet\Custom_Types', 'rewrite_texture_url'], 10 );
		//add_filter( 'post_type_link', ['\HomeViet\Custom_Types', 'rewrite_texture_link'], 10, 2 );

		//add_filter( 'fw_ext_page_builder_supported_post_types', ['\HomeViet\Custom_Types', '_theme_filter_builder_supported_custom_type']);

		add_action( 'pre_get_posts', ['\HomeViet\Custom_Types', 'query_custom_postype'] );
		
		//add_action( 'init', ['\HomeViet\Custom_Types', '_theme_action_register_post_status'], 10 );

		add_action( 'the_post', ['\HomeViet\Custom_Types', '_setup_loop_custom_type'], 10, 2 );

		//add_action( 'terms_clauses', ['\HomeViet\Custom_Types', '_setup_term_default_sort'], 10, 3 );

		add_filter( 'quick_edit_show_taxonomy', ['\HomeViet\Custom_Types', 'hide_tags_from_quick_edit'], 10, 3 );

		add_filter( 'list_cats', ['\HomeViet\Custom_Types', 'list_projects'], 10, 2 );

		add_filter( 'get_the_archive_title', ['\HomeViet\Custom_Types', 'get_the_archive_title'], 10, 2 );

	}

	private function hooks_authentication() {
		// trước khi query tài nguyên để hiển thị thì kiểm tra yêu cầu người dùng đăng nhập trước
		add_action( 'parse_request', ['\HomeViet\Authentication', 'require_login_use'] );

		//add_filter( 'rest_authentication_errors', ['\HomeViet\Authentication', 'rest_authentication_require'] );

		//add_filter( 'template_include', ['\HomeViet\Authentication', 'authentication_template_include'] );

		//add_filter('the_password_form', ['\HomeViet\Authentication', 'the_password_form'], 10, 2);
	}

	public function includes() {
		include_once THEME_DIR.'/inc/class-authentication.php';
		include_once THEME_DIR.'/inc/class-background-process.php';
		include_once THEME_DIR.'/inc/class-custom-types.php';

		if(defined('CUSTOMTAXORDER_VER')) {
			include_once THEME_DIR.'/inc/custom-taxonomy-order-ne/class-custom-taxonomy-order-ne.php';
		}

		if(is_admin()) {
			include_once THEME_DIR.'/inc/admin/class-admin.php';
		}

		include_once THEME_DIR.'/inc/class-setting.php';
		include_once THEME_DIR.'/inc/class-assets.php';
		include_once THEME_DIR.'/inc/class-post.php';
		include_once THEME_DIR.'/inc/class-texture.php';
		include_once THEME_DIR.'/inc/class-contractor.php';
		include_once THEME_DIR.'/inc/class-term.php';
		include_once THEME_DIR.'/inc/class-project.php';
		include_once THEME_DIR.'/inc/class-occupation.php';
		include_once THEME_DIR.'/inc/class-design_cat.php';
		include_once THEME_DIR.'/inc/class-shortcode.php';
		include_once THEME_DIR.'/inc/class-template-tags.php';
		include_once THEME_DIR.'/inc/class-walker-primary-menu.php';
		include_once THEME_DIR.'/inc/class-walker-secondary-menu.php';
		//include_once THEME_DIR.'/inc/class-widgets.php';
		include_once THEME_DIR.'/inc/class-ajax.php';

		
		$GLOBALS['theme_setting'] = \HomeViet\Setting::get_instance();
	}

	public function setup_theme() {
		show_admin_bar( false );
		// không dùng block editor
		add_filter('use_widgets_block_editor', '__return_false');
		add_filter('use_block_editor_for_post_type', '__return_false', 10);

		add_filter('wp_img_tag_add_auto_sizes', '__return_false');
		add_filter('image_size_names_choose', [$this, 'image_sizes_choose']);

		add_theme_support( 'title-tag' );
		
		add_theme_support( 'post-thumbnails' );

		remove_image_size( '1536x1536' );
		remove_image_size( '2048x2048' );

		register_nav_menus(
			array(
				'primary' => 'Menu chính',
				'secondary' => 'Menu phụ',
			)
		);

		add_theme_support('custom-background');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		// Add support for Block Styles.
		//add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		add_filter('get_the_archive_title_prefix', '__return_empty_string');

		remove_action( 'template_redirect', 'redirect_canonical' );
		remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10, 0 );
		remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		//remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );
		remove_action( 'do_feed_rdf', 'do_feed_rdf', 10, 0 );
		remove_action( 'do_feed_rss', 'do_feed_rss', 10, 0 );
		remove_action( 'do_feed_rss2', 'do_feed_rss2', 10, 1 );
		remove_action( 'do_feed_atom', 'do_feed_atom', 10, 1 );
		remove_action( 'do_pings', 'do_all_pings', 10, 0 );
		remove_action( 'do_all_pings', 'do_all_pingbacks', 10, 0 );
		remove_action( 'do_all_pings', 'do_all_enclosures', 10, 0 );
		remove_action( 'do_all_pings', 'do_all_trackbacks', 10, 0 );
		remove_action( 'do_all_pings', 'generic_ping', 10, 0 );
		
		// Redirect old slugs.
		remove_action( 'template_redirect', 'wp_old_slug_redirect' );

		// Embeds.
		remove_action( 'plugins_loaded', 'wp_maybe_load_embeds', 0 );
		
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4 );

		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' ); // Back-compat for sites disabling oEmbed host JS by removing action.
		remove_filter( 'embed_oembed_html', 'wp_maybe_enqueue_oembed_host_js' );

		remove_action( 'embed_head', 'enqueue_embed_scripts', 1 );
		remove_action( 'embed_head', 'print_emoji_detection_script' );
		remove_action( 'embed_head', 'wp_enqueue_embed_styles', 9 );
		remove_action( 'embed_head', 'print_embed_styles' ); // Retained for backwards-compatibility. Unhooked by wp_enqueue_embed_styles().
		remove_action( 'embed_head', 'wp_print_head_scripts', 20 );
		remove_action( 'embed_head', 'wp_print_styles', 20 );
		remove_action( 'embed_head', 'wp_robots' );
		remove_action( 'embed_head', 'rel_canonical' );
		remove_action( 'embed_head', 'locale_stylesheet', 30 );
		remove_action( 'enqueue_embed_scripts', 'wp_enqueue_emoji_styles' );

		remove_action( 'embed_content_meta', 'print_embed_comments_button' );
		remove_action( 'embed_content_meta', 'print_embed_sharing_button' );

		remove_action( 'embed_footer', 'print_embed_sharing_dialog' );
		remove_action( 'embed_footer', 'print_embed_scripts' );
		remove_action( 'embed_footer', 'wp_print_footer_scripts', 20 );

		remove_filter( 'excerpt_more', 'wp_embed_excerpt_more', 20 );
		remove_filter( 'the_excerpt_embed', 'wptexturize' );
		remove_filter( 'the_excerpt_embed', 'convert_chars' );
		remove_filter( 'the_excerpt_embed', 'wpautop' );
		remove_filter( 'the_excerpt_embed', 'shortcode_unautop' );
		remove_filter( 'the_excerpt_embed', 'wp_embed_excerpt_attachment' );

		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_iframe_title_attribute', 5, 3 );
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10, 3 );
		remove_filter( 'oembed_response_data', 'get_oembed_response_data_rich', 10, 4 );
		remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10, 3 );

		// Sitemaps actions.
		remove_action( 'init', 'wp_sitemaps_get_server' );

		// This option no longer exists; tell plugins we always support auto-embedding.
		remove_filter( 'pre_option_embed_autourls', '__return_true' );
	}

	public function image_sizes_choose($size_names) {
		$new_sizes = array(
			'medium_large' => 'Medium Large',
		);
		return array_merge( $size_names, $new_sizes );
	}

	public function theme_activation() {

		add_role( 'texture_manage', 'Upload Texture', array( 'read' => true, 'level_0' => true ) );

		$admin_role = get_role( 'administrator' );

		$admin_role->add_cap('edit_texture');
		$admin_role->add_cap('read_texture');
		$admin_role->add_cap('delete_texture');
		$admin_role->add_cap('edit_textures');
		$admin_role->add_cap('edit_others_textures');
		$admin_role->add_cap('delete_textures');
		$admin_role->add_cap('publish_textures');
		$admin_role->add_cap('read_private_textures');
		$admin_role->add_cap('delete_private_textures');
		$admin_role->add_cap('delete_published_textures');
		$admin_role->add_cap('delete_others_textures');
		$admin_role->add_cap('edit_private_textures');
		$admin_role->add_cap('edit_published_textures');

		$admin_role->add_cap('edit_contractor');
		$admin_role->add_cap('read_contractor');
		$admin_role->add_cap('delete_contractor');
		$admin_role->add_cap('edit_contractors');
		$admin_role->add_cap('edit_others_contractors');
		$admin_role->add_cap('delete_contractors');
		$admin_role->add_cap('publish_contractors');
		$admin_role->add_cap('read_private_contractors');
		$admin_role->add_cap('delete_private_contractors');
		$admin_role->add_cap('delete_published_contractors');
		$admin_role->add_cap('delete_others_contractors');
		$admin_role->add_cap('edit_private_contractors');
		$admin_role->add_cap('edit_published_contractors');

		$admin_role->add_cap('manage_projects');
		$admin_role->add_cap('edit_projects');
		$admin_role->add_cap('delete_projects');

		$admin_role->add_cap('manage_design_cats');
		$admin_role->add_cap('edit_design_cats');
		$admin_role->add_cap('delete_design_cats');

		$admin_role->add_cap('manage_occupations');
		$admin_role->add_cap('edit_occupations');
		$admin_role->add_cap('delete_occupations');

		$admin_role->add_cap('manage_cgroups');
		$admin_role->add_cap('edit_cgroups');
		$admin_role->add_cap('delete_cgroups');

		$admin_role->add_cap('manage_contractor_cats');
		$admin_role->add_cap('edit_contractor_cats');
		$admin_role->add_cap('delete_contractor_cats');

		$admin_role->add_cap('manage_contractor_sources');
		$admin_role->add_cap('edit_contractor_sources');
		$admin_role->add_cap('delete_contractor_sources');

		$admin_role->add_cap('manage_locations');
		$admin_role->add_cap('edit_locations');
		$admin_role->add_cap('delete_locations');

		$admin_role->add_cap('manage_segments');
		$admin_role->add_cap('edit_segments');
		$admin_role->add_cap('delete_segments');

		$admin_role->add_cap('manage_design_interiors');
		$admin_role->add_cap('edit_design_interiors');
		$admin_role->add_cap('delete_design_interiors');

		$admin_role->add_cap('manage_design_exteriors');
		$admin_role->add_cap('edit_design_exteriors');
		$admin_role->add_cap('delete_design_exteriors');

		/* ------------------------------------------------- */
		$texture_manage_role = get_role( 'texture_manage' );

		if($texture_manage_role) {
			$texture_manage_role->add_cap('edit_posts');
			$texture_manage_role->add_cap('edit_texture');
			$texture_manage_role->add_cap('read_texture');
			$texture_manage_role->add_cap('delete_texture');
			$texture_manage_role->add_cap('edit_textures');
			$texture_manage_role->add_cap('edit_others_textures');
			$texture_manage_role->add_cap('delete_textures');
			$texture_manage_role->add_cap('publish_textures');
			$texture_manage_role->add_cap('read_private_textures');
			$texture_manage_role->add_cap('delete_private_textures');
			$texture_manage_role->add_cap('delete_published_textures');
			$texture_manage_role->add_cap('delete_others_textures');
			$texture_manage_role->add_cap('edit_private_textures');
			$texture_manage_role->add_cap('edit_published_textures');

			$texture_manage_role->add_cap('manage_projects');
			$texture_manage_role->add_cap('edit_projects');
			$texture_manage_role->add_cap('delete_projects');

			// $texture_manage_role->add_cap('manage_occupations');
			// $texture_manage_role->add_cap('edit_occupations');
			// $texture_manage_role->add_cap('delete_occupations');

			$texture_manage_role->add_cap('manage_design_interiors');
			$texture_manage_role->add_cap('edit_design_interiors');
			$texture_manage_role->add_cap('delete_design_interiors');

			$texture_manage_role->add_cap('manage_design_exteriors');
			$texture_manage_role->add_cap('edit_design_exteriors');
			$texture_manage_role->add_cap('delete_design_exteriors');
		}
		flush_rewrite_rules();
	}

	public function theme_deactivation() {

		$texture_manage_role = get_role( 'texture_manage' );

		if($texture_manage_role) {
			$texture_manage_role->remove_cap('edit_posts');
			$texture_manage_role->remove_cap('edit_texture');
			$texture_manage_role->remove_cap('read_texture');
			$texture_manage_role->remove_cap('delete_texture');
			$texture_manage_role->remove_cap('edit_textures');
			$texture_manage_role->remove_cap('edit_others_textures');
			$texture_manage_role->remove_cap('delete_textures');
			$texture_manage_role->remove_cap('publish_textures');
			$texture_manage_role->remove_cap('read_private_textures');
			$texture_manage_role->remove_cap('delete_private_textures');
			$texture_manage_role->remove_cap('delete_published_textures');
			$texture_manage_role->remove_cap('delete_others_textures');
			$texture_manage_role->remove_cap('edit_private_textures');
			$texture_manage_role->remove_cap('edit_published_textures');

			$texture_manage_role->remove_cap('manage_projects');
			$texture_manage_role->remove_cap('edit_projects');
			$texture_manage_role->remove_cap('delete_projects');

			$texture_manage_role->remove_cap('manage_occupations');
			$texture_manage_role->remove_cap('edit_occupations');
			$texture_manage_role->remove_cap('delete_occupations');

			$texture_manage_role->remove_cap('manage_design_interiors');
			$texture_manage_role->remove_cap('edit_design_interiors');
			$texture_manage_role->remove_cap('delete_design_interiors');

			$texture_manage_role->remove_cap('manage_design_exteriors');
			$texture_manage_role->remove_cap('edit_design_exteriors');
			$texture_manage_role->remove_cap('delete_design_exteriors');
		}

		flush_rewrite_rules();
	}
}