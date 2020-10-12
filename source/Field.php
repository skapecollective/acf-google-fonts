<?php

namespace AcfGoogleFonts;

use acf_field;
use AcfGoogleFonts\Contracts\Ajax;
use AcfGoogleFonts\Controllers\Google;
use AcfGoogleFonts\Utilities\Prefix;
use AcfGoogleFonts\Wrappers\Constants;

class Field extends acf_field {

    public $ajaxFieldRenderer;

    /**
     * Field constructor.
     */
    public function __construct() {

        /**
         * @var string $name Single word, no spaces. Underscores allowed.
         */
        $this->name = 'google_fonts';

        /**
         * @var string $label Multiple words, can include spaces, visible when selecting a field type.
         */
        $this->label = __( 'Google Fonts', 'skape' );

        /**
         * @var string $category basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
         */
        $this->category = __( 'Choice' , 'acf' );

        /**
         * @var array $defaults Array of default settings which are merged into the field object. These are used later in settings.
         */
        $this->defaults = [
            'preview' => true,
            'enqueue_font' => true,
            'default_value' => [],
            'return_format' => 'array',
            'choices' => Google::getFontFamilies()
        ];

        /**
         * @var array $l10n Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via: var message = acf._e('FIELD_NAME', 'error');
         */
        $this->l10n = [
            'preview_text' => __( 'The quick brown fox jumps over the lazy dog', 'skape' )
        ];

        /**
         * @var Ajax $ajaxFieldRenderer The ajax request handler for updating fields.
         */
        $this->ajaxFieldRenderer = new Ajax( 'field-renderer', [ $this, 'ajaxFieldRenderer' ] );

        parent::__construct();
    }

    /**
     * Create extra settings for your field. These are visible when editing a field.
     *
     * @param array $field The $field being edited.
     */
    public function render_field_settings( $field ) {

        acf_render_field_setting( $field, [
            'label' => __( 'Font preview', 'skape' ),
            'instructions' => __( 'Show a preview of the selected font.', 'skape' ),
            'type' => 'true_false',
            'name' => 'preview',
            'ui' => true,
            'ui_on_text' => __( 'Show', 'skape' ),
            'ui_off_text' => __( 'Hide', 'skape' ),
        ] );

        acf_render_field_setting( $field, [
            'label' => __( 'Enqueue fonts', 'skape' ),
            'message'       => __( 'Load the selected fonts on the frontend.', 'skape' ),
            'type' => 'true_false',
            'name' => 'enqueue_font',
            'ui' => true,
            'ui_on_text' => __( 'Yes', 'skape' ),
            'ui_off_text' => __( 'No', 'skape' ),
        ] );

        acf_render_field_setting( $field, [
            'label' => __( 'Return format', 'skape' ),
            'instructions'  => __( 'Specify the value returned by the field.', 'skape' ),
            'type' => 'select',
            'name' => 'return_format',
            'choices'       => [
                'array' => 'All: Array (includes selected family, varients, subsets and url)',
                'family' => 'Family: The selected font family name',
                'url' => 'URL: The @import url for the selected font, varients and subsets'
            ]
        ] );

        acf_render_field_setting( $field, [
            'label' => __( 'Default value', 'skape' ),
            'type' => 'google_fonts',
            'name' => 'default_value'
        ] );

    }

    /**
     * Enqueue Assets
     *
     * This function enqueues the scripts and styles needed to display the
     * field
     *
     * @author Daniel Pataki
     * @since 3.0.0
     *
     */
    function input_admin_enqueue_scripts() {
        $url = rtrim( Constants::get( 'URL' ), '/' ) . '/';
        wp_enqueue_script( Prefix::prefix( 'field' ), $url . 'build/js/field.js', [ 'jquery', 'acf' ], Constants::get( 'VERSION' ) );
        wp_enqueue_style( Prefix::prefix( 'field' ), $url . 'build/css/field.css', [], Constants::get( 'VERSION' ) );
    }

    /**
     * Handle the admin ajax request.
     *
     * @param Ajax $request
     */
    public function ajaxFieldRenderer( Ajax $request ) {
        $family = $request->input( 'family' );
        $font = $family ? Google::getFont( $family ) : null;

        $request->validateToken();
        $request->conditionalError( 'family', __( 'No font selected.', 'skape' ), !$family );
        $request->conditionalError( 'family', __( 'Font information not found.', 'skape' ), $family && !$font );
        $request->catchErrors();

        return wp_send_json_success( $font->toArray() );
    }

    /**
     * Create the HTML interface for your field.
     *
     * @param array $field The $field being rendered.
     */
    public function render_field( $field ) {

        $values = !empty( $field[ 'value' ] ) ? $field[ 'value' ] : $field[ 'default_value' ];

        ?>
            <div class="acf-google_fonts">
                <?php if ( $fonts = $field[ 'choices' ] ): ?>

                    <?php if ( $field[ 'preview' ] ): ?>
                        <div class="acf-google_fonts-preview"></div>
                    <?php endif ?>

                    <div class="acf-google_fonts-row">

                        <div class="acf-google_fonts-column">
                            <div class="acf-google_fonts-choice">
                                <div class="acf-google_fonts-label"><?php _e('Font Family', 'skape' ); ?></div>
                                <select name="<?= esc_attr( $field[ 'name' ] . '[family]' ); ?>"
                                        data-js-action="<?= esc_attr( $this->ajaxFieldRenderer->requestName() ); ?>"
                                        data-js-token="<?= esc_attr( $this->ajaxFieldRenderer->getToken() ); ?>"
                                        data-js-name="<?= esc_attr( $field[ 'name' ] ); ?>"
                                        data-js-key="<?= esc_attr( $field[ 'id' ] ); ?>">
                                    <option value=""><?php _e( '&mdash; Select one &mdash;', 'skape' ); ?></option>
                                    <?php foreach( $fonts as $family ): ?>
                                        <option <?php selected( $family, $values[ 'family' ] ); ?> value="<?= $family; ?>"><?= $family; ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="acf-google_fonts-column">
                            <div class="acf-google_fonts-variants">
                                <div class="acf-google_fonts-label"><?php _e('Variants', 'skape' ); ?></div>
                                <div class="acf-google_fonts-js_values"></div>
                            </div>
                        </div>

                        <div class="acf-google_fonts-column">
                            <div class="acf-google_fonts-subsets">
                                <div class="acf-google_fonts-label"><?php _e('Subsets', 'skape' ); ?></div>
                                <div class="acf-google_fonts-js_values"></div>
                            </div>
                        </div>

                    </div>

                <?php else:?>
                    <?php _e( 'There are no fonts to choose from.', 'skape' ); ?>
                <?php endif ?>
            </div>
        <?php
    }

}
