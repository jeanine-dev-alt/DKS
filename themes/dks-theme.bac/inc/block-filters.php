<?php
/**
 * Block editor filters: allowed blocks, block patterns.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Register block patterns ────────────────────────────────────────────────
if ( ! function_exists( 'dks_register_block_patterns' ) ) :
	function dks_register_block_patterns() {

		register_block_pattern_category( 'dks', [
			'label' => __( 'DKS Real Estate', 'dks-theme' ),
		] );

		// ── Homepage full-page pattern ─────────────────────────────────────
		register_block_pattern( 'dks-theme/homepage', [
			'title'      => __( 'DKS Homepage', 'dks-theme' ),
			'categories' => [ 'dks' ],
			'description' => __( 'Full homepage layout with hero, listings, features, and newsletter.', 'dks-theme' ),
			'content'    => '<!-- wp:dks/hero {"backgroundUrl":"","heading":"A NEW <span class=\"accent\">BEGINNING<\/span> IN YOUR DREAM HOME","subheading":"Expert guidance for buying and renting premium properties in the heart of the Netherlands.","btnPrimaryText":"View Listings","btnPrimaryUrl":"#","btnSecondText":"Contact Us","btnSecondUrl":"#"} /-->
<!-- wp:dks/listings /-->
<!-- wp:dks/features /-->
<!-- wp:dks/newsletter /-->',
		] );

		// ── Hero-only pattern ─────────────────────────────────────────────
		register_block_pattern( 'dks-theme/hero-only', [
			'title'      => __( 'DKS Hero Banner', 'dks-theme' ),
			'categories' => [ 'dks', 'header' ],
			'description' => __( 'Standalone hero block — use on any page.', 'dks-theme' ),
			'content'    => '<!-- wp:dks/hero {"heading":"YOUR PAGE TITLE HERE","subheading":"A short supporting description for this page."} /-->',
		] );
	}
endif;
add_action( 'init', 'dks_register_block_patterns' );
