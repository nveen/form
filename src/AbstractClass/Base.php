<?php

namespace HBF\Form\AbstractClass;
use HBF\Form\Field;

defined( 'ABSPATH' ) || exit;
 
abstract class Base {
	
	/**
	 * [$forms description]
	 * @var [type]
	 */
	public $forms;

	/**
	 * [__construct description]
	 */
	public function __construct( ) {
		$this->field = new Field();
	}

	/**
	 * [register description]
	 * @param  [type] $key    [description]
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function register( $key, $config ) {
		$this->form = $key;
		$this->forms[$key]['fields'] = $config;
		return $this;
	}

	/**
	 * [get description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get( $key ) {
		if( !isset( $this->forms[$key]) ):
			throw new \Exception("Form $key does not exist.", 101);
		endif;
		
		$this->form = $key;
		return $this;
	}

	/**
	 * [get_fields description]
	 * @return [type] [description]
	 */
	protected function get_fields( ) {
		return call_user_func( $this->forms[$this->form]['fields'] );
	}

	/**
	 * [onSubmit description]
	 * @param  [type] $callback [description]
	 * @return [type]           [description]
	 */
	public function onSubmit( $callback ) {
		$this->forms[$this->form]['on_submit'] = $callback;
	}

	/**
	 * [execute_submit description]
	 * @return [type] [description]
	 */
	public function execute_submit() {
		call_user_func( $this->forms[$this->form]['on_submit'] );
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	public function render( ) {
		$fields = call_user_func( $this->forms[$this->form]['fields'] );

		do_action("HBF/FORM/START", $this->form, $fields );
			
		$fields = call_user_func($this->forms[$this->form]['fields']);

		foreach ( $fields  as $key => $field) {
			call_user_func( array( $this->field, 'render'),  apply_filters( "HBF/FORM/FIELD/ARGS",  $field, $this->form ) );
		}

		do_action("HBF/FORM/END", $this->form,  $fields );
	}
}


