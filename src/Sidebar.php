<?php

namespace HBF\Engine;

defined( 'ABSPATH' ) || exit;

class Sidebar {

    public function register( ARRAY $args ){
	 	register_sidebar( $args );
	}
}
