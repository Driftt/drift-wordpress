<?php
/*
 * Plugin Name: Drift
 * Version: 1.4
 * Plugin URI: https://www.drift.com/?ref=wordpress
 * Description: Adds Drift free live chat to your website. Designed for internet businesses like yours to increase sales, conversions and better support your customers.
 * Author: Drift
 * Author URI: http://www.drift.com/
 */

// Prevent Direct Access
defined('ABSPATH') or die("Restricted access!");

//=============================================
// Define Constants
//=============================================
defined('DRIFT_PATH') or define('DRIFT_PATH', untrailingslashit(plugins_url('', __FILE__)));

// Register settings
function Drift_register_settings()
{
  register_setting( 'Drift_settings_group', 'Drift_settings' );
}
add_action( 'admin_init', 'Drift_register_settings' );

// Create a option page for settings
add_action('admin_menu', 'add_drift_option_page');

// Add top-level admin bar link
add_action('admin_bar_menu', 'add_drift_link_to_admin_bar', 999);


// Adds Drift link to top-level admin bar
function add_drift_link_to_admin_bar()
{
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

// Hook in the options page functionå
function add_drift_option_page()
{
  add_options_page('Drift Options', 'Drift', 'activate_plugins', basename(__FILE__), 'drift_options_page');
}

 // Output the options page
function drift_options_page()
{
  // Get options
  $options = get_option('Drift_settings');

  // Check to see if Drift is enabled
  $drift_activated = false;
  if ( esc_attr( $options['drift_enabled'] ) == "on" ) { $drift_activated = true; }

?>
        <div class="wrap">
        <form name="Drift-form" action="options.php" method="post" enctype="multipart/form-data">
          <?php settings_fields( 'Drift_settings_group' ); ?>

            <h1>Drift</h1>
            <h3>Basic Options</h3>
            <?php if ( ! $drift_activated ) { ?>
                <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
                Drift Live Chat is currently <strong>DISABLED</strong>.
                </div>
            <?php } ?>
            <?php do_settings_sections( 'Drift_settings_group' ); ?>

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;">
                        <label for="drift_enabled">Drift (Live Chat) is:</label>
                    </th>
                    <td>
                      <?php
                          echo "<select name=\"Drift_settings[drift_enabled]\"  id=\"drift_enabled\">\n";

                          echo "<option value=\"on\"";
                          if ( $drift_activated ) { echo " selected='selected'"; }
                          echo ">Enabled</option>\n";

                          echo "<option value=\"off\"";
                          if ( ! $drift_activated ) { echo" selected='selected'"; }
                          echo ">Disabled</option>\n";
                          echo "</select>\n";
                        ?>
                    </td>
                </tr>
            </table>
                <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th valign="top" style="padding-top: 10px;">
                        <label for="Drift_widget_code">Drift JS code snippet:</label>
                    </th>
                    <td>
                      <textarea rows="8" cols="100" placeholder="<!-- Insert the Drift tag here -->" name="Drift_settings[drift_widget_code]"><?php echo esc_attr( $options['drift_widget_code'] ); ?></textarea>
                        <p style="margin: 5px 10px;">Enter your Drift JS code snippet.  You can find your <a href="https://app.driftt.com/settings/configure" target="_blank" title="Open Drift Settings">Drift JS code snippet here</a>. A Free Drift account is required to use this plugin.</p>
                    </td>
                </tr>
                </table>
            <p class="submit">
                <?php echo submit_button('Save Changes'); ?>
            </p>
        </div>
        </form>

<?php
}

// Add the script
add_action('wp_head', 'add_drift');

// If we can indentify the current user output
function get_drift_identify()
{
  global $current_user;
  get_currentuserinfo();
  if ($current_user->user_email) {
    $sanitized_email = sanitize_email($current_user->user_email);
    echo "<!-- Start Identify call for Drift -->\n";
    echo "<script>\n";
    echo "drift.identify(\"".md5($sanitized_email)."\", { email: \"".$sanitized_email."\", name: \"".sanitize_text_field($current_user->user_login)."\" });\n";
    echo "</script>\n";
    echo "<!-- End Identify call for Drift -->\n";
  } else {
    // See if current user is a commenter
    $commenter = wp_get_current_commenter();
    if ($commenter['comment_author_email']) {
      echo "<!-- Start Identify call for Drift -->\n";
      echo "<script>\n";
      echo "drift.identify(\"".md5(sanitize_email($commenter['comment_author_email']))."\", { email: \"".sanitize_email($commenter['comment_author_email'])."\", name: \"".sanitize_text_field($commenter['comment_author'])."\" });\n";
      echo "</script>\n";
      echo "<!-- End Identify call for Drift -->\n";
    }
  }
}

// The guts of the Drift script
function add_drift()
{
  // Ignore admin, feed, robots or trackbacks
  if ( is_feed() || is_robots() || is_trackback() )
  {
    return;
  }

  global $current_user;
  get_currentuserinfo();
  $options = get_option('Drift_settings');

  // If options is empty then exit
  if( empty( $options ) )
  {
    return;
  }

  // Check to see if Drift is enabled
  if ( esc_attr( $options['drift_enabled'] ) == "on" )
  {
    $drift_tag = $options['drift_widget_code'];

    // Insert tracker code
    if ( '' != $drift_tag )
    {
      echo "<!-- Start Drift By WP-Plugin: Drift -->\n";
      echo $drift_tag;
      echo"<!-- end: Drift Code. -->\n";

      // Optional
      get_drift_identify();
    }
  }
}

// Delete options on uninstall
function Drift_uninstall()
{
  delete_option( 'Drift_settings' );
}
register_uninstall_hook( __FILE__, 'Drift_uninstall' );
?>
