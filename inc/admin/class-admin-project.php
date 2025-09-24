<?php
namespace HomeViet\Admin;

class Project {

	public static function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='project') {
			wp_enqueue_style( 'manage-project', THEME_URI.'/assets/css/manage-project.css', [], '' );
			wp_enqueue_script('manage-project', THEME_URI.'/assets/js/manage-project.js', array('jquery'), '');
		}
	}

	public static function change_columns_links($term_links, $taxonomy, $terms) {
		if($taxonomy=='project') {
			foreach ( $term_links as $key => &$link) {
				$link = preg_replace('/(<a[^>]*>)([^<]*)(<\/a>)/', "$1".esc_html($terms[$key]->description.' ( '.$terms[$key]->name.' )')."$3", $link);
			}
		}

		return $term_links;
	}

	public static function change_check_list($args) {
		if($args['taxonomy']=='project') {
			$args['walker'] = new \Walker_Project_Checklist();
		}

		return $args;
	}

	public static function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => 'pro-'.$term_id], ['term_id' => $term_id] );
		// $term = get_term_by( 'term_id', $term_id, 'project' );
		// $slug = wp_unique_term_slug( sanitize_title( $term->description ), $term );
		// $wpdb->update( $wpdb->terms, ['slug' => $slug], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public static function quick_edit_save($term_id) {
		if(!current_user_can('edit_projects')) return;

		if (isset($_POST['desc'])) {
			//debug_log($_POST);
			remove_action( 'edited_project', ['\HomeViet\Admin\Project', 'quick_edit_save'], 10 );
			wp_update_term( $term_id, 'project', [
				'description' => sanitize_text_field($_POST['desc'])
			] );
			add_action( 'edited_project', ['\HomeViet\Admin\Project', 'quick_edit_save'], 10, 1 );
		}

	}

	public static function quick_edit_custom_box($column_name, $screen_id, $taxonomy) {

		if($screen_id=='edit-tags' && $taxonomy=='project' && $column_name=='desc') {
			?>
			<fieldset style="padding-right: 12px;">
				<div class="inline-edit-col">
				<label>
					<span class="title">Tên dự án</span>
					<span class="input-text-wrap"><input type="text" name="desc" class="ptitle" value="" /></span>
				</label>
			</fieldset>
			<?php
		}
	}

	public static function manage_edit_column_header($columns) {

		if(isset($columns['cb'])) {
			unset($columns['cb']);
		}

		if(isset($columns['slug'])) {
			unset($columns['slug']);
		}

		//$name = $columns['name'];
		if(isset($columns['name'])) {
			unset($columns['name']);
		}

		//$description = $columns['description'];
		if(isset($columns['description'])) {
			unset($columns['description']);
		}

		$new_columns = [
			'cb' => 'Chọn toàn bộ',
			'name' => 'Tên dự án',
			'desc' => 'Số điện thoại',
			//'location' => 'Địa điểm',
			'segment' => 'Phân khúc'
		];

		//$columns['term_id'] = 'ID';
		//$columns['order'] = 'STT';

		if(isset($columns['posts'])) {
			$columns['posts'] = 'Đếm';
		}

		$columns = array_merge($new_columns, $columns);

		return $columns;

	}

	public static function quick_edit_custom_field_value($actions, $term) {
		if($term->taxonomy=='project') {
			
			$action = ['custom_inline_hidden'=>'<div id="custom_inline_'.$term->term_id.'"><div class="description">' . esc_html(strip_tags($term->description)) . '</div></div>'];

			$actions = array_merge($action, $actions);
		}

		return $actions;
	}

	public static function manage_edit_columns_value($row, $column_name, $term_id) {
		$nonce = wp_create_nonce('quick_edit_'.$term_id);
		$term = get_term_by( 'term_id', $term_id, 'project' );

		$locations = fw_get_db_term_option($term_id, 'project', 'location');
		$segments = fw_get_db_term_option($term_id, 'project', 'segment');

		if($locations) {
			$locations = array_map(function($id){
				return get_term_field( 'name', $id, 'location' );
			}, $locations);
		}

		if($segments) {
			$segments = array_map(function($id){
				return get_term_field( 'name', $id, 'segment' );
			}, $segments);
		}

		switch ($column_name) {
			case 'order':
				echo intval(get_term_meta($term_id, 'order', true));
				break;
			case 'term_id':
				echo esc_html($term_id);
				break;
			case 'desc':
				echo esc_html($term->description);
				break;
			case 'location':
				if($locations) {
					echo esc_html(implode(', ', $locations));
				}
				break;
			case 'segment':
				if($segments) {
					echo esc_html(implode(', ', $segments));
				}
				break;
		}
	}

	public static function primary_column($default, $screen_id) {

		if($screen_id=='edit-project') {
			$default = 'description';
		}

		return $default;
	}
}