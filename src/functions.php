<?php 

if( !function_exists( "wrap_in_wrapper" )):
	/**
	 * [wrap_in_wrapper description]
	 * @param  [type] $field [description]
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	function wrap_in_wrapper( $field, $input ) {
		$html = "<div class='form-group'>";
			$html .= isset($field['label']) ? "<label for=". esc_attr($field['id'])  .">" . esc_attr($field['label']) . "</label>" :  "";
			$html .= $input;
		return $html .= "</div>";
	}
endif;

if( !function_exists( "esc_attrs" )):

	/**
	 * [esc_attrs description]
	 * @param  [type] $attrs [description]
	 * @return [type]        [description]
	 */
	function esc_attrs( $attrs ) {
		$html = '';
		foreach( $attrs as $k => $v ) {
			if( is_string($v) ) {
				$v = trim($v);
			} elseif( is_array($v) || is_object($v) ) {
				$v = json_encode($v);
			}
			if(empty( $v) ) { continue; }
			$html .= sprintf( ' %s="%s"', esc_attr($k), esc_attr($v) ) ;
		}
		return $html;
	}

endif;

if( !function_exists( "get_taxonomy_terms" )):

	/**
	 * [get_taxonomy_terms description]
	 * @param  [type] $taxonomy [description]
	 * @return [type]           [description]
	 */
	function get_taxonomy_terms ( $taxonomy ) {
		$terms = get_terms( array(
		    'taxonomy' => $taxonomy,
		    'hide_empty' => false,
		) );
		
		$options = [];

		foreach ($terms as $key => $term) {
			$options[ $term->term_id ] = $term->name;
		}

		return $options;
	}

endif;

if( !function_exists( "generate_form_id" )):

	/**
	 * [generate_form_id description]
	 * @param  [type] $form [description]
	 * @return [type]       [description]
	 */
	function generate_form_id ( $form ) {
		 return  preg_replace( "/[^a-zA-Z]+/", "", $form );
	}

endif;

if( !function_exists( "get_current_file_url" )):

	/**
	 * [get_current_file_url description]
	 * @return [type] [description]
	 */
	function get_current_file_url() {
		$protocol = is_ssl() ? 'https://' : 'http://';
	   	return $protocol.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(__DIR__)); 
	}

endif;
 

function create_field_attr( $attrs ) {
	
	if(!empty($attrs['attributes'])):
		foreach ($attrs['attributes'] as $key => $value) {
			$attrs["data-{$key}"] = $value;
		}
	endif;

	unset($attrs['attributes']);

	return $attrs;
}
  