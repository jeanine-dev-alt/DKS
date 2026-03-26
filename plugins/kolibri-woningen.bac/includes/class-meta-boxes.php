<?php
/**
 * Admin meta boxes — all woning fields in tabbed UI.
 *
 * Groups:
 *   base        — adres, prijs, foto's
 *   algemeen    — type, oppervlak, kamers
 *   constructie — bouwtype, fundering, isolatie
 *   indeling    — woonlagen, keuken, badkamer
 *   energie     — label, verwarming, cv-ketel
 *   buitenruimte— tuin, balkon, dakterras
 *   parkeren    — faciliteiten, garage
 *   dak         — type, bedekking
 *   overige     — servicekosten, VvE, bijzonderheden
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Meta_Boxes {

	// ── Registration ──────────────────────────────────────────────────────────

	public static function register(): void {
		add_meta_box(
			'kolibri_woning_details',
			__( 'Woningdetails', 'kolibri-woningen' ),
			[ self::class, 'render' ],
			'kolibri_woning',
			'normal',
			'high'
		);
	}

	// ── Render ────────────────────────────────────────────────────────────────

	public static function render( \WP_Post $post ): void {
		wp_nonce_field( 'kolibri_save_meta', 'kolibri_meta_nonce' );

		$tabs = [
			'basis'        => __( 'Basis', 'kolibri-woningen' ),
			'algemeen'     => __( 'Algemeen', 'kolibri-woningen' ),
			'constructie'  => __( 'Constructie', 'kolibri-woningen' ),
			'indeling'     => __( 'Indeling', 'kolibri-woningen' ),
			'energie'      => __( 'Energie', 'kolibri-woningen' ),
			'buitenruimte' => __( 'Buitenruimte', 'kolibri-woningen' ),
			'parkeren'     => __( 'Parkeren', 'kolibri-woningen' ),
			'dak'          => __( 'Dak', 'kolibri-woningen' ),
			'overige'      => __( 'Overige', 'kolibri-woningen' ),
		];

		echo '<div class="kolibri-tabs">';

		// Tab nav.
		echo '<nav class="kolibri-tab-nav">';
		$first = true;
		foreach ( $tabs as $id => $label ) {
			$active = $first ? ' kolibri-active' : '';
			echo '<button type="button" class="kolibri-tab-btn' . esc_attr( $active ) . '" data-tab="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</button>';
			$first = false;
		}
		echo '</nav>';

		// Tab panels.
		$first = true;
		foreach ( $tabs as $id => $label ) {
			$active = $first ? ' kolibri-active' : '';
			echo '<div class="kolibri-tab-panel' . esc_attr( $active ) . '" id="kolibri-tab-' . esc_attr( $id ) . '">';
			$method = 'render_tab_' . $id;
			if ( method_exists( self::class, $method ) ) {
				self::$method( $post );
			}
			echo '</div>';
			$first = false;
		}

		echo '</div>';
	}

	// ── Tab: Basis ────────────────────────────────────────────────────────────

	private static function render_tab_basis( \WP_Post $post ): void {
		$fields = [
			[ 'key' => 'straat',       'label' => __( 'Straatnaam', 'kolibri-woningen' ),   'type' => 'text' ],
			[ 'key' => 'huisnummer',   'label' => __( 'Huisnummer', 'kolibri-woningen' ),   'type' => 'text' ],
			[ 'key' => 'toevoeging',   'label' => __( 'Toevoeging', 'kolibri-woningen' ),   'type' => 'text' ],
			[ 'key' => 'postcode',     'label' => __( 'Postcode', 'kolibri-woningen' ),     'type' => 'text' ],
			[ 'key' => 'stad',         'label' => __( 'Stad', 'kolibri-woningen' ),         'type' => 'text' ],
			[ 'key' => 'wijk',         'label' => __( 'Wijk / buurt', 'kolibri-woningen' ), 'type' => 'text' ],
			[ 'key' => 'koopprijs',    'label' => __( 'Koopprijs (€)', 'kolibri-woningen' ), 'type' => 'text', 'placeholder' => '450000' ],
			[ 'key' => 'huurprijs',    'label' => __( 'Huurprijs p/m (€)', 'kolibri-woningen' ), 'type' => 'text', 'placeholder' => '1800' ],
			[ 'key' => 'prijs_tekst',  'label' => __( 'Prijstekst (bijv. "k.k." / "v.o.n.")', 'kolibri-woningen' ), 'type' => 'text' ],
			[ 'key' => 'uitgelicht',   'label' => __( 'Uitgelicht', 'kolibri-woningen' ),   'type' => 'checkbox', 'description' => __( 'Toon als featured woning', 'kolibri-woningen' ) ],
		];

		self::render_fields( $post, $fields );

		// Gallery field — image IDs stored as comma-separated.
		$gallery_ids = Post_Types::get_meta( $post->ID, 'gallery_ids' );
		echo '<div class="kolibri-field-row">';
		echo '<label class="kolibri-label">' . esc_html__( "Foto's (galerij)", 'kolibri-woningen' ) . '</label>';
		echo '<div class="kolibri-gallery-wrap">';
		echo '<input type="hidden" name="kolibri_gallery_ids" id="kolibri-gallery-ids" value="' . esc_attr( $gallery_ids ) . '">';
		echo '<div id="kolibri-gallery-preview" class="kolibri-gallery-preview">';

		if ( $gallery_ids ) {
			foreach ( explode( ',', $gallery_ids ) as $img_id ) {
				$img_id = (int) trim( $img_id );
				if ( $img_id ) {
					echo '<span class="kolibri-gallery-item" data-id="' . esc_attr( $img_id ) . '">';
					echo wp_get_attachment_image( $img_id, [ 60, 60 ] );
					echo '<button type="button" class="kolibri-remove-img" data-id="' . esc_attr( $img_id ) . '">&times;</button>';
					echo '</span>';
				}
			}
		}

		echo '</div>';
		echo '<button type="button" class="button kolibri-gallery-add" id="kolibri-gallery-add">' . esc_html__( "Foto's toevoegen", 'kolibri-woningen' ) . '</button>';
		echo '</div>';
		echo '</div>';

		// Maps URL.
		$map_url = Post_Types::get_meta( $post->ID, 'maps_url' );
		self::render_fields( $post, [
			[ 'key' => 'maps_url', 'label' => __( 'Google Maps URL', 'kolibri-woningen' ), 'type' => 'url' ],
		] );
	}

	// ── Tab: Algemeen ─────────────────────────────────────────────────────────

	private static function render_tab_algemeen( \WP_Post $post ): void {
		$fields = [
			[
				'key'     => 'woning_type',
				'label'   => __( 'Woningtype', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''                => __( '— Selecteer —', 'kolibri-woningen' ),
					'appartement'     => __( 'Appartement', 'kolibri-woningen' ),
					'tussenwoning'    => __( 'Tussenwoning', 'kolibri-woningen' ),
					'hoekwoning'      => __( 'Hoekwoning', 'kolibri-woningen' ),
					'twee_onder_een'  => __( 'Twee-onder-een-kapwoning', 'kolibri-woningen' ),
					'vrijstaand'      => __( 'Vrijstaande woning', 'kolibri-woningen' ),
					'villa'           => __( 'Villa', 'kolibri-woningen' ),
					'penthouse'       => __( 'Penthouse', 'kolibri-woningen' ),
					'studio'          => __( 'Studio', 'kolibri-woningen' ),
					'woonboerderij'   => __( 'Woonboerderij', 'kolibri-woningen' ),
					'overig'          => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[ 'key' => 'bouwjaar',       'label' => __( 'Bouwjaar', 'kolibri-woningen' ),          'type' => 'number', 'min' => '1800', 'max' => '2030' ],
			[ 'key' => 'perceel_m2',     'label' => __( 'Perceeloppervlak (m²)', 'kolibri-woningen' ), 'type' => 'number' ],
			[ 'key' => 'woon_m2',        'label' => __( 'Woonoppervlak (m²)', 'kolibri-woningen' ),  'type' => 'number' ],
			[ 'key' => 'inhoud_m3',      'label' => __( 'Inhoud (m³)', 'kolibri-woningen' ),         'type' => 'number' ],
			[ 'key' => 'kamers',         'label' => __( 'Aantal kamers', 'kolibri-woningen' ),        'type' => 'number' ],
			[ 'key' => 'slaapkamers',    'label' => __( 'Slaapkamers', 'kolibri-woningen' ),         'type' => 'number' ],
			[ 'key' => 'badkamers',      'label' => __( 'Badkamers', 'kolibri-woningen' ),           'type' => 'number' ],
			[ 'key' => 'verdiepingen',   'label' => __( 'Aantal verdiepingen', 'kolibri-woningen' ), 'type' => 'number' ],
		];

		self::render_fields( $post, $fields );
	}

	// ── Tab: Constructie ──────────────────────────────────────────────────────

	private static function render_tab_constructie( \WP_Post $post ): void {
		$fields = [
			[
				'key'     => 'bouw_type',
				'label'   => __( 'Bouwtype', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''              => __( '— Selecteer —', 'kolibri-woningen' ),
					'nieuwbouw'     => __( 'Nieuwbouw', 'kolibri-woningen' ),
					'bestaande_bouw'=> __( 'Bestaande bouw', 'kolibri-woningen' ),
					'project'       => __( 'Project', 'kolibri-woningen' ),
				],
			],
			[
				'key'     => 'fundering',
				'label'   => __( 'Fundering', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''         => __( '— Selecteer —', 'kolibri-woningen' ),
					'beton'    => __( 'Beton', 'kolibri-woningen' ),
					'hout'     => __( 'Houten palen', 'kolibri-woningen' ),
					'staal'    => __( 'Stalen palen', 'kolibri-woningen' ),
					'steen'    => __( 'Steenachtig', 'kolibri-woningen' ),
					'overig'   => __( 'Overig', 'kolibri-woningen' ),
				],
			],
		];

		self::render_fields( $post, $fields );

		// Multi-select: isolatie.
		$isolatie_options = [
			'spouwmuur'  => __( 'Spouwmuurisolatie', 'kolibri-woningen' ),
			'dak'        => __( 'Dakisolatie', 'kolibri-woningen' ),
			'vloer'      => __( 'Vloerisolatie', 'kolibri-woningen' ),
			'glas'       => __( 'Dubbel glas', 'kolibri-woningen' ),
			'hr_glas'    => __( 'HR++ glas', 'kolibri-woningen' ),
			'triple'     => __( 'Triple glas', 'kolibri-woningen' ),
			'geen'       => __( 'Geen isolatie', 'kolibri-woningen' ),
		];
		$isolatie_val = Post_Types::get_meta( $post->ID, 'isolatie' );
		$selected     = $isolatie_val ? explode( ',', $isolatie_val ) : [];

		echo '<div class="kolibri-field-row">';
		echo '<label class="kolibri-label">' . esc_html__( 'Isolatie', 'kolibri-woningen' ) . '</label>';
		echo '<div class="kolibri-checkboxes">';
		foreach ( $isolatie_options as $val => $lbl ) {
			$checked = in_array( $val, $selected, true ) ? ' checked' : '';
			echo '<label class="kolibri-checkbox-label">';
			echo '<input type="checkbox" name="kolibri_isolatie[]" value="' . esc_attr( $val ) . '"' . $checked . '> ';
			echo esc_html( $lbl );
			echo '</label>';
		}
		echo '</div></div>';
	}

	// ── Tab: Indeling ─────────────────────────────────────────────────────────

	private static function render_tab_indeling( \WP_Post $post ): void {
		$fields = [
			[ 'key' => 'woonlagen',       'label' => __( 'Woonlagen', 'kolibri-woningen' ),            'type' => 'text', 'placeholder' => 'bijv. begane grond, 1e verdieping' ],
			[ 'key' => 'verdieping_nr',   'label' => __( 'Aanwezig op verdieping', 'kolibri-woningen' ),'type' => 'number' ],
			[
				'key'     => 'woonkamer_type',
				'label'   => __( 'Soort woonkamer', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''              => __( '— Selecteer —', 'kolibri-woningen' ),
					'l_vorm'        => __( 'L-vormig', 'kolibri-woningen' ),
					'door'          => __( 'Doorkamer', 'kolibri-woningen' ),
					'suite'         => __( 'Suite', 'kolibri-woningen' ),
					'open'          => __( 'Open', 'kolibri-woningen' ),
					'overig'        => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[
				'key'     => 'keuken_type',
				'label'   => __( 'Keukentype', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''            => __( '— Selecteer —', 'kolibri-woningen' ),
					'open'        => __( 'Open keuken', 'kolibri-woningen' ),
					'gesloten'    => __( 'Gesloten keuken', 'kolibri-woningen' ),
					'eiland'      => __( 'Keukeneiland', 'kolibri-woningen' ),
					'inloop'      => __( 'Inloopkeuken', 'kolibri-woningen' ),
					'overig'      => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[ 'key' => 'keuken_m2',   'label' => __( 'Keukenoppervlak (m²)', 'kolibri-woningen' ), 'type' => 'number' ],
		];

		self::render_fields( $post, $fields );

		// Badkamer voorzieningen.
		$badkamer_options = [
			'douche'      => __( 'Douche', 'kolibri-woningen' ),
			'ligbad'      => __( 'Ligbad', 'kolibri-woningen' ),
			'wastafel'    => __( 'Wastafel', 'kolibri-woningen' ),
			'toilet'      => __( 'Toilet', 'kolibri-woningen' ),
			'bidet'       => __( 'Bidet', 'kolibri-woningen' ),
			'sauna'       => __( 'Sauna', 'kolibri-woningen' ),
			'stoomcabine' => __( 'Stoomcabine', 'kolibri-woningen' ),
		];
		$bk_val   = Post_Types::get_meta( $post->ID, 'badkamer_voorzieningen' );
		$selected = $bk_val ? explode( ',', $bk_val ) : [];

		echo '<div class="kolibri-field-row">';
		echo '<label class="kolibri-label">' . esc_html__( 'Badkamer voorzieningen', 'kolibri-woningen' ) . '</label>';
		echo '<div class="kolibri-checkboxes">';
		foreach ( $badkamer_options as $val => $lbl ) {
			$checked = in_array( $val, $selected, true ) ? ' checked' : '';
			echo '<label class="kolibri-checkbox-label">';
			echo '<input type="checkbox" name="kolibri_badkamer_voorzieningen[]" value="' . esc_attr( $val ) . '"' . $checked . '> ';
			echo esc_html( $lbl );
			echo '</label>';
		}
		echo '</div></div>';
	}

	// ── Tab: Energie ──────────────────────────────────────────────────────────

	private static function render_tab_energie( \WP_Post $post ): void {
		$fields = [
			[
				'key'     => 'energielabel',
				'label'   => __( 'Energielabel', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''    => __( '— Selecteer —', 'kolibri-woningen' ),
					'A++' => 'A++',
					'A+'  => 'A+',
					'A'   => 'A',
					'B'   => 'B',
					'C'   => 'C',
					'D'   => 'D',
					'E'   => 'E',
					'F'   => 'F',
					'G'   => 'G',
				],
			],
			[
				'key'     => 'verwarming',
				'label'   => __( 'Verwarming', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''              => __( '— Selecteer —', 'kolibri-woningen' ),
					'cv_ketel'      => __( 'CV-ketel', 'kolibri-woningen' ),
					'stadsverwarming'=> __( 'Stadsverwarming', 'kolibri-woningen' ),
					'warmtepomp'    => __( 'Warmtepomp', 'kolibri-woningen' ),
					'elektrisch'    => __( 'Elektrisch', 'kolibri-woningen' ),
					'vloerverwarming'=> __( 'Vloerverwarming', 'kolibri-woningen' ),
					'geen'          => __( 'Geen', 'kolibri-woningen' ),
					'overig'        => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[
				'key'     => 'warmwater',
				'label'   => __( 'Warm water', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''           => __( '— Selecteer —', 'kolibri-woningen' ),
					'cv_ketel'   => __( 'CV-ketel', 'kolibri-woningen' ),
					'geiser'     => __( 'Geiser', 'kolibri-woningen' ),
					'zonneboiler'=> __( 'Zonneboiler', 'kolibri-woningen' ),
					'elektrisch' => __( 'Elektrisch', 'kolibri-woningen' ),
					'overig'     => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[
				'key'     => 'cv_type',
				'label'   => __( 'CV-ketel type', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''              => __( '— Selecteer —', 'kolibri-woningen' ),
					'combi'         => __( 'Combi-ketel', 'kolibri-woningen' ),
					'hoog_rendement'=> __( 'Hoog-rendementketel', 'kolibri-woningen' ),
					'vr'            => __( 'VR-ketel', 'kolibri-woningen' ),
					'overig'        => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[ 'key' => 'cv_bouwjaar', 'label' => __( 'CV-ketel bouwjaar', 'kolibri-woningen' ), 'type' => 'number', 'min' => '1970', 'max' => '2030' ],
			[
				'key'     => 'cv_eigendom',
				'label'   => __( 'CV-ketel eigendom', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''       => __( '— Selecteer —', 'kolibri-woningen' ),
					'eigen'  => __( 'Eigen', 'kolibri-woningen' ),
					'huur'   => __( 'Huur', 'kolibri-woningen' ),
				],
			],
			[ 'key' => 'zonnepanelen', 'label' => __( 'Zonnepanelen', 'kolibri-woningen' ), 'type' => 'text', 'placeholder' => 'bijv. 8 panelen' ],
		];

		self::render_fields( $post, $fields );
	}

	// ── Tab: Buitenruimte ─────────────────────────────────────────────────────

	private static function render_tab_buitenruimte( \WP_Post $post ): void {
		$fields = [
			[ 'key' => 'tuin',          'label' => __( 'Tuin aanwezig', 'kolibri-woningen' ), 'type' => 'checkbox' ],
			[
				'key'     => 'tuin_type',
				'label'   => __( 'Soort tuin', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''             => __( '— Selecteer —', 'kolibri-woningen' ),
					'achtertuin'   => __( 'Achtertuin', 'kolibri-woningen' ),
					'voortuin'     => __( 'Voortuin', 'kolibri-woningen' ),
					'zijtuin'      => __( 'Zijtuin', 'kolibri-woningen' ),
					'rondom'       => __( 'Rondom', 'kolibri-woningen' ),
					'gemeenschappelijk' => __( 'Gemeenschappelijk', 'kolibri-woningen' ),
				],
			],
			[
				'key'     => 'tuin_positie',
				'label'   => __( 'Tuinligging', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''      => __( '— Selecteer —', 'kolibri-woningen' ),
					'noord' => __( 'Noord', 'kolibri-woningen' ),
					'oost'  => __( 'Oost', 'kolibri-woningen' ),
					'zuid'  => __( 'Zuid', 'kolibri-woningen' ),
					'west'  => __( 'West', 'kolibri-woningen' ),
					'noord_oost' => __( 'Noord-Oost', 'kolibri-woningen' ),
					'noord_west' => __( 'Noord-West', 'kolibri-woningen' ),
					'zuid_oost'  => __( 'Zuid-Oost', 'kolibri-woningen' ),
					'zuid_west'  => __( 'Zuid-West', 'kolibri-woningen' ),
				],
			],
			[ 'key' => 'tuin_m2',       'label' => __( 'Tuinoppervlak (m²)', 'kolibri-woningen' ), 'type' => 'number' ],
			[ 'key' => 'balkon',        'label' => __( 'Balkon', 'kolibri-woningen' ),   'type' => 'checkbox' ],
			[ 'key' => 'balkon_m2',     'label' => __( 'Balkonoppervlak (m²)', 'kolibri-woningen' ), 'type' => 'number' ],
			[ 'key' => 'dakterras',     'label' => __( 'Dakterras', 'kolibri-woningen' ),'type' => 'checkbox' ],
			[ 'key' => 'dakterras_m2',  'label' => __( 'Dakterrasoppervlak (m²)', 'kolibri-woningen' ), 'type' => 'number' ],
		];

		self::render_fields( $post, $fields );
	}

	// ── Tab: Parkeren ─────────────────────────────────────────────────────────

	private static function render_tab_parkeren( \WP_Post $post ): void {
		// Multi-select: parkeerfaciliteiten.
		$park_options = [
			'eigen_oprit'     => __( 'Eigen oprit', 'kolibri-woningen' ),
			'garagebox'       => __( 'Garagebox', 'kolibri-woningen' ),
			'carport'         => __( 'Carport', 'kolibri-woningen' ),
			'inpandig'        => __( 'Inpandig', 'kolibri-woningen' ),
			'openbaar'        => __( 'Openbaar', 'kolibri-woningen' ),
			'betaald'         => __( 'Betaald parkeren', 'kolibri-woningen' ),
			'geen'            => __( 'Geen', 'kolibri-woningen' ),
		];
		$park_val = Post_Types::get_meta( $post->ID, 'parkeer_faciliteiten' );
		$selected = $park_val ? explode( ',', $park_val ) : [];

		echo '<div class="kolibri-field-row">';
		echo '<label class="kolibri-label">' . esc_html__( 'Parkeerfaciliteiten', 'kolibri-woningen' ) . '</label>';
		echo '<div class="kolibri-checkboxes">';
		foreach ( $park_options as $val => $lbl ) {
			$checked = in_array( $val, $selected, true ) ? ' checked' : '';
			echo '<label class="kolibri-checkbox-label">';
			echo '<input type="checkbox" name="kolibri_parkeer_faciliteiten[]" value="' . esc_attr( $val ) . '"' . $checked . '> ';
			echo esc_html( $lbl );
			echo '</label>';
		}
		echo '</div></div>';

		$fields = [
			[ 'key' => 'garage',          'label' => __( 'Garage aanwezig', 'kolibri-woningen' ), 'type' => 'checkbox' ],
			[
				'key'     => 'garage_type',
				'label'   => __( 'Garagetype', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''             => __( '— Selecteer —', 'kolibri-woningen' ),
					'inpandig'     => __( 'Inpandig', 'kolibri-woningen' ),
					'aangebouwd'   => __( 'Aangebouwd', 'kolibri-woningen' ),
					'vrijstaand'   => __( 'Vrijstaand', 'kolibri-woningen' ),
					'carport'      => __( 'Carport', 'kolibri-woningen' ),
					'souterrain'   => __( 'Souterrain', 'kolibri-woningen' ),
					'overig'       => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[ 'key' => 'garage_capaciteit','label' => __( 'Capaciteit (auto\'s)', 'kolibri-woningen' ), 'type' => 'number' ],
			[ 'key' => 'garage_m2',        'label' => __( 'Garageoppervlak (m²)', 'kolibri-woningen' ), 'type' => 'number' ],
		];

		self::render_fields( $post, $fields );
	}

	// ── Tab: Dak ──────────────────────────────────────────────────────────────

	private static function render_tab_dak( \WP_Post $post ): void {
		$fields = [
			[
				'key'     => 'dak_type',
				'label'   => __( 'Daktype', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''             => __( '— Selecteer —', 'kolibri-woningen' ),
					'plat'         => __( 'Plat dak', 'kolibri-woningen' ),
					'schuin'       => __( 'Schuin dak', 'kolibri-woningen' ),
					'zadeldak'     => __( 'Zadeldak', 'kolibri-woningen' ),
					'mansardedak'  => __( 'Mansardedak', 'kolibri-woningen' ),
					'schilddak'    => __( 'Schilddak', 'kolibri-woningen' ),
					'lessenaarsdak'=> __( 'Lessenaarsdak', 'kolibri-woningen' ),
					'overig'       => __( 'Overig', 'kolibri-woningen' ),
				],
			],
			[
				'key'     => 'dak_bedekking',
				'label'   => __( 'Dakbedekking', 'kolibri-woningen' ),
				'type'    => 'select',
				'options' => [
					''         => __( '— Selecteer —', 'kolibri-woningen' ),
					'pannen'   => __( 'Dakpannen', 'kolibri-woningen' ),
					'bitumen'  => __( 'Bitumen', 'kolibri-woningen' ),
					'epdm'     => __( 'EPDM', 'kolibri-woningen' ),
					'riet'     => __( 'Riet', 'kolibri-woningen' ),
					'metaal'   => __( 'Metaal', 'kolibri-woningen' ),
					'leisteen' => __( 'Leisteen', 'kolibri-woningen' ),
					'overig'   => __( 'Overig', 'kolibri-woningen' ),
				],
			],
		];

		self::render_fields( $post, $fields );
	}

	// ── Tab: Overige ──────────────────────────────────────────────────────────

	private static function render_tab_overige( \WP_Post $post ): void {
		$fields = [
			[ 'key' => 'servicekosten',  'label' => __( 'Servicekosten p/m (€)', 'kolibri-woningen' ), 'type' => 'text' ],
			[ 'key' => 'vve',            'label' => __( 'VvE aanwezig', 'kolibri-woningen' ),           'type' => 'checkbox' ],
			[ 'key' => 'vve_kosten',     'label' => __( 'VvE kosten p/m (€)', 'kolibri-woningen' ),    'type' => 'text' ],
			[ 'key' => 'oplevering',     'label' => __( 'Opleveringsdatum', 'kolibri-woningen' ),       'type' => 'text', 'placeholder' => 'bijv. direct / in overleg / Q3 2025' ],
			[ 'key' => 'bijzonderheden', 'label' => __( 'Bijzonderheden', 'kolibri-woningen' ),         'type' => 'textarea' ],
			[ 'key' => 'inwendig_360',   'label' => __( '360° rondleiding URL', 'kolibri-woningen' ),   'type' => 'url' ],
			[ 'key' => 'video_url',      'label' => __( 'Video URL', 'kolibri-woningen' ),              'type' => 'url' ],
			[ 'key' => 'brochure_url',   'label' => __( 'Brochure URL / PDF', 'kolibri-woningen' ),     'type' => 'url' ],
		];

		self::render_fields( $post, $fields );
	}

	// ── Field Renderer ────────────────────────────────────────────────────────

	private static function render_fields( \WP_Post $post, array $fields ): void {
		foreach ( $fields as $f ) {
			$key   = $f['key'];
			$value = Post_Types::get_meta( $post->ID, $key );
			$label = $f['label'];
			$type  = $f['type'] ?? 'text';

			echo '<div class="kolibri-field-row">';
			echo '<label class="kolibri-label" for="kolibri_' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label>';

			switch ( $type ) {
				case 'textarea':
					echo '<textarea name="kolibri_' . esc_attr( $key ) . '" id="kolibri_' . esc_attr( $key ) . '" class="kolibri-input kolibri-textarea" rows="4">' . esc_textarea( $value ) . '</textarea>';
					break;

				case 'select':
					echo '<select name="kolibri_' . esc_attr( $key ) . '" id="kolibri_' . esc_attr( $key ) . '" class="kolibri-input">';
					foreach ( $f['options'] as $opt_val => $opt_label ) {
						$selected = selected( $value, $opt_val, false );
						echo '<option value="' . esc_attr( $opt_val ) . '"' . $selected . '>' . esc_html( $opt_label ) . '</option>';
					}
					echo '</select>';
					break;

				case 'checkbox':
					$checked = checked( $value, '1', false );
					echo '<label class="kolibri-toggle">';
					echo '<input type="hidden" name="kolibri_' . esc_attr( $key ) . '" value="0">';
					echo '<input type="checkbox" name="kolibri_' . esc_attr( $key ) . '" id="kolibri_' . esc_attr( $key ) . '" value="1"' . $checked . '>';
					if ( ! empty( $f['description'] ) ) {
						echo ' <span>' . esc_html( $f['description'] ) . '</span>';
					}
					echo '</label>';
					break;

				default:
					$attrs = 'class="kolibri-input" type="' . esc_attr( $type ) . '"';
					if ( ! empty( $f['min'] ) ) {
						$attrs .= ' min="' . esc_attr( $f['min'] ) . '"';
					}
					if ( ! empty( $f['max'] ) ) {
						$attrs .= ' max="' . esc_attr( $f['max'] ) . '"';
					}
					if ( ! empty( $f['placeholder'] ) ) {
						$attrs .= ' placeholder="' . esc_attr( $f['placeholder'] ) . '"';
					}
					echo '<input ' . $attrs . ' name="kolibri_' . esc_attr( $key ) . '" id="kolibri_' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
					break;
			}

			echo '</div>';
		}
	}

	// ── Save ──────────────────────────────────────────────────────────────────

	public static function save( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST['kolibri_meta_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kolibri_meta_nonce'] ) ), 'kolibri_save_meta' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Simple scalar fields.
		$scalar_fields = [
			'straat', 'huisnummer', 'toevoeging', 'postcode', 'stad', 'wijk',
			'koopprijs', 'huurprijs', 'prijs_tekst', 'uitgelicht',
			'maps_url', 'woning_type', 'bouwjaar', 'perceel_m2', 'woon_m2',
			'inhoud_m3', 'kamers', 'slaapkamers', 'badkamers', 'verdiepingen',
			'bouw_type', 'fundering', 'woonlagen', 'verdieping_nr', 'woonkamer_type',
			'keuken_type', 'keuken_m2', 'energielabel', 'verwarming', 'warmwater',
			'cv_type', 'cv_bouwjaar', 'cv_eigendom', 'zonnepanelen',
			'tuin', 'tuin_type', 'tuin_positie', 'tuin_m2',
			'balkon', 'balkon_m2', 'dakterras', 'dakterras_m2',
			'garage', 'garage_type', 'garage_capaciteit', 'garage_m2',
			'dak_type', 'dak_bedekking',
			'servicekosten', 'vve', 'vve_kosten', 'oplevering',
			'bijzonderheden', 'inwendig_360', 'video_url', 'brochure_url',
		];

		foreach ( $scalar_fields as $field ) {
			if ( isset( $_POST[ 'kolibri_' . $field ] ) ) {
				$raw = wp_unslash( $_POST[ 'kolibri_' . $field ] );
				update_post_meta( $post_id, '_kolibri_' . $field, sanitize_text_field( $raw ) );
			}
		}

		// Multi-checkbox fields (stored as comma-separated string).
		$multi_fields = [ 'isolatie', 'badkamer_voorzieningen', 'parkeer_faciliteiten' ];
		foreach ( $multi_fields as $field ) {
			$values = isset( $_POST[ 'kolibri_' . $field ] ) && is_array( $_POST[ 'kolibri_' . $field ] )
				? array_map( 'sanitize_text_field', wp_unslash( $_POST[ 'kolibri_' . $field ] ) )
				: [];
			update_post_meta( $post_id, '_kolibri_' . $field, implode( ',', $values ) );
		}

		// Gallery IDs.
		if ( isset( $_POST['kolibri_gallery_ids'] ) ) {
			$ids = sanitize_text_field( wp_unslash( $_POST['kolibri_gallery_ids'] ) );
			// Allow only integers and commas.
			$ids = preg_replace( '/[^0-9,]/', '', $ids );
			update_post_meta( $post_id, '_kolibri_gallery_ids', $ids );
		}
	}
}
