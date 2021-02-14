<?php

namespace HBF\Form;

defined( 'ABSPATH' ) || exit;
 
class Validator {
 	
 	/**
 	 * [$errors description]
 	 * @var array
 	 */
	private $errors = [];

	/**
	 * [$instance description]
	 * @var null
	 */
	private static $instance = null;

	/**
	 * [$rules description]
	 * @var array
	 */
	private $rules = [];
 	
 	/**
 	 * [instance description]
 	 * @return [type] [description]
 	 */
    public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * [__construct description]
	 */
	public function __construct( ) {
		$this->messages = [
			'required'	=>	__( 'This field is required.', HBF ),
			'email' => __( "Please enter a valid email address.", HBF ),
			'url' => __( "Please enter a valid URL.", HBF ),
			'date' => __( "Please enter a valid date.", HBF ),
			'digits' => __( "Please enter only digits.", HBF ),
			'sameTo' => __( "Please enter the same value again.", HBF ),
			'maxlength' => __( "Please enter no more than {0} characters.", HBF ),
			'minlength' => __( "Please enter at least {0} characters.", HBF ),
			'max' => __( "Please enter a value less than or equal to {0}.", HBF ),
			'min' => __( "Please enter a value greater than or equal to {0}.", HBF ),
		];
	}
 	
 	/**
 	 * [make description]
 	 * @param  [type] $request [description]
 	 * @param  [type] $fields  [description]
 	 * @return [type]          [description]
 	 */
	public function make( $request, $fields ) {
		
		$this->request = $request;

		foreach ($fields as $key => $field) {
			
			if( empty( $field['validate'] ) ):
				continue;
			endif;

			$this->set_field( $field )->validate_request( );
		}
	}

	/**
	 * [add_method description]
	 * @param [type] $name     [description]
	 * @param [type] $callback [description]
	 * @param [type] $message  [description]
	 */
	public function add_method( $name, $callback, $message ) {
		$this->messages[$name] = $message;
		$this->rules[$name] = $callback;
	}

	/**
	 * [required description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function required( $value  ) {
		return !empty( trim( $value ) );
	}

	/**
	 * [email description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function email( $value ) {
		return  filter_var( $value, FILTER_VALIDATE_EMAIL );
	}

	/**
	 * [url description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function url( $value ) {
		return filter_var($value, FILTER_VALIDATE_URL);
	}

	/**
	 * [date description]
	 * @param  [type] $value [description]
	 * @param  [type] $arg  [description]
	 * @return [type]        [description]
	 */
	public function date( $value, $arg ) {
		return date_create_from_format( $arg, $value ) !== false;
	}

	/**
	 * [digits description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function digits( $value ) {
		return is_numeric( $value );
	}

	/**
	 * [sameTo description]
	 * @param  [type] $value [description]
	 * @param  [type] $arg   [description]
	 * @return [type]        [description]
	 */
	public function sameTo( $value, $arg ) {
		return trim( $value ) == HBF()->request->get( $_POST, $arg );
	}

	/**
	 * [maxlength description]
	 * @param  [type] $value [description]
	 * @param  [type] $arg   [description]
	 * @return [type]        [description]
	 */
	public function maxlength( $value, $arg ) {
		return strlen($value) <= $arg;
	}

	/**
	 * [minlength description]
	 * @param  [type] $value [description]
	 * @param  [type] $arg   [description]
	 * @return [type]        [description]
	 */
	public function minlength( $value, $arg ) {
		return strlen($value) >= $arg;
	}

	/**
	 * [max description]
	 * @param  [type] $value [description]
	 * @param  [type] $arg   [description]
	 * @return [type]        [description]
	 */
	public function max( $value, $arg ) {
		return $value <= $arg;
	}

	/**
	 * [min description]
	 * @param  [type] $value [description]
	 * @param  [type] $arg   [description]
	 * @return [type]        [description]
	 */
	public function min( $value, $arg ) {
		return $value >= $arg;
	}

	/**
	 * [passed description]
	 * @return [type] [description]
	 */
	public function passed( ) {
		return empty( $this->errors );
	}

	/**
	 * [errors description]
	 * @return [type] [description]
	 */
	public function errors( ) {
		return $this->errors;
	}	

	/**
	 * [get_message_strings description]
	 * @return [type] [description]
	 */
	public function get_message_strings( ) {
		return $this->messages;
	}

	/**
	 * [set_field description]
	 * @param [type] $field [description]
	 */
	private function set_field( $field ) {
		$field['value'] = trim( $this->request[$field['name']] );
		$this->field = $field;

		return $this;
	}

	/**
	 * [validate_request description]
	 * @return [type] [description]
	 */
	private function validate_request( ) {
		$rules = explode( "|", $this->field['validate'] );

		foreach ($rules as $rule) {

			$rule_pair = explode( ":", $rule );
			
		 	if( $this->execute_rule( $rule_pair ) == false ) {
		 		$this->errors[$this->field['name']] = str_replace( "{0}", $rule_pair[1], $this->messages[$rule_pair[0]] );
		 		break;
		 	}
		}
	}

	/**
	 * [execute_rule description]
	 * @param  [type] $rule_pair [description]
	 * @return [type]            [description]
	 */
	private function execute_rule( $rule_pair ) {
		$callback = $this->rules[$rule_pair[0]] ? $this->rules[$rule_pair[0]] : array( $this, $rule_pair[0] );
		return call_user_func( $callback, $this->request[$this->field['name']], $rule_pair[1] );
	}

}
 