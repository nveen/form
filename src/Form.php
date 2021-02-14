<?php

namespace HBF\Form;
use HBF\Form\Validator;
use HBF\Form\AbstractClass\Base;

defined( 'ABSPATH' ) || exit;

require 'functions.php';
 
class Form extends Base  {

 	/**
 	 * [$instance description]
 	 * @var null
 	 */
    private static $instance = null;
 	

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
 		parent::__construct();

 		add_filter( "HBF/FORM/FIELD/ARGS", array( $this, "rearrange_field_args" ), 10, 2 );

 		add_filter( "HBF/FORM/START", array( $this, "form_start" ), 10, 2 );

 		add_filter( "HBF/FORM/START", array( $this, "parse_validation" ), 11, 2 );
 		add_filter( "HBF/FORM/END", array( $this, "form_end" ), 10, 2 );
 		
 		add_action( 'wp_enqueue_scripts', array( $this, 'load_form_scripts' ) );

 		add_action( "wp_ajax_hbf-form", array( $this, "handle_client_request" ) );
 		add_action( "wp_ajax_nopriv_hbf-form", array( $this, "handle_client_request" ) );
	}

	/**
	 * [handle_client_request description]
	 * @return [type] [description]
	 */
	public function handle_client_request( ) {
		$form_id = HBF()->request->get( $_POST, 'form_id' );
		$nonce = HBF()->request->get( $_POST, "hb-{$form_id}_nonce_field" );	

	   	if ( !wp_verify_nonce( $nonce, "hb-{$form_id}") ) {
	      	wp_send_json_error([
	      		'status' => true,
	      		'error'	=> true,
	      		'message'	=>	__( "No naughty business please", HBF ),
	      		'data'	=>	[]
	      	], 400);
	   	}   

	   	$fields = $this->get( $form_id )->get_fields();

	   	$validator = Validator::instance();
	   	
	   	$validator->make( HBF()->request->all(), $fields );

	   	if( !$validator->passed() ):
	   		wp_send_json_error([
	      		'status' => true,
	      		'error'	=> true,
	      		'message'	=> __( "You have errors in the fields below, please check each field throughly.", HBF ), 
	      		'data'	=> $validator->errors()
	      	], 400);
   		endif;

   		$this->execute_submit( );
	}

	/**
	 * [parse_validation description]
	 * @param  [type] $form   [description]
	 * @param  [type] $fields [description]
	 * @return [type]         [description]
	 */
	public function parse_validation( $form, $fields ) {
		$rules = [];

		foreach ($fields as $key => $field) {
			if( !isset( $field['validate'] ) || empty( $field['validate'] ) ) :
				continue;
			endif;

			$field_rules = [];

			foreach ( explode( "|", $field['validate'] ) as $rule) {
				$rule_value = explode( ":", $rule );
				$field_rules[$rule_value[0]] = isset($rule_value[1]) ? $rule_value[1] : true;
			} 

			$rules[ $field['name'] ] = $field_rules;
		} 

		wp_localize_script( 'form-main',   strtoupper( generate_form_id( $form ) )  , array( 
			'rules' => $rules, 
		) );
	}

	/**
	 * [load_form_scripts description]
	 * @return [type] [description]
	 */
	public function load_form_scripts( ) {
  		
  		wp_enqueue_script( 'jquery-ui-datepicker' );
	    wp_enqueue_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );

		wp_enqueue_script( 'jquery-validate', get_current_file_url() .'/assets/js/vendors.js', array("jquery"), '1.19.3', true );
		wp_enqueue_script( 'form-main', get_current_file_url() .'/assets/js/main.js' , array('jquery-validate'), '0.0.1', true );

		wp_localize_script( 'form-main', 'HBF', array( 
			'endpoint' => admin_url( 'admin-ajax.php' ),
			'validation_messages' =>  Validator::instance()->get_message_strings()
		) );
	} 

	/**
	 * [rearrange_field_args description]
	 * @param  [type] $field [description]
	 * @param  [type] $form  [description]
	 * @return [type]        [description]
	 */
	public function rearrange_field_args( $field, $form ) {

		if( empty( trim( $field['id']) ) ) :
			$field['id'] = $form .'-'. $field['name'];
		endif;

		switch ($field['type']) {
			case 'radio':
				$field['class'] = "form-check-input {$field['class']}"; 
				break;

			case 'checkbox':
				$field['class'] = "form-check-input {$field['class']}"; 
				break;

			case 'button':
				break;
			
			default:
				$field['class'] = "form-control {$field['class']}";
				break;
		}

		if( isset( $field['taxonomy'] ) ) :
			$field['options'] = get_taxonomy_terms( $attrs['taxonomy'] );
		endif;

		return $field;
	}

	/**
	 * [form_start description]
	 * @param  [type] $form [description]
	 * @return [type]       [description]
	 */
	public function form_start( $form ) {
		ob_start();

		echo sprintf( "<form class='%s' id='%s'>%s", 'hb-ajax-form', generate_form_id( $form ), wp_nonce_field( "hb-{$form}", "hb-{$form}_nonce_field", true, false ) );
		echo sprintf( "<input type='hidden' name='form_id' value='%s'/>", $form );
		echo sprintf( "<input type='hidden' name='action' value='hbf-form'/>");
		
		echo ob_get_clean();
	}

	/**
	 * [form_end description]
	 * @return [type] [description]
	 */
	public function form_end( ) {
		echo sprintf("</form>");
	}

}
 