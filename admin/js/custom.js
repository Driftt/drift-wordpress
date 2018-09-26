/**
 * jQuery interactions for Drift WP plugin
 * Filename: custom.js
 * Version: 1.0.0
 * Build timestamp: 2018/09/25
 * Author: Vlăduț Ilie <vladilie94@gmail.com>
 * Author URI: https://vladilie.ro
 */
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
