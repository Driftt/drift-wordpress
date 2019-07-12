<?php
/**
* Loader for hooks.
*
* Register all actions and filters for the plugin.
*
* @package Drift
* @subpackage Drift/includes
* @since 2.0.0
*/

if ( ! class_exists( 'Drift_Loader' ) ) {
	/**
	 * Stores the filters and the actions of the plugin and execute them.
	 *
	 * Maintain a list of all hooks that are registered throughout
	 * the plugin, and register them with the WordPress API. Call the
	 * run function to execute the list of actions and filters.
	 *
	 * @since 2.0.0
	 */
	class Drift_Loader {

		/**
		 * The array of actions registered with WordPress.
		 *
		 * @since 2.0.0
		 * @access protected
		 * @var array $actions The actions registered with WordPress to fire when the plugin loads.
		 */
		protected $actions;

		/**
		 * The array of filters registered with WordPress.
		 *
		 * @since 2.0.0
		 * @access protected
		 * @var array $filters The filters registered with WordPress to fire when the plugin loads.
		 */
		protected $filters;

		/**
		 * Constructor method.
		 *
		 * Initiates the collections used to maintain the actions and filters.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			$this->actions = array();
			$this->filters = array();
		}

		/**
		 * Adds actions.
		 *
		 * Adds a new action to the collection to be registered with WordPress.
		 *
		 * @since 2.0.0
		 *
		 * @param string $hook      Required. Name of the hook.
		 * @param string $component Required. Name of the class which $callback belongs to.
		 * @param string $callback  Required. Name of the method (function) from the $component class.
		 * @param int    $priority  Optional. Default 10. Priority of the $callback.
		 * @param int    $acc_args  Optional. Default 1. Number of the arguments of the $callback.
		 */
		public function add_action( $hook, $component, $callback, $priority = 10, $acc_args = 1 ) {
			$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $acc_args );
		}

		/**
		 * Adds filters.
		 *
		 * Adds a new filter to the collection to be registered with WordPress.
		 *
		 * @since 2.0.0
		 *
		 * @param string $hook      Required. Name of the hook.
		 * @param string $component Required. Name of the class which $callback belongs to.
		 * @param string $callback  Required. Name of the method (function) from the $component class.
		 * @param int    $priority  Optional. Default 10. Priority of the $callback.
		 * @param int    $acc_args  Optional. Default 1. Number of the arguments of the $callback.
		 */
		public function add_filter( $hook, $component, $callback, $priority = 10, $acc_args = 1 ) {
			$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $acc_args );
		}

		/**
		 * Registers actions and filters.
		 *
		 * A utility function that is used to register the actions and hooks into a single collection.
		 *
		 * @since 2.0.0
		 * @access  private
		 *
		 * @param string $hooks     Required. The hooks property array.
		 * @param string $hook      Required. Name of the hook.
		 * @param string $component Required. Name of the class which $callback belongs to.
		 * @param string $callback  Required. Name of the method (function) from the $component class.
		 * @param int    $priority  Optional. Default 10. Priority of the $callback.
		 * @param int    $acc_args  Optional. Default 1. Number of the arguments of the $callback.
		 *
		 * @return array An array with all the actions and filters.
		 */
		private function add( $hooks, $hook, $component, $callback, $priority, $acc_args ) {
			$hooks[] = array(
				'hook'      => $hook, // tag
				'component' => $component, // class
				'callback'  => $callback, // method
				'priority'  => $priority, // priority
				'acc_args'  => $acc_args, // accepted args
			);
			return $hooks;
		}

		/**
		 * Runs the hooks.
		 * Runs the filters and the actions from the property arrays.
		 *
		 * @since 2.0.0
		 *
		 * @see add_filter function is relied on
		 * @link https://developer.wordpress.org/reference/functions/add_filter/
		 *
		 * @see add_action function is relied on
		 * @link https://developer.wordpress.org/reference/functions/add_action/
		 */
		public function run() {
			foreach ( $this->filters as $hook ) {
				add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['acc_args'] );
			}

			foreach ( $this->actions as $hook ) {
				add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['acc_args'] );
			}
		}
	}
}
