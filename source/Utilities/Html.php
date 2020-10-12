<?php

namespace AcfGoogleFonts\Utilities;

class Html {

	/**
	 * Inline array of classes into a single line ready for printing
	 *
	 * @param array|string $classes
	 * @return string
	 */
	public static function inlineClasses( $classes, string $prefix = '' ) {

		/**
		 * Convert string to array.
		 */
		if ( is_string( $classes ) ) {
			$classes = preg_split( '/\s/', $classes );
		}

		/**
		 * Bail early if classes is anything but an array.
		 */
		if ( !is_array( $classes ) ) {
			return null;
		}

		// For each item in `$classes` array
		$classes = array_map( function( $item ) use ( $prefix ) {

			// Check if item has multiple classes in one
			$items = preg_split( '/\s+/' , $item );

			// Sanitize prefixed values for DOM
			$items = array_map( function( $value ) use ( $prefix ) {
				return sanitize_html_class( $prefix . $value );
			}, $items );

			// Return inlined string
			return implode( ' ', $items );

		}, array_filter( $classes ) );

		// Implode values into a single string
		$classes = implode( ' ', array_filter( $classes ) );

		// Remove double white space
		$classes = preg_replace( '/\s{2,}/', ' ', $classes );

		// Trim any extra white space
		$classes = trim( $classes );

		return $classes;
	}

}
