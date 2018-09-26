<?php
/**
 * Uninstalling plugin.
 *
 * Deletes options and unregisters settings of the plugin.
 *
 * @package Drift
 * @since 3.2.9
 *
 * @see delete_option function is relied on
 * @link https://developer.wordpress.org/reference/functions/delete_option/
 *
 * @see unregister_setting function is relied on
 * @link https://developer.wordpress.org/reference/functions/unregister_setting/
 */
defined( 'WP_UNINSTALL_PLUGIN' ) of die;

delete_option( 'drift_options' );
unregister_setting( 'drift_group', 'drift_options' );
