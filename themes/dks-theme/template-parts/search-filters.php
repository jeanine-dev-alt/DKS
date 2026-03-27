<?php
/**
 * template-parts/search-filters.php
 *
 * Shared search/filter form used on the homepage hero and the property archive.
 *
 * Expected variables (set before get_template_part / include):
 *   array  $dks_active_filters  — current active values (from $_GET on the archive).
 *                                 Defaults to empty array (nothing pre-selected).
 *   string $dks_submit_label    — button label. Defaults to 'Find My Home'.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

$af  = $dks_active_filters ?? [];
$btn = $dks_submit_label   ?? __( 'Find My Home', 'dks-theme' );

$min_prijs_options = [
	''        => __( 'No minimum', 'dks-theme' ),
	'300000'  => '&euro;&nbsp;300.000',
	'500000'  => '&euro;&nbsp;500.000',
	'750000'  => '&euro;&nbsp;750.000',
	'1000000' => '&euro;&nbsp;1.000.000',
	'1500000' => '&euro;&nbsp;1.500.000',
];

$max_prijs_options = [
	''         => __( 'No maximum', 'dks-theme' ),
	'750000'   => '&euro;&nbsp;750.000',
	'1000000'  => '&euro;&nbsp;1.000.000',
	'1250000'  => '&euro;&nbsp;1.250.000',
	'1500000'  => '&euro;&nbsp;1.500.000',
	'2000000'  => '&euro;&nbsp;2.000.000',
	'2500000'  => '&euro;&nbsp;2.500.000',
];
?>
<form class="dks-hero-search__form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="hidden" name="post_type" value="kolibri_woning">

	<!-- Stad / adres -->
	<div class="dks-hero-search__field dks-hero-search__field--city">
		<label class="dks-hero-search__label" for="sf-stad"><?php esc_html_e( 'City or address', 'dks-theme' ); ?></label>
		<div class="dks-hero-search__input-row">
			<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="dks-hero-search__icon"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.979-5.121 3.979-8.827a8.25 8.25 0 00-16.5 0c0 3.706 2.035 6.744 3.979 8.827a19.58 19.58 0 002.686 2.282 16.975 16.975 0 001.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
			<input
				id="sf-stad"
				type="text"
				name="stad"
				class="dks-hero-search__input"
				placeholder="<?php esc_attr_e( 'Amsterdam, Prinsengracht…', 'dks-theme' ); ?>"
				value="<?php echo esc_attr( $af['stad'] ?? '' ); ?>"
				autocomplete="off"
			>
		</div>
	</div>

	<div class="dks-hero-search__sep" aria-hidden="true"></div>

	<!-- Min. prijs -->
	<div class="dks-hero-search__field">
		<label class="dks-hero-search__label" for="sf-min-prijs"><?php esc_html_e( 'Min. price', 'dks-theme' ); ?></label>
		<select id="sf-min-prijs" name="min_prijs" class="dks-hero-search__select">
			<?php foreach ( $min_prijs_options as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( (string) ( $af['min_prijs'] ?? '' ), (string) $val ); ?>>
					<?php echo $label; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="dks-hero-search__sep" aria-hidden="true"></div>

	<!-- Max. prijs -->
	<div class="dks-hero-search__field">
		<label class="dks-hero-search__label" for="sf-max-prijs"><?php esc_html_e( 'Max. price', 'dks-theme' ); ?></label>
		<select id="sf-max-prijs" name="max_prijs" class="dks-hero-search__select">
			<?php foreach ( $max_prijs_options as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( (string) ( $af['max_prijs'] ?? '' ), (string) $val ); ?>>
					<?php echo $label; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="dks-hero-search__sep" aria-hidden="true"></div>

	<!-- Slaapkamers -->
	<div class="dks-hero-search__field">
		<label class="dks-hero-search__label" for="sf-kamers"><?php esc_html_e( 'Bedrooms', 'dks-theme' ); ?></label>
		<select id="sf-kamers" name="kamers" class="dks-hero-search__select">
			<option value=""><?php esc_html_e( 'Any', 'dks-theme' ); ?></option>
			<?php foreach ( [ 1, 2, 3, 4, 5 ] as $n ) : ?>
				<option value="<?php echo $n; ?>" <?php selected( (int) ( $af['kamers'] ?? 0 ), $n ); ?>><?php echo $n; ?>+</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="dks-hero-search__sep" aria-hidden="true"></div>

	<!-- Min. m² -->
	<div class="dks-hero-search__field">
		<label class="dks-hero-search__label" for="sf-m2">m&sup2;</label>
		<select id="sf-m2" name="min_m2" class="dks-hero-search__select">
			<option value=""><?php esc_html_e( 'Any', 'dks-theme' ); ?></option>
			<?php foreach ( [ 50, 75, 100, 125, 150, 200, 250 ] as $m ) : ?>
				<option value="<?php echo $m; ?>" <?php selected( (int) ( $af['min_m2'] ?? 0 ), $m ); ?>><?php echo $m; ?>+ m&sup2;</option>
			<?php endforeach; ?>
		</select>
	</div>

	<!-- Submit -->
	<button type="submit" class="dks-hero-search__btn">
		<?php echo esc_html( $btn ); ?>
	</button>

</form>
