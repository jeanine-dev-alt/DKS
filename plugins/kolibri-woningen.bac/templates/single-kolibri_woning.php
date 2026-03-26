<?php
/**
 * Single woning template.
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Post_Types;
use KolibriWoningen\Template_Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();

	// Base data.
	$straat     = Post_Types::get_meta( $post_id, 'straat' );
	$huisnummer = Post_Types::get_meta( $post_id, 'huisnummer' );
	$toevoeging = Post_Types::get_meta( $post_id, 'toevoeging' );
	$postcode   = Post_Types::get_meta( $post_id, 'postcode' );
	$stad       = Post_Types::get_meta( $post_id, 'stad' );
	$wijk       = Post_Types::get_meta( $post_id, 'wijk' );
	$koopprijs  = Post_Types::get_meta( $post_id, 'koopprijs' );
	$huurprijs  = Post_Types::get_meta( $post_id, 'huurprijs' );
	$prijs_tekst= Post_Types::get_meta( $post_id, 'prijs_tekst' );
	$woon_m2    = Post_Types::get_meta( $post_id, 'woon_m2' );
	$kamers     = Post_Types::get_meta( $post_id, 'kamers' );
	$slaapkamers= Post_Types::get_meta( $post_id, 'slaapkamers' );
	$gallery_ids= Post_Types::get_meta( $post_id, 'gallery_ids' );
	$maps_url   = Post_Types::get_meta( $post_id, 'maps_url' );
	$video_url  = Post_Types::get_meta( $post_id, 'video_url' );
	$brochure   = Post_Types::get_meta( $post_id, 'brochure_url' );
	$inwendig   = Post_Types::get_meta( $post_id, 'inwendig_360' );

	$adres       = trim( $straat . ' ' . $huisnummer . $toevoeging );
	$adres_city  = trim( $postcode . ' ' . $stad );
	$prijs_label = $koopprijs
		? Post_Types::format_price( $koopprijs ) . ( $prijs_tekst ? ' ' . $prijs_tekst : '' )
		: ( $huurprijs ? Post_Types::format_price( $huurprijs ) . ' /mnd' : '' );

	// Gallery: thumbnail IDs.
	$gallery_array = [];
	if ( $gallery_ids ) {
		$gallery_array = array_filter( array_map( 'intval', explode( ',', $gallery_ids ) ) );
	} elseif ( has_post_thumbnail() ) {
		$gallery_array = [ get_post_thumbnail_id() ];
	}

	// Status taxonomy term.
	$status_terms = get_the_terms( $post_id, 'kolibri_status' );
	$status_label = ! is_wp_error( $status_terms ) && $status_terms ? $status_terms[0]->name : '';
?>

<main id="main" class="kolibri-single">

	<!-- Gallery -->
	<?php Template_Loader::partial( 'gallery', [ 'gallery_ids' => $gallery_array, 'status_label' => $status_label ] ); ?>

	<!-- Detail body -->
	<div class="kolibri-container kolibri-single-layout">

		<!-- Main content -->
		<div class="kolibri-single-main">

			<!-- Title block -->
			<div class="kolibri-single-titleblock">
				<?php if ( $wijk ) : ?>
					<span class="kolibri-single-wijk"><?php echo esc_html( $wijk ); ?></span>
				<?php endif; ?>
				<h1 class="kolibri-single-title"><?php the_title(); ?></h1>
				<?php if ( $adres ) : ?>
					<p class="kolibri-single-adres"><?php echo esc_html( $adres ); ?>, <?php echo esc_html( $adres_city ); ?></p>
				<?php endif; ?>
				<?php if ( $prijs_label ) : ?>
					<p class="kolibri-single-prijs"><?php echo esc_html( $prijs_label ); ?></p>
				<?php endif; ?>
			</div>

			<!-- Quick stats strip -->
			<div class="kolibri-quick-stats">
				<?php if ( $woon_m2 ) : ?>
					<div class="kolibri-stat">
						<span class="kolibri-stat-icon material-symbols-outlined">straighten</span>
						<span><?php echo esc_html( $woon_m2 ); ?> m²</span>
					</div>
				<?php endif; ?>
				<?php if ( $kamers ) : ?>
					<div class="kolibri-stat">
						<span class="kolibri-stat-icon material-symbols-outlined">meeting_room</span>
						<span><?php echo esc_html( $kamers ); ?> <?php esc_html_e( 'kamers', 'kolibri-woningen' ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $slaapkamers ) : ?>
					<div class="kolibri-stat">
						<span class="kolibri-stat-icon material-symbols-outlined">bed</span>
						<span><?php echo esc_html( $slaapkamers ); ?> <?php esc_html_e( 'slaapkamers', 'kolibri-woningen' ); ?></span>
					</div>
				<?php endif; ?>
			</div>

			<!-- Description -->
			<?php if ( get_the_content() ) : ?>
				<div class="kolibri-single-description">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<!-- Specs sections (all groups, only filled fields) -->
			<?php Template_Loader::partial( 'specs', [ 'post_id' => $post_id ] ); ?>

			<!-- Video / 360 -->
			<?php if ( $video_url || $inwendig ) : ?>
				<div class="kolibri-media-links">
					<?php if ( $video_url ) : ?>
						<a href="<?php echo esc_url( $video_url ); ?>" target="_blank" rel="noopener" class="kolibri-btn kolibri-btn-outline">
							<span class="material-symbols-outlined">play_circle</span>
							<?php esc_html_e( 'Video bekijken', 'kolibri-woningen' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $inwendig ) : ?>
						<a href="<?php echo esc_url( $inwendig ); ?>" target="_blank" rel="noopener" class="kolibri-btn kolibri-btn-outline">
							<span class="material-symbols-outlined">360</span>
							<?php esc_html_e( '360° rondleiding', 'kolibri-woningen' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<!-- Map -->
			<?php if ( $maps_url ) : ?>
				<div class="kolibri-map-wrap">
					<h3 class="kolibri-section-title"><?php esc_html_e( 'Locatie', 'kolibri-woningen' ); ?></h3>
					<a href="<?php echo esc_url( $maps_url ); ?>" target="_blank" rel="noopener" class="kolibri-map-link">
						<span class="material-symbols-outlined">map</span>
						<?php esc_html_e( 'Bekijk op Google Maps', 'kolibri-woningen' ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div><!-- .kolibri-single-main -->

		<!-- Sticky sidebar CTA -->
		<?php Template_Loader::partial( 'sticky-cta', [
			'post_id'      => $post_id,
			'prijs_label'  => $prijs_label,
			'adres'        => $adres,
			'brochure_url' => $brochure,
		] ); ?>

	</div><!-- .kolibri-single-layout -->

</main>

<?php
endwhile;
get_footer();
