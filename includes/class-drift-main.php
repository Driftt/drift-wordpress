<?php
/**
 * Settings API: drift_settings
 *
 * Registers the plugin settings and functionalities.
 *
 * @package Drift
 * @subpackage Drift/includes
 * @since 2.0.0
 */

if ( ! class_exists( 'Drift_Main' ) ) {
	/**
	* The main class for the plugin.
	*
	* Coordinates all the things for doing a great work!
	*
	* @since 2.0.0
	*/
	class Drift_Main {

		/**
		 * The version of the plugin.
		 *
		 * @since 2.0.0
		 * @access protected
		 * @var string $version The version of the plugin, stored for releases updates. Please
		 *                      update it in the below __constructor for every version change.
		 */
		protected $version;

		/**
		 * The URL of the plugin.
		 *
		 * @since 2.0.0
		 * @access protected
		 * @var string $plugin_url Stores the URL of the plugin for further scripts.
		 */
		protected $plugin_url;

		/**
		 * The loader for hooks.
		 *
		 * @since 2.0.0
		 * @access protected
		 * @var string $loader Stores the object of the Drift_Loader class.
		 */
		protected $loader;

		/**
		 * Initiates the main settings of the plugin.
		 *
		 * Defines the properties of the class and defines hooks:
		 * - plugin URL;
		 * - plugin version;
		 * - hooks.
		 *
		 * @since 2.0.0
		 *
		* @see plugins_url function is relied on
		* @link https://developer.wordpress.org/reference/functions/plugins_url/
		 */
		public function __construct() {
			$this->plugin_url = plugins_url( '', dirname( __FILE__ ) );
			$this->version    = '2.0.0';
			$this->define_hooks();
		}

		/**
		 * Adds the hooks.
		 *
		 * Initializes the Loader.
		 * Adds filters and actions:
		 * - localization
		 * - registers options for plugin settings
		 * - enqueues scripts
		 *
		 * @since 2.0.0
		 * @access private
		 *
		 * @see plugin_dir_path function is relied on
		 * @link https://developer.wordpress.org/reference/functions/plugin_dir_path/
		 *
		 * @see add_action function is relied on
		 * @link https://developer.wordpress.org/reference/functions/add_action/
		 *
		 * @see add_filter function is relied on
		 * @link https://developer.wordpress.org/reference/functions/add_filter/
		 */
		private function define_hooks() {
			require_once( plugin_dir_path( __FILE__ ) . 'class-drift-loader.php' );
			$this->loader = new Drift_Loader();
			$this->loader->add_action( 'plugins_loaded', $this, 'load_textdomain' );
			$this->loader->add_action( 'admin_bar_menu', $this, 'add_link_to_admin_bar', 999 );
			$this->loader->add_action( 'admin_menu', $this, 'add_settings_page' );
			$this->loader->add_filter( 'plugin_action_links_drift/drift.php', $this, 'add_action_links' );
			$this->loader->add_action( 'admin_init', $this, 'register_settings' );
			$this->loader->add_action( 'admin_enqueue_scripts', $this, 'settings_enqueue_script' );
			$this->loader->add_action( $this->get_js_load_hook(), $this, 'enqueue_script' );
		}

		/**
		 * Translations.
		 *
		 * Loads translation for the plugin.
		 *
		 * @since 2.0.0
		 *
		 * @see load_plugin_textdomain function is relied on
		 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
		 *
		 * @see plugin_basename function is relied on
		 * @link https://developer.wordpress.org/reference/functions/plugin_basename/
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'drift', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );
		}

		/**
		 * Top-level admin bar link.
		 *
		 * Adds a link to Drift dashboard settings in the admin bar.
		 *
		 * @since 2.0.0
		 *
		 * @see current_user_can function is relied on
		 * @link https://developer.wordpress.org/reference/functions/current_user_can/
		 *
		 * @see admin_url function is relied on
		 * @link https://developer.wordpress.org/reference/functions/admin_url/
		 *
		 * @see __ function is relied on
		 * @link https://developer.wordpress.org/reference/functions/__/
		 *
		 * @global object $wp_admin_bar Used to add the new node.
		 */
		public function add_link_to_admin_bar() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			global $wp_admin_bar;
			$icon = '<img src="' . $this->plugin_url . '/public/images/drift-icon-16x16-white.png" />';
			$args = array(
				'id'     => 'drift-admin-menu',
				'title'  => '<span class="ab-icon">' . $icon . '</span><span class="ab-label">' . __( 'Drift', 'drift' ) . '</span>',
				'parent' => false, // Set parent to false to make it a top level (parent) node
				'href'   => admin_url( 'options-general.php?page=drift-settings' ),
				'meta'   => array( 'title' => __( 'Drift', 'drift' ) ),
			);
			$wp_admin_bar->add_node( $args );
		}

		/**
		 * Creates an options page for settings.
		 *
		 * Hooks in the options page functions.
		 *
		 * @since 2.0.0
		 *
		 * @see add_options_page function relied on
		 * @link https://developer.wordpress.org/reference/functions/add_options_page/
		 *
		 * @see __ function relied on
		 * @link https://developer.wordpress.org/reference/functions/__/
		 */
		public function add_settings_page() {
			add_options_page( __( 'Drift options', 'drift' ), __( 'Drift', 'drift' ), 'activate_plugins', 'drift-settings', array( $this, 'render_options_page' ) );
		}

		/**
		 * Plugin settings link.
		 *
		 * Adds a settings link to the plugin in plugins list.
		 *
		 * @since 2.0.0
		 *
		 * @see admin_url function is relied on
		 * @link https://developer.wordpress.org/reference/functions/admin_url/
		 *
		 * @see __ function is relied on
		 * @link https://developer.wordpress.org/reference/functions/__/
		 *
		 * @param array $links Used to concatenate the plugin link.
		 */
		public function add_action_links( $links ) {
			$mylink = array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=drift-settings' ) . '">' . __( 'Settings', 'drift' ) . '</a>',
			);
			return array_reverse( array_merge( $links, $mylink ) );
		}

		/**
		 * Registers settings.
		 *
		 * Initiates and registers the settings of the plugin.
		 *
		 * @since 2.0.0
		 *
		 * @see register_setting function is relied on
		 * @link https://developer.wordpress.org/reference/functions/register_setting/
		 *
		 * @see add_settings_section function is relied on
		 * @link https://developer.wordpress.org/reference/functions/add_settings_section/
		 *
		 * @see add_settings_field function is relied on
		 * @link https://developer.wordpress.org/reference/functions/add_settings_field/
		 *
		 * @see __ function is relied on
		 * @link https://developer.wordpress.org/reference/functions/__/
		 */
		public function register_settings() {
			$args = array(
				'type'              => 'array',
				/* translators: Description of the registered setting for the plugin */
				'description'       => __( 'Form options for Drift plugin.', 'drift' ),
				'sanitize_callback' => array( $this, 'sanitize' ),
				'show_in_rest'      => false,
			);
			register_setting( 'drift_group', 'drift_options', $args );
			add_settings_section( 'drift_options_section', '', '', 'drift_group' );
			add_settings_field( 'activation', __( 'Drift', 'drift' ), array( $this, 'activation_render' ), 'drift_group', 'drift_options_section' );
			add_settings_field( 'identifying', __( 'Identifying', 'drift' ), array( $this, 'identifying_render' ), 'drift_group', 'drift_options_section' );
			add_settings_field( 'hideon_pages', __( 'Hide on these pages', 'drift' ), array( $this, 'hideon_pages_render' ), 'drift_group', 'drift_options_section' );
			add_settings_field( 'code_snippet', __( 'JS code snippet', 'drift' ), array( $this, 'code_snippet_render' ), 'drift_group', 'drift_options_section' );
			add_settings_field( 'js_hook', __( 'JS hook', 'drift' ), array( $this, 'js_hook_render' ), 'drift_group', 'drift_options_section' );
		}

		/**
		 * Renders Drift settings page.
		 *
		 * Hooks in the options page functions.
		 *
		 * @since 2.0.0
		 *
		 * @see current_user_can function relied on
		 * @link https://developer.wordpress.org/reference/functions/current_user_can/
		 *
		 * @see _e function relied on
		 * @link https://developer.wordpress.org/reference/functions/_e/
		 *
		 * @see settings_fields function relied on
		 * @link https://developer.wordpress.org/reference/functions/settings_fields/
		 *
		 * @see do_settings_sections function relied on
		 * @link https://developer.wordpress.org/reference/functions/settings_fields/
		 *
		 * @see submit_button function relied on
		 * @link https://developer.wordpress.org/reference/functions/submit_button/
		 */
		public function render_options_page() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			?>
			<div class="wrap">
				<h1><?php _e( 'Drift settings', 'drift' ); ?></h1>
				<form action="options.php" method="post">
					<?php
					settings_fields( 'drift_group' );
					do_settings_sections( 'drift_group' );
					submit_button();
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Sanitizes the input.
		 *
		 * Checks the input settings and sanitizes them.
		 *
		 * @since 2.0.0
		 *
		 * @see sanitize_text_field function relied on
		 * @link https://developer.wordpress.org/reference/functions/sanitize_text_field/
		 *
		 * @see absint function relied on
		 * @link https://developer.wordpress.org/reference/functions/absint/
		 *
		 * @param array $input Data from Drift settings page.
		 * @return array Returns sanitized input.
		 */
		public function sanitize( $input ) {
			if ( isset( $input ) ) {
				if ( ! empty( $input['activation'] ) ) {
					$input['activation'] = sanitize_text_field( 'on' );
				} else {
					unset( $input['activation'] );
				}

				if ( ! empty( $input['identifying'] ) ) {
					$input['identifying'] = sanitize_text_field( 'on' );
				} else {
					unset( $input['identifying'] );
				}

				if ( ! empty( $input['hideon_pages'] ) ) {
					$input['hideon_pages'] = array_map( 'absint', explode( ',', $input['hideon_pages'] ) );
					$pages                 = '';
					foreach ( $input['hideon_pages'] as $id ) {
						if ( $id ) {
							$pages .= $id . ',';
						}
					}
					$input['hideon_pages'] = sanitize_text_field( $pages );
				} else {
					unset( $input['hideon_pages'] );
				}

				if ( empty( $input['code_snippet'] ) ) {
					unset( $input['code_snippet'] );
				}

				if ( ! empty( $input['js_hook'] ) ) {
					$input['js_hook'] = sanitize_text_field( 'on' );
				} else {
					unset( $input['js_hook'] );
				}
			}
			return $input;
		}

		/**
		 * Enqueues script in dashboard.
		 *
		 * Adds custom jQuery script in dashboard plugin settings page.
		 *
		 * @since 2.0.0
		 *
		 * @param string $hook Stores the hook of the page.
		 *
		 * @see wp_register_script function is relied on
		 * @link https://developer.wordpress.org/reference/functions/wp_register_script/
		 *
		 * @see wp_enqueue_script function is relied on
		 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
		 */
		public function settings_enqueue_script( $hook ) {
			if ( 'settings_page_drift-settings' !== $hook ) {
				return;
			}
			wp_register_script( 'drift-custom-js', $this->plugin_url . '/admin/js/custom.min.js' );
			wp_enqueue_script( 'drift-custom-js' );
		}

		/**
		 * Determines the hook.
		 *
		 * Determines the hook where Drift JavaScript will be loaded.
		 *
		 * @since 2.0.0
		 * @access private
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 *
		 * @return string wp_footer Drift JS will be loaded in the site footer (for page speed)
		 *                wp_head   Drift JS will be loaded in the site header (for quickly show live chat)
		 */
		private function get_js_load_hook() {
			$options = get_option( 'drift_options' );
			return isset( $options['js_hook'] ) ? 'wp_footer' : 'wp_head';
		}

		/**
		 * Enqueues scripts in front pages.
		 *
		 * Enqueues the javascript from Drift options in pages and posts.
		 *
		 * @since 2.0.0
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 *
		 * @see get_the_ID function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_the_ID/
		 *
		 * @see absint function is relied on
		 * @link https://developer.wordpress.org/reference/functions/absint/
		 *
		 * @see is_feed function is relied on
		 * @link https://developer.wordpress.org/reference/functions/is_feed/
		 *
		 * @see is_robots function is relied on
		 * @link https://developer.wordpress.org/reference/functions/is_robots/
		 *
		 * @see is_trackback function is relied on
		 * @link https://developer.wordpress.org/reference/functions/is_trackback/
		 *
		 * @see is_single function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_the_ID/
		 *
		 * @see is_page function is relied on
		 * @link https://developer.wordpress.org/reference/functions/is_page/
		 *
		 * @see __ function is relied on
		 * @link https://developer.wordpress.org/reference/functions/__/
		 */
		public function enqueue_script() {
			$options      = get_option( 'drift_options' );
			$this_page_id = get_the_ID();
			if ( isset( $options['hideon_pages'] ) ) {
				$hideon_pages = array_map( 'absint', explode( ',', $options['hideon_pages'] ) );
			} else {
				$hideon_pages = array();
			}
			if ( is_feed() || is_robots() || is_trackback() ||
			( is_single() || is_page() ) && in_array( $this_page_id, $hideon_pages, true ) ||
			empty( $options ) || ! isset( $options['activation'] ) ||
			! isset( $options['code_snippet'] ) ) {
				return;
			}
			printf(
				/* translators: 1: Opening HTML comment 2: Closing HTML comment 3: HTML JavaScript tag */
				__( '%1$s Start code snippet by Drift plugin %2$s%3$s%1$s End code snippet by Drift plugin %2$s', 'drift' ),
				'<!--', // %1$s
				'-->',  // %2$s
				$options['code_snippet'] // %3$s
			);
			if ( isset( $options['identifying'] ) ) {
				$this->identifying_user();
			}
		}

		/**
		 * Rock'n roll the world.
		 *
		 * Run the loader with filters and actions.
		 *
		 * @since 2.0.0
		 */
		public function run() {
			$this->loader->run();
		}

		/**
		 * Drift settings: `activation` field.
		 *
		 * Renders the checkbox input for Drift plugin settings page.
		 * Permits activation/deactivation of the Drift plugin effect.
		 *
		 * @since 2.0.0
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 *
		 * @see checked function is relied on
		 * @link https://developer.wordpress.org/reference/functions/checked/
		 *
		 * @see _e function is relied on
		 * @link https://developer.wordpress.org/reference/functions/_e/
		 */
		public function activation_render() {
			$options = get_option( 'drift_options' );
			?>
			<label for="activation">
				<input type="checkbox" name="drift_options[activation]" id="activation" <?php isset( $options['activation'] ) ? checked( $options['activation'], 'on' ) : ''; ?> />
				<?php _e( 'Enable Drift plugin and the live chat on your site', 'drift' ); ?>
			</label>
			<?php
		}

		/**
		 * Drift settings: `identifying` field.
		 *
		 * Renders the checkbox input for Drift plugin settings page.
		 * Whether to allow the identifying of the authenticated user on the site.
		 *
		 * @since 2.0.0
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 *
		 * @see checked function is relied on
		 * @link https://developer.wordpress.org/reference/functions/checked/
		 *
		 * @see _e function is relied on
		 * @link https://developer.wordpress.org/reference/functions/_e/
		 */
		public function identifying_render() {
			$options = get_option( 'drift_options' );
			?>
			<label for="identifying">
				<input type="checkbox" name="drift_options[identifying]" id="identifying" <?php isset( $options['identifying'] ) ? checked( $options['identifying'], 'on' ) : ''; ?> />
				<?php _e( 'Enable identifying authenticated users', 'drift' ); ?>
			</label>
			<?php
		}

		/**
		 * Drift settings: `hide on these pages` field.
		 *
		 * Renders the text input for Drift plugin settings page.
		 * Permits to set the posts/pages where the Drift live chat not appear.
		 *
		 * @since 2.0.0
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 */
		public function hideon_pages_render() {
			$options = get_option( 'drift_options' );
			?>
			<label for="hideon_pages">
				<?php /* translators: 1: Page IDs examples separated by comma. */ ?>
				<input type="text" name="drift_options[hideon_pages]" placeholder="<?php _e( '1,23,456', 'drift' ); ?>" id="hideon_pages" value="<?php echo isset( $options['hideon_pages'] ) ? $options['hideon_pages'] : ''; ?>" />
				<?php
				printf(
					/* translators: 1: Opening HTML span tag 2: Closing HTML span tag */
					__( '%1$sYou can add many IDs of posts or pages separated by comma where live chat does not appear.%2$s', 'drift' ),
					'<span class="description">',
					'</span>'
				);
				?>
			</label>
			<?php
		}

		/**
		 * Drift settings: `code snippet` field.
		 *
		 * Renders the textarea for Drift plugin settings page.
		 * Permits to enter the JavaScript from Drift
		 *
		 * @link https://app.drift.com/settings/widget
		 *
		 * @since 2.0.0
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 *
		 * @see _e function is relied on
		 * @link https://developer.wordpress.org/reference/functions/_e/
		 *
		 * @see __ function is relied on
		 * @link https://developer.wordpress.org/reference/functions/__/
		 */
		public function code_snippet_render() {
			$options = get_option( 'drift_options' );
			?>
			<textarea rows="15" cols="100" id="code_snippet" placeholder="<!-- <?php _e( 'Insert the Drift tag here', 'drift' ); ?> -->" name="drift_options[code_snippet]"><?php echo isset( $options['code_snippet'] ) ? $options['code_snippet'] : ''; ?></textarea>
			<p class="description">
			<?php
			printf(
				/* translators: 1: Opening HTML a tag 2: Closing HTML a tag */
				__( 'Enter your Drift JS code snippet. You can find your Drift JS code snippet %1$shere%2$s. A Free Drift account is required to use this plugin.', 'drift' ),
				'<a href="https://app.drift.com/settings/widget" target="_blank" title="' . __( 'Open Drift Settings', 'drift' ) . '">', // %1$s
				'</a>' // %2$s
			);
			?>
			</p>
			<?php
		}

		/**
		 * Drift settings: `JS hook` field.
		 *
		 * Renders the checkbox input for Drift plugin settings page.
		 * Permits to load the JavaScript in site header or footer.
		 *
		 * @since 2.0.0
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 *
		 * @see checked function is relied on
		 * @link https://developer.wordpress.org/reference/functions/checked/
		 *
		 * @see _e function is relied on
		 * @link https://developer.wordpress.org/reference/functions/_e/
		 */
		public function js_hook_render() {
			$options = get_option( 'drift_options' );
			?>
			<label for="js_hook">
				<input type="checkbox" name="drift_options[js_hook]" id="js_hook" <?php isset( $options['js_hook'] ) ? checked( $options['js_hook'], 'on' ) : ''; ?> />
				<?php _e( 'Enable to load the above javascript in the site footer', 'drift' ); ?>
			</label>
			<?php
		}

		/**
		 * Identifies the users.
		 *
		 * Identifies users from their email comments or from their role if they are logged in.
		 *
		 * @since 2.0.0
		 * @access private
		 *
		 * @see wp_get_current_user function is relied on
		 * @link https://developer.wordpress.org/reference/functions/wp_get_current_user/
		 *
		 * @see sanitize_email function is relied on
		 * @link https://developer.wordpress.org/reference/functions/sanitize_email/
		 *
		 * @see __ function is relied on
		 * @link https://developer.wordpress.org/reference/functions/__/
		 *
		 * @see sanitize_text_field function is relied on
		 * @link https://developer.wordpress.org/reference/functions/sanitize_text_field/
		 *
		 * @see wp_get_current_commenter function is relied on
		 * @link https://developer.wordpress.org/reference/functions/wp_get_current_commenter/
		 */
		private function identifying_user() {
			$current_user = wp_get_current_user();
			if ( $current_user->user_email ) {
				$email = sanitize_email( $current_user->user_email );
				printf(
					/* translators: 1: Opening HTML comment 2: Closing HTML comment 3: HTML JavaScript tag */
					__( '%1$s Start identify call for Drift %2$s%3$s%1$s End identify call for Drift %2$s', 'drift' ),
					'<!--', // %1$s
					'-->', // %2$s
					'<script>
					drift.identify("' . md5( $email ) . '", { email: "' . $email . '", name: "' . sanitize_text_field( $current_user->user_login ) . '", userRole: "' . sanitize_text_field( $current_user->roles[0] ) . '" });
					</script>' // %3$s
				);
			} else {
				$commenter = wp_get_current_commenter();
				if ( isset( $commenter['comment_author_email'] ) ) {
					$email = sanitize_email( $commenter['comment_author_email'] );
					printf(
						/* translators: 1: Opening HTML comment 2: Closing HTML comment 3: HTML JavaScript tag */
						__( '%1$s Start identify call for Drift %2$s%3$s%1$s End identify call for Drift %2$s', 'drift' ),
						'<!--', // %1$s
						'-->', // %2$s
						'<script>
						drift.identify("' . md5( $email ) . '", { email: "' . $email . '", name: "' . sanitize_text_field( $commenter['comment_author'] ) . '" });
						</script>' // %3$s
					);
				}
			}
		}

		/**
		 * Releases updates.
		 *
		 * Makes _options database table modifications if the new release has other data configurations.
		 * This static method is only used in register_activation_hook() function from drift.php file.
		 *
		 * @since 2.0.0
		 * @access private
		 * @todo Modify this method after all the users which use Drift 1.8.4 have beed updated to this one version.
		 *
		 * @see get_option function is relied on
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 *
		 * @see maybe_serialize function is relied on
		 * @link https://developer.wordpress.org/reference/functions/maybe_serialize/
		 *
		 * @global object $wpdb Used for database queries.
		 */
		public static function release_updates() {
			if ( version_compare( $this->version, '1.8.4', '>' ) ) {
				$old = get_option( 'Drift_settings' ); // The old name of the option
				if ( isset( $old ) ) {
					global $wpdb;
					$new = array(); // The new data for Drift
					if ( isset( $old['drift_enabled'] ) && 'on' == $old['drift_enabled'] ) {
						$new['activation'] = 'on';
					}
					if ( isset( $old['drift_identify'] ) ) {
						$new['identifying'] = 'on';
					}
					if ( isset( $old['drift_widget_code'] ) ) {
						$new['code_snippet'] = $old['drift_widget_code'];
					}
					$wpdb->query(
						$wpdb->prepare(
							"UPDATE $wpdb->options
							SET option_name = %s, option_value = %s
							WHERE option_name = %s",
							'drift_options',
							maybe_serialize( $new ),
							'Drift_settings'
						)
					);
				}
			}
		}
	}
}
