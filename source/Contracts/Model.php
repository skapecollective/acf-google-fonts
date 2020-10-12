<?php

namespace AcfGoogleFonts\Contracts;

use ReflectionClass;
use ReflectionMethod;

class Model {

	/**
	 * @var object Store the original object.
	 */
	public $object;

	/**
	 * @var string[] Property aliases.
	 */
	public static $aliases = [];

	/**
	 * @var string[] Array of property names to append to toArray().
	 */
	public static $appends = [];

	/**
	 * @var string[] Properties to be excluded from toArray() method.
	 */
	public static $protected = [
		'object'
	];

	/**
	 * Font constructor.
	 *
	 * @param object $api_data
	 */
	public function __construct( object $data ) {
		$this->object = $data;
	}

	/**
	 * @return array
	 */
	public function __debugInfo() {
		return $this->toArray( true );
	}

	/**
	 * Get property directly from WP_Post object.
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __get( $name ) {
		/**
		 * Generate a magic attribute method name from getter key. Inspired by Laravel.
		 *
		 * @example importUrl -> getImportUrlAttribute()
		 * @example infoUrl -> getInfoUrlAttribute()
		 */
		$propert_method = 'get' . ucfirst( $name ) . 'Attribute';

		/**
		 * See if we have a magic attribute method.
		 */
		if ( method_exists( $this, $propert_method ) ) {
			return call_user_func( [ $this, $propert_method ] );
		}

		/**
		 * Check if property exists on WP_Post object
		 */
		else if ( $this->object && property_exists( $this->object, $name ) ) {
			return $this->object->{$name};
		}

		/**
		 * Finally, check if we have an alias for this property
		 */
		else if ( $this->object && isset( static::$aliases[ $name ] ) ) {
			return $this->object->{ static::$aliases[ $name ] };
		}
	}

	/**
	 * Call method directly from WP_Post object.
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		if ( $this->object && method_exists( $this->object, $name ) ) {
			return call_user_func_array( [ $this->object, $name ], $arguments );
		}
	}

	/**
	 * Convert object data to associative array.
	 *
	 * @return array
	 */
	public function toArray( $excludeProtected = true ) {
		$info = [
			'object' => $this->object
		];

		// Add appendages
		foreach ( static::$appends as $key ) {
			if ( !array_key_exists( $key, $info ) ) {
				$info[ $key ] = $this->{$key};
			}
		}

		// Add aliases
		foreach ( static::$aliases as $alias => $property ) {
			if ( !array_key_exists( $alias, $info ) ) {
				$info[ $alias ] = $this->{$alias};
			}
		}

		// Add dynamic attributes
		$class = new ReflectionClass( static::class );
		$methods = $class->getMethods( ReflectionMethod::IS_PUBLIC );
		foreach ( $methods as $method ) {
			if ( preg_match( '/^get(\w+)Attribute$/', $method->name, $matches ) ) {
				$property = lcfirst( $matches[ 1 ] );
				if ( !array_key_exists( $property, $info ) ) {
					$info[ $property ] = call_user_func( [ $this, $matches[ 0 ] ] );
				}
			}
		}

		if ( $excludeProtected ) {
			foreach ( static::$protected as $key ) {
				unset( $info[ $key ] );
			}
		}

		return $info;
	}

}
