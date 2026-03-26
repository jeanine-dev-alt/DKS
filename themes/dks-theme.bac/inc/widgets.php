<?php
/**
 * Sidebar and widget area registration.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'dks_widgets_init' ) ) :
	function dks_widgets_init() {

		register_sidebar( [
			'name'          => __( 'Primary Sidebar', 'dks-theme' ),
			'id'            => 'sidebar-primary',
			'description'   => __( 'Add widgets here to appear on blog/archive pages.', 'dks-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		] );

		register_sidebar( [
			'name'          => __( 'Footer Widget Area', 'dks-theme' ),
			'id'            => 'footer-widgets',
			'description'   => __( 'Add widgets here to appear in the footer.', 'dks-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		] );
	}
endif;
add_action( 'widgets_init', 'dks_widgets_init' );
