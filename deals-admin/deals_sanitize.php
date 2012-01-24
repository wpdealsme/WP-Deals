<?php

/* Text */

add_filter( 'deals_sanitize_text', 'sanitize_text_field' );

/* Textarea */

function deals_sanitize_textarea($input) {
	global $allowedposttags;
	$output = wp_kses( $input, $allowedposttags);
	return $output;
}

add_filter( 'deals_sanitize_textarea', 'deals_sanitize_textarea' );

/* Info */

add_filter( 'deals_sanitize_info', 'deals_sanitize_allowedposttags' );

/* Select */

add_filter( 'deals_sanitize_select', 'deals_sanitize_enum', 10, 2);

/* Radio */

add_filter( 'deals_sanitize_radio', 'deals_sanitize_enum', 10, 2);

/* Images */

add_filter( 'deals_sanitize_images', 'deals_sanitize_enum', 10, 2);

/* Checkbox */

function deals_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = "1";
	} else {
		$output = "0";
	}
	return $output;
}
add_filter( 'deals_sanitize_checkbox', 'deals_sanitize_checkbox' );

/* Multicheck */

function deals_sanitize_multicheck( $input, $option ) {
	$output = '';
	if ( is_array( $input ) ) {
		foreach( $option['options'] as $key => $value ) {
			$output[$key] = "0";
		}
		foreach( $input as $key => $value ) {
			if ( array_key_exists( $key, $option['options'] ) && $value ) {
				$output[$key] = "1"; 
			}
		}
	}
	return $output;
}
add_filter( 'deals_sanitize_multicheck', 'deals_sanitize_multicheck', 10, 2 );

/* Color Picker */

add_filter( 'deals_sanitize_color', 'deals_sanitize_hex' );

/* Uploader */

function deals_sanitize_upload( $input ) {
	$output = '';
	$filetype = wp_check_filetype($input);
	if ( $filetype["ext"] ) {
		$output = $input;
	}
	return $output;
}
add_filter( 'deals_sanitize_upload', 'deals_sanitize_upload' );

/* Allowed Tags */

function deals_sanitize_allowedtags($input) {
	global $allowedtags;
	$output = wpautop(wp_kses( $input, $allowedtags));
	return $output;
}

add_filter( 'deals_sanitize_info', 'deals_sanitize_allowedtags' );

/* Allowed Post Tags */

function deals_sanitize_allowedposttags($input) {
	global $allowedposttags;
	$output = wpautop(wp_kses( $input, $allowedposttags));
	return $output;
}

add_filter( 'deals_sanitize_info', 'deals_sanitize_allowedposttags' );


/* Check that the key value sent is valid */

function deals_sanitize_enum( $input, $option ) {
	$output = '';
	if ( array_key_exists( $input, $option['options'] ) ) {
		$output = $input;
	}
	return $output;
}

/* Background */

function deals_sanitize_background( $input ) {
	$output = wp_parse_args( $input, array(
		'color' => '',
		'image'  => '',
		'repeat'  => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	) );

	$output['color'] = apply_filters( 'deals_sanitize_hex', $input['color'] );
	$output['image'] = apply_filters( 'deals_sanitize_upload', $input['image'] );
	$output['repeat'] = apply_filters( 'deals_background_repeat', $input['repeat'] );
	$output['position'] = apply_filters( 'deals_background_position', $input['position'] );
	$output['attachment'] = apply_filters( 'deals_background_attachment', $input['attachment'] );

	return $output;
}
add_filter( 'deals_sanitize_background', 'deals_sanitize_background' );

function deals_sanitize_background_repeat( $value ) {
	$recognized = deals_recognized_background_repeat();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'deals_default_background_repeat', current( $recognized ) );
}
add_filter( 'deals_background_repeat', 'deals_sanitize_background_repeat' );

function deals_sanitize_background_position( $value ) {
	$recognized = deals_recognized_background_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'deals_default_background_position', current( $recognized ) );
}
add_filter( 'deals_background_position', 'deals_sanitize_background_position' );

function deals_sanitize_background_attachment( $value ) {
	$recognized = deals_recognized_background_attachment();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'deals_default_background_attachment', current( $recognized ) );
}
add_filter( 'deals_background_attachment', 'deals_sanitize_background_attachment' );


/* Size */

