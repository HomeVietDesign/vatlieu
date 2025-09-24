<?php
namespace HomeViet;

final class Setting {
	use \HomeViet\Singleton;

	public $data = [];

	protected function __construct() {
		if(!function_exists('is_plugin_active')) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

	}

	public function cf_captcha_verify($token) {
		// Get Turnstile Keys from Settings
		$key = sanitize_text_field($this->get('cf_turnstile_key'));
		$secret = sanitize_text_field($this->get('cf_turnstile_secret'));

		if ($key && $secret) {

			$headers = array(
				'body' => [
					'secret' => $secret,
					'response' => $token
				]
			);
			$verify = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $headers);
			$verify = wp_remote_retrieve_body($verify);
			$response = json_decode($verify);

			if($response->success) {
				return true;
			}
		}

		return false;
	}

	public function get_admin_email_address() {
		$admin_email_address = explode(',',$this->get('admin_email_address'));
		return array_map('sanitize_email', $admin_email_address);
	}

	public function get($setting_id, $default='') {
		if(!isset($this->data[$setting_id])) {
			$this->data[$setting_id] = fw_get_db_settings_option($setting_id, $default);
		}
		return $this->data[$setting_id]; 
	}
}