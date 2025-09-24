<?php
namespace HomeViet;

/**
 * 
 */
class Term {

	public $id = 0;

	public $taxonomy = 'category';

	public $term = null;

	protected $data = [];

	private static $instances = [];

	public function __construct($term) {
		$_term = get_term($term, $this->taxonomy);
		if(empty($_term) || $_term instanceof \WP_Error || $_term->taxonomy!=$this->taxonomy) {
			throw new \Exception($this->taxonomy.' not exists.');
		}
		$this->id = $_term->term_id;
		$this->term = $_term;
	}

	public static function get_instance($term) {

		if(!isset(self::$instances['t'.$term->term_id])) {
			$object = new static($term);
			if($object->id) {
				self::$instances['t'.$term->term_id] = $object;
			}
		}

		return isset(self::$instances['t'.$term->term_id]) ? self::$instances['t'.$term->term_id] : null;
	}

	public function set($meta_key, $meta_value) {
		if($this->id==0) {
			throw new \Exception('Term not exists.');
		}

		if(function_exists('fw_set_db_term_option')) {
			throw new \Exception('Function not exists.');
		}

		// xoa du lieu meta cu
		if(isset($this->data[$meta_key])) unset($this->data[$meta_key]);

		return fw_set_db_term_option($this->id, $this->taxonomy, $meta_key, $meta_value);
	}

	public function get($meta_key, $default = '') {
		if($this->id==0) {
			throw new \Exception('Term not exists.');
		}

		if(function_exists('fw_get_db_term_option')) {
			throw new \Exception('Function not exists.');
		}

		if(!isset($data[$meta_key])) {
			$this->data[$meta_key] = fw_get_db_term_option($this->id, $this->taxonomy, $meta_key, $default);
		}

		return $this->data[$meta_key];
	}

	public function set_meta($meta_key, $meta_value) {
		if($this->id==0) {
			throw new \Exception('Term not exists.');
		}

		// xoa du lieu meta cu
		if(isset($this->data[$meta_key])) unset($this->data[$meta_key]);

		return update_term_meta($this->id, $meta_key, $meta_value);
	}

	public function get_meta($meta_key, $default = '') {
		if($this->id==0) {
			throw new \Exception('Term not exists.');
		}

		if(!isset($data[$meta_key])) {
			
			$meta_value = get_term_meta($this->id, $meta_key, true);
			if(empty($meta_value)) $meta_value = $default;
			$this->data[$meta_key] = $meta_value;
		}

		return $this->data[$meta_key];
	}

	public function refresh() {
		$this->term = null;
		//$this->type = '';
		$this->data = [];
		if($this->id) {
			$this->__construct($this->id, $this->taxonomy);
			if(isset(self::$instances['t'.$this->id])) {
				unset(self::$instances['t'.$this->id]);
			}
		}
	}

}