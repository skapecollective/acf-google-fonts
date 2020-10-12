<?php

namespace AcfGoogleFonts\Admin;

use AcfGoogleFonts\Wrappers\Constants;

class View {

	public $name = null;
	public $vars = [];

	public function __construct( string $name, array $vars = [] ) {
		$this->name = $name;
		$this->vars = $vars;
	}

	/**
	 * Get the full absolute view path
	 *
	 * @return string
	 */
	public function getPath() {
		$file = preg_replace( '/\.php$/', '', $this->name );
		return rtrim( Constants::get( 'PATH' ), '/' ) . '/' . ltrim( $file, '/' ) . '.php';
	}

	/**
	 * Add a variable to the var array
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function addVar( string $name, $value ) {
		$this->vars[ $name ] = $value;
	}

	/**
	 * Remove a variable from the var array
	 *
	 * @param string $name
	 * @return void
	 */
	public function removeVar( string $name ) {
		unset( $this->vars[ $name ] );
	}

	/**
	 * Render the view if exists
	 *
	 * @return void
	 */
	public function render() {
		if ( $this->getPath() ) {
			extract( $this->vars );
			require $this->getPath();
		}
	}

}

