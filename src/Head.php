<?php

namespace HBF\Engine;

defined( 'ABSPATH' ) || exit;

class Head {
 
    public function add_script( $handle, $src, $deps, $ver, $in_footer ) {
   		add_action( 'wp_enqueue_scripts', function( ) use( $handle, $src, $deps, $ver, $in_footer) {
            wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
        } );  
	}

	public function add_style( $handle, $src, $deps, $ver, $media = "all" ) {
        add_action( 'wp_enqueue_scripts', function( ) use( $handle, $src, $deps, $ver, $media) {
            wp_enqueue_style( $handle, $src, $deps, $ver, $media );
        } );      
	}
}


