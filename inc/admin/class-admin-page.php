<?php
namespace HomeViet\Admin;

class Page {
	
	public static function page_title($title, $post_id) {
		$post = get_post( $post_id );
		if($post->post_type=='page') {
			$title = $post->ID;
		}

		return $title;
	}

	public static function save_page($post_id, $post, $update) {
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if(!$update) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, ['post_name' => date('Ymd-His', strtotime($post->post_date))], ['ID' => $post_id] );
			
			wp_cache_delete( $post_id, 'posts' );
		}
	}

	public static function enqueue_scripts($hook) {
		global $post_type;

		if(($hook=='edit.php' || $hook=='post.php') && $post_type=='page') {
			//add_thickbox();
			//wp_enqueue_script('jquery-input-number', THEME_URI.'/libs/jquery-input-number/jquery-input-number.js', array('jquery'), '', false);

			wp_enqueue_style('admin-page', THEME_URI.'/assets/css/admin-page.css', [], '');
			wp_enqueue_script('admin-page', THEME_URI.'/assets/js/admin-page.js', array('jquery'), '');
		}

	}
}