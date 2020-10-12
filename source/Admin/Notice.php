<?php

namespace AcfGoogleFonts\Admin;

use AcfGoogleFonts\Utilities\Html;

class Notice {

	/**
	 * @var boolean Determin if the notice is dismissible.
	 */
	public $dismissible = false;

	/**
	 * @var string WordPress notice style class (success | warning | error).
	 */
	public $type = 'success';

	/**
	 * @var null|string The opening and closing tags to wrap the message.
	 */
	public $wrap = 'p';

	/**
	 * @var array Array of classes render on the notice container.
	 */
	public $classes = [];

	/**
	 * @var string Message printed in the notice.
	 */
	public $message = '';

	/**
	 * Notice constructor.
	 *
	 * @param string $message
	 * @param array $args
	 */
	function __construct( string $message, array $args = [] ) {

		// Set message attribute.
		$args[ 'message' ] = $message;

		// Set property default.
		$args = wp_parse_args( $args, [
			'dismissible' => false,
			'type' => 'success',
			'wrap' => 'p',
			'classes' => [],
			'message' => 'Empty notice.',
		] );

		// Apply to instance.
		$this->dismissible( $args[ 'dismissible' ] );
		$this->type( $args[ 'type' ] );
		$this->wrap( $args[ 'wrap' ] );
		$this->classes( $args[ 'classes' ] );
		$this->message( $args[ 'message' ] );
	}

	/**
	 * Set dismissible option.
	 *
	 * @param bool $toggle
	 * @return void
	 */
	public function dismissible( bool $toggle ) {
		$this->dismissible = $toggle;
	}

	/**
	 * Set the notice type.
	 *
	 * @param string $type
	 * @return void
	 */
	public function type( string $type ) {
		$this->type = $type;
	}
	/**
	 * Set the notice wrapper tag.
	 *
	 * @param string $wrap
	 * @return void
	 */
	public function wrap( string $wrap ) {
		$this->wrap = $wrap;
	}

	/**
	 * Override the classes array.
	 *
	 * @param array $classes
	 * @return void
	 */
	public function classes( array $classes ) {
		$this->classes = $classes;
	}

	/**
	 * Set messsage property.
	 *
	 * @param string $message
	 * @return void
	 */
	public function message( string $message ) {
		$this->message = $message;
	}

	/**
	 * Check if notice has a specific class.
	 *
	 * @param string $class
	 * @return bool
	 */
	public function hasClass( string $class ) {
		return in_array( $class, $this->classes );
	}

	/**
	 * Add a single class to the classes array.
	 *
	 * @param string $class
	 * @return void
	 */
	public function addClass( string $class ) {
		if ( !$this->hasClass( $class ) ) {
			$this->classes[] = $class;
		}
	}

	/**
	 * Remove a single class from the classes array.
	 *
	 * @param string $class
	 */
	public function removeClass( string $class ) {
		if ( $this->hasClass( $class ) ) {
			$index = array_search( $class, $this->classes );
			if ( $index !== false ) {
				unset( $this->classes[ $index ] );
			}
		}
	}

	/**
	 * Toggle a single class.
	 *
	 * @param string $class
	 * @param null|boolean $toggle If boolean, will add class if true, remove if false.
	 */
	public function toggleClass( string $class, $toggle = null ) {
		$add = is_bool( $toggle ) ? $toggle : !$this->hasClass( $class );

		if ( $add ) {
			$this->addClass( $class );
		} else {
			$this->removeClass( $class );
		}
	}

	/**
	 * Render the notice HTML.
	 *
	 * @param bool $echo False, to return the html
	 * @return ?string
	 */
	public function render( bool $echo = true ) {

		// Always add WP core class.
		$this->addClass( 'notice' );

		// Add WP core dismissible class.
		$this->toggleClass( 'is-dismissible', $this->dismissible );

		// Add WP core notice style class.
		if ( !empty( $this->type ) ) {
			$this->addClass( 'notice-' . $this->type );
		}

		$open = $this->wrap ? "<{$this->wrap}>" : '';
		$close = $this->wrap ? "</{$this->wrap}>" : '';

		$html = sprintf( '<div class="%s">%s%s%s</div>', Html::inlineClasses( $this->classes ), $open, $this->message, $close );

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

}
