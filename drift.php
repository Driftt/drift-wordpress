<?php
/*
 * Plugin Name: Drift
 * Version: 0.1.4
 * Plugin URI: https://www.drift.com/?ref=wordpress
 * Description: Adds Drift free live chat to your website. Designed for internet businesses like yours to increase sales, conversions and better support your customers.
 * Author: driftcom
 * Author URI: http://www.drift.com/
 */

//=============================================
// Define Constants
//=============================================
if (!defined('DRIFT_PATH'))
    define('DRIFT_PATH', untrailingslashit(plugins_url('', __FILE__)));

if (!defined('DRIFT_PLUGIN_DIR'))
    define('DRIFT_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));

if (!defined('DRIFT_PLUGIN_SLUG'))
    define('DRIFT_PLUGIN_SLUG', basename(dirname(__FILE__)));

// Constants for enabled/disabled state
define("drift_enabled", "enabled", true);
define("drift_disabled", "disabled", true);

// Defaults, etc.
define("key_drift_status", "drift_status", true);
define("key_drift_admin", "drift_admin_status", true);
define("key_drift_tag", "drift_tag", true);
// define("key_drift_footer", "drift_footer", true);

define("drift_status_default", drift_disabled, true);
define("drift_admin_default", drift_enabled, true);
define("drift_extra_default", "", true);
// define("drift_footer_default", drift_disabled, true);

// Create the default key and status
add_option(key_drift_status, drift_status_default, 'If Drift logging in turned on or off.');
add_option(key_drift_admin, drift_admin_default, 'If WordPress admins are counted in Drift.');
add_option(key_drift_tag, drift_extra_default, 'Additional Drift tracking options');

// Create a option page for settings
add_action('admin_menu', 'add_drift_option_page');
add_action('admin_menu', 'add_drift_menu_items');

// Add top-level admin bar link
add_action('admin_bar_menu', 'add_drift_link_to_admin_bar', 999);


// Adds Drift link to top-level admin bar
function add_drift_link_to_admin_bar() {

    global $wp_version;
    global $wp_admin_bar;

    $drift_icon = '<img src="' . DRIFT_PATH . '/assets/drift-icon-16x16-white.png' . '">';

    $args = array(
        'id' => 'drift-admin-menu',
        'title' => '<span class="ab-icon" ' . ($wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? ' style="margin-top: 3px;"' : '') . '>' . $drift_icon . '</span><span class="ab-label">Drift</span>', // alter the title of existing node
        'parent' => FALSE,   // set parent to false to make it a top level (parent) node
        'href' => get_bloginfo('wpurl') . '/wp-admin/admin.php?page=drift.php',
        'meta' => array('title' => 'Drift')
    );

    $wp_admin_bar->add_node($args);
}

// Hook in the options page function
function add_drift_option_page() {
    add_options_page('Drift Options', 'Drift', 8, basename(__FILE__), 'drift_options_page');
}

function drift_build_app() {
    global $wp_version;
    echo '<div id="drift" class="wrap ' . ($wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? 'pre-mp6' : '') . '"></div>';
}

// Adds Drift menu to /wp-admin sidebar
function add_drift_menu_items()
{
    $options = get_option('drift_options');

    global $submenu;
    global $wp_version;

    // Block non-sanctioned users from accessing Drift
    $capability = 'activate_plugins';
    if (!current_user_can('activate_plugins')) {
        if (!array_key_exists('li_grant_access_to_' . drift_get_user_role(), $options))
            return FALSE;
        else {
            if (current_user_can('manage_network')) // super admin
                $capability = 'manage_network';
            else if (current_user_can('edit_pages')) // editor
                $capability = 'edit_pages';
            else if (current_user_can('publish_posts')) // author
                $capability = 'publish_posts';
            else if (current_user_can('edit_posts')) // contributor
                $capability = 'edit_posts';
            else if (current_user_can('read')) // subscriber
                $capability = 'read';

        }
    }

    $drift_icon = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDMwMCAzMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPHRpdGxlPgogICAgSWNvbiBPbmx5IFdoaXRlCiAgPC90aXRsZT4KICA8cGF0aCBkPSJNMTQ5LjkwOCA1LjQ5NkM3MC4yNCA1LjQ5NiA1LjY2IDcwLjA3OCA1LjY2IDE0OS43NDhjMCA3OS42NjcgNjQuNTggMTQ0LjI1IDE0NC4yNDggMTQ0LjI1IDc5LjY2NSAwIDE0NC4yNS02NC41ODMgMTQ0LjI1LTE0NC4yNSAwLTc5LjY3LTY0LjU4NS0xNDQuMjUyLTE0NC4yNS0xNDQuMjUyem0tNC41MDggMTgwLjQ3YzAgMi4xOTQtMS4xNiA0LjIyMy0zLjA0NSA1LjMyLTEuODcgMS4wODYtNC4xNiAxLjA5LTYuMDMyLjAyNC0uMDE4LS4wMS0uMDQtLjAxNy0uMDU3LS4wMjhsLTI1LjU5OC0xNC45MnY1NS40MWMwIDIuMTk0LTEuMTU3IDQuMjI2LTMuMDQgNS4zMjQtLjk0Ni41NDMtMS45OTQuODIyLTMuMDQ1LjgyMi0xLjA0OCAwLTIuMS0uMjgtMy4wNDUtLjgyMmwtMTUuMy04LjkyYy0xLjg4NC0xLjA5Ni0zLjA0NC0zLjEyNS0zLjA0NC01LjMxNXYtNjIuNTE0bC0yNC43My0xNC40MWMtMS44ODQtMS4xLTMuMDQ1LTMuMTI3LTMuMDQ1LTUuMzJ2LTIzLjg5YzAtMi4xOTMgMS4xNi00LjIyMiAzLjA0NC01LjMxNSAxLjg4NC0xLjEgNC4yMDUtMS4xIDYuMDkgMGw3Ny44IDQ1LjMyN2MxLjg4NSAxLjEgMy4wNDYgMy4xMzMgMy4wNDYgNS4zMjd2MjMuOTAyem0xLjMxNi05NS43M2MwIDMuMDQ1LTEuNjE2IDUuODU2LTQuMjQ0IDcuMzlsLTIzLjUzIDEzLjcxNHYxOC4wNmMwIDEuODM3LTEuOTcgMi45OTUtMy41NTcgMi4wNzQtOS40OC01LjUxOC0zNS4yODItMjAuNTMtNDQuNzE4LTI2LTEuNTgtLjkxOC0xLjU1My0zLjE4LjAyOC00LjFsNjYuODg3LTM4Ljk3N2MxLjg4NC0xLjA5MyA0LjIwMi0xLjA5MyA2LjA5IDAgMS44ODMgMS4xIDMuMDQ0IDMuMTMgMy4wNDQgNS4zMjR2MjIuNTE2em03LjY5Ny0yMi41MTVjMC0yLjE5MyAxLjE2LTQuMjIyIDMuMDQ4LTUuMzIzIDEuODgyLTEuMDkzIDQuMjAzLTEuMDkzIDYuMDg3IDBsNjYuODUyIDM4Ljk1NmMxLjU5My45MyAxLjYyMiAzLjIxNy4wMjQgNC4xNC05LjQ0OCA1LjQ4LTM1LjA4NSAyMC4zOTYtNDQuNjEgMjUuOTM4LTEuNjE1Ljk0LTMuNjI2LS4yMzUtMy42MjYtMi4xMDhWMTExLjU4YzAtLjE0OC0uMDc4LS4yOS0uMjA4LS4zNjNsLTI0LjUyLTE0LjI5MmMtMS44ODYtMS4wOTctMy4wNDctMy4xMjYtMy4wNDctNS4zMlY2Ny43MnptODkuOTggNzIuODk1YzAgMi4xOTQtMS4xNiA0LjIyMi0zLjA0NCA1LjMybC0yNC43MyAxNC40MXY2Mi41MTZjMCAyLjE5LTEuMTYgNC4yMi0zLjA0NSA1LjMxN2wtMTUuMyA4LjkyYy0uOTQ2LjU0Mi0xLjk5NC44Mi0zLjA0NS44Mi0xLjA1IDAtMi4xLS4yNzgtMy4wNC0uODItMS44ODgtMS4xLTMuMDQ1LTMuMTMtMy4wNDUtNS4zMjV2LTU1LjQxbC0yNS41OTggMTQuOTJjLS4wMTguMDEtLjA0LjAxOC0uMDUzLjAyOC0xLjg3NyAxLjA2Ni00LjE2NiAxLjA2Mi02LjAzMy0uMDI0LTEuODg2LTEuMDk3LTMuMDQ3LTMuMTI2LTMuMDQ3LTUuMzJ2LTIzLjkwMmMwLTIuMTk0IDEuMTYtNC4yMjYgMy4wNDgtNS4zMjdsNzcuOC00NS4zMjZjMS44ODQtMS4xIDQuMjA1LTEuMSA2LjA5IDAgMS44ODMgMS4wOTQgMy4wNDQgMy4xMjMgMy4wNDQgNS4zMTd2MjMuODg4eiIgZmlsbD0iI0ZGRiIgZmlsbC1ydWxlPSJldmVub2RkIi8+Cjwvc3ZnPgo=';

    add_menu_page('Drift', 'Drift', $capability, 'drift', array($this, 'drift_options_page'), $drift_icon, '25.100713');
    add_submenu_page('drift', 'Settings', 'Settings', 'activate_plugins', basename(__FILE__), array($this, 'drift_options_page'));

    $submenu['drift'][0][0] = 'Dashboard';

}


function drift_options_page() {
    // If we are a postback, store the options
    if (isset($_POST['info_update'])) {
        // Update the status
        $drift_status = $_POST[key_drift_status];
        if (($drift_status != drift_enabled) && ($drift_status != drift_disabled))
            $drift_status = drift_status_default;
        update_option(key_drift_status, $drift_status);

        // Update the admin logging
        $drift_admin = $_POST[key_drift_admin];
        if (($drift_admin != drift_enabled) && ($drift_admin != drift_disabled))
            $drift_admin = drift_admin_default;
        update_option(key_drift_admin, $drift_admin);

        // Update the extra tracking code
        $drift_tag = $_POST[key_drift_tag];
        update_option(key_drift_tag, $drift_tag);

        // Give an updated message
        echo "<div class='updated fade'><p><strong>Drift settings saved.</strong></p></div>";
    }
    // Output the options page
    ?>

        <div class="wrap">
        <form method="post" action="options-general.php?page=drift.php">
        <?php //ga_nonce_field(); ?>
            <h1>Drift</h1>
            <h3>Basic Options</h3>
            <?php if (get_option(key_drift_status) == drift_disabled) { ?>
                <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
                Drift Live Chat is currently <strong>DISABLED</strong>.
                </div>
            <?php } ?>
            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;">
                        <label for="<?php echo key_drift_status ?>">Drift (Live Chat) is:</label>
                    </th>
                    <td>
                        <?php
                        echo "<select name='".key_drift_status."' id='".key_drift_status."'>\n";

                        echo "<option value='".drift_enabled."'";
                        if(get_option(key_drift_status) == drift_enabled)
                            echo " selected='selected'";
                        echo ">Enabled</option>\n";

                        echo "<option value='".drift_disabled."'";
                        if(get_option(key_drift_status) == drift_disabled)
                            echo" selected='selected'";
                        echo ">Disabled</option>\n";

                        echo "</select>\n";
                        ?>
                    </td>
                </tr>
            </table>
                <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th valign="top" style="padding-top: 10px;">
                        <label for="key_drift_tag">Drift JS code snippet:</label>
                    </th>
                    <td>
                      <textarea rows="8" cols="100" placeholder="<!-- Insert the Drift tag here -->" name="drift_tag"><?php echo stripslashes(get_option(drift_tag)); ?></textarea>
                        <p style="margin: 5px 10px;">Enter your Drift JS code snippet.  You can find your <a href="https://app.driftt.com/settings/configure" target="_blank" title="Open Drift Settings">Drift JS here</a>. A Free Drift account is required to use this plugin.</p>
                    </td>
                </tr>
                </table>
            <p class="submit">
                <input type='submit' name='info_update' value='Save Changes' />
            </p>
        </div>
        </form>

<?php
}

// Add the script
add_action('wp_head', 'add_drift');

// If we can indentify the current user output
function get_drift_identify() {
    global $current_user;
    get_currentuserinfo();
    if ($current_user->user_email) {
        echo "<!-- Start Identify call for Drift -->\n";
        echo "<script>\n";
        echo "drift.identify(\"".md5($current_user->user_email)."\", { email: \"".$current_user->user_email."\", name: \"".$current_user->user_login."\" });\n";
        echo "</script>\n";
        echo "<!-- End Identify call for Drift -->\n";
    } else {
        // See if current user is a commenter
        $commenter = wp_get_current_commenter();
        if ($commenter['comment_author_email']) {
          echo "<!-- Start Identify call for Drift -->\n";
          echo "<script>\n";
          echo "drift.identify(\"".md5($commenter['comment_author_email'])."\", { email: \"".$commenter['comment_author_email']."\", name: \"".$commenter['comment_author']."\" });\n";
          echo "</script>\n";
          echo "<!-- End Identify call for Drift -->\n";
        }
    }
}

// The guts of the Drift script
function add_drift() {
    global $current_user;
    get_currentuserinfo();
    $drift_tag = stripslashes(get_option(drift_tag));

    // If GA is enabled and has a valid key
    if (get_option(key_drift_status) != drift_disabled) {

            // Insert tracker code
            if ( '' != $drift_tag ) {
                echo "<!-- Start Drift By WP-Plugin: Drift -->\n";
                echo $drift_tag;
                echo"<!-- end: Drift Code. -->\n";
                
                // Optional
                get_drift_identify();
            }
    }
}
?>
