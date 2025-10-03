<?php
namespace HomeViet;

class Custom_Types {

	public static function rewrite_texture_url() {
		add_rewrite_rule(
			'([^/]+)/([^/]+)\.(\w+)$',
			'index.php?post_type=texture&name=$matches[2]',
			'top'
		);
	}

	public static function rewrite_texture_link($post_link, $post ) {
		if ('texture' === $post->post_type) {
			if(is_tax()) {
				$queried = get_queried_object();
				$post_link = str_replace('%texture_slug_holder%', $queried->slug, $post_link);
			} else {
				$post_link = str_replace('%texture_slug_holder%', 'vat-lieu', $post_link); // fallback
			}
			if(has_post_thumbnail()) {
				$file_url = wp_get_attachment_image_url( get_post_thumbnail_id($post), 'full', false );
				$path_parts = pathinfo($file_url);
				$post_link .= '.'.$path_parts['extension'];
			}
		}
		return $post_link;
	}

	public static function _theme_filter_builder_supported_custom_type($result) {
		$texture = get_post_type_object( 'texture' );
		if($texture) {
			$result[$texture->name] = $texture->label;
		}
		return $result;
	}

	public static function hide_tags_from_quick_edit($show_in_quick_edit, $taxonomy_name, $post_type) {
		$taxs = [
			'design_cat',
			//'design_interior',
			//'design_exterior',
			'project',
		];
		if( in_array($taxonomy_name, $taxs) && $post_type=='texture') {
			$show_in_quick_edit = false;
		}

		if( in_array($taxonomy_name, ['contractor_cat', 'segment']) && $post_type=='contractor') {
			$show_in_quick_edit = false;
		}

		return $show_in_quick_edit;
	}

	public static function _theme_action_register_post_status() {
		register_post_status( 'cancel', array(
			'label'                     => 'Đã hủy',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Đã hủy (%s)', 'Đã hủy (%s)' ),
		) );
	}

	public static function _theme_action_register_custom_type() {

		$labels = array(
			'name'               => 'Nhà thầu',
			'singular_name'      => 'Nhà thầu',
			'add_new'            => 'Thêm mới Nhà thầu',
			'add_new_item'       => 'Thêm mới Nhà thầu',
			'edit_item'          => 'Sửa Nhà thầu',
			'new_item'           => 'Nhà thầu mới',
			'view_item'          => 'Xem Nhà thầu',
			'search_items'       => 'Tìm Nhà thầu',
			'not_found'          => 'Không có phần tử nào',
			'not_found_in_trash' => 'Không có phần tử nào trong Thùng rác',
			'parent_item_colon'  => 'Cấp trên:',
			'menu_name'          => 'Nhà thầu',
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-hammer',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true, // ẩn bài viết ở front-end
			'exclude_from_search' => true, // loại khỏi kết quả tìm kiếm
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'contractor',
			'map_meta_cap'		  => true,
			'supports'            => array(
				'title',
				'thumbnail',
				//'editor',
				//'excerpt',
				//'revisions',
				//'page-attributes',
			),
		);
	
		register_post_type( 'contractor', $args );
		
		$labels = array(
			'name'               => 'Map vật liệu',
			'singular_name'      => 'Map vật liệu',
			'add_new'            => 'Thêm mới Map vật liệu',
			'add_new_item'       => 'Thêm mới Map vật liệu',
			'edit_item'          => 'Sửa Map vật liệu',
			'new_item'           => 'Map vật liệu mới',
			'view_item'          => 'Xem Map vật liệu',
			'search_items'       => 'Tìm Map vật liệu',
			'not_found'          => 'Không có phần tử nào',
			'not_found_in_trash' => 'Không có phần tử nào trong Thùng rác',
			'parent_item_colon'  => 'Cấp trên:',
			'menu_name'          => 'Map vật liệu',
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-format-image',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true, // ẩn bài viết ở front-end
			'exclude_from_search' => false, // loại khỏi kết quả tìm kiếm
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => [
				'slug'=>'map',
				//'slug' => '%texture_slug_holder%',
        		'with_front' => false
			],
			'capability_type'     => 'texture',
			'map_meta_cap'		  => true,
			'supports'            => array(
				'title',
				'thumbnail',
				//'editor',
				//'excerpt',
				//'revisions',
				//'page-attributes',
			),
		);
	
		register_post_type( 'texture', $args );

	}

