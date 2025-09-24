<?php
namespace HomeViet;

class Duplicate_Post {
	use \HomeViet\Singleton;

	protected function __construct() {
		add_filter( 'duplicate_post_enabled_post_types', [$this, 'duplicate_post_enabled_post_types'], 10, 1 );
	}

	public function duplicate_post_enabled_post_types($post_types) {

		debug_log($post_types);

		return $post_types;
	}

}