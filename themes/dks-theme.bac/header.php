<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary">
	<?php esc_html_e( 'Skip to content', 'dks-theme' ); ?>
</a>

<header id="masthead" class="site-header" role="banner">
	<div class="dks-container">

		<!-- Logo -->
		<div class="site-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-logo__link">
					<div class="site-logo__mark" aria-hidden="true">DKS</div>
					<div class="site-logo__text">
						<span class="site-logo__name"><?php bloginfo( 'name' ); ?></span>
						<span class="site-logo__tagline">
							<?php
							$description = get_bloginfo( 'description', 'display' );
							echo $description ? esc_html( $description ) : esc_html__( 'Real Estate', 'dks-theme' );
							?>
						</span>
					</div>
				</a>
			<?php endif; ?>
		</div>

		<!-- Primary Navigation -->
		<nav id="site-navigation" class="primary-navigation" role="navigation"
			aria-label="<?php esc_attr_e( 'Primary Navigation', 'dks-theme' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'container'      => false,
				'fallback_cb'    => 'dks_fallback_menu',
			] );
			?>
		</nav>

		<!-- Header CTA -->
		<div class="header-actions">
			<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ?: home_url( '/contact' ) ); ?>"
				class="header-cta">
				<?php esc_html_e( 'Contact Us', 'dks-theme' ); ?>
			</a>
		</div>

		<!-- Mobile Menu Toggle -->
		<button class="menu-toggle" id="menu-toggle"
			aria-controls="site-navigation"
			aria-expanded="false"
			aria-label="<?php esc_attr_e( 'Open menu', 'dks-theme' ); ?>">
			<span></span>
			<span></span>
			<span></span>
		</button>

	</div><!-- .dks-container -->
</header><!-- #masthead -->

<div id="page" class="site">
<main id="primary" class="site-main">
<?php
/**
 * Fallback menu shown when no menu is assigned to 'primary'.
 * Lists all top-level pages.
 */
function dks_fallback_menu() {
	wp_page_menu( [
		'menu_id'    => 'primary-menu',
		'menu_class' => 'menu',
		'show_home'  => true,
		'echo'       => true,
	] );
}
