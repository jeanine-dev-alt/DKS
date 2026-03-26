<?php
/**
 * Search / filter form partial.
 *
 * Variables provided:
 *   array  $filters     Currently active filters.
 *   bool   $show_count  Show result count.
 *
 * Builds dynamic filters from published data only.
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$filters    = $filters ?? [];
$show_count = $show_count ?? false;

// Dynamic taxonomy terms — only those present in published posts.
$type_terms    = get_terms( [ 'taxonomy' => 'kolibri_type',   'hide_empty' => true ] );
$stad_terms    = get_terms( [ 'taxonomy' => 'kolibri_stad',   'hide_empty' => true ] );
$status_terms  = get_terms( [ 'taxonomy' => 'kolibri_status', 'hide_empty' => true ] );
$energie_terms = get_terms( [ 'taxonomy' => 'kolibri_energie','hide_empty' => true ] );

// Dynamic kamers values.
$kamers_vals = Query::get_meta_values( 'kamers' );

// Price range.
$price_range  = Query::get_price_range();
?>

<form
	id="kolibri-filter-form"
	class="kolibri-filter-form"
	method="get"
	action="<?php echo esc_url( get_post_type_archive_link( 'kolibri_woning' ) ); ?>"
	data-ajax-form
>

	<div class="kolibri-filter-header">
		<h3 class="kolibri-filter-title"><?php esc_html_e( 'Zoekfilters', 'kolibri-woningen' ); ?></h3>
		<button type="reset" class="kolibri-filter-reset"><?php esc_html_e( 'Wissen', 'kolibri-woningen' ); ?></button>
	</div>

	<!-- Woningtype -->
	<?php if ( ! is_wp_error( $type_terms ) && $type_terms ) : ?>
		<div class="kolibri-filter-group">
			<label for="kolibri-f-type" class="kolibri-filter-label">
				<?php esc_html_e( 'Woningtype', 'kolibri-woningen' ); ?>
			</label>
			<select id="kolibri-f-type" name="type" class="kolibri-filter-select" data-ajax-filter>
				<option value=""><?php esc_html_e( 'Alle typen', 'kolibri-woningen' ); ?></option>
				<?php foreach ( $type_terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>"<?php selected( $filters['type'] ?? '', $term->slug ); ?>>
						<?php echo esc_html( $term->name ); ?> (<?php echo esc_html( $term->count ); ?>)
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<!-- Stad -->
	<?php if ( ! is_wp_error( $stad_terms ) && $stad_terms ) : ?>
		<div class="kolibri-filter-group">
			<label for="kolibri-f-stad" class="kolibri-filter-label">
				<?php esc_html_e( 'Stad', 'kolibri-woningen' ); ?>
			</label>
			<select id="kolibri-f-stad" name="stad" class="kolibri-filter-select" data-ajax-filter>
				<option value=""><?php esc_html_e( 'Alle steden', 'kolibri-woningen' ); ?></option>
				<?php foreach ( $stad_terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>"<?php selected( $filters['stad'] ?? '', $term->slug ); ?>>
						<?php echo esc_html( $term->name ); ?> (<?php echo esc_html( $term->count ); ?>)
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<!-- Status -->
	<?php if ( ! is_wp_error( $status_terms ) && $status_terms ) : ?>
		<div class="kolibri-filter-group">
			<label for="kolibri-f-status" class="kolibri-filter-label">
				<?php esc_html_e( 'Status', 'kolibri-woningen' ); ?>
			</label>
			<select id="kolibri-f-status" name="status" class="kolibri-filter-select" data-ajax-filter>
				<option value=""><?php esc_html_e( 'Alle statussen', 'kolibri-woningen' ); ?></option>
				<?php foreach ( $status_terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>"<?php selected( $filters['status'] ?? '', $term->slug ); ?>>
						<?php echo esc_html( $term->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<!-- Energielabel -->
	<?php if ( ! is_wp_error( $energie_terms ) && $energie_terms ) : ?>
		<div class="kolibri-filter-group">
			<label for="kolibri-f-energie" class="kolibri-filter-label">
				<?php esc_html_e( 'Energielabel', 'kolibri-woningen' ); ?>
			</label>
			<select id="kolibri-f-energie" name="energie" class="kolibri-filter-select" data-ajax-filter>
				<option value=""><?php esc_html_e( 'Alle labels', 'kolibri-woningen' ); ?></option>
				<?php foreach ( $energie_terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>"<?php selected( $filters['energie'] ?? '', $term->slug ); ?>>
						<?php echo esc_html( $term->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<!-- Minimale prijs -->
	<?php if ( $price_range['max'] > 0 ) : ?>
		<div class="kolibri-filter-group">
			<label for="kolibri-f-min-prijs" class="kolibri-filter-label">
				<?php esc_html_e( 'Minimale prijs (€)', 'kolibri-woningen' ); ?>
			</label>
			<input
				type="number"
				id="kolibri-f-min-prijs"
				name="min_prijs"
				class="kolibri-filter-input"
				min="<?php echo esc_attr( $price_range['min'] ); ?>"
				max="<?php echo esc_attr( $price_range['max'] ); ?>"
				step="10000"
				placeholder="<?php echo esc_attr( $price_range['min'] ); ?>"
				value="<?php echo esc_attr( $filters['min_prijs'] ?? '' ); ?>"
				data-ajax-filter
			>
		</div>

		<div class="kolibri-filter-group">
			<label for="kolibri-f-max-prijs" class="kolibri-filter-label">
				<?php esc_html_e( 'Maximale prijs (€)', 'kolibri-woningen' ); ?>
			</label>
			<input
				type="number"
				id="kolibri-f-max-prijs"
				name="max_prijs"
				class="kolibri-filter-input"
				min="<?php echo esc_attr( $price_range['min'] ); ?>"
				max="<?php echo esc_attr( $price_range['max'] ); ?>"
				step="10000"
				placeholder="<?php echo esc_attr( $price_range['max'] ); ?>"
				value="<?php echo esc_attr( $filters['max_prijs'] ?? '' ); ?>"
				data-ajax-filter
			>
		</div>
	<?php endif; ?>

	<!-- Min oppervlak -->
	<div class="kolibri-filter-group">
		<label for="kolibri-f-min-m2" class="kolibri-filter-label">
			<?php esc_html_e( 'Minimaal woonoppervlak (m²)', 'kolibri-woningen' ); ?>
		</label>
		<input
			type="number"
			id="kolibri-f-min-m2"
			name="min_m2"
			class="kolibri-filter-input"
			min="0"
			step="10"
			placeholder="0"
			value="<?php echo esc_attr( $filters['min_m2'] ?? '' ); ?>"
			data-ajax-filter
		>
	</div>

	<!-- Aantal kamers -->
	<?php if ( $kamers_vals ) : ?>
		<div class="kolibri-filter-group">
			<label for="kolibri-f-kamers" class="kolibri-filter-label">
				<?php esc_html_e( 'Minimaal aantal kamers', 'kolibri-woningen' ); ?>
			</label>
			<select id="kolibri-f-kamers" name="kamers" class="kolibri-filter-select" data-ajax-filter>
				<option value=""><?php esc_html_e( 'Alle', 'kolibri-woningen' ); ?></option>
				<?php foreach ( $kamers_vals as $kv ) : ?>
					<option value="<?php echo esc_attr( $kv ); ?>"<?php selected( $filters['kamers'] ?? '', $kv ); ?>>
						<?php echo esc_html( $kv ); ?>+
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<button type="submit" class="kolibri-btn kolibri-btn-primary kolibri-filter-submit">
		<?php esc_html_e( 'Zoeken', 'kolibri-woningen' ); ?>
	</button>

</form>
