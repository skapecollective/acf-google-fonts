<?php
/**
 * @var $api_key string|null
 * @var $page AcfGoogleFonts\Admin\Page
 */

use AcfGoogleFonts\Contracts\Prefixer;
use AcfGoogleFonts\Controllers\Google;
use AcfGoogleFonts\Admin\Notice;

$success = true;

if ( $api_key ) {
    if ( !Google::getFonts( true ) ) {

        ( new Notice( __( 'Unable to verify Google API key, please check you have entered this correctly.', 'skape' ), [
            'type' => 'error'
        ] ) )->render();

	    $success = false;
    }
}

if ( array_key_exists( 'settings-updated', $_GET ) && $success ) {
	( new Notice( __( 'Successful API key verification.', 'skape' ), [
	    'dismissible' => true
    ] ) )->render();
}

?>
<div class="wrap">
	<h2><?= $page->title; ?></h2>
	<form method="post" action="<?= admin_url( 'options.php"' ); ?>">
		<?php
			settings_fields( Prefixer::$prefixerDefault );
			do_settings_sections( $page->getSlug() );
			submit_button();
		?>
	</form>
</div>
