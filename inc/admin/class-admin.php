<?php
namespace HomeViet\Admin;

class Main {
	use \HomeViet\Singleton;

	protected function __construct() {
		$this->includes();
		$this->hooks_page();
		$this->hooks_texture();
		$this->hooks_contractor();
		$this->hooks_design_cat();
		$this->hooks_occupation();
		//$this->hooks_location();
		$this->hooks_segment();
		$this->hooks_design_exterior();
		$this->hooks_design_interior();
		$this->hooks_project();
		$this->hooks_cgroup();
		$this->hooks_contractor_cat();
		$this->hooks_contractor_source();
		//$this->hooks_texture_upload();

		add_action( 'admin_print_scripts', [$this,'admin_print_head_scripts'] );

		remove_action( 'admin_init', 'customtaxorder_tag_edit_screen' );
	}

	public function admin_print_head_scripts() {
		//global $post_type;
		?>
			
		<script type="text/javascript">
			function sanitize_phone_number(phone_number) {
				phone_number = phone_number.replace(/\D/g,'');
				phone_number = phone_number.replace(/^84/,'0');

				if(phone_number.match(/^0\d{9}$/)) {
					return phone_number;
				}
				return '';
			}
		</script>
		<?php
	}

	private function hooks_texture_upload() {
		if(is_admin()) {
			add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Texture_Upload', 'enqueue_scripts'] );
			add_action( 'admin_menu', ['\HomeViet\Admin\Texture_Upload', 'admin_menu' ] );
		}

		add_action('wp_ajax_admin_texture_upload', ['\HomeViet\Admin\Texture_Upload', 'ajax_admin_texture_upload' ] );
	}

