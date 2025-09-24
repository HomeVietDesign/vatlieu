<?php
namespace HomeViet;

class Shortcode {

	public static function texture_edit_button($atts) {
		if(current_user_can('edit_textures')) {
			global $texture;
			?>
			<a class="edit-texture-button position-absolute end-0 bottom-0 z-2 p-1 lh-1 d-block text-info" href="<?php echo esc_url(get_edit_post_link( $texture->id )); ?>" target="_blank">
				<span class="dashicons dashicons-welcome-write-blog"></span>
			</a>
			<?php
		}
	}

	public static function texture_upload_button($atts) {
		ob_start();

		$occ = isset($_GET['occ']) ? absint($_GET['occ']) : 0;
		$pro = isset($_GET['pro']) ? absint($_GET['pro']) : 0;
		$int = isset($_GET['int']) ? absint($_GET['int']) : 0;
		$ext = isset($_GET['ext']) ? absint($_GET['ext']) : 0;

		if(current_user_can('edit_textures') && is_tax('design_cat') && $pro && ($int || $ext) ):
			$queried = get_queried_object();
			$cate = $queried->term_id;
		?>
		<div class="col-auto">
			<a href="<?php echo esc_url(fw_current_url()); ?>" class="btn btn-sm btn-secondary me-2 my-3"><span class="dashicons dashicons-update-alt"></span></a>
			<button class="btn btn-sm btn-primary my-3" type="button" data-bs-toggle="collapse" data-bs-target="#texture-upload-container" aria-expanded="false" aria-controls="texture-upload-container">Tải lên</button>
		</div>
		<div class="collapse" id="texture-upload-container">
			<input type="hidden" id="ucate" value="<?=$cate?>">
			<input type="hidden" id="uocc" value="<?=$occ?>">
			<input type="hidden" id="upro" value="<?=$pro?>">
			<input type="hidden" id="uint" value="<?=$int?>">
			<input type="hidden" id="uext" value="<?=$ext?>">
			<input type="hidden" id="unonce" value="<?php echo esc_attr(wp_create_nonce( 'texture-upload' )); ?>">
			<div class="card card-body rounded-0 p-0 border-0">
				<label class="px-3 py-5 text-center border border-success" id="texture-upload-dragandrop-handler" style="--bs-border-opacity: .3;">
					<span class="text-info-emphasis fs-4 opacity-50 d-block py-5">Kéo thả hoặc click để tải lên</span>
					<input type="file" name="maps[]" id="umap" class="d-none" multiple>
				</label>
			</div>
		</div>
		<?php
		endif;

		return ob_get_clean();
	}

	public static function user_info_block($atts) {
		ob_start();
		?>
		<ul class="navbar-nav navbar-nav-icons flex-row">
			<li class="nav-item d-lg-none"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal">Tìm kiếm</a></li>
			<?php if(is_user_logged_in()) {
				$user = wp_get_current_user();
			?>
			<li class="nav-item">
				<a class="nav-link px-3 d-block" href="<?php echo esc_url(admin_url()); ?>" target="_blank"><span><?php echo esc_html($user->display_name); ?></span></a>
			</li>
			<?php } else { ?>
			<li class="nav-item">
				<a class="nav-link px-3 d-block" href="<?php echo esc_url(wp_login_url(fw_current_url())); ?>"><span>Đăng nhập</span></a>
			</li>
			<?php } ?>
		</ul>
		<?php
		return ob_get_clean();
	}
}