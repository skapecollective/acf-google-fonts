<?php

namespace AcfGoogleFonts\Contracts;

/**
 *
 */
class Ajax {

	use StaticInitiator;
	use Errorable;
	use Prefixer;

	/**
	 * @var string Name of input field for CSRF checking.
	 */
	public static $csrfField = 'csrf';

	/**
	 * @var string CSRF string used for verifying request.
	 */
	public static $csrfToken = 'acf-google-fonts';

	/**
	 * @var string A unique request name used when calling the correct hook
	 */
	public $name;

	/**
	 * @var string Set to true if ajax request can be made without authentication
	 */
	public $public = false;

	/**
	 * @var callable A callable function that will handle the request.
	 */
	public $callback;

	/**
	 * Ajax constructor.
	 *
	 * @param string $name
	 * @param callable $callback
	 * @param false $public
	 */
	public function __construct( $name, $callback, $public = false ) {
		$this->name = $name;
		$this->callback = $callback;
		$this->public = $public;

		add_action( 'wp_ajax_' . $this->requestName(), [ $this, 'handle' ] );

		if ( $this->public ) {
			add_action( 'wp_ajax_nopriv_' . $this->requestName(), [ $this, 'handle' ] );
		}
	}

	/**
	 * Request handler
	 *
	 * @return void
	 */
	public function handle() {
		if ( is_callable( $this->callback ) ) {
			call_user_func( $this->callback, $this );
		} else {
			wp_die( __( 'Request handling failed.', 'skape' ) );
		}
	}

	/**
	 * Conditionally return error response if errors exist
	 *
	 * @return void
	 */
	public function catchErrors() {
		if ( $this->hasErrors() ) {
			self::sendError( $this->getErrors() );
		}
	}

	/**
	 * Add generic error if nonce token is invalid
	 *
	 * @return void
	 */
	public function validateToken() {
		if ( !self::validToken() ) {
			$this->addError( 'csrf', __( 'Session timedout.', 'skape' ) );
		}
	}

	/**
	 * Get the full prefixed request name
	 *
	 * @return string
	 */
	public function requestName() {
		return self::prefix( $this->name );
	}

	/**
	 * Get sanitized value from request
	 *
	 * @param string $name Name of field in request
	 * @param string|null $filter Name of function to clean field
	 * @return mixed
	 */
	public function input( string $name, ?string $filter = 'sanitize_text_field' ) {
		$value = !empty( $_REQUEST[ $name ] ) ? $_REQUEST[ $name ] : null;

		if ( $filter ) {
			$value = call_user_func_array( $filter, [ $value ] );
		}

		return $value;
	}

	/**
	 * Generate a request token
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_create_nonce/
	 *
	 * @return string
	 */
	public function getToken() {
		return wp_create_nonce( static::$csrfToken );
	}

	/**
	 * Check if request is valid
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_verify_nonce/
	 *
	 * @return boolean
	 */
	public function validToken() {
		return wp_verify_nonce( static::input( static::$csrfField ), static::$csrfToken );
	}

	/**
	 * Send JSON error response
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function sendError( $data = null ) {
		wp_send_json_error( $data );
	}

	/**
	 * Send JSON success response
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function sendSuccess( $data = null ) {
		wp_send_json_success( $data );
	}

}
