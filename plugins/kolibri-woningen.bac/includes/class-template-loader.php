<?php
/**
 * Loads plugin templates for archive and single views,
 * falling back to the active theme if a theme override exists.
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Template_Loader {

	public static function load( string $template ): string {
		if ( is_singular( 'kolibri_woning' ) ) {
			return self::locate( 'single-kolibri_woning.php', $template );
		}

		if ( is_post_type_archive( 'kolibri_woning' )
			|| is_tax( [ 'kolibri_type', 'kolibri_status', 'kolibri_stad', 'kolibri_energie' ] )
		) {
			return self::locate( 'archive-kolibri_woning.php', $template );
		}

		return $template;
	}

	/**
	 * Locate template: theme > plugin.
	 */
	private static function locate( string $filename, string $fallback ): string {
		// Allow theme override: place file in theme root.
		$theme_file = locate_template( [ $filename ] );
		if ( $theme_file ) {
			return $theme_file;
		}

		$plugin_file = KOLIBRI_DIR . 'templates/' . $filename;
		if ( file_exists( $plugin_file ) ) {
			return $plugin_file;
		}

		return $fallback;
	}

	/**
	 * Load a partial from /templates/partials/, with optional data.
	 */
	public static function partial( string $name, array $data = [] ): void {
		$file = KOLIBRI_DIR . 'templates/partials/' . $name . '.php';
		if ( ! file_exists( $file ) ) {
			return;
		}
		if ( $data ) {
			extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract
		}
		include $file;
	}
}
