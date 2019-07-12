<?php
/**
 * Uninstalling plugin.
 *
 * Deletes options and unregisters settings of the plugin.
 *
 * @package Drift
 * @since 2.0.0
 *
 * @see delete_option function is relied on
 * @link https://developer.wordpress.org/reference/functions/delete_option/
 *
 * @see unregister_setting function is relied on
 * @link https://developer.wordpress.org/reference/functions/unregister_setting/
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || die;

delete_option( 'drift_options' );
unregister_setting( 'drift_group', 'drift_options' );
