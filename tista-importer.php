<?php
/**
 * Plugin Name: Tista Importer
 * Plugin URI: 
 * Description: Demo data impoter
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
define( 'TISTA_IMPORTER_VERSION', '4.2.1' );

/* Debug output control. */
define( 'TISTA_IMPORTER_DEBUG_OUTPUT', 0 );

/* Set constant path to the plugin directory. */
define( 'TISTA_IMPORTER_SLUG', basename( plugin_dir_path( __FILE__ ) ) );

/* Set constant path to the main file for activation call */
define( 'TISTA_IMPORTER_CORE_FILE', __FILE__ );

/* Set constant path to the plugin directory. */
define( 'TISTA_IMPORTER_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set the constant path to the plugin directory URI. */
define( 'TISTA_IMPORTER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
	
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		// Makes sure the plugin functions are defined before trying to use them.
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}
	define( 'TISTA_IMPORTER_NETWORK_ACTIVATED', is_plugin_active_for_network( TISTA_IMPORTER_SLUG . '/tista-importer.php' ) );

	/* Tista_Admin_Importer Class */
	require_once TISTA_IMPORTER_PATH . 'inc/tista-importer-functions.php';
	require_once TISTA_IMPORTER_PATH . 'inc/class-tista-admin-importer.php';

	if ( ! function_exists( 'tista_importer' ) ) :
		/**
		 * The main function responsible for returning the one true
		 * Tista_Admin_Importer Instance to functions everywhere.
		 *
		 * Use this function like you would a global variable, except
		 * without needing to declare the global.
		 *
		 * Example: <?php $tista_importer = tista_importer(); ?>
		 *
		 * @since 1.0.0
		 * @return Tista_Admin_Importer The one true Tista_Admin_Importer Instance
		 */
		function tista_importer() {
			return Tista_Admin_Importer::instance();
		}
	endif;

	/**
	 * Loads the main instance of Tista_Admin_Importer to prevent
	 * the need to use globals.
	 *
	 * This doesn't fire the activation hook correctly if done in 'after_setup_theme' hook.
	 *
	 * @since 1.0.0
	 * @return object Tista_Admin_Importer
	 */
	tista_importer();