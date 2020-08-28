<?php

namespace HBF\Engine;

defined( 'ABSPATH' ) || exit;

class Repository {

	private $data = array();
 
	public function get( $key ) {
		return ( isset( $this->data[$key] ) ? $this->data[$key] : null );
	}
 
	public function set( $key, $value ) {
		$this->data[$key] = $value;
	}
}
