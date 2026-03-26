<?php
/**
 * 404.php — "Page Not Found" error template.
 *
 * Displayed when WordPress cannot find a matching post or page.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<div class="dks-container dks-section">

	<div style="text-align:center;padding:4rem 0 6rem;">

		<p style="font-size:clamp(6rem,20vw,14rem);font-weight:900;line-height:1;letter-spacing:-.05em;color:var(--dks-color-accent);margin:0;">
			404
		</p>

		<h1 style="font-size:clamp(1.75rem,5vw,3.5rem);font-weight:900;letter-spacing:-.02em;margin:1.5rem 0 1rem;">
			<?php esc_html_e( 'Page not found', 'dks-theme' ); ?>
		</h1>

		<p style="font-size:1.125rem;color:rgba(26,26,26,.6);max-width:40ch;margin:0 auto 2.5rem;">
			<?php esc_html_e( 'The page you are looking for no longer exists or has been moved. Use the search bar or return to the homepage.', 'dks-theme' ); ?>
		</p>

		<div class="dks-form-wrap">
			<?php get_search_form(); ?>
		</div>

		<p style="margin-top:2rem;">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
			   style="display:inline-flex;align-items:center;gap:.5rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--dks-color-accent);text-decoration:none;">
				&larr; <?php esc_html_e( 'Back to homepage', 'dks-theme' ); ?>
			</a>
		</p>

	</div>

</div>

<?php get_footer();
