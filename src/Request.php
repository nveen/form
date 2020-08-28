<?php

namespace HBF\Engine;

defined( 'ABSPATH' ) || exit;

class Request {

    public function has( Array $data, String $key ) {
       return isset($data[$key]);
	}

	public function get( Array $data, String $key ) {
        if( $this->has( $data, $key) ) :
            return $this->clean( $data[$key] );
        endif;          
	}

    public function all( ) {
        return $this->clean( $_REQUEST );
    }

    private function clean( $data ) {

        if ( is_array($data) ) {
            foreach ( $data as $key => $value ) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean( $value );
            }
        } else {
            $data = trim( htmlspecialchars( $data, ENT_COMPAT, 'UTF-8') );
        }

        return $data;
    }
}
