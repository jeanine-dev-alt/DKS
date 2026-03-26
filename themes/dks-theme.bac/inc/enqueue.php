<?php
/**
 * Enqueue scripts and styles.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'dks_enqueue_assets' ) ) :
	/**
	 * Enqueue front-end assets.
	 * Child themes can dequeue any handle and substitute their own.
	 */
	function dks_enqueue_assets() {

		// ── Google Fonts ───────────────────────────────────────────────────
		wp_enqueue_style(
			'dks-google-fonts',
			'https://fonts.googleapis.com/css2?family=Gabarito:wght@400;700;900&family=Montserrat:wght@500;700&display=swap',
			[],
			null
		);

		// ── Main theme stylesheet ──────────────────────────────────────────
		wp_enqueue_style(
			'dks-style',
			get_stylesheet_uri(),   // Automatically uses child-theme style.css when active
			[ 'dks-google-fonts' ],
			filemtime( get_stylesheet_directory() . '/style.css' )
		);

		// When a child theme is active, also load parent stylesheet explicitly.
		if ( is_child_theme() ) {
			wp_enqueue_style(
				'dks-parent-style',
				get_template_directory_uri() . '/style.css',
				[ 'dks-google-fonts' ],
				filemtime( get_template_directory() . '/style.css' )
			);
		}

		// ── Main JS ────────────────────────────────────────────────────────
		wp_enqueue_script(
			'dks-main',
			DKS_THEME_URI . '/assets/js/main.js',
			[],
			DKS_VERSION,
			true  // Load in footer
		);

		// ── Localise script (passes site data to JS) ───────────────────────
		wp_localize_script( 'dks-main', 'dksTheme', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'dks_nonce' ),
			'siteUrl' => esc_url( home_url() ),
			'i18n'    => [
				'menuOpen'  => __( 'Open menu',  'dks-theme' ),
				'menuClose' => __( 'Close menu', 'dks-theme' ),
			],
		] );

		// ── Comment reply script (WordPress standard) ──────────────────────
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'dks_enqueue_assets' );

// ── Block editor assets ────────────────────────────────────────────────────
if ( ! function_exists( 'dks_enqueue_editor_assets' ) ) :
	function dks_enqueue_editor_assets() {

		wp_enqueue_style(
			'dks-editor-style',
			DKS_THEME_URI . '/assets/css/editor-style.css',
			[],
			DKS_VERSION
		);

		wp_enqueue_script(
			'dks-blocks',
			DKS_THEME_URI . '/assets/js/blocks.js',
			[ 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-data' ],
			DKS_VERSION,
			true
		);

		// Pass REST API nonce and translations to block editor
		wp_localize_script( 'dks-blocks', 'dksBlocks', [
			'restUrl' => esc_url_raw( rest_url() ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
		] );

		// Load JS translations for blocks
		wp_set_script_translations( 'dks-blocks', 'dks-theme', DKS_THEME_DIR . '/languages' );
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dks_enqueue_editor_assets' );