	public static function _theme_action_register_taxonomy() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Dự án',
			'singular_name'     => 'Dự án',
			'search_items'      => 'Tìm Dự án',
			'all_items'         => 'Tất cả Dự án',
			'edit_item'         => 'Sửa Dự án',
			'update_item'       => 'Cập nhật Dự án',
			'add_new_item'      => 'Thêm Dự án mới',
			'new_item_name'     => 'Dự án mới',
			'menu_name'         => 'Dự án',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			//'rewrite'           => ['slug'=>'du-an'],
			'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_projects',
				'edit_terms'   => 'edit_projects',
				'delete_terms' => 'delete_projects',
				'assign_terms' => 'edit_textures',
			],
			'public' 			=> false,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'project', 'texture', $args ); // our new 'format' taxonomy

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Loại thiết kế',
			'singular_name'     => 'Loại thiết kế',
			'search_items'      => 'Tìm Loại thiết kế',
			'all_items'         => 'Tất cả Loại thiết kế',
			'edit_item'         => 'Sửa Loại thiết kế',
			'update_item'       => 'Cập nhật Loại thiết kế',
			'add_new_item'      => 'Thêm Thiết kế mới',
			'new_item_name'     => 'Thiết kế mới',
			'menu_name'         => 'Loại thiết kế',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => ['slug'=>'thiet-ke'],
			//'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_design_cats',
				'edit_terms'   => 'edit_design_cats',
				'delete_terms' => 'delete_design_cats',
				'assign_terms' => 'edit_textures',
			],
			'public' 			=> true,
			'show_in_nav_menus' => true,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'design_cat', 'texture', $args ); // our new 'format' taxonomy

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Hạng mục',
			'singular_name'     => 'Hạng mục',
			'search_items'      => 'Tìm Hạng mục',
			'all_items'         => 'Tất cả Hạng mục',
			'edit_item'         => 'Sửa Hạng mục',
			'update_item'       => 'Cập nhật Hạng mục',
			'add_new_item'      => 'Thêm Hạng mục mới',
			'new_item_name'     => 'Hạng mục mới',
			'menu_name'         => 'Hạng mục',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			//'rewrite'           => ['slug'=>'hang-muc'],
			'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_occupations',
				'edit_terms'   => 'edit_occupations',
				'delete_terms' => 'delete_occupations',
				'assign_terms' => 'edit_occupations',
			],
			'public' 			=> false,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'occupation', ['texture', 'contractor'], $args ); // ngành nghề / lĩnh vực công việc / sản phẩm dịch vụ cung cấp

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Tính chất',
			'singular_name'     => 'Tính chất',
			'search_items'      => 'Tìm Tính chất',
			'all_items'         => 'Tất cả Tính chất',
			'edit_item'         => 'Sửa Tính chất',
			'update_item'       => 'Cập nhật Tính chất',
			'add_new_item'      => 'Thêm Tính chất mới',
			'new_item_name'     => 'Tính chất mới',
			'menu_name'         => 'Tính chất',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => false,
			'show_admin_column' => false,
			'query_var'         => false,
			//'rewrite'           => ['slug'=>'hang-muc'],
			//'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_cgroups',
				'edit_terms'   => 'edit_cgroups',
				'delete_terms' => 'delete_cgroups',
				'assign_terms' => 'edit_contractors',
			],
			'public' 			=> false,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		//register_taxonomy( 'cgroup', ['contractor'], $args ); // tạm thời không sử dụng


		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Nhóm nhà thầu',
			'singular_name'     => 'Nhóm nhà thầu',
			'search_items'      => 'Tìm Nhóm nhà thầu',
			'all_items'         => 'Tất cả Nhóm nhà thầu',
			'edit_item'         => 'Sửa Nhóm nhà thầu',
			'update_item'       => 'Cập nhật Nhóm nhà thầu',
			'add_new_item'      => 'Thêm Nhóm nhà thầu mới',
			'new_item_name'     => 'Nhóm nhà thầu mới',
			'menu_name'         => 'Nhóm nhà thầu',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			//'rewrite'           => ['slug'=>'hang-muc'],
			//'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_contractor_cats',
				'edit_terms'   => 'edit_contractor_cats',
				'delete_terms' => 'delete_contractor_cats',
				'assign_terms' => 'edit_contractors',
			],
			'public' 			=> false,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'contractor_cat', ['contractor'], $args );

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Nguồn nhà thầu',
			'singular_name'     => 'Nguồn nhà thầu',
			'search_items'      => 'Tìm Nguồn nhà thầu',
			'all_items'         => 'Tất cả Nguồn nhà thầu',
			'edit_item'         => 'Sửa Nguồn nhà thầu',
			'update_item'       => 'Cập nhật Nguồn nhà thầu',
			'add_new_item'      => 'Thêm Nguồn nhà thầu mới',
			'new_item_name'     => 'Nguồn nhà thầu mới',
			'menu_name'         => 'Nguồn nhà thầu',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			//'rewrite'           => ['slug'=>'hang-muc'],
			//'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_contractor_sources',
				'edit_terms'   => 'edit_contractor_sources',
				'delete_terms' => 'delete_contractor_sources',
				'assign_terms' => 'edit_contractors',
			],
			'public' 			=> false,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'contractor_source', ['contractor'], $args );

		
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Địa điểm',
			'singular_name'     => 'Địa điểm',
			'search_items'      => 'Tìm Địa điểm',
			'all_items'         => 'Tất cả Địa điểm',
			'edit_item'         => 'Sửa Địa điểm',
			'update_item'       => 'Cập nhật Địa điểm',
			'add_new_item'      => 'Thêm Địa điểm mới',
			'new_item_name'     => 'Địa điểm mới',
			'menu_name'         => 'Địa điểm',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			//'rewrite'           => ['slug'=>'dia-diem'],
			'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_locations',
				'edit_terms'   => 'edit_locations',
				'delete_terms' => 'delete_locations',
				'assign_terms' => 'edit_contractors',
			],
			'public' 			=> false,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);

		$default_location = [
			'name' => 'Toàn quốc',
			'slug' => 'toan-quoc',
			'description' => ''
		];
		$default = (int) get_option( 'default_term_location', -1 );
		$args['default_term'] = ($default>0)?$default:$default_location;

		register_taxonomy( 'location', ['contractor'], $args );
		

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Phân khúc',
			'singular_name'     => 'Phân khúc',
			'search_items'      => 'Tìm Phân khúc',
			'all_items'         => 'Tất cả Phân khúc',
			'edit_item'         => 'Sửa Phân khúc',
			'update_item'       => 'Cập nhật Phân khúc',
			'add_new_item'      => 'Thêm Phân khúc mới',
			'new_item_name'     => 'Phân khúc mới',
			'menu_name'         => 'Phân khúc',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			//'rewrite'           => ['slug'=>'hang-muc'],
			'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_segments',
				'edit_terms'   => 'edit_segments',
				'delete_terms' => 'delete_segments',
				'assign_terms' => 'edit_contractors',
			],
			'public' 			=> false,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'segment', ['contractor'], $args );

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Không gian Nội thất',
			'singular_name'     => 'Không gian Nội thất',
			'search_items'      => 'Tìm Không gian Nội thất',
			'all_items'         => 'Tất cả Không gian Nội thất',
			'edit_item'         => 'Sửa Không gian Nội thất',
			'update_item'       => 'Cập nhật Không gian Nội thất',
			'add_new_item'      => 'Thêm mới',
			'new_item_name'     => 'Mới',
			'menu_name'         => 'Không gian Nội thất',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			//'rewrite'           => ['slug'=>'noi-that'],
			'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_design_interiors',
				'edit_terms'   => 'edit_design_interiors',
				'delete_terms' => 'delete_design_interiors',
				'assign_terms' => 'edit_textures',
			],
			'public' 			=> false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'design_interior', 'texture', $args ); // our new 'format' taxonomy

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Không gian Ngoại thất',
			'singular_name'     => 'Không gian Ngoại thất',
			'search_items'      => 'Tìm Không gian Ngoại thất',
			'all_items'         => 'Tất cả Không gian Ngoại thất',
			'edit_item'         => 'Sửa Không gian Ngoại thất',
			'update_item'       => 'Cập nhật Không gian Ngoại thất',
			'add_new_item'      => 'Thêm mới',
			'new_item_name'     => 'Mới',
			'menu_name'         => 'Không gian Ngoại thất',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			//'rewrite'           => ['slug'=>'ngoai-that'],
			'rewrite'           => false,
			'capabilities'      => [
				'manage_terms' => 'manage_design_exteriors',
				'edit_terms'   => 'edit_design_exteriors',
				'delete_terms' => 'delete_design_exteriors',
				'assign_terms' => 'edit_textures',
			],
			'public' 			=> false,
			'show_in_nav_menus' => false,
			'show_tagcloud' 	=> false,
		);
		register_taxonomy( 'design_exterior', 'texture', $args ); // our new 'format' taxonomy

	}

	public static function _theme_action_change_default() {
		global $wp_post_types, $wp_taxonomies;
		
		// Someone has changed this post type, always check for that!
		if( isset($wp_post_types['post']) ) {
			// $wp_post_types['post']->label = 'Vật liệu';
			// $wp_post_types['post']->labels->name               = 'Vật liệu';
			// $wp_post_types['post']->labels->singular_name      = 'Vật liệu';
			// $wp_post_types['post']->labels->add_new            = 'Thêm Vật liệu';
			// $wp_post_types['post']->labels->add_new_item       = 'Thêm mới Vật liệu';
			// $wp_post_types['post']->labels->all_items          = 'Tất cả Vật liệu';
			// $wp_post_types['post']->labels->edit_item          = 'Chỉnh sửa Vật liệu';
			// $wp_post_types['post']->labels->name_admin_bar     = 'Vật liệu';
			// $wp_post_types['post']->labels->menu_name          = 'Vật liệu';
			// $wp_post_types['post']->labels->new_item           = 'Vật liệu mới';
			// $wp_post_types['post']->labels->not_found          = 'Không có Vật liệu nào';
			// $wp_post_types['post']->labels->not_found_in_trash = 'Không có Vật liệu nào';
			// $wp_post_types['post']->labels->search_items       = 'Tìm Vật liệu';
			// $wp_post_types['post']->labels->view_item          = 'Xem Vật liệu';

			//debug_log($wp_post_types['post']);
		}

		if( isset($wp_taxonomies['category']) ) {
			// $wp_taxonomies['category']->label = 'Vị trí sử dụng';
			// $wp_taxonomies['category']->labels->name = 'Vị trí sử dụng';
			// $wp_taxonomies['category']->labels->singular_name = 'Vị trí sử dụng';
			// $wp_taxonomies['category']->labels->add_new = 'Thêm vị trí sử dụng';
			// $wp_taxonomies['category']->labels->add_new_item = 'Thêm vị trí sử dụng';
			// $wp_taxonomies['category']->labels->edit_item = 'Sửa vị trí sử dụng';
			// $wp_taxonomies['category']->labels->new_item = 'Vị trí sử dụng';
			// $wp_taxonomies['category']->labels->view_item = 'Xem vị trí sử dụng';
			// $wp_taxonomies['category']->labels->search_items = 'Tìm vị trí sử dụng';
			// $wp_taxonomies['category']->labels->not_found = 'Không có vị trí sử dụng nào được tìm thấy';
			// $wp_taxonomies['category']->labels->not_found_in_trash = 'Không có vị trí sử dụng nào trong thùng rác';
			// $wp_taxonomies['category']->labels->all_items = 'Tất cả vị trí sử dụng';
			// $wp_taxonomies['category']->labels->menu_name = 'Vị trí sử dụng';
			// $wp_taxonomies['category']->labels->name_admin_bar = 'Vị trí sử dụng';
			
			// $wp_taxonomies['category']->public = false;
			// $wp_taxonomies['category']->show_ui = false;
			// $wp_taxonomies['category']->show_in_nav_menus = false;
			// $wp_taxonomies['category']->rewrite = false;
		}

		if( isset($wp_taxonomies['post_tag']) ) {
			// $wp_taxonomies['post_tag']->public = false;
			// $wp_taxonomies['post_tag']->show_ui = false;
			// $wp_taxonomies['post_tag']->show_in_nav_menus = false;
			// $wp_taxonomies['post_tag']->rewrite = false;
		}
	}

	public static function _admin_action_rename_post_menu() {
		global $menu, $submenu;

		//debug_log($submenu);

		remove_menu_page( 'edit-comments.php' ); // ẩn menu Comments
		remove_menu_page( 'edit.php' ); // ẩn menu Blog posts
		remove_menu_page( 'fw-extensions' ); // ẩn menu Unyson
		remove_menu_page( 'separator1' );

		add_menu_page( 'Dự án', 'Dự án', 'manage_projects', 'edit-tags.php?taxonomy=project', null, 'dashicons-bank', 4 );
		add_menu_page( 'Hạng mục', 'Hạng mục', 'manage_occupations', 'edit-tags.php?taxonomy=occupation', null, 'dashicons-category', 4 );
		add_menu_page( 'Địa điểm', 'Địa điểm', 'manage_locations', 'edit-tags.php?taxonomy=location', null, 'dashicons-location', 5 );
		add_menu_page( 'Phân khúc', 'Phân khúc', 'manage_segments', 'edit-tags.php?taxonomy=segment', null, 'dashicons-money-alt', 5 );
	}

	public static function list_projects($name, $category) {
		if($category && $category->taxonomy=='project') {
			$name = $category->name.(($category->description!='')?' ( '.$category->description.' )':'');
		}

		return $name;
	}

	public static function admin_menu_highlight($parent_file) {
		global $pagenow, $taxonomy;

		$taxonomies = [
			'project',
			'occupation',
			'location',
			'segment'
		];

		if(($pagenow=='edit-tags.php' || $pagenow=='term.php') && in_array($taxonomy, $taxonomies)) {
			$parent_file = 'edit-tags.php?taxonomy='.$taxonomy;
		}

		// if ( $pagenow == 'post.php')
		// 	$parent_file = "post.php?post={$_REQUEST['post']}&action=edit";
		// elseif($pagenow == 'post-new.php')
		// 	$parent_file = "post-new.php?post_type={$_REQUEST['post_type']}";

		return $parent_file;
	}

	public static function _setup_loop_custom_type($post, $wp_query) {
		global $texture, $contractor;

		if($post->post_type=='texture') {
			$texture = \HomeViet\Texture::get_instance($post->ID);
		} elseif($post->post_type=='contractor') {
			$contractor = \HomeViet\Contractor::get_instance($post->ID);
		}

	}

	public static function get_the_archive_title($title, $original_title) {
		if(is_tax( 'project' )) {
			$queried = get_queried_object();
			$title = esc_html($queried->name.(($queried->description!='')?' ( '.$queried->description.' )':''));
		}
		return $title;
	}

	public static function query_custom_postype($wp_query) {
		if(!is_admin() && $wp_query->is_main_query() ) {
			
			//$wp_query->set('post_type','texture');
			
			$tax_query = [];


			if(is_search()) {
				$wp_query->set('post_type','texture');
			} elseif(is_tax('design_cat')) {
				$wp_query->set('post_type','texture');

				$pro = isset($_GET['pro']) ? absint($_GET['pro']) : 0;
				$occ = isset($_GET['occ']) ? absint($_GET['occ']) : 0;
				$int = isset($_GET['int']) ? absint($_GET['int']) : 0;
				$ext = isset($_GET['ext']) ? absint($_GET['ext']) : 0;

				if($pro) {
					$tax_query['pro'] = [
						'taxonomy' => 'project',
						'field' => 'term_id',
						'terms' => [$pro]
					];
				}

				if($int) {
					$tax_query['int'] = [
						'taxonomy' => 'design_interior',
						'field' => 'term_id',
						'terms' => [$int]
					];
				}

				if($ext) {
					$tax_query['ext'] = [
						'taxonomy' => 'design_exterior',
						'field' => 'term_id',
						'terms' => [$ext]
					];
				}

				if($occ) {
					$tax_query['occ'] = [
						'taxonomy' => 'occupation',
						'field' => 'term_id',
						'terms' => [$occ]
					];
				}
				
			}

			if(!empty($tax_query)) {
				$wp_query->set('tax_query', $tax_query);
			}

			//debug_log($wp_query);
		}
	}

	public static function _setup_term_default_sort($pieces, $taxonomies, $args) {
		
		if(isset($taxonomies[0]) && 'texture_price' == $taxonomies[0] ) {

			$orderby = isset($_REQUEST['orderby']) ? trim(wp_unslash($_REQUEST['orderby'])) : 'name';
			$order   = isset($_REQUEST['order'])   ? trim(wp_unslash($_REQUEST['order']))   : 'ASC';

			if($orderby=='name') {
				$pieces['orderby'] = "ORDER BY name+0";
			}

			$pieces['order']   = $order;
		}

		return $pieces;
	}
}