<?php

namespace Engine;
use Engine\AbstractClass\Base;

defined( 'ABSPATH' ) || exit;
 
class HBF extends Base {
 
    private static $instance = null;
 
    public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( (new Repository( )) );
		}
		return self::$instance;
	}

	public function __construct( Repository $repository ) {
 		$this->repository = $repository;

 		$this->head = new Head( );
 		$this->menu = new Menu( );
 		$this->request = new Request( );
 		$this->sidebar = new Sidebar( );
 		$this->template = new Template( );
	}

}
 