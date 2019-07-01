/**
 * jQuery interactions for Drift WP plugin
 * Filename: custom.js
 * Version: 1.0.1
 * Build timestamp: 2019/07/01
 * Author: Vlăduț Ilie <hello@vladilie.ro>
 * Author URI: https://vladilie.ro
 */
(function() {
	jQuery( document ).ready( function( $ ) {
		var inputs = $( '#identifying, #hideon_pages, #code_snippet, #js_hook' );
		if ( true === $( '#activation' ).is(':checked') ) {
			inputs.prop( 'disabled', false );
		} else {
			inputs.prop( 'disabled', true );
		}

		$( '#activation' ).click( function() {
			if ( $( this ).is( ':checked' ) ) {
				inputs.prop( 'disabled', false );
			} else {
				$( '#identifying, #js_hook' ).prop( 'checked', false );
				$( '#hideon_pages, #code_snippet' ).val('');
				inputs.prop( 'disabled', true );
			}
		});
	});
})(jQuery);
