<?php
namespace HomeViet;

/**
 * 
 */
class Project extends Term {
	
	public $taxonomy = 'project';

	public function remove_local_contractor($contractor) {
		$contractors = $this->get_local_contractors();
		if(in_array($contractor, $contractors)) {
			unset($contractors[array_search($contractor, $contractors)]);
			$this->set_local_contractors($contractors);
		}

		return true;
	}

	public function add_local_contractor($contractor) {

		$contractors = $this->get_local_contractors();
		if(!in_array($contractor, $contractors)) {
			$contractors[] = $contractor;
			$this->set_local_contractors($contractors);
		}

		return true;
	}

	public function set_local_contractors($contractors) {
		return $this->set_meta('local_contractors', $contractors);
	}

	public function get_local_contractors() {
		return $this->get_meta('local_contractors', []);
	}

	public function remove_contractor($contractor) {
		$contractors = $this->get_contractors();
		if(in_array($contractor, $contractors)) {
			unset($contractors[array_search($contractor, $contractors)]);
			$this->set_contractors($contractors);
		}

		return true;
	}

	public function add_contractor($contractor) {

		$contractors = $this->get_contractors();
		if(!in_array($contractor, $contractors)) {
			$contractors[] = $contractor;
			$this->set_contractors($contractors);
		}

		return true;
	}

	public function set_contractors($contractors) {
		return $this->set_meta('contractors', $contractors);
	}

	public function get_contractors() {
		return $this->get_meta('contractors', []);
	}
}