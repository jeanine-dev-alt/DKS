<?php
/**
 * Listings hooks — integration points for the child theme.
 *
 * This file documents every filter and action the listings system exposes.
 * The parent theme provides sensible HTML defaults; the child theme can
 * swap in real CPT data without modifying any parent files.
 *
 * CHILD THEME INTEGRATION EXAMPLES
 * ─────────────────────────────────
 *
 * 1. Replace placeholder cards with real 'property' CPT data:
 *
 *    add_filter( 'dks_listings_items', 'my_child_get_properties', 10, 2 );
 *    function my_child_get_properties( $default_items, $block_attributes ) {
 *        $query = new WP_Query([
 *            'post_type'      => 'property',
 *            'posts_per_page' => $block_attributes['columns'] ?? 3,
 *            'post_status'    => 'publish',
 *        ]);
 *        $items = [];
 *        if ( $query->have_posts() ) {
 *            while ( $query->have_posts() ) {
 *                $query->the_post();
 *                $items[] = [
 *                    'image'     => get_the_post_thumbnail_url( null, 'dks-property-card' ),
 *                    'badge'     => get_post_meta( get_the_ID(), '_dks_badge', true ),
 *                    'price'     => get_post_meta( get_the_ID(), '_dks_price', true ),
 *                    'location'  => get_post_meta( get_the_ID(), '_dks_location', true ),
 *                    'beds'      => (int) get_post_meta( get_the_ID(), '_dks_beds', true ),
 *                    'baths'     => (int) get_post_meta( get_the_ID(), '_dks_baths', true ),
 *                    'sqm'       => get_post_meta( get_the_ID(), '_dks_sqm', true ),
 *                    'permalink' => get_permalink(),
 *                ];
 *            }
 *            wp_reset_postdata();
 *        }
 *        return $items;
 *    }
 *
 * 2. Replace the newsletter form with a shortcode:
 *
 *    add_filter( 'dks_newsletter_form_html', function( $html, $atts ) {
 *        return do_shortcode( '[mailchimp_form]' );
 *    }, 10, 2 );
 *
 * 3. Filter the hero background image (e.g. per-page ACF field):
 *
 *    add_filter( 'dks_hero_background_url', function( $url, $atts ) {
 *        if ( is_front_page() ) {
 *            $img = get_field( 'hero_image' );
 *            return $img ? $img['url'] : $url;
 *        }
 *        return $url;
 *    }, 10, 2 );
 *
 * 4. Add new post types that use property meta boxes:
 *
 *    add_filter( 'dks_property_post_types', function( $types ) {
 *        $types[] = 'property';
 *        return $types;
 *    } );
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// This file is intentionally empty at run time — it contains only
// documentation. All hooks are defined inline in blocks.php and meta-boxes.php.
