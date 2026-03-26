</main><!-- #primary -->

<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="dks-container">

		<div class="site-footer__grid">

			<!-- Brand column -->
			<div>
				<div class="site-logo">
					<?php
					$custom_logo_url = get_theme_mod( 'dks_brand_logo', '' );
					if ( $custom_logo_url ) :
						?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-logo__img-link">
							<img
								src="<?php echo esc_url( $custom_logo_url ); ?>"
								alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
								class="site-logo__img"
							>
						</a>
					<?php elseif ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<div class="site-logo__mark" aria-hidden="true">DKS</div>
						<div class="site-logo__text">
							<span class="site-logo__name"><?php bloginfo( 'name' ); ?></span>
							<span class="site-logo__tagline"><?php esc_html_e( 'Real Estate', 'dks-theme' ); ?></span>
						</div>
					<?php endif; ?>
				</div>

				<p class="site-footer__brand-desc">
					<?php
					echo wp_kses_post( apply_filters(
						'dks_footer_description',
						__( 'The leading name in premium Dutch real estate since 2004. We turn dream houses into your new home.', 'dks-theme' )
					) );
					?>
				</p>

				<!-- Social links — populate via Customizer or child theme filter -->
				<?php $social = apply_filters( 'dks_social_links', [] ); ?>
				<?php if ( ! empty( $social ) ) : ?>
					<div class="site-footer__social">
						<?php foreach ( $social as $link ) : ?>
							<a href="<?php echo esc_url( $link['url'] ); ?>"
								target="_blank" rel="noopener noreferrer"
								aria-label="<?php echo esc_attr( $link['label'] ); ?>">
								<?php echo $link['icon'] ?? ''; // Escaped upstream ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Footer Navigation -->
			<div>
				<h2 class="site-footer__col-title"><?php esc_html_e( 'Explore', 'dks-theme' ); ?></h2>
				<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Navigation', 'dks-theme' ); ?>">
					<?php
					wp_nav_menu( [
						'theme_location' => 'footer',
						'menu_id'        => 'footer-menu',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					] );
					?>
				</nav>
			</div>

			<!-- Contact info -->
			<div>
				<h2 class="site-footer__col-title"><?php esc_html_e( 'Contact', 'dks-theme' ); ?></h2>
				<?php
				$address = get_theme_mod( 'dks_contact_address', __( 'Prinsengracht 124, 1016 DZ Amsterdam', 'dks-theme' ) );
				$phone   = get_theme_mod( 'dks_contact_phone',   '+31 (0) 20 123 4567' );
				$email   = get_theme_mod( 'dks_contact_email',   'info@dksrealestate.nl' );
				?>
				<ul class="site-footer__contact-list">
					<?php if ( $address ) : ?>
						<li>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.979-5.121 3.979-8.827a8.25 8.25 0 00-16.5 0c0 3.706 2.035 6.744 3.979 8.827a19.58 19.58 0 002.686 2.282 16.975 16.975 0 001.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
							<span><?php echo esc_html( $address ); ?></span>
						</li>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<li>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd"/></svg>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						</li>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<li>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/></svg>
							<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- Office hours -->
			<div>
				<h2 class="site-footer__col-title"><?php esc_html_e( 'Office Hours', 'dks-theme' ); ?></h2>
				<ul class="site-footer__hours-list">
					<li>
						<span><?php esc_html_e( 'Mon – Fri', 'dks-theme' ); ?></span>
						<span class="hours-value"><?php echo esc_html( get_theme_mod( 'dks_hours_weekdays', '09:00 – 18:00' ) ); ?></span>
					</li>
					<li>
						<span><?php esc_html_e( 'Saturday', 'dks-theme' ); ?></span>
						<span class="hours-value"><?php echo esc_html( get_theme_mod( 'dks_hours_saturday', '10:00 – 15:00' ) ); ?></span>
					</li>
					<li>
						<span><?php esc_html_e( 'Sunday', 'dks-theme' ); ?></span>
						<span class="closed"><?php esc_html_e( 'Closed', 'dks-theme' ); ?></span>
					</li>
				</ul>
			</div>

		</div><!-- .site-footer__grid -->

		<!-- Bottom bar -->
		<div class="site-footer__bottom">
			<p>
				<?php
				printf(
					/* translators: 1: year, 2: site name */
					esc_html__( '© %1$s %2$s. All rights reserved.', 'dks-theme' ),
					date_i18n( 'Y' ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</p>
			<div class="site-footer__legal">
				<?php
				wp_nav_menu( [
					'theme_location' => 'footer_legal',
					'menu_id'        => 'footer-legal-menu',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				] );
				?>
				<?php if ( get_privacy_policy_url() ) : ?>
					<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy Policy', 'dks-theme' ); ?></a>
				<?php endif; ?>
			</div>
		</div>

	</div><!-- .dks-container -->
</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
