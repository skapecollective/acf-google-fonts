<?php

namespace AcfGoogleFonts\Wrappers;

use AcfGoogleFonts\Contracts\Prefixer;

class Options {

	use Prefixer;

	/**
	 * Prefix the option key.
	 *
	 * @param string $key
	 * @return string
	 */
	public static function key( string $key ) {
		return self::prefix( $key );
	}

	/**
	 * @param string $name
	 * @param null $default
	 *
	 * @return false|mixed|void
	 */
	public static function get( string $name, $default = null ) {
		return get_option( self::key( $name ), $default );
	}

	/**
	 * @param string $name
	 * @param $value
	 * @param bool $autoload
	 *
	 * @return bool
	 */
	public static function update( string $name, $value, bool $autoload = true ) {
		return update_option( self::key( $name ), $value, $autoload );
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param bool $autoload
	 *
	 * @return bool
	 */
	public static function add( string $name, $value, bool $autoload = true ) {
		/**
		 * @link https://developer.wordpress.org/reference/functions/add_option/
		 */
		return add_option( self::key( $name ), $value, '', $autoload );
	}

}
