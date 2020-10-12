( function( $, acf ) {
    'use strict';

    if ( typeof acf !== 'undefined' ) {

        acf.addAction( 'new_field/type=google_fonts', function( field ) {
            const $field = field.$el;
            const $container = $field.find( '.acf-google_fonts' );
            const $select = $container.find( '.acf-google_fonts-choice select' );
            const $preview = $container.find( '.acf-google_fonts-preview' );

            // Initiate select2
            $select.select2();

            const checkboxTypes = [ 'variants', 'subsets' ];
            var request = null;

            const name = $select.data( 'js-name' );
            const key = $select.data( 'js-key' );
            const action = $select.data( 'js-action' );
            const token = $select.data( 'js-token' );

            const handleError = ( response ) => console.log( response );
            const handleSuccess = ( response ) => {

                // Update font preview
                if ( $preview.length ) {

                    // Preserve edited text
                    const previewText = $preview.text();

                    // Remove previous font styles
                    $preview.empty();

                    // Add stylesheet
                    $( '<link>', {
                        rel: 'stylesheet',
                        type: 'text/css',
                        href: response.data.importUrl
                    } ).appendTo( $preview );

                    // Add font preview
                    $( '<div>', {
                        contenteditable: true,
                        text: previewText || acf._e( 'google_fonts', 'preview_text' ),
                        style: `font-family: ${response.data.family};`
                    } ).appendTo( $preview );
                }

                // Update font fields
                for ( let j = 0; j < checkboxTypes.length; j++ ) {
                    const type = checkboxTypes[ j ];
                    const $type = $container.find( `.acf-google_fonts-${type} .acf-google_fonts-js_values` );

                    $type.empty();

                    if ( type in response.data ) {

                        const $ul = $( '<ul>', {
                            class: 'acf-google_fonts-list'
                        } ).appendTo( $type );

                        for ( var value in response.data[ type ] ) {

                            const label = response.data[ type ][ value ];
                            const id = `_${key}_${type}_${value}`;

                            const $li = $( '<li>' ).appendTo( $ul );

                            $( '<input>', {
                                type: 'checkbox',
                                name: `${name}[${type}][]`,
                                value: value,
                                id: id
                            } ).appendTo( $li );

                            $( '<label>', {
                                text: label,
                                for: id
                            } ).appendTo( $li );

                        }
                    }
                }
            };

            // Handle font family change
            $select.on( 'change load', function() {

                // Abort previous process
                if ( request && typeof request.abort !== 'undefined' ) {
                    request.abort();
                }

                // Ensure we have a value before making a request
                if ( $select.val() ) {

                    // Disable field
                    $container.css( {
                        'opacity': 0.4,
                        'pointer-events': 'none'
                    } );

                    // Make request
                    request = $.ajax( {
                        url: acf.data.ajaxurl,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: action,
                            csrf: token,
                            family: $select.val()
                        }
                    } ).done( ( response, textStatus, jqXHR ) => {

                        if ( response.success ) {
                            handleSuccess( response );
                        } else {
                            handleError( jqXHR );
                        }

                    } ).fail( ( jqXHR, textStatus, errorThrown ) => {

                        handleError( jqXHR );

                    } ).always( () => {

                        // Reset request
                        request = null;

                        // Reset CSS
                        $container.css( {
                            'opacity': 1,
                            'pointer-events': ''
                        } );

                    } );

                }

            } ).trigger( 'load' );

        } );

    }

} )( jQuery, window.acf );
