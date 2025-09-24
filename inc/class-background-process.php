<?php
namespace HomeViet;

class Background_Process {

	public static function delete_texture_images_process($data) {
		$retry_count = isset( $data['retry_count'] ) ? intval( $data['retry_count'] ) : 0;
		try {
			//debug_log($data);
			if($data['images']) {
				foreach ($data['images'] as $id) {
					wp_delete_attachment( $id, true );
				}
			}
		} catch (\Exception $e) {
			if ( $retry_count < 5 ) {
				$data['retry_count'] = $retry_count + 1;
				as_enqueue_async_action('delete_texture_images_process', [$data], 'texture');
			} else {

			}
		}
	}
}