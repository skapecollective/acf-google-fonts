<?php

namespace AcfGoogleFonts\Controllers;

use AcfGoogleFonts\Admin\Notices;
use AcfGoogleFonts\Admin\Page;
use AcfGoogleFonts\Admin\View;
use AcfGoogleFonts\Contracts\Prefixer;
use AcfGoogleFonts\Contracts\StaticInitiator;
use AcfGoogleFonts\Wrappers\Options;

class Admin {

	use StaticInitiator;

	/**
	 * @var null|string The Google API key.
	 */
	public $apiKey;

	/**
	 * @var string The full slug which the setting page should sit under.
	 */
	public $parent = 'edit.php?post_type=acf-field-group';

	/**
	 * @var Page The admin settings page.
	 */
	public $page;

	/**
	 * @var string The key used for the settings section.
	 */
	public $section;

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		$this->apiKey = Options::get( Google::apiKeyOption );

		$this->page = new Page( __( 'Google Fonts', 'skape' ), null, $this->parent );
			$this->page->slug = 'settings'; // Overrides generated translated slug.
			$this->page->priority = PHP_INT_MAX; // Ensure always at the end of the list.
			$this->page->view = new View( 'views/admin/settings', [
				'api_key' => $this->apiKey,
				'page' => $this->page
			] );
		$this->page->register();

		if ( empty( $this->apiKey ) ) {
			Notices::addErrorNotice( __( 'The ACF Google Fonts Field requires an Google API key to work. You can set your API key in the field settings page.', 'skape' ) );
		}

		add_action( 'admin_init', [ $this, 'registerFields' ] );
	}

	/**
	 * Register WordPress settings and fields.
	 */
	public function registerFields() {

		register_setting( Prefixer::$prefixerDefault, Options::key( Google::apiKeyOption ), [
			'type' => 'string',
			'default' => null,
			'show_in_rest' => false
		] );

		add_settings_section( 'google_fonts', __( 'Google API settings', 'sakpe' ), '__return_false', $this->page->getSlug() );

		add_settings_field( Options::key( Google::apiKeyOption ), __( 'API Key', 'skape' ), [ $this, 'renderApiKeyField' ], $this->page->getSlug(), 'google_fonts' );

	}

	/**
	 * Render the API key field.
	 */
	public function renderApiKeyField() {
		echo '<input type="text" name="' . esc_attr( Options::key( Google::apiKeyOption ) ) . '" value="' . esc_attr( $this->apiKey ) . '">';
	}

}
