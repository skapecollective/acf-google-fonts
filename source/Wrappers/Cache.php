<?php

namespace AcfGoogleFonts\Wrappers;

/**
 * @link  https://codex.wordpress.org/Class_Reference/WP_Object_Cache
 */
class Cache {

	public static $storageGroup = 'AcfGoogleFonts';

	/**
	 * Retrieves the WP cache contents from the cache by key and group
	 *
	 * @param  string   $key   [description]
	 * @param  boolean  $found [description]
	 * @return mixed
	 */
	public static function get( string $key, bool $found = false ) {

		$_found = false;

		$data = wp_cache_get( $key, self::$storageGroup, false, $_found );

		if ( $found ) {
			return (object)[
				'data' => $data,
				'found' => $_found
			];
		}

		return $data;
	}

	/**
	 * Saves the data to the WP cache
	 *
	 * @param  string   $key    [description]
	 * @param  mixed    $data   [description]
	 * @param  integer  $expire When to expire the cache contents, in seconds. Default 0 (no expiration).
	 * @return boolean
	 */
	public static function set( string $key, $data, int $expire = 0 ) {
		return wp_cache_set( $key, $data, self::$storageGroup, $expire );
	}

	/**
	 * Removes the WP cache contents matching key and group.
	 *
	 * @param  string $key [description]
	 * @return boolean     True on successful removal, false on failure.
	 */
	public static function delete( string $key ) {
		return wp_cache_delete( $key, self::$storageGroup );
	}

	/**
	 * Conditionally get/set data to cache
	 *
	 * @param  string   $key      [description]
	 * @param  callable $callback [description]
	 * @param  mixed    $default  [description]
	 * @return mixed
	 */
	public static function conditional( string $key, callable $callback, $default = null ) {

		// Get cached status object.
		$cached = self::get( $key, true );

		// If was found in cache, return value. Supports boolean values.
		if ( $cached->found ) {
			return $cached->data;
		}

		// Call callback, should return data to cache.
		$data = call_user_func( $callback );

		// Store data in cache.
		if ( self::set( $key, $data ) ) {
			return $data;
		}

		// Return default value.
		return $default;
	}

}
