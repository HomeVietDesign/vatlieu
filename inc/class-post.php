<?php
namespace HomeViet;

/**
 * 
 */
class Post {

	public $id = 0;

	public $type = 'post';

	public $post = null;

	protected $data = [];

	private static $instances = [];

	public function __construct($post) {
		$_post = get_post($post);
		//debug_log($this);
		if($_post && $_post->post_type==$this->type) {
			$this->id = $_post->ID;
			$this->post = $_post;
		}
	}

	public static function get_instance($id) {

		if(!isset(self::$instances['p'.$id])) {
			$object = new static($id);
			if($object->id) {
				self::$instances['p'.$id] = $object;
			}
		}

		return isset(self::$instances['p'.$id])?self::$instances['p'.$id]:null;

	}

	public function set($meta_key, $meta_value) {
		if($this->id==0) {
			throw new \Exception('Post not exists.');
		}

		if(!function_exists('fw_set_db_post_option')) {
			throw new \Exception('Function not exists.');
		}

		// xoa du lieu meta cu
		if(isset($this->data[$meta_key])) unset($this->data[$meta_key]);

		return fw_set_db_post_option($this->id, $meta_key, $meta_value);
		
	}

	public function get($meta_key, $default = '') {
		if($this->id==0) {
			throw new \Exception('Post not exists.');
		}

		if(!function_exists('fw_get_db_post_option')) {
			throw new \Exception('Function not exists.');
		}

		if(!isset($this->data[$meta_key])) {
			$this->data[$meta_key] = fw_get_db_post_option($this->id, $meta_key, $default);
		}

		return $this->data[$meta_key];
	}

	public function set_meta($meta_key, $meta_value) {
		if($this->id==0) {
			throw new \Exception('Post not exists.');
		}

		// xoa du lieu meta cu
		if(isset($this->data[$meta_key])) unset($this->data[$meta_key]);

		return update_post_meta($this->id, $meta_key, $meta_value);

	}

	public function get_meta($meta_key, $default = '') {
		if($this->id==0) {
			throw new \Exception('Post not exists.');
		}

		if(!isset($this->data[$meta_key])) {
			$meta_value = get_post_meta($this->id, $meta_key, true);
			if(empty($meta_value)) $meta_value = $default;
			$this->data[$meta_key] = $meta_value;
		}

		return $this->data[$meta_key];
	}

	public function refresh() {
		$this->post = null;
		$this->data = [];
		if($this->id) {
			$this->__construct($this->id);
			if(isset(self::$instances['p'.$this->id])) {
				unset(self::$instances['p'.$this->id]);
			}
		}
	}

	public function get_id() {
		return $this->id;
	}

	public function id() {
		return $this->id;
	}
}