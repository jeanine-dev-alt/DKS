<?php
/**
 * searchform.php — Custom search form template.
 *
 * WordPress uses this file automatically instead of the built-in form,
 * giving us full control over labels and button text.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
$unique_id = 'search-form-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $unique_id ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'dks-theme' ); ?></span>
		<input
			type="search"
			id="<?php echo esc_attr( $unique_id ); ?>"
			class="search-field"
			placeholder="<?php esc_attr_e( 'Search&hellip;', 'dks-theme' ); ?>"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			name="s"
		>
	</label>
	<button type="submit" class="search-submit">
		<?php esc_html_e( 'Search', 'dks-theme' ); ?>
	</button>
</form>
