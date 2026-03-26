<?php
/**
 * Archive template — Kolibri Woningen.
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Query;
use KolibriWoningen\Shortcodes;
use KolibriWoningen\Template_Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Apply filters from GET params.
$filters = [
	'type'      => sanitize_text_field( wp_unslash( $_GET['type']      ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification
	'stad'      => sanitize_text_field( wp_unslash( $_GET['stad']      ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification
	'status'    => sanitize_text_field( wp_unslash( $_GET['status']    ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification
	'energie'   => sanitize_text_field( wp_unslash( $_GET['energie']   ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification
	'min_prijs' => (int) ( $_GET['min_prijs'] ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'max_prijs' => (int) ( $_GET['max_prijs'] ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'min_m2'    => (int) ( $_GET['min_m2']    ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'kamers'    => (int) ( $_GET['kamers']    ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'paged'     => max( 1, (int) ( get_query_var( 'paged' ) ?: 1 ) ),
];
$filters = array_filter( $filters );

$query = Query::get_woningen( $filters );
?>

<main id="main" class="kolibri-archive">

	<!-- Archive header -->
	<div class="kolibri-archive-header">
		<div class="kolibri-container">
			<h1 class="kolibri-archive-title">
				<?php
				if ( is_tax() ) {
					single_term_title();
				} else {
					esc_html_e( 'Woningaanbod', 'kolibri-woningen' );
				}
				?>
			</h1>
			<p class="kolibri-archive-count">
				<?php
				printf(
					/* translators: %d = number of properties */
					esc_html( _n( '%d woning gevonden', '%d woningen gevonden', $query->found_posts, 'kolibri-woningen' ) ),
					esc_html( $query->found_posts )
				);
				?>
			</p>
		</div>
	</div>

	<!-- Search form + filters -->
	<div class="kolibri-container kolibri-archive-layout">
		<aside class="kolibri-sidebar">
			<?php Template_Loader::partial( 'search-form', [ 'filters' => $filters ] ); ?>
		</aside>

		<section class="kolibri-results-section">
			<!-- Sort bar -->
			<div class="kolibri-sort-bar">
				<label for="kolibri-sort" class="kolibri-sort-label">
					<?php esc_html_e( 'Sorteren op:', 'kolibri-woningen' ); ?>
				</label>
				<select id="kolibri-sort" class="kolibri-sort-select" data-ajax-filter>
					<option value="date-DESC"><?php esc_html_e( 'Nieuwste eerst', 'kolibri-woningen' ); ?></option>
					<option value="date-ASC"><?php esc_html_e( 'Oudste eerst', 'kolibri-woningen' ); ?></option>
					<option value="meta_value_num-ASC" data-meta="_kolibri_koopprijs"><?php esc_html_e( 'Prijs laag → hoog', 'kolibri-woningen' ); ?></option>
					<option value="meta_value_num-DESC" data-meta="_kolibri_koopprijs"><?php esc_html_e( 'Prijs hoog → laag', 'kolibri-woningen' ); ?></option>
				</select>
			</div>

			<!-- Results -->
			<div id="kolibri-results">
				<?php Shortcodes::render_grid( $query, 3 ); ?>
			</div>
		</section>
	</div>

</main>

<?php
wp_reset_postdata();
get_footer();
