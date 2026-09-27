<?php

/**
 * Look and feel settings for the search form.
 *
 * Stored under odr_rruff_search_plugin_options['appearance'] and applied to the
 * public form as CSS custom properties (--odr-search-*), which the public
 * stylesheet reads with the original colors as fallbacks.
 *
 * @link       https://opendatarepository.org
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/includes
 */
class Odr_Rruff_Search_Appearance {

	/**
	 * Color settings: key => [label, default].
	 */
	const COLORS = array(
		'form_bg'      => array( 'Form background', '#3c6e99' ),
		'text'         => array( 'Labels and text', '#ffffff' ),
		'link'         => array( 'Links (Mineral, Chemistry)', '#ffffff' ),
		'input_bg'     => array( 'Input background', '#ffffff' ),
		'input_text'   => array( 'Input text', '#475569' ),
		'input_border' => array( 'Input border', '#d1d5db' ),
		'button_bg'    => array( 'Button background', '#52ab75' ),
		'button_text'  => array( 'Button text', '#ffffff' ),
	);

	/**
	 * Corner radius settings in px: key => [label, default].
	 */
	const RADII = array(
		'form_radius'   => array( 'Form corners', 18 ),
		'input_radius'  => array( 'Input corners', 4 ),
		'button_radius' => array( 'Button corners', 5 ),
	);

	const MAX_RADIUS = 60;

	public static function defaults() {
		$defaults = array();
		foreach ( self::COLORS as $key => $def )
			$defaults[ $key ] = $def[1];
		foreach ( self::RADII as $key => $def )
			$defaults[ $key ] = $def[1];
		return $defaults;
	}

	/**
	 * Returns a complete set of values; anything missing or invalid falls back
	 * to its default.
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$values = array();
		foreach ( self::COLORS as $key => $def ) {
			$color = isset( $input[ $key ] ) ? sanitize_hex_color( trim( (string) $input[ $key ] ) ) : null;
			$values[ $key ] = $color ? strtolower( $color ) : $def[1];
		}
		foreach ( self::RADII as $key => $def ) {
			$radius = isset( $input[ $key ] ) && is_numeric( $input[ $key ] ) ? (int) $input[ $key ] : $def[1];
			$values[ $key ] = max( 0, min( self::MAX_RADIUS, $radius ) );
		}
		return $values;
	}

	/**
	 * Inline CSS custom properties for the saved appearance.
	 */
	public static function css_vars( $options ) {
		$values = self::sanitize( isset( $options['appearance'] ) ? $options['appearance'] : array() );
		$css = array();
		foreach ( self::COLORS as $key => $def )
			$css[] = '--odr-search-' . str_replace( '_', '-', $key ) . ': ' . $values[ $key ];
		foreach ( self::RADII as $key => $def )
			$css[] = '--odr-search-' . str_replace( '_', '-', $key ) . ': ' . $values[ $key ] . 'px';
		return implode( '; ', $css ) . ';';
	}
}
