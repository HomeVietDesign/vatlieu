<?php
namespace HomeViet\Admin;

class Occupation {

	public static function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='occupation') {
			wp_enqueue_style( 'manage-occupation', THEME_URI.'/assets/css/manage-occupation.css', [], '' );
			//wp_enqueue_script('manage-occupation', THEME_URI.'/assets/js/manage-occupation.js', array('jquery'), '');
		}
	}

	public static function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => 't'.$term_id], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public static function manage_edit_column_header($columns) {
		if(isset($columns['slug'])) {
			unset($columns['slug']);
		}

		//$columns['term_id'] = 'ID';
		//$columns['order'] = 'STT';

		if(isset($columns['posts'])) {
			$columns['posts'] = 'Đếm';
		}
		return $columns;
	}

	public static function manage_edit_columns_value($row, $column_name, $term_id) {
		$nonce = wp_create_nonce('quick_edit_'.$term_id);

		switch ($column_name) {
			case 'order':
				echo intval(get_term_meta($term_id, 'order', true));
				break;
			case 'term_id':
				echo esc_html($term_id);
				break;
		}
	}
}