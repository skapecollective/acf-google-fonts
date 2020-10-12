<?php

namespace AcfGoogleFonts\Contracts;

trait Errorable {

	public $errorBag = [];

	/**
	 * Empty the error bag of all errors
	 *
	 * @return void
	 */
	public function emptyErrorBag() {
		$this->errorBag = [];
	}

	/**
	 * Get the full error bag
	 *
	 * @return array
	 */
	public function getErrors() {
		return $this->errorBag;
	}

	/**
	 * Check if any errors exists
	 *
	 * @return bool
	 */
	public function hasErrors() {
		return !empty( $this->getErrors() );
	}

	/**
	 * Returns all errors matching the key, or the first error if `$first` is true.
	 * @param string $key
	 * @param bool $first
	 * @return array
	 */
	public function getError( string $key, $first = false ) {
		$found = array_filter( $this->getErrors(), function ( $error ) use ( $key ) {
			return $error[ 'key' ] === $key;
		} );

		$found = array_values( $found );

		if ( $first ) {
			return array_shift( $found );
		}

		return $found;
	}

	/**
	 * Check if an error exits in the bag already.
	 *
	 * @param string $key
	 * @param string $message
	 * @param int|string $code
	 * @return bool
	 */
	public function errorExists( string $key, string $message, $code = 0 ) {
		$found = array_filter( $this->getErrors(), function ( $error ) use ( $key, $message, $code ) {
			return $error[ 'key' ] === $key && $error[ 'message' ] === $message && $error[ 'code' ] === $code;
		} );

		return count( $found ) > 0;
	}

	/**
	 * Conditionall add an arror to the bag. Reurns false if already exists.
	 *
	 * @param string $key
	 * @param string $message
	 * @param int|string $code
	 * @return bool
	 */
	public function addError( string $key, string $message, $code = 0 ) {
		if ( !$this->errorExists( $key, $message, $code ) ) {
			$this->errorBag[] = [
				'key' => $key,
				'message' => $message,
				'code' => $code
			];

			return true;
		}

		return false;
	}

	/**
	 * Add error to bag based on `$condition`
	 *
	 * @param string $key
	 * @param string $message
	 * @param bool $condition
	 * @param int $code
	 * @return bool
	 */
	public function conditionalError( string $key, string $message, bool $condition, $code = 0 ) {
		if ( $condition ) {
			return $this->addError( $key, $message, $code );
		}

		return false;
	}

	/**
	 * Remove errors by key from bag. Returns number of errors removed.
	 *
	 * @param string $key
	 * @return int
	 */
	public function removeError( string $key ) {
		$removed = 0;
		foreach ( $this->errorBag as $key => $error ) {
			if ( $error->key === $key ) {
				unset( $this->errorBag[ $key ] );
				$removed++;
			}
		}
		return $removed;
	}

}
