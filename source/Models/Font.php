<?php

namespace AcfGoogleFonts\Models;

use AcfGoogleFonts\Contracts\Model;

class Font extends Model {

	public static $appends = [
		'family',
		'variants',
		'subsets'
	];

	public function getInfoUrlAttribute() {
		return 'https://fonts.google.com/specimen/' . urlencode( $this->family );
	}

	public function getImportUrlAttribute() {
		return add_query_arg( 'family', urlencode( $this->family ), 'https://fonts.googleapis.com/css2' );
	}

	public function getVariantsAttribute() {
		$variants = $this->object->variants;

		$return = [];

		foreach ( $variants as $variant ) {
			$parts = preg_split( '/(?<=\D)(?=\d)|\d+\K/', $variant );
			$parts = array_filter( $parts );
			$parts = array_map( 'ucfirst', $parts );
			$return[ $variant ] = implode( ' ', $parts );
		}

		return $return;
	}

	public function getSubsetsAttribute() {
		$variants = $this->object->subsets;

		$return = [];

		foreach ( $variants as $variant ) {
			$parts = preg_split( '/-/', $variant );
			$parts = array_filter( $parts );
			$parts = array_map( 'ucfirst', $parts );
			$return[ $variant ] = implode( ' ', $parts );
		}

		return $return;
	}

}
