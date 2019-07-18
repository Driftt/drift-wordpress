<?php
/*
 * Plugin Name: Drift
 * Version: 3.0.1
 * Description: Adds 100% free live chat & targeted messages to your website. Designed for internet businesses like yours to increase sales, conversions and better support your customers.
 * Author: Drift
 * Author URI: https://www.drift.com/?ref=wordpress
 * Plugin URI: https://www.drift.com/?ref=wordpress
 */

Drift is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

/*
* Define
*/
define('DRIFT_4f050d29b8BB9_VERSION', '3.0.1');
define('DRIFT_4f050d29b8BB9_DIR', plugin_dir_path(__FILE__));
define('DRIFT_4f050d29b8BB9_URL', plugin_dir_url(__FILE__));
defined('DRIFT_4f050d29b8BB9_PATH') or define('DRIFT_4f050d29b8BB9_PATH', untrailingslashit(plugins_url('', __FILE__)));

You should have received a copy of the GNU General Public License
along with Drift. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

defined( 'ABSPATH' ) || exit;

require_once( 'includes/class-drift-main.php' );
$drift = new Drift_Main();
register_activation_hook( __FILE__, array( $drift, 'release_updates' ) );
$drift->run();
