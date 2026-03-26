<?php
/**
 * Plugin Name:       Kolibri Woningen
 * Plugin URI:        https://jeanine.marketing
 * Description:       Professioneel woningbeheer en -presentatie voor WordPress. Custom post type, uitgebreide metavelden, AJAX-filters, Gutenberg-blokken en galerij met lightbox.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      8.1
 * Author:            Jeanine.marketing
 * Author URI:        https://jeanine.marketing
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kolibri-woningen
 * Domain Path:       /languages
 *
 * @package KolibriWoningen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'KOLIBRI_VERSION',  '1.0.0' );
define( 'KOLIBRI_DIR',      plugin_dir_path( __FILE__ ) );
define( 'KOLIBRI_URI',      plugin_dir_url( __FILE__ ) );
define( 'KOLIBRI_BASENAME', plugin_basename( __FILE__ ) );

// Autoload classes.
spl_autoload_register( function ( string $class ): void {
	$prefix = 'KolibriWoningen\\';
	$len    = strlen( $prefix );

	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative = substr( $class, $len );
	$file     = KOLIBRI_DIR . 'includes/class-' . strtolower( str_replace( [ '\\', '_' ], [ '/', '-' ], $relative ) ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * Bootstrap.
 */
function kolibri_run(): void {
	require_once KOLIBRI_DIR . 'includes/class-plugin.php';
	\KolibriWoningen\Plugin::get_instance();
}
add_action( 'plugins_loaded', 'kolibri_run' );

/**
 * Activation hook — flush rewrite rules after CPT registration.
 */
register_activation_hook( __FILE__, function (): void {
	require_once KOLIBRI_DIR . 'includes/class-post-types.php';
	\KolibriWoningen\Post_Types::register();
	flush_rewrite_rules();
} );

/**
 * Deactivation hook.
 */
register_deactivation_hook( __FILE__, function (): void {
	flush_rewrite_rules();
} );
