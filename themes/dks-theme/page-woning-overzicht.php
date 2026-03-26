<?php
/**
 * Template Name: Woning overzicht
 *
 * Full property overview with search/filter bar and infinite-scroll grid.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

use KolibriWoningen\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// ── Active filters from URL ────────────────────────────────────────────────────
$f_stad      = sanitize_text_field( $_GET['stad']      ?? '' );
$f_min_prijs = absint( $_GET['min_prijs'] ?? 0 );
$f_max_prijs = absint( $_GET['max_prijs'] ?? 0 );
$f_kamers    = absint( $_GET['kamers']    ?? 0 );
$f_min_m2    = absint( $_GET['min_m2']   ?? 0 );

// ── Query ──────────────────────────────────────────────────────────────────────
$paged     = max( 1, get_query_var( 'paged' ) ?: absint( $_GET['page'] ?? 1 ) );
$per_page  = 9;

$query_args = [
	'post_type'      => 'kolibri_woning',
	'post_status'    => 'publish',
	'posts_per_page' => $per_page,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

// Meta-based filters.
$meta_query = [ 'relation' => 'AND' ];

if ( $f_min_prijs ) {
	$meta_query[] = [
		'key'     => '_kolibri_koopprijs',
		'value'   => $f_min_prijs,
		'compare' => '>=',
		'type'    => 'NUMERIC',
	];
}

if ( $f_max_prijs ) {
	$meta_query[] = [
		'key'     => '_kolibri_koopprijs',
		'value'   => $f_max_prijs,
		'compare' => '<=',
		'type'    => 'NUMERIC',
	];
}

if ( $f_kamers ) {
	$meta_query[] = [
		'key'     => '_kolibri_kamers',
		'value'   => $f_kamers,
		'compare' => '>=',
		'type'    => 'NUMERIC',
	];
}

if ( $f_min_m2 ) {
	$meta_query[] = [
		'key'     => '_kolibri_woon_m2',
		'value'   => $f_min_m2,
		'compare' => '>=',
		'type'    => 'NUMERIC',
	];
}

if ( count( $meta_query ) > 1 ) {
	$query_args['meta_query'] = $meta_query;
}

// Taxonomy-based stad filter.
if ( $f_stad ) {
	$query_args['tax_query'] = [
		[
			'taxonomy' => 'kolibri_stad',
			'field'    => 'slug',
			'terms'    => sanitize_title( $f_stad ),
		],
	];
}

$woningen = new WP_Query( $query_args );
$max_pages = $woningen->max_num_pages;
$total     = $woningen->found_posts;

// Current page URL (without paging) for form action.
$page_url = esc_url( get_permalink() );
?>

<!-- ── Page hero ────────────────────────────────────────────────────────────── -->
<section class="dks-page-hero">
	<div class="dks-container">
		<p class="dks-page-hero__eyebrow"><?php esc_html_e( 'Properties', 'dks-theme' ); ?></p>
		<h1 class="dks-page-hero__title">
			<?php echo wp_kses_post( get_the_title() ?: __( 'Property Overview', 'dks-theme' ) ); ?>
		</h1>
	</div>
</section>

<!-- ── Search bar ───────────────────────────────────────────────────────────── -->
<div class="dks-hero-search dks-overzicht-search" role="search">
	<div class="dks-container">
		<div class="dks-hero-search__wrap">
			<form class="dks-hero-search__form" method="get" action="<?php echo $page_url; ?>">

				<!-- Stad -->
				<div class="dks-hero-search__field dks-hero-search__field--city">
					<label class="dks-hero-search__label" for="ov-stad"><?php esc_html_e( 'Stad', 'dks-theme' ); ?></label>
					<div class="dks-hero-search__input-row">
						<span class="dks-hero-search__icon material-symbols-outlined" aria-hidden="true">location_on</span>
						<input
							id="ov-stad"
							type="text"
							name="stad"
							class="dks-hero-search__input"
							placeholder="<?php esc_attr_e( 'Amsterdam, Rotterdam…', 'dks-theme' ); ?>"
							value="<?php echo esc_attr( $f_stad ); ?>"
							autocomplete="off"
						>
					</div>
				</div>

				<div class="dks-hero-search__sep" aria-hidden="true"></div>

				<!-- Min. prijs -->
				<div class="dks-hero-search__field">
					<label class="dks-hero-search__label" for="ov-min-prijs"><?php esc_html_e( 'Min. prijs', 'dks-theme' ); ?></label>
					<select id="ov-min-prijs" name="min_prijs" class="dks-hero-search__select">
						<option value=""><?php esc_html_e( 'No minimum', 'dks-theme' ); ?></option>
						<option value="100000" <?php selected( $f_min_prijs, 100000 ); ?>>&euro;&nbsp;100.000</option>
						<option value="200000" <?php selected( $f_min_prijs, 200000 ); ?>>&euro;&nbsp;200.000</option>
						<option value="300000" <?php selected( $f_min_prijs, 300000 ); ?>>&euro;&nbsp;300.000</option>
						<option value="400000" <?php selected( $f_min_prijs, 400000 ); ?>>&euro;&nbsp;400.000</option>
						<option value="500000" <?php selected( $f_min_prijs, 500000 ); ?>>&euro;&nbsp;500.000</option>
						<option value="750000" <?php selected( $f_min_prijs, 750000 ); ?>>&euro;&nbsp;750.000</option>
					</select>
				</div>

				<div class="dks-hero-search__sep" aria-hidden="true"></div>

				<!-- Max. prijs -->
				<div class="dks-hero-search__field">
					<label class="dks-hero-search__label" for="ov-max-prijs"><?php esc_html_e( 'Max. prijs', 'dks-theme' ); ?></label>
					<select id="ov-max-prijs" name="max_prijs" class="dks-hero-search__select">
						<option value=""><?php esc_html_e( 'No maximum', 'dks-theme' ); ?></option>
						<option value="200000" <?php selected( $f_max_prijs, 200000 ); ?>>&euro;&nbsp;200.000</option>
						<option value="300000" <?php selected( $f_max_prijs, 300000 ); ?>>&euro;&nbsp;300.000</option>
						<option value="400000" <?php selected( $f_max_prijs, 400000 ); ?>>&euro;&nbsp;400.000</option>
						<option value="500000" <?php selected( $f_max_prijs, 500000 ); ?>>&euro;&nbsp;500.000</option>
						<option value="750000" <?php selected( $f_max_prijs, 750000 ); ?>>&euro;&nbsp;750.000</option>
						<option value="1000000" <?php selected( $f_max_prijs, 1000000 ); ?>>&euro;&nbsp;1.000.000</option>
					</select>
				</div>

				<div class="dks-hero-search__sep" aria-hidden="true"></div>

				<!-- Slaapkamers -->
				<div class="dks-hero-search__field">
					<label class="dks-hero-search__label" for="ov-kamers"><?php esc_html_e( 'Bedrooms', 'dks-theme' ); ?></label>
					<select id="ov-kamers" name="kamers" class="dks-hero-search__select">
						<option value=""><?php esc_html_e( 'Any', 'dks-theme' ); ?></option>
						<option value="1" <?php selected( $f_kamers, 1 ); ?>>1+</option>
						<option value="2" <?php selected( $f_kamers, 2 ); ?>>2+</option>
						<option value="3" <?php selected( $f_kamers, 3 ); ?>>3+</option>
						<option value="4" <?php selected( $f_kamers, 4 ); ?>>4+</option>
						<option value="5" <?php selected( $f_kamers, 5 ); ?>>5+</option>
					</select>
				</div>

				<div class="dks-hero-search__sep" aria-hidden="true"></div>

				<!-- Min. m² -->
				<div class="dks-hero-search__field">
					<label class="dks-hero-search__label" for="ov-m2">m&sup2;</label>
					<select id="ov-m2" name="min_m2" class="dks-hero-search__select">
						<option value=""><?php esc_html_e( 'Any', 'dks-theme' ); ?></option>
						<option value="50"  <?php selected( $f_min_m2, 50 );  ?>>50+ m&sup2;</option>
						<option value="75"  <?php selected( $f_min_m2, 75 );  ?>>75+ m&sup2;</option>
						<option value="100" <?php selected( $f_min_m2, 100 ); ?>>100+ m&sup2;</option>
						<option value="150" <?php selected( $f_min_m2, 150 ); ?>>150+ m&sup2;</option>
						<option value="200" <?php selected( $f_min_m2, 200 ); ?>>200+ m&sup2;</option>
					</select>
				</div>

				<!-- Find My Home -->
				<button type="submit" class="dks-hero-search__btn">
					<?php esc_html_e( 'Find My Home', 'dks-theme' ); ?>
				</button>

			</form>
		</div><!-- .dks-hero-search__wrap -->
	</div><!-- .dks-container -->
</div><!-- .dks-overzicht-search -->

<!-- ── Properties grid ──────────────────────────────────────────────────────── -->
<section class="dks-overzicht dks-section">
	<div class="dks-container">

		<!-- Result count -->
		<p class="dks-overzicht__count">
			<?php
			printf(
				/* translators: %d: number of properties */
				esc_html( _n( '%d property found', '%d properties found', $total, 'dks-theme' ) ),
				$total
			);
			?>
		</p>

		<!-- Grid -->
		<div
			id="dks-overzicht-grid"
			class="dks-listings__grid"
			style="--dks-cols:3;"
			data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
			data-current-page="<?php echo esc_attr( $paged ); ?>"
		>
			<?php if ( $woningen->have_posts() ) : ?>
				<?php while ( $woningen->have_posts() ) : $woningen->the_post(); ?>
					<?php
					$post_id     = get_the_ID();
					$straat      = Post_Types::get_meta( $post_id, 'straat' );
					$huisnummer  = Post_Types::get_meta( $post_id, 'huisnummer' );
					$toevoeging  = Post_Types::get_meta( $post_id, 'toevoeging' );
					$stad        = Post_Types::get_meta( $post_id, 'stad' );
					$wijk        = Post_Types::get_meta( $post_id, 'wijk' );
					$koopprijs   = Post_Types::get_meta( $post_id, 'koopprijs' );
					$huurprijs   = Post_Types::get_meta( $post_id, 'huurprijs' );
					$prijs_tekst = Post_Types::get_meta( $post_id, 'prijs_tekst' );
					$woon_m2     = Post_Types::get_meta( $post_id, 'woon_m2' );
					$slaapkamers = Post_Types::get_meta( $post_id, 'slaapkamers' );
					$badkamers   = Post_Types::get_meta( $post_id, 'badkamers' );
					$uitgelicht  = Post_Types::get_meta( $post_id, 'uitgelicht' );

					$adres    = trim( $straat . ' ' . $huisnummer . $toevoeging );
					$location = $wijk ? $stad . ', ' . $wijk : $stad;

					$prijs_label = $koopprijs
						? Post_Types::format_price( $koopprijs ) . ( $prijs_tekst ? ' ' . $prijs_tekst : '' )
						: ( $huurprijs ? Post_Types::format_price( $huurprijs ) . ' /mnd' : '' );

					$badge     = ( '1' === $uitgelicht ) ? esc_html__( 'Uitgelicht', 'kolibri-woningen' ) : '';
					$permalink = esc_url( get_permalink( $post_id ) );
					?>
					<article class="dks-property-card">

						<a href="<?php echo $permalink; ?>" class="dks-property-card__image-wrap">
							<?php echo Post_Types::render_card_slides( $post_id, 'dks-property-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php if ( $badge ) : ?>
								<span class="dks-property-card__badge"><?php echo esc_html( $badge ); ?></span>
							<?php endif; ?>
						</a>

						<div class="dks-property-card__info">

							<h3 class="dks-property-card__price">
								<a href="<?php echo $permalink; ?>"><?php echo esc_html( $prijs_label ); ?></a>
							</h3>

							<?php if ( $adres ) : ?>
								<p class="dks-property-card__address"><?php echo esc_html( $adres ); ?></p>
							<?php endif; ?>

							<?php if ( $location ) : ?>
								<p class="dks-property-card__location">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true">
										<path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.979-5.121 3.979-8.827a8.25 8.25 0 00-16.5 0c0 3.706 2.035 6.744 3.979 8.827a19.58 19.58 0 002.686 2.282 16.975 16.975 0 001.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
									</svg>
									<?php echo esc_html( $location ); ?>
								</p>
							<?php endif; ?>

							<div class="dks-property-card__meta">
								<?php if ( $slaapkamers ) : ?>
									<div class="dks-property-card__meta-item">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
										</svg>
										<?php echo esc_html( sprintf(
											_n( '%d Bedroom', '%d Bedrooms', (int) $slaapkamers, 'dks-theme' ),
											(int) $slaapkamers
										) ); ?>
									</div>
								<?php endif; ?>
								<?php if ( $badkamers ) : ?>
									<div class="dks-property-card__meta-item">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a6 6 0 0112 0v1H6v-1z"/>
										</svg>
										<?php echo esc_html( sprintf(
											_n( '%d Bathroom', '%d Bathrooms', (int) $badkamers, 'dks-theme' ),
											(int) $badkamers
										) ); ?>
									</div>
								<?php endif; ?>
								<?php if ( $woon_m2 ) : ?>
									<div class="dks-property-card__meta-item">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
										</svg>
										<?php echo esc_html( $woon_m2 ); ?> m²
									</div>
								<?php endif; ?>
							</div>

						</div>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p class="dks-overzicht__empty">
					<?php esc_html_e( 'No properties found. Try adjusting your search filters.', 'dks-theme' ); ?>
				</p>
			<?php endif; ?>
		</div><!-- #dks-overzicht-grid -->

		<!-- Infinite scroll sentinel — becomes a "Load more" button on no-JS -->
		<?php if ( $max_pages > 1 ) : ?>
			<div
				id="dks-overzicht-sentinel"
				class="dks-overzicht__sentinel"
				data-next-page="<?php echo esc_attr( $paged + 1 ); ?>"
				data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
				aria-hidden="true"
			></div>
			<noscript>
				<div style="text-align:center;padding:2rem 0;">
					<?php
					$next_url = add_query_arg( array_merge(
						array_filter( [
							'stad'      => $f_stad,
							'min_prijs' => $f_min_prijs ?: null,
							'max_prijs' => $f_max_prijs ?: null,
							'kamers'    => $f_kamers ?: null,
							'min_m2'    => $f_min_m2 ?: null,
						] ),
						[ 'page' => $paged + 1 ]
					), $page_url );
					?>
					<a href="<?php echo esc_url( $next_url ); ?>" class="dks-btn">
						<?php esc_html_e( 'Load more', 'dks-theme' ); ?>
					</a>
				</div>
			</noscript>
		<?php endif; ?>

	</div><!-- .dks-container -->
</section><!-- .dks-overzicht -->

<?php get_footer(); ?>
