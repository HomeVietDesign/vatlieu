<?php
namespace HomeViet\Admin;

class Contractor {

	public static function ajax_check_contractor_exists() {
		$id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$phone_number = isset($_POST['phone_number']) ? sanitize_phone_number($_POST['phone_number']) : '';

		$phone_number_exists = false;

		if(''!=$phone_number) {
			$phone_number_exists = self::contractor_exists($phone_number, $id);
		}
		
		wp_send_json($phone_number_exists);

		die;
	}

	public static function custom_columns_value($column, $post_id) {
		global $contractor;
		
		switch ($column) {
			case 'image':
				if(has_post_thumbnail( $contractor->post )) {
					echo get_the_post_thumbnail( $contractor->post, 'thumbnail' );
				} else {
					echo '<i>(No image)</i>';
				}
				break;
			case 'phone_number':
				$phone_number = fw_get_db_post_option($post_id, '_phone_number');
				echo esc_html($phone_number);
				break;
		}
	}

	public static function custom_columns_header($columns) {
		$cb = $columns['cb'];
		unset($columns['cb']);

		$title = $columns['title'];
		unset($columns['title']);

		$new_columns = ['cb' => $cb];

		$new_columns['image'] = 'Ảnh';
		$new_columns['title'] = $title;
		$new_columns['phone_number'] = 'Số điện thoại';

		$columns = array_merge($new_columns, $columns);

		return $columns;

	}

	public static function meta_boxes() {
		// remove_meta_box(
		// 	'customerdiv' // ID
		// 	,   'contractor'            // Screen, empty to support all post types
		// 	,   'side'      // Context
		// );

		add_meta_box(
            'content'     // Reusing just 'content' doesn't work.
        ,   'Mô tả thêm'    // Title
        ,   [__CLASS__, 'post_content_editor'] // Display function
        ,   'contractor'              // Screen, we use all screens with meta boxes.
        ,   'normal'          // Context
        ,   'default'            // Priority
        );
	}

	public static function save_contractor_15($post_id, $post) {
		if ($post->post_type!='contractor') return;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_contractor', $post_id ) ) {
			return;
		}

		if(isset($_POST['fw_options']['_phone_number'])) {
			global $wpdb;
			$phone_number = sanitize_phone_number($_POST['fw_options']['_phone_number']);

			update_post_meta( $post_id, '_phone_number', $phone_number );
			$wpdb->update( $wpdb->posts, ['post_excerpt' => phone_8420($phone_number).' '.phone_0284($phone_number)], ['ID' => $post_id] );
			
			if($post->post_status=='publish') {
				// không được đăng đối tượng với số điện thoại đã tồn tại. trường xác định là _phone_number hoặc không có thông tin nhận dạng
				if( $phone_number!='' && self::contractor_exists($phone_number, $post->ID) ) {
					$wpdb->update( $wpdb->posts, ['post_status' => 'draft'], ['ID' => $post_id] );
					wp_cache_delete( $post_id, 'posts' );
				}
			}
			
		}
	}

	public static function contractor_exists($phone_number, $id=0) {
		$args = [
			'post_type'=>'contractor',
			'posts_per_page'=>1,
			'fields'=>'ids',
			'meta_key'=>'_phone_number',
			'meta_value'=>$phone_number,
			'post_status'=>['publish']
		];
		if($id>0) {
			$args['post__not_in'] = [$id];
		}

		$check = new \WP_Query( $args );

		if($check->have_posts()) {
			return true;
		}

		return false;
	}

	public static function post_content_editor($post) {
		wp_editor( unescape($post->post_content), 'content2', [
			'tinymce' => true,
			'textarea_name' => 'content',
			'editor_height' => 400,
		] );
	}

	public static function taxonomy_parse_filter($query) {
		//modify the query only if it admin and main query.
		if( !(is_admin() AND $query->is_main_query()) ){ 
			return $query;
		}

		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	
		if($post_type=='contractor') {
			$tax_query = [];

			$occ = isset($_GET['occ']) ? intval($_GET['occ']) : 0;
			if($occ!=0) {
				$tax_query['occ'] = ['taxonomy' => 'occupation'];
				if($occ>0) {
					$tax_query['occ']['field'] = 'term_id';
					$tax_query['occ']['terms'] = $occ;
				} else {
					$tax_query['occ']['operator'] = 'NOT EXISTS';
				}

			}


			if(!empty($tax_query)) {
				$query->set('tax_query', $tax_query);
			}
		}

		return $query;
	}

	public static function filter_by_taxonomy($post_type) {
		if ($post_type == 'contractor') {
			
			wp_dropdown_categories(array(
				'show_option_all' => '- Hạng mục -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'occupation',
				'name'            => 'occ',
				'orderby'         => 'name',
				'selected'        => isset($_GET['occ']) ? intval($_GET['occ']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));
		};
	}

	public static function disable_months_dropdown($disabled, $post_type) {
		if($post_type=='contractor') {
			$disabled = true;
		}
		return $disabled;
	}

	public static function delete_contractor_images($post_id, $post) {
		if($post->post_type=='contractor') {
			if(has_post_thumbnail( $post )) {
				wp_delete_attachment( get_post_thumbnail_id( $post ), true );
			}
			
		}
	}

	public static function save_contractor($post_id, $post, $update) {
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if(!$update) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, ['post_name' => 'c'.$post_id], ['ID' => $post_id] );
			
			wp_cache_delete( $post_id, 'posts' );
		}
	}

	public static function enqueue_scripts($hook) {
		global $post_type;

		if(($hook=='edit.php' || $hook=='post.php' || $hook=='post-new.php') && $post_type=='contractor') {
			wp_enqueue_style('admin-contractor', THEME_URI.'/assets/css/admin-contractor.css', [], '');
			wp_enqueue_script('admin-contractor', THEME_URI.'/assets/js/admin-contractor.js', array('jquery'), '');
		}

	}
}