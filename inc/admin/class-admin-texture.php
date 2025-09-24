<?php
namespace HomeViet\Admin;

class Texture {

	public static function custom_columns_value($column, $post_id) {
		global $texture;
		
		switch ($column) {
			case 'image':
				if(has_post_thumbnail( $texture->post )) {
					echo get_the_post_thumbnail( $texture->post, 'thumbnail' );
				} else {
					echo '<i>(No image)</i>';
				}
				break;
		}
	}

	public static function custom_columns_header($columns) {
		$cb = $columns['cb'];
		unset($columns['cb']);

		$new_columns = ['cb' => $cb];

		$new_columns['image'] = 'Ảnh';

		$columns = array_merge($new_columns, $columns);

		return $columns;

	}

	public static function meta_boxes() {
		// remove_meta_box(
		// 	'customerdiv' // ID
		// 	,   'texture'            // Screen, empty to support all post types
		// 	,   'side'      // Context
		// );

		add_meta_box(
            'content'     // Reusing just 'content' doesn't work.
        ,   'Mô tả thêm'    // Title
        ,   [__CLASS__, 'post_content_editor'] // Display function
        ,   'texture'              // Screen, we use all screens with meta boxes.
        ,   'normal'          // Context
        ,   'default'            // Priority
        );
	}

	public static function post_content_editor($post) {
		wp_editor( unescape($post->post_content), 'content2', [
			'tinymce' => true,
			'textarea_name' => 'content',
			'editor_height' => 500,
		] );
	}

	public static function taxonomy_parse_filter($query) {
		//modify the query only if it admin and main query.
		if( !(is_admin() AND $query->is_main_query()) ){ 
			return $query;
		}

		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	
		if($post_type=='texture') {
			$tax_query = [];

			$dc = isset($_GET['dc']) ? intval($_GET['dc']) : 0;
			if($dc!=0) {
				$tax_query['dc'] = ['taxonomy' => 'design_cat'];
				if($dc>0) {
					$tax_query['dc']['field'] = 'term_id';
					$tax_query['dc']['terms'] = $dc;
				} else {
					$tax_query['dc']['operator'] = 'NOT EXISTS';
				}

			}

			$pro = isset($_GET['pro']) ? intval($_GET['pro']) : 0;
			if($pro!=0) {
				$tax_query['pro'] = ['taxonomy' => 'project'];
				if($pro>0) {
					$tax_query['pro']['field'] = 'term_id';
					$tax_query['pro']['terms'] = $pro;
				} else {
					$tax_query['pro']['operator'] = 'NOT EXISTS';
				}

			}

			$int = isset($_GET['int']) ? intval($_GET['int']) : 0;
			if($int!=0) {
				$tax_query['int'] = ['taxonomy' => 'design_interior'];
				if($int>0) {
					$tax_query['int']['field'] = 'term_id';
					$tax_query['int']['terms'] = $int;
				} else {
					$tax_query['int']['operator'] = 'NOT EXISTS';
				}

			}

			$ext = isset($_GET['ext']) ? intval($_GET['ext']) : 0;
			if($ext!=0) {
				$tax_query['ext'] = ['taxonomy' => 'design_exterior'];
				if($ext>0) {
					$tax_query['ext']['field'] = 'term_id';
					$tax_query['ext']['terms'] = $ext;
				} else {
					$tax_query['ext']['operator'] = 'NOT EXISTS';
				}

			}

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
		if ($post_type == 'texture') {
			wp_dropdown_categories(array(
				'show_option_all' => '- Loại thiết kế -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'design_cat',
				'name'            => 'dc',
				'orderby'         => 'name',
				'selected'        => isset($_GET['dc']) ? intval($_GET['dc']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));

			wp_dropdown_categories(array(
				'show_option_all' => '- Dự án -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'project',
				'name'            => 'pro',
				'orderby'         => 'name',
				'selected'        => isset($_GET['pro']) ? intval($_GET['pro']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));

			wp_dropdown_categories(array(
				'show_option_all' => '- Nội thất -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'design_interior',
				'name'            => 'int',
				'orderby'         => 'name',
				'selected'        => isset($_GET['int']) ? intval($_GET['int']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));

			wp_dropdown_categories(array(
				'show_option_all' => '- Ngoại thất -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'design_exterior',
				'name'            => 'ext',
				'orderby'         => 'name',
				'selected'        => isset($_GET['ext']) ? intval($_GET['ext']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));

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
		if($post_type=='texture') {
			$disabled = true;
		}
		return $disabled;
	}

	public static function delete_texture_images($post_id, $post) {
		if($post->post_type=='texture') {
			if(has_post_thumbnail( $post )) {
				wp_delete_attachment( get_post_thumbnail_id( $post ), true );
			}
		}
	}

	public static function save_texture($post_id, $post, $update) {
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if(!$update) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, ['post_name' => 'm'.$post_id], ['ID' => $post_id] );
			
			wp_cache_delete( $post_id, 'posts' );
		}
	}

	public static function enqueue_scripts($hook) {
		global $post_type;

		if(($hook=='edit.php' || $hook=='post.php' || $hook=='post-new.php') && $post_type=='texture') {
			wp_enqueue_style('admin-texture', THEME_URI.'/assets/css/admin-texture.css', [], '');
			wp_enqueue_script('admin-texture', THEME_URI.'/assets/js/admin-texture.js', array('jquery'), '');
		}

	}
}