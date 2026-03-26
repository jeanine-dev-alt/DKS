<?php
/**
 * Theme setup: supports, menus, image sizes, editor styles.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'dks_theme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 * Hooked to 'after_setup_theme' so child themes can override by hooking earlier.
	 */
	function dks_theme_setup() {

		// ── Translations ───────────────────────────────────────────────────
		load_theme_textdomain( 'dks-theme', DKS_THEME_DIR . '/languages' );

		// ── WordPress core features ────────────────────────────────────────
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		] );
		add_theme_support( 'custom-logo', [
			'height'               => 48,
			'width'                => 48,
			'flex-height'          => true,
			'flex-width'           => true,
			'header-text'          => [ 'site-title', 'site-description' ],
			'unlink-homepage-logo' => false,
		] );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );

		// ── Editor colour palette (mirrors CSS variables) ──────────────────
		add_theme_support( 'editor-color-palette', [
			[
				'name'  => __( 'Primary', 'dks-theme' ),
				'slug'  => 'primary',
				'color' => '#1A1A1A',
			],
			[
				'name'  => __( 'Accent', 'dks-theme' ),
				'slug'  => 'accent',
				'color' => '#ef5225',
			],
			[
				'name'  => __( 'Background Light', 'dks-theme' ),
				'slug'  => 'background-light',
				'color' => '#F9F9F7',
			],
			[
				'name'  => __( 'Background Dark', 'dks-theme' ),
				'slug'  => 'background-dark',
				'color' => '#121212',
			],
			[
				'name'  => __( 'Surface', 'dks-theme' ),
				'slug'  => 'surface',
				'color' => '#242424',
			],
			[
				'name'  => __( 'White', 'dks-theme' ),
				'slug'  => 'white',
				'color' => '#ffffff',
			],
		] );

		// ── Editor font sizes ──────────────────────────────────────────────
		add_theme_support( 'editor-font-sizes', [
			[ 'name' => __( 'Small',    'dks-theme' ), 'slug' => 'small',    'size' => 14 ],
			[ 'name' => __( 'Normal',   'dks-theme' ), 'slug' => 'normal',   'size' => 16 ],
			[ 'name' => __( 'Large',    'dks-theme' ), 'slug' => 'large',    'size' => 20 ],
			[ 'name' => __( 'X-Large',  'dks-theme' ), 'slug' => 'x-large',  'size' => 28 ],
			[ 'name' => __( 'Huge',     'dks-theme' ), 'slug' => 'huge',     'size' => 48 ],
		] );

		// ── Navigation menus ───────────────────────────────────────────────
		register_nav_menus( [
			'primary'      => __( 'Primary Navigation', 'dks-theme' ),
			'footer'       => __( 'Footer Navigation',  'dks-theme' ),
			'footer_legal' => __( 'Footer Legal Links', 'dks-theme' ),
			'mobile'       => __( 'Mobile Navigation',  'dks-theme' ),
		] );

		// ── Image sizes ────────────────────────────────────────────────────
		add_image_size( 'dks-hero',         1920, 1080, true  );
		add_image_size( 'dks-property-card', 480,  600, true  );
		add_image_size( 'dks-property-full', 1200, 800, true  );
		add_image_size( 'dks-thumbnail',      400, 300, true  );

		// ── Post formats (optional, useful for blog/news) ──────────────────
		add_theme_support( 'post-formats', [ 'image', 'gallery', 'video' ] );

		// ── Editor styles ──────────────────────────────────────────────────
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'dks_theme_setup' );

// ── Content width ──────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_content_width' ) ) :
	function dks_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'dks_content_width', 1280 );
	}
endif;
add_action( 'after_setup_theme', 'dks_content_width', 0 );
