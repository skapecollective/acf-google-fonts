<?php

namespace AcfGoogleFonts\Controllers;

use AcfGoogleFonts\Models\Font;
use AcfGoogleFonts\Wrappers\Cache;
use AcfGoogleFonts\Wrappers\Constants;
use AcfGoogleFonts\Wrappers\Options;

use Exception;
use WP_Error;

class Google {


	/**
	 * @var string The key used for storing/retreiving the API key from the database.
	 */
	const apiKeyOption = 'api_key';

	/**
	 * @var string The name of cache file including extension.
	 */
	const cacheFileName = 'api-cached-response.json';

	/**
	 * @var string The full Google Fonts API endpoint without params.
	 */
	const endpoint = 'https://www.googleapis.com/webfonts/v1/webfonts';

	/**
	 * @var null|array Store the last API request.
	 */
	public $lastRequest = null;

	/**
	 * Get the full cache file path.
	 *
	 * @return string
	 */
	public static function cacheFilePath() {
		return rtrim( Constants::get( 'PATH' ), '/' ) . '/' . self::cacheFileName;
	}

	/**
	 * Get the full cache file URL.
	 *
	 * @return string
	 */
	public static function cacheFileUrl() {
		return rtrim( Constants::get( 'URL' ), '/' ) . '/' . self::cacheFileName;
	}

	/**
	 * Get the cache file contents.
	 *
	 * @return mixed|null
	 */
	public static function getCache() {
		if ( file_exists( self::cacheFilePath() ) ) {
			try {
				return json_decode( file_get_contents( self::cacheFilePath() ) );
			} catch ( Exception $exception ) {
				// Failed to read cache.
			}
		}

		return null;
	}

	/**
	 * Get the array of fonts from the API response.
	 *
	 * @param bool $force If true, cache will be ignored.
	 * @return Font[]|null
	 */
	public static function getFonts( bool $force = false ) {

		$runtime_cache = Cache::get( 'api_response' );

		// Check if we already have a runtime cached value
		if ( $runtime_cache && !$force ) {
			return $runtime_cache;
		}

		$use_cache = file_exists( self::cacheFilePath() ) ?  time() - filemtime( self::cacheFilePath() ) > DAY_IN_SECONDS : false;

		$contents = self::getCache();

		if ( $force || !$use_cache || empty( $contents ) ) {
			$api = new self;
			$api->request();

			if ( $api->responseIs( 200 ) ) {
				$api->cacheResponse();
				$contents = self::getCache();
			} else {
				$contents = null;
			}

		}

		// Get just the font items from respose
		if ( !empty( $contents->items ) ) {
			$contents = $contents->items;
		}

		// Process each item
		if ( !empty( $contents ) ) {
			$contents = array_map( function( $el ) {
				return new Font( $el );
			}, $contents );
		}

		// Set runtime cache
		Cache::set( 'api_response', $contents );

		return $contents;
	}

	/**
	 * Get an array of all the font familys.
	 *
	 * @return array
	 */
	public static function getFontFamilies() {
		$return = [];

		if ( $fonts = self::getFonts() ) {
			foreach ( $fonts as $font ) {
				$return[ $font->family ] = $font->family;
			}
		}

		return $return;
	}

	/**
	 * Find a font by family name.
	 *
	 * @param string $family
	 * @return Font|null
	 */
	public static function getFont( string $family ) {
		if ( $fonts = self::getFonts() ) {
			foreach ( $fonts as $font ) {
				if ( $font->family === $family ) {
					return $font;
				}
			}
		}

		return null;
	}

	/**
	 * Make an API request.
	 *
	 * @param array $params
	 * @return array|WP_Error
	 */
	public function request( array $params = [] ) {

		// Set defaults
		$params = wp_parse_args( $params, [ 'key' => Options::get( self::apiKeyOption ) ] );

		// Full URI
		$uri = add_query_arg( $params, self::endpoint );

		// Make request
		$this->lastRequest = wp_remote_get( $uri );

		// Return request
		return $this->lastRequest;
	}

	/**
	 * Get the last request response code.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_remote_retrieve_response_code/
	 *
	 * @return int|string
	 */
	public function responseCode() {
		return wp_remote_retrieve_response_code( $this->lastRequest );
	}

	/**
	 * Compare if response code with `$code`
	 *
	 * @param mixed $code
	 * @return bool
	 */
	public function responseIs( $code ) {
		return $this->responseCode() === $code;
	}

	/**
	 * Get the last request response body.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_remote_retrieve_body/
	 *
	 * @param string|null $attr Return a body attribute.
	 * @param mixed|null $default The fallback value if attribute is not found.
	 * @return string|array
	 */
	public function responseBody( ?string $attr = null, $default = null ) {
		$body = wp_remote_retrieve_body( $this->lastRequest );
		$decoded = null;

		if ( $body ) {
			try {
				$decoded = json_decode( $body, true );
			} catch ( Exception $exception ) {
				// Do nothing.
			}
		}

		$value = $decoded ?: $body;

		if ( $attr ) {
			if ( is_array( $value ) && array_key_exists( $attr, $value ) ) {
				return $value[ $attr ];
			}

			return $default;
		}

		return $value;
	}

	/**
	 * Cache the last api response.
	 *
	 * @return bool True if local cache was successful.
	 */
	public function cacheResponse() {

		if ( $body = $this->responseBody() ) {
			try {
				file_put_contents( self::cacheFilePath(), json_encode( $body ) );
				return true;
			} catch ( Exception $exception ) {
				// Do nothing.
			}
		}

		return false;
	}

}
