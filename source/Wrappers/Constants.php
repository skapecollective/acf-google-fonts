<?php

namespace AcfGoogleFonts\Wrappers;

use AcfGoogleFonts\Contracts\Prefixer;

class Constants {

	use Prefixer;

	/**
	 * Conditionally register a constant
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public static function set( string $key, $value ) {
		$key = self::prefix( $key, 'strtoupper' );

		if ( !defined( $key ) ) {
			define( $key, $value );
		}
	}

	/**
	 * Get a prefixed contstant
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get( string $key, $default = null ) {
		$key = self::prefix( $key, 'strtoupper' );

		if ( defined( $key ) ) {
			return constant( $key );
		}

		return $default;
	}

}
