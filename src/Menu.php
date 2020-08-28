<?php

namespace HBF\Engine;

defined( 'ABSPATH' ) || exit;

class Menu {

    public function register( $location, $description ){
	 	register_nav_menu( $location, $description );
	}
}
