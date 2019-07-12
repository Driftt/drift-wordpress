<?php
/*
Plugin Name: Drift
Plugin URI: https://wordpress.org/plugins/drift/
Description: Adds 100% free live chat & targeted messages to your website. Designed for internet businesses like yours to increase sales, conversions and better support your customers.
Version: 2.0.0
Author: Drift
Author URI: https://www.drift.com/?ref=wordpress
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: drift
Domain Path: /languages

Drift is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Drift is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Drift. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

defined( 'ABSPATH' ) || exit;

require_once( 'includes/class-drift-main.php' );
$drift = new Drift_Main();
register_activation_hook( __FILE__, array( $drift, 'release_updates' ) );
$drift->run();
