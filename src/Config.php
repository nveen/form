<?php

namespace HBF\Engine;

defined( 'ABSPATH' ) || exit;

class Config {

    public function set( String $key, $value ) {
        if( !$this->isset( $key) ) :
   		   define( $key, $value );
        endif;
	}

	public function get( String $key ) {
        if( $this->isset( $key) ) :
            return constant($key);
        endif;          
	}

    public function isset( String $key ) {
        return defined( $key );
    }
}
