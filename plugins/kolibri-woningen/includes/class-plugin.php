<?php
/**
 * Plugin bootstrap — wires up all sub-components.
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	private static ?Plugin $instance = null;

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load_includes();
		$this->init_hooks();
	}

	private function load_includes(): void {
		require_once KOLIBRI_DIR . 'includes/class-post-types.php';
		require_once KOLIBRI_DIR . 'includes/class-meta-boxes.php';
		require_once KOLIBRI_DIR . 'includes/class-assets.php';
		require_once KOLIBRI_DIR . 'includes/class-template-loader.php';
		require_once KOLIBRI_DIR . 'includes/class-query.php';
		require_once KOLIBRI_DIR . 'includes/class-shortcodes.php';
		require_once KOLIBRI_DIR . 'includes/class-ajax.php';
	}

	private function init_hooks(): void {
		// i18n.
		add_action( 'init', [ $this, 'load_textdomain' ] );

		// Core registrations.
		add_action( 'init', [ Post_Types::class, 'register' ] );
		add_action( 'add_meta_boxes', [ Meta_Boxes::class, 'register' ] );
		add_action( 'save_post_kolibri_woning', [ Meta_Boxes::class, 'save' ], 10, 2 );

		// Assets.
		add_action( 'wp_enqueue_scripts',        [ Assets::class, 'enqueue_frontend' ] );
		add_action( 'admin_enqueue_scripts',      [ Assets::class, 'enqueue_admin' ] );
		add_action( 'enqueue_block_editor_assets', [ Assets::class, 'enqueue_block_editor' ] );

		// Template loader.
		add_filter( 'template_include', [ Template_Loader::class, 'load' ] );

		// Shortcodes.
		Shortcodes::register();

		// AJAX.
		Ajax::register();

		// Gutenberg blocks.
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain(
			'kolibri-woningen',
			false,
			KOLIBRI_DIR . 'languages'
		);
	}

	public function register_blocks(): void {
		$blocks = [ 'woningen-grid', 'woningen-zoekformulier' ];
		foreach ( $blocks as $block ) {
			$dir = KOLIBRI_DIR . 'blocks/' . $block;
			if ( file_exists( $dir . '/block.json' ) ) {
				register_block_type( $dir );
			}
		}
	}
}
