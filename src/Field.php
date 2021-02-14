<?php

namespace HBF\Form;
use HBF\Form\AbstractClass\Base;

defined( 'ABSPATH' ) || exit;

class Field {

	/**
	 * [$instance description]
	 * @var null
	 */
    private static $instance = null;

    /**
     * [$registered_fields description]
     * @var array
     */
    private $registered_fields = array( );
    	

    /**
     * [render description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
	public function render( $field ) {
		echo call_user_func( $this->load_field_callback( $field ), $field );
	}

	/**
	 * [register description]
	 * @param  [type] $name     [description]
	 * @param  [type] $callback [description]
	 * @return [type]           [description]
	 */
	public function register( $name, $callback ) {
		$this->registered_fields[ $name ] = $callback;
	}

	/**
	 * [load_field_callback description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	protected function load_field_callback( $field ) {
		if(isset( $this->registered_fields[$field['type']] ) ):
			$callback = $this->registered_fields[$field['type']];
		elseif ( method_exists( $this, $field['type']) ) :
			$callback = array( $this, $field['type'] );
		else:
			throw new \Exception( sprintf( __( "Could not find field with name %s", HBF), $field['type'] ), 102);
		endif;

		return $callback;
	}

	/**
	 * [text description]
	 * @param  [type] $attrs [description]
	 * @return [type]        [description]
	 */
	public function text( $attrs ) {
		$field = create_field_attr(shortcode_atts( array(
			'type'	=>	'',
			'id' => '',
			'name'	=>	'',
			'class'	=>	'',
			'disable' => '',
			'readonly'	=>	'',
			'attributes'	=>	[],
			'autocomplete'	=>	'off',
		), $attrs ));

		$field_markup = sprintf( '<input %s />', esc_attrs($field) );

   		return wrap_in_wrapper( $attrs,  $field_markup );
	}

	/**
	 * [email description]
	 * @param  [type] $attrs [description]
	 * @return [type]        [description]
	 */
	public function email( $attrs ) {
   		return $this->text( $attrs );
	}

	/**
	 * [password description]
	 * @param  [type] $attrs [description]
	 * @return [type]        [description]
	 */
	public function password( $attrs ) {
		return $this->text( $attrs );
	}

	/**
	 * [select description]
	 * @param  [type] $attrs [description]
	 * @return [type]        [description]
	 */
	public function select( $attrs ) {
		$field = create_field_attr(shortcode_atts( array(
			'id' => '',
			'name'	=>	'',
			'class'	=>	'',
			'attributes'	=>	[]
		), $attrs ) );

		$options = isset( $attrs['default_option'] ) ? sprintf( '<option value="">%s</option>', $attrs['default_option'] ) : "";

		foreach ( $attrs['options'] as $key => $option) {
			$options .= sprintf( '<option value="%s">%s</option>', $key, $option );
		}

		$field_markup = sprintf( '<select %s>%s</select>', esc_attrs($field), $options );

		return wrap_in_wrapper( $attrs, $field_markup);
	}

	/**
	 * [radio description]
	 * @param  [type] $attrs [description]
	 * @return [type]        [description]
	 */
	public function radio( $attrs ) {
		$field = create_field_attr(shortcode_atts( array(
			'type'	=>	'',
			'id' => '',
			'class'	=>	'',
			'name'	=>	'',
			'attributes'	=>	[]
		), $attrs ) );

		foreach ( $attrs['options'] as $key => $option) {
			$field['id'] = $attrs['name'] . '_'. $key;
			
			$options .= sprintf( '<div class="form-check"><input %s /> <label for="%s">%s</label></div>', 
			 	esc_attrs($field), 
			 	$field['id'],
			 	$option );
		}

		$field_markup = sprintf('<div class="form-check-group">%s</div>', $options );

		return wrap_in_wrapper( $attrs, $field_markup );
	}

	/**
	 * [checkbox description]
	 * @param  [type] $attrs [description]
	 * @return [type]        [description]
	 */
	public function checkbox( $attrs ) {
		$field = create_field_attr(shortcode_atts( array(
			'type'	=>	'checkbox',
			'id' => '',
			'name'	=>	'',
			'class'	=>	'',
			'attributes'	=>	[]
		), $attrs ) );

		foreach ( $attrs['options'] as $key => $option) {
			$field['id'] = $attrs['name'] . '_'. $key;
			
			$options .= sprintf( '<div class="form-check"><input %s /> <label for="%s">%s</label></div>', 
			 	esc_attrs($field), 
			 	$field['id'],
			 	$option );
		}

		$field_markup = sprintf( '<div class="form-check-group">%s</div>', $options );

		return wrap_in_wrapper( $attrs, $field_markup );
	}

	public function textarea( $attrs ) {
		$field = create_field_attr(shortcode_atts( array(
			'id' => '',
			'name'	=>	'',
			'class'	=>	'',
			'rows'	=>	4, 
			'cols' => 50,
			'attributes'	=>	[]
		), $attrs ) );

		$field_markup = sprintf( '<textarea %s>%s</textarea>', esc_attrs($field), "" );

		return wrap_in_wrapper( $attrs, $field_markup );
	}

	public function switch( $attrs ) {
		$field = create_field_attr(shortcode_atts( array(
			'id' => '',
			'name'	=>	'',
			'class'	=>	'',
			'autocomplete'	=>	'off',
			'attributes'	=>	[]
		), $attrs ) );
		
		$field['type'] = 'checkbox';
		$field['class'] =  'custom-control-input';

		$field_markup = sprintf('<div class="custom-control custom-switch"> <input %s> <label class="custom-control-label" for="%s">%s</label></div>', esc_attrs($field), $attrs['id'], $attrs['label'] );
		
		unset($attrs['label']);
	 
		return wrap_in_wrapper( $attrs, $field_markup );
	}

	/**
	 * [submit description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function submit( $field ) {
		$attrs = shortcode_atts( array(
			'type' => 'submit'
		), $field );
			
		return sprintf( '<button %s >%s</button>', 
				esc_attrs($attrs), 
				isset($field['label']) ? esc_attr($field['label']) : __("Submit", HBF) 
			);
	}

 }