	private function hooks_contractor_source() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Contractor_Source', 'enqueue_scripts'] );
		add_action( 'created_contractor_source', ['\HomeViet\Admin\Contractor_Source', 'auto_slug'] );
		add_action( 'manage_edit-contractor_source_columns', ['\HomeViet\Admin\Contractor_Source', 'manage_edit_column_header'] );
		add_action( 'manage_contractor_source_custom_column', ['\HomeViet\Admin\Contractor_Source', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_contractor_cat() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Contractor_Cat', 'enqueue_scripts'] );
		add_action( 'created_contractor_cat', ['\HomeViet\Admin\Contractor_Cat', 'auto_slug'] );
		add_action( 'manage_edit-contractor_cat_columns', ['\HomeViet\Admin\Contractor_Cat', 'manage_edit_column_header'] );
		add_action( 'manage_contractor_cat_custom_column', ['\HomeViet\Admin\Contractor_Cat', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_cgroup() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Cgroup', 'enqueue_scripts'] );
		add_action( 'created_cgroup', ['\HomeViet\Admin\Cgroup', 'auto_slug'] );
		add_action( 'manage_edit-cgroup_columns', ['\HomeViet\Admin\Cgroup', 'manage_edit_column_header'] );
		add_action( 'manage_cgroup_custom_column', ['\HomeViet\Admin\Cgroup', 'manage_edit_columns_value'], 15, 3 );
	}
	
	private function hooks_project() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Project', 'enqueue_scripts'] );
		add_action( 'created_project', ['\HomeViet\Admin\Project', 'auto_slug'] );
		add_action( 'manage_edit-project_columns', ['\HomeViet\Admin\Project', 'manage_edit_column_header'] );
		add_action( 'manage_project_custom_column', ['\HomeViet\Admin\Project', 'manage_edit_columns_value'], 15, 3 );
		//add_action( 'quick_edit_custom_box', ['\HomeViet\Admin\Project', 'quick_edit_custom_box'], 10, 3 );
		//add_action( 'edited_project', ['\HomeViet\Admin\Project', 'quick_edit_save'], 10, 1 );

		//add_filter( 'tag_row_actions', ['\HomeViet\Admin\Project', 'quick_edit_custom_field_value'], 10, 2 );
		//add_filter( 'list_table_primary_column', ['\HomeViet\Admin\Project', 'primary_column'], 10, 2 );
		//add_filter( 'wp_terms_checklist_args', ['\HomeViet\Admin\Project', 'change_check_list'], 10, 1 );
		//add_filter( 'post_column_taxonomy_links', ['\HomeViet\Admin\Project', 'change_columns_links'], 10, 3 );
	}


	private function hooks_design_cat() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Design_Cat', 'enqueue_scripts'] );
		//add_action( 'created_design_cat', ['\HomeViet\Admin\Design_Cat', 'auto_slug'] );
		add_action( 'manage_edit-design_cat_columns', ['\HomeViet\Admin\Design_Cat', 'manage_edit_column_header'] );
		add_action( 'manage_design_cat_custom_column', ['\HomeViet\Admin\Design_Cat', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_occupation() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Occupation', 'enqueue_scripts'] );
		add_action( 'created_occupation', ['\HomeViet\Admin\Occupation', 'auto_slug'] );
		add_action( 'manage_edit-occupation_columns', ['\HomeViet\Admin\Occupation', 'manage_edit_column_header'] );
		//add_action( 'manage_occupation_custom_column', ['\HomeViet\Admin\Occupation', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_location() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Location', 'enqueue_scripts'] );
		add_action( 'created_location', ['\HomeViet\Admin\Location', 'auto_slug'] );
		add_action( 'manage_edit-location_columns', ['\HomeViet\Admin\Location', 'manage_edit_column_header'] );
		//add_action( 'manage_location_custom_column', ['\HomeViet\Admin\Location', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_segment() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Segment', 'enqueue_scripts'] );
		add_action( 'created_segment', ['\HomeViet\Admin\Segment', 'auto_slug'] );
		add_action( 'manage_edit-segment_columns', ['\HomeViet\Admin\Segment', 'manage_edit_column_header'] );
		//add_action( 'manage_segment_custom_column', ['\HomeViet\Admin\Segment', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_design_interior() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Design_Interior', 'enqueue_scripts'] );
		add_action( 'created_design_interior', ['\HomeViet\Admin\Design_Interior', 'auto_slug'] );
		add_action( 'manage_edit-design_interior_columns', ['\HomeViet\Admin\Design_Interior', 'manage_edit_column_header'] );
		add_action( 'manage_design_interior_custom_column', ['\HomeViet\Admin\Design_Interior', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_design_exterior() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Design_Exterior', 'enqueue_scripts'] );
		add_action( 'created_design_exterior', ['\HomeViet\Admin\Design_Exterior', 'auto_slug'] );
		add_action( 'manage_edit-design_exterior_columns', ['\HomeViet\Admin\Design_Exterior', 'manage_edit_column_header'] );
		add_action( 'manage_design_exterior_custom_column', ['\HomeViet\Admin\Design_Exterior', 'manage_edit_columns_value'], 15, 3 );
	}

	private function hooks_contractor() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Contractor', 'enqueue_scripts'] );
		add_action( 'save_post_contractor', ['\HomeViet\Admin\Contractor', 'save_contractor'], 15, 3 );
		//add_action( 'before_delete_post', ['\HomeViet\Admin\Contractor', 'delete_contractor_images'], 15, 2 );
		add_action( 'add_meta_boxes', ['\HomeViet\Admin\Contractor', 'meta_boxes'] );

		add_filter( 'manage_contractor_posts_columns', ['\HomeViet\Admin\Contractor', 'custom_columns_header'] );
		add_action( 'manage_contractor_posts_custom_column', ['\HomeViet\Admin\Contractor', 'custom_columns_value'], 2, 2 );

		add_filter( 'disable_months_dropdown', ['\HomeViet\Admin\Contractor', 'disable_months_dropdown'], 10, 2 );
		add_action( 'restrict_manage_posts', ['\HomeViet\Admin\Contractor', 'filter_by_taxonomy'] );
		add_filter( 'parse_query', ['\HomeViet\Admin\Contractor', 'taxonomy_parse_filter'] );

		if(unyson_exists()) {
			add_action( 'fw_save_post_options', ['\HomeViet\Admin\Contractor', 'save_contractor_15'], 15, 2 );
		} else {
			add_action( 'save_post_contractor', ['\HomeViet\Admin\Contractor', 'save_contractor_15'], 15, 2 );
		}

		add_action( 'wp_ajax_check_contractor_exists', ['\HomeViet\Admin\Contractor', 'ajax_check_contractor_exists'] );
	}

	private function hooks_texture() {
		add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Texture', 'enqueue_scripts'] );
		add_action( 'save_post_texture', ['\HomeViet\Admin\Texture', 'save_texture'], 15, 3 );
		add_action( 'before_delete_post', ['\HomeViet\Admin\Texture', 'delete_texture_images'], 15, 2 );
		//add_action( 'add_meta_boxes', ['\HomeViet\Admin\Texture', 'meta_boxes'] );

		add_filter( 'manage_texture_posts_columns', ['\HomeViet\Admin\Texture', 'custom_columns_header'] );
		add_action( 'manage_texture_posts_custom_column', ['\HomeViet\Admin\Texture', 'custom_columns_value'], 2, 2 );

		add_filter( 'disable_months_dropdown', ['\HomeViet\Admin\Texture', 'disable_months_dropdown'], 10, 2 );
		add_action( 'restrict_manage_posts', ['\HomeViet\Admin\Texture', 'filter_by_taxonomy'] );
		add_filter( 'parse_query', ['\HomeViet\Admin\Texture', 'taxonomy_parse_filter'] );
	}

	private function hooks_page() {
		//add_action( 'admin_enqueue_scripts', ['\HomeViet\Admin\Page', 'enqueue_scripts'] );
		add_action( 'save_post_page', ['\HomeViet\Admin\Page', 'save_page'], 15, 3 );
	}

	private function includes() {
		require_once THEME_DIR.'/inc/admin/class-walker-project-checklist.php';

		include_once THEME_DIR.'/inc/admin/class-admin-page.php';
		include_once THEME_DIR.'/inc/admin/class-admin-texture.php';
		include_once THEME_DIR.'/inc/admin/class-admin-contractor.php';
		include_once THEME_DIR.'/inc/admin/class-admin-design_interior.php';
		include_once THEME_DIR.'/inc/admin/class-admin-design_exterior.php';
		include_once THEME_DIR.'/inc/admin/class-admin-design_cat.php';
		include_once THEME_DIR.'/inc/admin/class-admin-occupation.php';
		include_once THEME_DIR.'/inc/admin/class-admin-location.php';
		include_once THEME_DIR.'/inc/admin/class-admin-segment.php';
		include_once THEME_DIR.'/inc/admin/class-admin-project.php';
		include_once THEME_DIR.'/inc/admin/class-admin-cgroup.php';
		include_once THEME_DIR.'/inc/admin/class-admin-contractor_cat.php';
		include_once THEME_DIR.'/inc/admin/class-admin-contractor_source.php';

		//require_once THEME_DIR.'/inc/admin/class-admin-texture-upload.php';
		
	}

}
Main::get_instance();