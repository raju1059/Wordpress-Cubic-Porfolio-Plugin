<?php
/**
 * Plugin Name: Tista cubeportfolio
 * Plugin URI: 
 * Description: Nice cubeportfolio page
 * Version: 4.2.1
 * Author: TistaTeam
 * Author URI: 
 * Requires at least: 
 * Tested up to: 
 *
 * @package TistaTeam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* Set plugin version constant. */
define( 'TISTA_CUBEPORTFOLIO_VERSION', '4.2.1' );

/* Debug output control. */
define( 'TISTA_CUBEPORTFOLIO_DEBUG_OUTPUT', 0 );

/* Set constant path to the plugin directory. */
define( 'TISTA_CUBEPORTFOLIO_SLUG', basename( plugin_dir_path( __FILE__ ) ) );

/* Set constant path to the main file for activation call */
define( 'TISTA_CUBEPORTFOLIO_CORE_FILE', __FILE__ );

/* Set constant path to the plugin directory. */
define( 'TISTA_CUBEPORTFOLIO_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set the constant path to the plugin directory URI. */
define( 'TISTA_CUBEPORTFOLIO_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
	
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		// Makes sure the plugin functions are defined before trying to use them.
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}
	define( 'TISTA_CUBEPORTFOLIO_NETWORK_ACTIVATED', is_plugin_active_for_network( TISTA_CUBEPORTFOLIO_SLUG . '/tista-cubeportfolio.php' ) );

	/* Tista_Cubeportfolio Class */
	require_once TISTA_CUBEPORTFOLIO_PATH . 'inc/class-tista-cubeportfolio.php';

	if ( ! function_exists( 'tista_cubeportfolio' ) ) :
		/**
		 * The main function responsible for returning the one true
		 * Tista_Cubeportfolio Instance to functions everywhere.
		 *
		 * Use this function like you would a global variable, except
		 * without needing to declare the global.
		 *
		 * Example: <?php $tista_cubeportfolio = tista_cubeportfolio(); ?>
		 *
		 * @since 1.0.0
		 * @return Tista_Cubeportfolio The one true Tista_Cubeportfolio Instance
		 */
		function tista_cubeportfolio() {
			return Tista_Cubeportfolio::instance();
		}
	endif;

	/**
	 * Loads the main instance of Tista_Cubeportfolio to prevent
	 * the need to use globals.
	 *
	 * This doesn't fire the activation hook correctly if done in 'after_setup_theme' hook.
	 *
	 * @since 1.0.0
	 * @return object Tista_Cubeportfolio
	 */
	tista_cubeportfolio();