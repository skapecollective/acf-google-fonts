<?php

namespace AcfGoogleFonts\Models;

use AcfGoogleFonts\Contracts\Model;

class Font extends Model {

	public static $appends = [
		'family',
		'variants',
		'subsets'
	];

	/**
	 * @var array Currently supported font styles
	 */
	const supportedStyles = [
		'regular',
		'italic'
	];

	/**
	 * @var string auto | block | swap | fallback | optional
	 */
	public $display = 'swap';

	/**
	 * Break variant into parts.
	 *
	 * @param string $variant
	 * @param string|null $attr
	 * @return array|mixed|null
	 */
	private function getVariantParts( string $variant, ?string $attr = null ) {
		$pattern = '/^([0-9]+)?(' . implode( '|', array_map( function( $part ) {
			return preg_quote( $part, '/' );
		}, self::supportedStyles ) ) . ')?$/i';

		$matched = preg_match( $pattern, $variant, $matches );

		if ( $matched ) {
			$parts = [
				'style' => !empty( $matches[ 2 ] ) ? $matches[ 2 ] : 'regular',
				'weight' => !empty( $matches[ 1 ] ) ? (int)$matches[ 1 ] : 0
			];

			if ( $attr ) {
				return array_key_exists( $attr, $parts ) ? $parts[ $attr ] : null;
			}

			return $parts;
		}

		return null;
	}

	/**
	 * Get the Google Fonts landing page for this family.
	 *
	 * @return string
	 */
	public function getInfoUrlAttribute() {
		return 'https://fonts.google.com/specimen/' . urlencode( $this->family );
	}

	/**
	 * Get the Google Fonts import API url.
	 *
	 * @return string
	 */
	public function getImportUrlAttribute( array $selected = [] ) {

		$subsets = !empty( $selected[ 'subsets' ] ) ? implode( ',', $selected[ 'subsets' ] ) : null;
		$variants = !empty( $selected[ 'variants' ] ) ? implode( ',', $selected[ 'variants' ] ) : null;

		return add_query_arg( [
			'family' => urlencode( $this->family ) . ( $variants ? ':' . $variants : '' ),
			'subset' => $subsets,
			'display' => $this->display
		], 'https://fonts.googleapis.com/css' );
	}

	/**
	 * Prettify the font variants output.
	 *
	 * @return array
	 */
	public function getVariantsAttribute() {
		$return = [];

		foreach ( $this->object->variants as $variant ) {
			if ( $parts = $this->getVariantParts( $variant ) ) {
				if ( !empty( $parts[ 'weight' ] ) ) {
					$return[ $variant ] = ucfirst( $parts[ 'style' ] ) . ' ' . $parts[ 'weight' ];
				}
			}
		}

		asort( $return );

		return $return;
	}

	/**
	 * Prettify the font style output.
	 *
	 * @return array
	 */
	public function getStylesAttribute() {
		$return = [];

		foreach ( $this->object->variants as $variant ) {
			if ( $style = $this->getVariantParts( $variant, 'style' ) ) {
				$return[ $style ] = ucfirst( $style );
			}
		}

		return $return;
	}

	/**
	 * Prettify the font weight output.
	 *
	 * @return array
	 */
	public function getWeightsAttribute() {
		$return = [];

		foreach ( $this->object->variants as $variant ) {
			if ( $weight = $this->getVariantParts( $variant, 'weight' ) ) {
				$return[ $weight ] = $weight;
			}
		}

		return $return;
	}

	/**
	 * Prettify the subsets output.
	 *
	 * @return array
	 */
	public function getSubsetsAttribute() {
		$variants = $this->object->subsets;
		$has_selected = !empty( $this->selected[ 'subsets' ] );

		$return = [];

		foreach ( $variants as $variant ) {
			if ( !$has_selected || $has_selected && in_array( $variant, $this->selected[ 'subsets' ] ) ) {
				$parts = preg_split( '/-/', $variant );
				$parts = array_filter( $parts );
				$parts = array_map( 'ucfirst', $parts );
				$return[ $variant ] = implode( ' ', $parts );
			}
		}

		asort( $return );

		return $return;
	}

}
