<?php
namespace HomeViet;

class Authentication {

	public static function authentication_template_include($template) {

		return $template;
	}

	// vô hiệu hóa rest api nếu chưa đăng nhập
	public static function rest_authentication_require( $result ) {
		if ( ! empty( $result ) ) {
			return $result;
		}
		if ( ! is_user_logged_in() ) {
			return new \WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
		}
		return $result;
	}

	public static function require_login_use($wp) {
		if(!is_user_logged_in()) { // bắt buộc đăng nhập để truy cập hệ thống
			// chuyển hướng sang trang đăng nhập
			wp_redirect(wp_login_url(fw_current_url()));
			exit;
		}
	}

	public static function the_password_form($output, $post) {
		ob_start();
		?>
		<form action="<?=home_url( 'wp-login.php?action=postpass' )?>" class="post-password-form" method="post">
			<div class="mb-3 text-uppercase">Đăng nhập bằng email của bạn</div>
			<div class="input-group mb-3">
				<input name="post_password" type="text" class="form-control" spellcheck="false">
				<button class="btn btn-primary" type="submit">Nhập</button>
			</div>
		</form>
		<?php
		$output = ob_get_clean();
		return $output;
	}
	
}