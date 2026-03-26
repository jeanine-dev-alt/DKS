<?php
/**
 * Specs partial — all woning details, grouped, only filled fields shown.
 *
 * Variable provided:
 *   int $post_id
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Helper: render a spec row only when value is non-empty. ──────────────────
function kolibri_spec_row( string $label, $value ): void {
	if ( '' === (string) $value || false === $value || null === $value ) {
		return;
	}
	echo '<tr>';
	echo '<th scope="row">' . esc_html( $label ) . '</th>';
	echo '<td>' . wp_kses_post( $value ) . '</td>';
	echo '</tr>';
}

// ── Helper: translate stored key to Dutch label. ─────────────────────────────
function kolibri_multi_label( string $stored, array $map ): string {
	if ( ! $stored ) {
		return '';
	}
	$parts = array_filter( array_map( function( $k ) use ( $map ) {
		return $map[ $k ] ?? $k;
	}, explode( ',', $stored ) ) );
	return implode( ', ', $parts );
}

// ── Data ─────────────────────────────────────────────────────────────────────

$m = function( string $key ) use ( $post_id ): string {
	return (string) Post_Types::get_meta( $post_id, $key );
};

$isolatie_labels = [
	'spouwmuur' => 'Spouwmuurisolatie',
	'dak'       => 'Dakisolatie',
	'vloer'     => 'Vloerisolatie',
	'glas'      => 'Dubbel glas',
	'hr_glas'   => 'HR++ glas',
	'triple'    => 'Triple glas',
	'geen'      => 'Geen isolatie',
];
$badkamer_labels = [
	'douche'      => 'Douche',
	'ligbad'      => 'Ligbad',
	'wastafel'    => 'Wastafel',
	'toilet'      => 'Toilet',
	'bidet'       => 'Bidet',
	'sauna'       => 'Sauna',
	'stoomcabine' => 'Stoomcabine',
];
$parkeer_labels  = [
	'eigen_oprit'  => 'Eigen oprit',
	'garagebox'    => 'Garagebox',
	'carport'      => 'Carport',
	'inpandig'     => 'Inpandig',
	'openbaar'     => 'Openbaar',
	'betaald'      => 'Betaald parkeren',
	'geen'         => 'Geen',
];

// ── Groups ────────────────────────────────────────────────────────────────────
$groups = [
	[
		'title' => __( 'Woning', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Type', 'kolibri-woningen' ),           $m('woning_type') ],
			[ __( 'Bouwjaar', 'kolibri-woningen' ),       $m('bouwjaar') ],
			[ __( 'Woonoppervlak', 'kolibri-woningen' ),  $m('woon_m2') ? $m('woon_m2') . ' m²' : '' ],
			[ __( 'Perceeloppervlak', 'kolibri-woningen' ), $m('perceel_m2') ? $m('perceel_m2') . ' m²' : '' ],
			[ __( 'Inhoud', 'kolibri-woningen' ),         $m('inhoud_m3') ? $m('inhoud_m3') . ' m³' : '' ],
			[ __( 'Kamers', 'kolibri-woningen' ),         $m('kamers') ],
			[ __( 'Slaapkamers', 'kolibri-woningen' ),   $m('slaapkamers') ],
			[ __( 'Badkamers', 'kolibri-woningen' ),      $m('badkamers') ],
			[ __( 'Verdiepingen', 'kolibri-woningen' ),   $m('verdiepingen') ],
		],
	],
	[
		'title' => __( 'Constructie', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Bouwtype', 'kolibri-woningen' ),    $m('bouw_type') ],
			[ __( 'Fundering', 'kolibri-woningen' ),   $m('fundering') ],
			[ __( 'Isolatie', 'kolibri-woningen' ),    kolibri_multi_label( $m('isolatie'), $isolatie_labels ) ],
		],
	],
	[
		'title' => __( 'Indeling', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Woonlagen', 'kolibri-woningen' ),           $m('woonlagen') ],
			[ __( 'Aanwezig op verdieping', 'kolibri-woningen' ), $m('verdieping_nr') ],
			[ __( 'Soort woonkamer', 'kolibri-woningen' ),     $m('woonkamer_type') ],
			[ __( 'Keukentype', 'kolibri-woningen' ),          $m('keuken_type') ],
			[ __( 'Keukenoppervlak', 'kolibri-woningen' ),     $m('keuken_m2') ? $m('keuken_m2') . ' m²' : '' ],
			[ __( 'Badkamer voorzieningen', 'kolibri-woningen' ), kolibri_multi_label( $m('badkamer_voorzieningen'), $badkamer_labels ) ],
		],
	],
	[
		'title' => __( 'Energie', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Energielabel', 'kolibri-woningen' ),   $m('energielabel') ],
			[ __( 'Verwarming', 'kolibri-woningen' ),     $m('verwarming') ],
			[ __( 'Warm water', 'kolibri-woningen' ),     $m('warmwater') ],
			[ __( 'CV-ketel type', 'kolibri-woningen' ),  $m('cv_type') ],
			[ __( 'CV-ketel bouwjaar', 'kolibri-woningen' ), $m('cv_bouwjaar') ],
			[ __( 'CV-ketel eigendom', 'kolibri-woningen' ), $m('cv_eigendom') ],
			[ __( 'Zonnepanelen', 'kolibri-woningen' ),   $m('zonnepanelen') ],
		],
	],
	[
		'title' => __( 'Buitenruimte', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Tuin', 'kolibri-woningen' ),             '1' === $m('tuin') ? __( 'Ja', 'kolibri-woningen' ) : '' ],
			[ __( 'Soort tuin', 'kolibri-woningen' ),       $m('tuin_type') ],
			[ __( 'Tuinligging', 'kolibri-woningen' ),      $m('tuin_positie') ],
			[ __( 'Tuinoppervlak', 'kolibri-woningen' ),    $m('tuin_m2') ? $m('tuin_m2') . ' m²' : '' ],
			[ __( 'Balkon', 'kolibri-woningen' ),           '1' === $m('balkon') ? __( 'Ja', 'kolibri-woningen' ) : '' ],
			[ __( 'Balkonoppervlak', 'kolibri-woningen' ),  $m('balkon_m2') ? $m('balkon_m2') . ' m²' : '' ],
			[ __( 'Dakterras', 'kolibri-woningen' ),        '1' === $m('dakterras') ? __( 'Ja', 'kolibri-woningen' ) : '' ],
			[ __( 'Dakterrasoppervlak', 'kolibri-woningen' ), $m('dakterras_m2') ? $m('dakterras_m2') . ' m²' : '' ],
		],
	],
	[
		'title' => __( 'Parkeren', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Parkeerfaciliteiten', 'kolibri-woningen' ), kolibri_multi_label( $m('parkeer_faciliteiten'), $parkeer_labels ) ],
			[ __( 'Garage', 'kolibri-woningen' ),             '1' === $m('garage') ? __( 'Ja', 'kolibri-woningen' ) : '' ],
			[ __( 'Garagetype', 'kolibri-woningen' ),         $m('garage_type') ],
			[ __( 'Capaciteit', 'kolibri-woningen' ),         $m('garage_capaciteit') ? $m('garage_capaciteit') . ' ' . __( 'auto\'s', 'kolibri-woningen' ) : '' ],
			[ __( 'Garageoppervlak', 'kolibri-woningen' ),    $m('garage_m2') ? $m('garage_m2') . ' m²' : '' ],
		],
	],
	[
		'title' => __( 'Dak', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Daktype', 'kolibri-woningen' ),      $m('dak_type') ],
			[ __( 'Dakbedekking', 'kolibri-woningen' ), $m('dak_bedekking') ],
		],
	],
	[
		'title' => __( 'Financieel & overig', 'kolibri-woningen' ),
		'rows'  => [
			[ __( 'Servicekosten', 'kolibri-woningen' ), $m('servicekosten') ? '€ ' . $m('servicekosten') . ' /mnd' : '' ],
			[ __( 'VvE', 'kolibri-woningen' ),           '1' === $m('vve') ? __( 'Ja', 'kolibri-woningen' ) : '' ],
			[ __( 'VvE kosten', 'kolibri-woningen' ),    $m('vve_kosten') ? '€ ' . $m('vve_kosten') . ' /mnd' : '' ],
			[ __( 'Oplevering', 'kolibri-woningen' ),    $m('oplevering') ],
			[ __( 'Bijzonderheden', 'kolibri-woningen' ),$m('bijzonderheden') ],
		],
	],
];

// ── Output ────────────────────────────────────────────────────────────────────
foreach ( $groups as $group ) {
	// Check if any row in this group has a value.
	$has_data = false;
	foreach ( $group['rows'] as $row ) {
		if ( '' !== (string) $row[1] && false !== $row[1] ) {
			$has_data = true;
			break;
		}
	}
	if ( ! $has_data ) {
		continue;
	}
	?>
	<div class="kolibri-specs-group">
		<h3 class="kolibri-specs-title"><?php echo esc_html( $group['title'] ); ?></h3>
		<table class="kolibri-specs-table">
			<tbody>
				<?php foreach ( $group['rows'] as $row ) : ?>
					<?php kolibri_spec_row( $row[0], $row[1] ); ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
