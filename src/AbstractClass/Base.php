<?php

namespace HBF\Engine\AbstractClass;

defined( 'ABSPATH' ) || exit;
 
abstract class Base {
	
	public $repository;

	public function __get( $key ) {
		return $this->repository->get( $key );
	}
}


