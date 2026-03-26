<?php
/**
 * Gallery partial — main image + thumbnail strip + lightbox trigger.
 *
 * Variables provided by Template_Loader::partial():
 *   int[]  $gallery_ids   Array of attachment IDs.
 *   string $status_label  Optional status badge label.
 *
 * @package KolibriWoningen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $gallery_ids ) ) {
	return;
}

$main_id  = $gallery_ids[0];
$main_src = wp_get_attachment_image_url( $main_id, 'large' );
$main_full= wp_get_attachment_image_url( $main_id, 'full' );
?>

<div class="kolibri-gallery" id="kolibri-gallery" data-count="<?php echo esc_attr( count( $gallery_ids ) ); ?>">

	<!-- Main image -->
	<div class="kolibri-gallery-main">
		<button
			type="button"
			class="kolibri-gallery-open"
			data-index="0"
			aria-label="<?php esc_attr_e( 'Galerij openen', 'kolibri-woningen' ); ?>"
		>
			<img
				src="<?php echo esc_url( $main_src ); ?>"
				alt="<?php echo esc_attr( get_the_title() ); ?>"
				class="kolibri-gallery-main-img"
				loading="eager"
				fetchpriority="high"
			>
			<span class="kolibri-gallery-count-badge">
				<span class="material-symbols-outlined">photo_library</span>
				<?php echo esc_html( count( $gallery_ids ) ); ?>
			</span>
		</button>

		<?php if ( ! empty( $status_label ) ) : ?>
			<span class="kolibri-gallery-status"><?php echo esc_html( $status_label ); ?></span>
		<?php endif; ?>
	</div>

	<!-- Thumbnail grid (up to 4 thumbs beside main) -->
	<?php if ( count( $gallery_ids ) > 1 ) : ?>
		<div class="kolibri-gallery-thumbs">
			<?php
			$thumbs = array_slice( $gallery_ids, 1, 4 );
			foreach ( $thumbs as $i => $img_id ) :
				$src  = wp_get_attachment_image_url( $img_id, 'medium' );
				$full = wp_get_attachment_image_url( $img_id, 'full' );
				$real_index = $i + 1;
				$is_last    = ( $i === count( $thumbs ) - 1 ) && count( $gallery_ids ) > 5;
			?>
				<button
					type="button"
					class="kolibri-gallery-thumb<?php echo $is_last ? ' kolibri-gallery-thumb--more' : ''; ?>"
					data-index="<?php echo esc_attr( $real_index ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Foto %d bekijken', 'kolibri-woningen' ), $real_index + 1 ) ); ?>"
				>
					<img
						src="<?php echo esc_url( $src ); ?>"
						alt=""
						loading="lazy"
					>
					<?php if ( $is_last && count( $gallery_ids ) > 5 ) : ?>
						<span class="kolibri-gallery-more-label">
							+<?php echo esc_html( count( $gallery_ids ) - 5 ); ?>
						</span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- Lightbox overlay (hidden by default, managed by gallery.js) -->
	<div id="kolibri-lightbox" class="kolibri-lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Fotogalerij', 'kolibri-woningen' ); ?>" hidden>
		<button type="button" class="kolibri-lb-close" aria-label="<?php esc_attr_e( 'Sluiten', 'kolibri-woningen' ); ?>">
			<span class="material-symbols-outlined">close</span>
		</button>
		<button type="button" class="kolibri-lb-prev" aria-label="<?php esc_attr_e( 'Vorige', 'kolibri-woningen' ); ?>">
			<span class="material-symbols-outlined">chevron_left</span>
		</button>
		<button type="button" class="kolibri-lb-next" aria-label="<?php esc_attr_e( 'Volgende', 'kolibri-woningen' ); ?>">
			<span class="material-symbols-outlined">chevron_right</span>
		</button>
		<div class="kolibri-lb-stage">
			<img class="kolibri-lb-img" src="" alt="" loading="lazy">
		</div>
		<div class="kolibri-lb-counter">
			<span id="kolibri-lb-current">1</span> / <span id="kolibri-lb-total"><?php echo esc_html( count( $gallery_ids ) ); ?></span>
		</div>
	</div>

	<!-- Image data for JS -->
	<script type="application/json" id="kolibri-gallery-data">
	<?php
	$data = array_map( function( $img_id ) {
		return [
			'src'  => wp_get_attachment_image_url( $img_id, 'full' ),
			'alt'  => get_post_meta( $img_id, '_wp_attachment_image_alt', true ),
			'w'    => (int) ( wp_get_attachment_metadata( $img_id )['width']  ?? 0 ),
			'h'    => (int) ( wp_get_attachment_metadata( $img_id )['height'] ?? 0 ),
		];
	}, $gallery_ids );
	echo wp_json_encode( $data );
	?>
	</script>

</div>
