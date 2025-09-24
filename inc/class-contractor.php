<?php
namespace HomeViet;

/**
 * 
 */
class Contractor extends Post {
	
	public $type = 'contractor';

	public $image_id = 0;

	public function __construct($post) {
		parent::__construct($post);

		if(has_post_thumbnail( $this->post )) {
			$this->image_id = get_post_thumbnail_id( $this->post );
		}
	}

	public function get_image($size='full', $attr = '') {
		$image = '';

		// if($this->get('images')) {
		// 	$image = wp_get_attachment_image( $this->get('images')[0]['attachment_id'], $size );
		// }

		if($this->image_id) {
			$image = wp_get_attachment_image( $this->image_id, $size, false, $attr );
		}

		return $image;
	}

	public function get_image_src($size='full') {
		$image_src = '';

		// if($this->get('images')) {
		// 	$image_srcs = wp_get_attachment_image_src( $this->get('images')[0]['attachment_id'], $size, false );
		// 	$image_src = $image_srcs[0];
		// }

		if($this->image_id) {
			$image_srcs = wp_get_attachment_image_src( $this->image_id, $size, false );
			$image_src = $image_srcs[0];
		}

		return $image_src;
	}

	public function get_image_file() {
		$image_file = '';

		// if($this->get('images')) {
		// 	$image_file = get_attached_file($this->get('images')[0]['attachment_id'], true);
		// }

		if($this->image_id) {
			$image_file = get_attached_file($this->image_id, true);
		}

		return $image_file;
	}

}