function deals_sanitize_size( $input ) {
	$output = wp_parse_args( $input, array(
		'width'  => 100,
		'heigth' => 100
	) );

	$output['width']  = apply_filters( 'deals_image_width', $output['width'] );
	$output['height']  = apply_filters( 'deals_image_height', $output['height'] );

	return $output;
}
add_filter( 'deals_sanitize_size', 'deals_sanitize_size' );


/* Typography */

function deals_sanitize_typography( $input ) {
	$output = wp_parse_args( $input, array(
		'size'  => '',
		'face'  => '',
		'style' => '',
		'color' => ''
	) );

	$output['size']  = apply_filters( 'deals_font_size', $output['size'] );
	$output['face']  = apply_filters( 'deals_font_face', $output['face'] );
	$output['style'] = apply_filters( 'deals_font_style', $output['style'] );
	$output['color'] = apply_filters( 'deals_color', $output['color'] );

	return $output;
}
add_filter( 'deals_sanitize_typography', 'deals_sanitize_typography' );


function deals_sanitize_font_size( $value ) {
	$recognized = deals_recognized_font_sizes();
	$value = preg_replace('/px/','', $value);
	if ( in_array( (int) $value, $recognized ) ) {
		return (int) $value;
	}
	return (int) apply_filters( 'deals_default_font_size', $recognized );
}
add_filter( 'deals_font_face', 'deals_sanitize_font_face' );


function deals_sanitize_font_style( $value ) {
	$recognized = deals_recognized_font_styles();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'deals_default_font_style', current( $recognized ) );
}
add_filter( 'deals_font_style', 'deals_sanitize_font_style' );


function deals_sanitize_font_face( $value ) {
	$recognized = deals_recognized_font_faces();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'deals_default_font_face', current( $recognized ) );
}
add_filter( 'deals_font_face', 'deals_sanitize_font_face' );

/**
 * Get recognized background repeat settings
 *
 * @return   array
 *
 */
function deals_recognized_background_repeat() {
	$default = array(
		'no-repeat' => 'No Repeat',
		'repeat-x'  => 'Repeat Horizontally',
		'repeat-y'  => 'Repeat Vertically',
		'repeat'    => 'Repeat All',
		);
	return apply_filters( 'deals_recognized_background_repeat', $default );
}

/**
 * Get recognized background positions
 *
 * @return   array
 *
 */
function deals_recognized_background_position() {
	$default = array(
		'top left'      => 'Top Left',
		'top center'    => 'Top Center',
		'top right'     => 'Top Right',
		'center left'   => 'Middle Left',
		'center center' => 'Middle Center',
		'center right'  => 'Middle Right',
		'bottom left'   => 'Bottom Left',
		'bottom center' => 'Bottom Center',
		'bottom right'  => 'Bottom Right'
		);
	return apply_filters( 'deals_recognized_background_position', $default );
}

/**
 * Get recognized background attachment
 *
 * @return   array
 *
 */
function deals_recognized_background_attachment() {
	$default = array(
		'scroll' => 'Scroll Normally',
		'fixed'  => 'Fixed in Place'
		);
	return apply_filters( 'deals_recognized_background_attachment', $default );
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */
 
function deals_sanitize_hex( $hex, $default = '' ) {
	if ( deals_validate_hex( $hex ) ) {
		return $hex;
	}
	return $default;
}

/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */
 
function deals_recognized_font_sizes() {
	$sizes = range( 9, 71 );
	$sizes = apply_filters( 'deals_recognized_font_sizes', $sizes );
	$sizes = array_map( 'absint', $sizes );
	return $sizes;
}

/**
 * Get recognized font faces.
 *
 * Returns an array of all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function deals_recognized_font_faces() {
	$default = array(
		'arial'     => 'Arial',
		'verdana'   => 'Verdana, Geneva',
		'trebuchet' => 'Trebuchet',
		'georgia'   => 'Georgia',
		'times'     => 'Times New Roman',
		'tahoma'    => 'Tahoma, Geneva',
		'palatino'  => 'Palatino',
		'helvetica' => 'Helvetica*'
		);
	return apply_filters( 'deals_recognized_font_faces', $default );
}

/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function deals_recognized_font_styles() {
	$default = array(
		'normal'      => 'Normal',
		'italic'      => 'Italic',
		'bold'        => 'Bold',
		'bold italic' => 'Bold Italic'
		);
	return apply_filters( 'deals_recognized_font_styles', $default );
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */
 
function deals_validate_hex( $hex ) {
	$hex = trim( $hex );
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return false;
	}
	else {
		return true;
	}
}