<?php
namespace HomeViet\Admin;

class Texture_Upload {

	public static function admin_menu() {
		add_submenu_page( 'edit.php?post_type=texture', 'Tải lên map vật liệu', 'Tải lên', 'edit_textures', 'texture-upload', [__CLASS__, 'admin_texture_upload'] );
	}

	public static function admin_texture_upload() {

		?>
		<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<p></p>
			<div id="admin-texture-upload">
				<div id="texture-upload-terms">
					<?php
					wp_nonce_field( 'admin-texture-upload', 'upload_nonce' );

					wp_dropdown_categories([
						'taxonomy' => 'project',
						'name' => 'project',
						'id' => 'upload_to_project',
						'hide_empty' => false,
						'show_option_all' => '--Dự án--',
					]);

					wp_dropdown_categories([
						'taxonomy' => 'design_type',
						'name' => 'design_type',
						'id' => 'upload_to_design_type',
						'hide_empty' => false,
						'show_option_all' => '--Vị trí--',
					]);

					?>
				</div>
				<div id="texture-upload-dragandrop-handler">
					Kéo thả để tải lên
				</div>
				<div id="texture-upload-statusbars"></div>
				<br><br>
				<div id="texture-upload-status"></div>
			</div>
		</div>
		<?php
	}

	public static function ajax_admin_texture_upload() {
		if(current_user_can('edit_textures') && check_ajax_referer( 'admin-texture-upload', 'nonce', false )) {
			$project = isset($_POST['project']) ? absint($_POST['project']) : 0;
			$design_type = isset($_POST['design_type']) ? absint($_POST['design_type']) : 0;

			$upload = isset($_FILES['image']) ? $_FILES['image'] : null;

			// tải lên file dự toán
			if ( ! function_exists( 'media_handle_upload' ) ) {
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');
			}

			$attachment_id = media_handle_upload( 'image', 0 );

			if ($upload['error']==0 && $attachment_id && ! is_array( $attachment_id ) ) {
				$path_parts = pathinfo($upload['full_path']);
				$insert_id = wp_insert_post([
					'post_title' => $path_parts['filename'],
					'post_type' => 'texture',
					'post_status' => 'publish',
				]);

				if(is_int($insert_id)) {
					//$texture = \HomeViet\Texture::get_instance($insert_id);
					set_post_thumbnail( $insert_id, $attachment_id );
					
					if($project>0) {
						wp_set_object_terms( $insert_id, [$project], 'project' );
					}
					if($design_type>0) {
						wp_set_object_terms( $insert_id, [$design_type], 'design_type' );
					}
					
				}
			}
		}
		//debug_log($_FILES);

		exit;
	}

	public static function enqueue_scripts($hook) {
		//debug_log($hook);
		if(($hook=='texture_page_texture-upload')) {
			wp_enqueue_style( 'admin-texture-upload', THEME_URI.'/assets/css/admin-texture-upload.css', [], '' );
			wp_enqueue_script('admin-texture-upload', THEME_URI.'/assets/js/admin-texture-upload.js', array('jquery'), '');
		}
	}
}