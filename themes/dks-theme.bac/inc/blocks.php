<?php
/**
 * Custom Gutenberg block registration.
 *
 * Registers server-side rendered blocks so they work in both the
 * editor and on the front end. Child themes can unregister a block
 * and re-register their own version.
 *
 * Blocks registered:
 *   dks/hero          — Full-width hero with editable image, heading, subheading, buttons
 *   dks/listings      — Premium listings grid (HTML placeholder; child theme can swap to CPT query)
 *   dks/features      — "Why Choose DKS" section with icon items
 *   dks/newsletter    — Email subscribe section
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Register blocks via block.json when available ──────────────────────────
// For each block we fall back to a PHP-only dynamic registration when
// block.json is absent, keeping dependencies minimal.

if ( ! function_exists( 'dks_register_blocks' ) ) :
	function dks_register_blocks() {

		// ── dks/hero ───────────────────────────────────────────────────────
		register_block_type( 'dks/hero', [
			'title'           => __( 'DKS Hero', 'dks-theme' ),
			'description'     => __( 'Full-width hero section with background image, heading, and CTA buttons. Works on any page.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'cover-image',
			'supports'        => [
				'align'           => [ 'full' ],
				'anchor'          => true,
				'customClassName' => true,
			],
			'attributes'      => [
				'backgroundId'   => [ 'type' => 'number',  'default' => 0 ],
				'backgroundUrl'  => [ 'type' => 'string',  'default' => '' ],
				'backgroundAlt'  => [ 'type' => 'string',  'default' => '' ],
				'heading'        => [ 'type' => 'string',  'default' => 'A NEW <span class="accent">BEGINNING</span> IN YOUR DREAM HOME' ],
				'subheading'     => [ 'type' => 'string',  'default' => 'Expert guidance for buying and renting premium properties.' ],
				'btnPrimaryText' => [ 'type' => 'string',  'default' => 'View Listings' ],
				'btnPrimaryUrl'  => [ 'type' => 'string',  'default' => '#' ],
				'btnSecondText'  => [ 'type' => 'string',  'default' => 'Contact Us' ],
				'btnSecondUrl'   => [ 'type' => 'string',  'default' => '#' ],
				'overlayOpacity' => [ 'type' => 'number',  'default' => 45 ],
				'minHeight'      => [ 'type' => 'string',  'default' => '90vh' ],
			],
			'render_callback' => 'dks_render_hero_block',
		] );

		// ── dks/listings ──────────────────────────────────────────────────
		register_block_type( 'dks/listings', [
			'title'           => __( 'DKS Premium Listings', 'dks-theme' ),
			'description'     => __( 'Displays a grid of property listings. Defaults to HTML placeholders; a child theme can hook in real CPT data.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'building',
			'supports'        => [
				'anchor'          => true,
				'customClassName' => true,
			],
			'attributes'      => [
				'title'         => [ 'type' => 'string', 'default' => 'Premium Listings' ],
				'eyebrow'       => [ 'type' => 'string', 'default' => 'Selected Collection' ],
				'browseText'    => [ 'type' => 'string', 'default' => 'Browse All Properties' ],
				'browseUrl'     => [ 'type' => 'string', 'default' => '#' ],
				'columns'       => [ 'type' => 'number', 'default' => 3 ],
				'cards'         => [
					'type'    => 'array',
					'default' => [],
					'items'   => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_listings_block',
		] );

		// ── dks/features ──────────────────────────────────────────────────
		register_block_type( 'dks/features', [
			'title'           => __( 'DKS Features', 'dks-theme' ),
			'description'     => __( '"Why Choose DKS" section with icon feature items.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'star-filled',
			'supports'        => [
				'align'           => [ 'full' ],
				'anchor'          => true,
				'customClassName' => true,
			],
			'attributes'      => [
				'eyebrow'  => [ 'type' => 'string', 'default' => 'DKS Distinction' ],
				'title'    => [ 'type' => 'string', 'default' => 'Why Choose <span class="accent">DKS</span>' ],
				'intro'    => [ 'type' => 'string', 'default' => 'We combine deep local market insights with a commitment to excellence, ensuring your real estate journey is seamless and successful.' ],
				'items'    => [
					'type'    => 'array',
					'default' => [
						[
							'icon'  => 'home',
							'title' => 'Local Expertise',
							'desc'  => 'In-depth knowledge of the Dutch housing regulations and hidden gems in every neighbourhood.',
						],
						[
							'icon'  => 'handshake',
							'title' => 'Personal Service',
							'desc'  => 'A dedicated advisor tailored to your unique lifestyle needs and investment goals.',
						],
						[
							'icon'  => 'verified',
							'title' => 'Trusted Results',
							'desc'  => 'Over 20 years of experience delivering premium real estate solutions across the Netherlands.',
						],
					],
					'items'   => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_features_block',
		] );

		// ── dks/text-image ────────────────────────────────────────────────
		register_block_type( 'dks/text-image', [
			'title'           => __( 'DKS Text + Image', 'dks-theme' ),
			'description'     => __( 'Two-column block with text and an image. Layout can be reversed.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'align-pull-right',
			'supports'        => [ 'anchor' => true, 'align' => [ 'wide' ] ],
			'attributes'      => [
				'eyebrow'  => [ 'type' => 'string',  'default' => '' ],
				'heading'  => [ 'type' => 'string',  'default' => 'Your Compelling <span class="accent">Headline</span>' ],
				'text'     => [ 'type' => 'string',  'default' => 'Add your supporting text here. Explain the value clearly and concisely.' ],
				'btnText'  => [ 'type' => 'string',  'default' => 'Learn More' ],
				'btnUrl'   => [ 'type' => 'string',  'default' => '#' ],
				'imageId'  => [ 'type' => 'number',  'default' => 0 ],
				'imageUrl' => [ 'type' => 'string',  'default' => '' ],
				'imageAlt' => [ 'type' => 'string',  'default' => '' ],
				'reversed' => [ 'type' => 'boolean', 'default' => false ],
			],
			'render_callback' => 'dks_render_text_image_block',
		] );

		// ── dks/steps ─────────────────────────────────────────────────────
		register_block_type( 'dks/steps', [
			'title'           => __( 'DKS Steps', 'dks-theme' ),
			'description'     => __( 'Numbered steps / method block for explaining a process.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'list-view',
			'supports'        => [ 'anchor' => true ],
			'attributes'      => [
				'eyebrow' => [ 'type' => 'string', 'default' => 'How It Works' ],
				'heading' => [ 'type' => 'string', 'default' => 'Our <span class="accent">Method</span>' ],
				'intro'   => [ 'type' => 'string', 'default' => '' ],
				'steps'   => [
					'type'    => 'array',
					'default' => [
						[ 'title' => 'First Step',  'desc' => 'Describe what happens in this step.' ],
						[ 'title' => 'Second Step', 'desc' => 'Describe what happens in this step.' ],
						[ 'title' => 'Third Step',  'desc' => 'Describe what happens in this step.' ],
					],
					'items' => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_steps_block',
		] );

		// ── dks/testimonials ──────────────────────────────────────────────
		register_block_type( 'dks/testimonials', [
			'title'           => __( 'DKS Testimonials', 'dks-theme' ),
			'description'     => __( 'Social proof section with quotes, names and optional photos.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'format-quote',
			'supports'        => [ 'anchor' => true ],
			'attributes'      => [
				'eyebrow' => [ 'type' => 'string', 'default' => 'What Clients Say' ],
				'heading' => [ 'type' => 'string', 'default' => 'Client <span class="accent">Testimonials</span>' ],
				'items'   => [
					'type'    => 'array',
					'default' => [
						[ 'quote' => '"Exceptional service from start to finish."', 'name' => 'Jan de Vries',   'role' => 'Buyer',  'imageUrl' => '', 'imageId' => 0, 'rating' => 5 ],
						[ 'quote' => '"They found our dream home in two weeks."',    'name' => 'Marieke Smit',  'role' => 'Buyer',  'imageUrl' => '', 'imageId' => 0, 'rating' => 5 ],
						[ 'quote' => '"Sold above asking price. Impressive."',       'name' => 'Peter van Dam', 'role' => 'Seller', 'imageUrl' => '', 'imageId' => 0, 'rating' => 5 ],
					],
					'items' => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_testimonials_block',
		] );

		// ── dks/logos ─────────────────────────────────────────────────────
		register_block_type( 'dks/logos', [
			'title'           => __( 'DKS Logo Grid', 'dks-theme' ),
			'description'     => __( 'Grid of partner logos or certifications.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'images-alt2',
			'supports'        => [ 'anchor' => true ],
			'attributes'      => [
				'heading' => [ 'type' => 'string', 'default' => '' ],
				'logos'   => [
					'type'    => 'array',
					'default' => [],
					'items'   => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_logos_block',
		] );

		// ── dks/process ───────────────────────────────────────────────────
		register_block_type( 'dks/process', [
			'title'           => __( 'DKS Process', 'dks-theme' ),
			'description'     => __( 'Vertical timeline / next-steps section.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'arrow-down-alt',
			'supports'        => [ 'anchor' => true ],
			'attributes'      => [
				'eyebrow' => [ 'type' => 'string', 'default' => 'Next Steps' ],
				'heading' => [ 'type' => 'string', 'default' => 'How We <span class="accent">Work</span>' ],
				'intro'   => [ 'type' => 'string', 'default' => '' ],
				'steps'   => [
					'type'    => 'array',
					'default' => [
						[ 'title' => 'Initial Consultation', 'desc' => 'We start with a free consultation to understand your needs and goals.' ],
						[ 'title' => 'Market Analysis',      'desc' => 'Our experts analyse the market to find the best opportunities for you.' ],
						[ 'title' => 'Viewings & Negotiation','desc' => 'We arrange viewings and negotiate the best possible price on your behalf.' ],
						[ 'title' => 'Closing the Deal',     'desc' => 'We guide you through the paperwork and handover to ensure a smooth closing.' ],
					],
					'items' => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_process_block',
		] );

		// ── dks/cta ───────────────────────────────────────────────────────
		register_block_type( 'dks/cta', [
			'title'           => __( 'DKS CTA', 'dks-theme' ),
			'description'     => __( 'Strong call-to-action section with title and buttons.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'megaphone',
			'supports'        => [ 'anchor' => true, 'align' => [ 'wide', 'full' ] ],
			'attributes'      => [
				'eyebrow'        => [ 'type' => 'string', 'default' => '' ],
				'heading'        => [ 'type' => 'string', 'default' => 'Ready to Find Your <span class="accent">Dream Home</span>?' ],
				'subheading'     => [ 'type' => 'string', 'default' => 'Get in touch today and let our experts guide you.' ],
				'btnPrimaryText' => [ 'type' => 'string', 'default' => 'Contact Us' ],
				'btnPrimaryUrl'  => [ 'type' => 'string', 'default' => '#' ],
				'btnSecondText'  => [ 'type' => 'string', 'default' => '' ],
				'btnSecondUrl'   => [ 'type' => 'string', 'default' => '#' ],
				'variant'        => [ 'type' => 'string', 'default' => 'dark' ],
			],
			'render_callback' => 'dks_render_cta_block',
		] );

		// ── dks/bonus ─────────────────────────────────────────────────────
		register_block_type( 'dks/bonus', [
			'title'           => __( 'DKS Bonus Benefits', 'dks-theme' ),
			'description'     => __( 'Highlight extra benefits in a card grid.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'awards',
			'supports'        => [ 'anchor' => true ],
			'attributes'      => [
				'eyebrow' => [ 'type' => 'string', 'default' => 'Included' ],
				'heading' => [ 'type' => 'string', 'default' => 'Extra <span class="accent">Benefits</span>' ],
				'intro'   => [ 'type' => 'string', 'default' => '' ],
				'items'   => [
					'type'    => 'array',
					'default' => [
						[ 'icon' => 'star',      'title' => 'Free Valuation',   'desc' => 'Get a professional valuation of your property at no cost.' ],
						[ 'icon' => 'verified',  'title' => 'Legal Support',    'desc' => 'We guide you through all legal requirements and paperwork.' ],
						[ 'icon' => 'home',      'title' => 'After-Sale Service','desc' => 'Our team remains available after the deal is closed.' ],
					],
					'items' => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_bonus_block',
		] );

		// ── dks/faq ───────────────────────────────────────────────────────
		register_block_type( 'dks/faq', [
			'title'           => __( 'DKS FAQ', 'dks-theme' ),
			'description'     => __( 'Accordion FAQ with question/answer repeater.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'info',
			'supports'        => [ 'anchor' => true ],
			'attributes'      => [
				'eyebrow' => [ 'type' => 'string', 'default' => 'FAQ' ],
				'heading' => [ 'type' => 'string', 'default' => 'Frequently Asked <span class="accent">Questions</span>' ],
				'intro'   => [ 'type' => 'string', 'default' => '' ],
				'items'   => [
					'type'    => 'array',
					'default' => [
						[ 'question' => 'What services do you offer?',             'answer' => 'We offer a full range of real estate services including buying, selling, renting and investment advice.' ],
						[ 'question' => 'How long does it take to buy a property?', 'answer' => 'The process typically takes 2–4 months from initial search to final handover.' ],
						[ 'question' => 'What are your fees?',                     'answer' => 'Our fees are transparent and agreed upon upfront. Contact us for a personalised quote.' ],
					],
					'items' => [ 'type' => 'object' ],
				],
			],
			'render_callback' => 'dks_render_faq_block',
		] );

		// ── dks/newsletter ────────────────────────────────────────────────
		register_block_type( 'dks/newsletter', [
			'title'           => __( 'DKS Newsletter', 'dks-theme' ),
			'description'     => __( 'Stay Informed email subscribe section.', 'dks-theme' ),
			'category'        => 'dks-blocks',
			'icon'            => 'email',
			'attributes'      => [
				'heading'     => [ 'type' => 'string', 'default' => 'Stay <span class="accent">Informed</span>' ],
				'description' => [ 'type' => 'string', 'default' => 'Receive exclusive market insights and premium listings directly to your inbox.' ],
				'buttonText'  => [ 'type' => 'string', 'default' => 'Subscribe' ],
			],
			'render_callback' => 'dks_render_newsletter_block',
		] );
	}
endif;
add_action( 'init', 'dks_register_blocks' );

// ── Register a custom block category ──────────────────────────────────────
if ( ! function_exists( 'dks_register_block_category' ) ) :
	function dks_register_block_category( $categories ) {
		return array_merge(
			[
				[
					'slug'  => 'dks-blocks',
					'title' => __( 'DKS Blocks', 'dks-theme' ),
					'icon'  => 'building',
				],
			],
			$categories
		);
	}
endif;
add_filter( 'block_categories_all', 'dks_register_block_category', 10, 2 );


// ══════════════════════════════════════════════════════════════════════════════
// RENDER CALLBACKS
// Each callback is pluggable: child themes can override by declaring the
// function before the parent theme loads, or by using the filter hooks below.
// ══════════════════════════════════════════════════════════════════════════════

// ── Hero ──────────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_hero_block' ) ) :
	/**
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block inner content (unused for server-render).
	 * @param WP_Block $block      Block instance.
	 * @return string  HTML output.
	 */
	function dks_render_hero_block( $attributes, $content, $block ) {

		$bg_url     = esc_url( $attributes['backgroundUrl'] ?? '' );
		$bg_alt     = esc_attr( $attributes['backgroundAlt'] ?? '' );
		$heading    = wp_kses_post( $attributes['heading'] ?? '' );
		$subheading = esc_html( $attributes['subheading'] ?? '' );
		$btn1_text  = esc_html( $attributes['btnPrimaryText'] ?? '' );
		$btn1_url   = esc_url( $attributes['btnPrimaryUrl'] ?? '#' );
		$btn2_text  = esc_html( $attributes['btnSecondText'] ?? '' );
		$btn2_url   = esc_url( $attributes['btnSecondUrl'] ?? '#' );
		$opacity    = absint( $attributes['overlayOpacity'] ?? 45 );
		$min_height = esc_attr( $attributes['minHeight'] ?? '90vh' );
		$opacity_d  = round( $opacity / 100, 2 );

		// Allow child theme / plugin to swap out the background image source
		$bg_url = apply_filters( 'dks_hero_background_url', $bg_url, $attributes );

		$inline_style = $bg_url
			? sprintf( 'background-image:url(%s);', $bg_url )
			: '';

		ob_start();
		?>
		<section class="dks-hero alignfull" style="min-height:<?php echo $min_height; ?>;" aria-label="<?php esc_attr_e( 'Hero section', 'dks-theme' ); ?>">
			<div class="dks-hero__bg" style="<?php echo $inline_style; ?>" role="img" aria-label="<?php echo $bg_alt; ?>"></div>
			<div class="dks-hero__overlay" style="opacity:<?php echo $opacity_d; ?>;"></div>
			<div class="dks-hero__content dks-container">
				<div class="dks-hero__inner">
					<?php if ( $heading ) : ?>
						<h1 class="dks-hero__heading"><?php echo $heading; ?></h1>
					<?php endif; ?>
					<?php if ( $subheading ) : ?>
						<p class="dks-hero__subheading"><?php echo $subheading; ?></p>
					<?php endif; ?>
					<?php if ( $btn1_text || $btn2_text ) : ?>
						<div class="dks-hero__actions">
							<?php if ( $btn1_text ) : ?>
								<a href="<?php echo $btn1_url; ?>" class="dks-btn dks-btn--primary">
									<?php echo $btn1_text; ?>
								</a>
							<?php endif; ?>
							<?php if ( $btn2_text ) : ?>
								<a href="<?php echo $btn2_url; ?>" class="dks-btn dks-btn--outline">
									<?php echo $btn2_text; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Listings ─────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_listings_block' ) ) :
	function dks_render_listings_block( $attributes ) {

		$title      = esc_html( $attributes['title']      ?? 'Premium Listings' );
		$eyebrow    = esc_html( $attributes['eyebrow']    ?? 'Selected Collection' );
		$browse_txt = esc_html( $attributes['browseText'] ?? 'Browse All Properties' );
		$browse_url = esc_url(  $attributes['browseUrl']  ?? '#' );
		$columns    = absint(   $attributes['columns']    ?? 3 );

		// Gebruik kaarten uit de editor als die er zijn, anders de filter/placeholder
		$editor_cards = $attributes['cards'] ?? [];

		if ( ! empty( $editor_cards ) ) {
			// Zet editor-kaarten om naar het standaard item-formaat
			$items = array_map( function( $card ) {
				return [
					'image'     => esc_url( $card['imageUrl'] ?? '' ),
					'badge'     => $card['badge']    ?? '',
					'price'     => $card['price']    ?? '',
					'location'  => $card['location'] ?? '',
					'beds'      => absint( $card['beds']  ?? 0 ),
					'baths'     => absint( $card['baths'] ?? 0 ),
					'sqm'       => $card['sqm']      ?? '',
					'permalink' => esc_url( $card['permalink'] ?? '#' ),
				];
			}, $editor_cards );
		} else {
			// Geen editor-kaarten: gebruik de filter (child theme kan hier CPT-data injecteren)
			$items = apply_filters( 'dks_listings_items', dks_get_placeholder_listings(), $attributes );
		}

		ob_start();
		?>
		<section class="dks-listings dks-section">
			<div class="dks-container">
				<div class="dks-listings__header">
					<div>
						<span class="dks-listings__eyebrow"><?php echo $eyebrow; ?></span>
						<h2 class="dks-listings__title"><?php echo $title; ?></h2>
					</div>
					<a href="<?php echo $browse_url; ?>" class="dks-listings__browse">
						<?php echo $browse_txt; ?>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
					</a>
				</div>
				<div class="dks-listings__grid" style="--dks-cols:<?php echo $columns; ?>;">
					<?php foreach ( $items as $item ) : ?>
						<?php echo dks_render_property_card( $item ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Features ──────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_features_block' ) ) :
	function dks_render_features_block( $attributes ) {

		$eyebrow = esc_html( $attributes['eyebrow'] ?? '' );
		$title   = wp_kses_post( $attributes['title']   ?? '' );
		$intro   = esc_html( $attributes['intro']   ?? '' );
		$items   = $attributes['items']   ?? [];

		ob_start();
		?>
		<section class="dks-features alignfull">
			<div class="dks-container">
				<?php if ( $eyebrow ) : ?>
					<span class="dks-features__eyebrow"><?php echo $eyebrow; ?></span>
				<?php endif; ?>
				<?php if ( $title ) : ?>
					<h2 class="dks-features__title"><?php echo $title; ?></h2>
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<p class="dks-features__intro"><?php echo $intro; ?></p>
				<?php endif; ?>
				<div class="dks-features__grid">
					<?php foreach ( $items as $item ) : ?>
						<div class="dks-feature-item">
							<div class="dks-feature-item__icon" aria-hidden="true">
								<?php echo dks_svg_icon( $item['icon'] ?? 'star' ); ?>
							</div>
							<h3 class="dks-feature-item__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
							<p class="dks-feature-item__desc"><?php echo esc_html( $item['desc'] ?? '' ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Newsletter ────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_newsletter_block' ) ) :
	function dks_render_newsletter_block( $attributes ) {

		$heading     = wp_kses_post( $attributes['heading']     ?? '' );
		$description = esc_html( $attributes['description'] ?? '' );
		$button_text = esc_html( $attributes['buttonText']  ?? __( 'Subscribe', 'dks-theme' ) );

		ob_start();
		?>
		<section class="dks-newsletter dks-section">
			<div class="dks-container">
				<div class="dks-newsletter__inner">
					<div>
						<?php if ( $heading ) : ?>
							<h2 class="dks-newsletter__heading"><?php echo $heading; ?></h2>
						<?php endif; ?>
						<?php if ( $description ) : ?>
							<p class="dks-newsletter__text"><?php echo $description; ?></p>
						<?php endif; ?>
					</div>
					<?php
					/**
					 * Filter to swap in a real newsletter form plugin output.
					 *   add_filter( 'dks_newsletter_form_html', function( $html, $atts ) {
					 *       return do_shortcode( '[mailchimp_form]' );
					 *   }, 10, 2 );
					 */
					$form_html = apply_filters( 'dks_newsletter_form_html', '', $attributes );
					if ( $form_html ) {
						echo $form_html;
					} else {
					?>
					<form class="dks-newsletter__form" method="post" novalidate>
						<?php wp_nonce_field( 'dks_newsletter_subscribe', 'dks_newsletter_nonce' ); ?>
						<label for="dks-email" class="screen-reader-text"><?php esc_html_e( 'Email address', 'dks-theme' ); ?></label>
						<input
							id="dks-email"
							class="dks-newsletter__input"
							type="email"
							name="dks_email"
							placeholder="<?php esc_attr_e( 'Email Address', 'dks-theme' ); ?>"
							required
						/>
						<button type="submit" class="dks-newsletter__submit">
							<?php echo $button_text; ?>
						</button>
					</form>
					<?php } ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;


// ══════════════════════════════════════════════════════════════════════════════
// HELPER FUNCTIONS
// ══════════════════════════════════════════════════════════════════════════════

// ── Text + Image ──────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_text_image_block' ) ) :
	function dks_render_text_image_block( $attributes ) {
		$eyebrow   = esc_html( $attributes['eyebrow']  ?? '' );
		$heading   = wp_kses_post( $attributes['heading'] ?? '' );
		$text      = esc_html( $attributes['text']     ?? '' );
		$btn_text  = esc_html( $attributes['btnText']  ?? '' );
		$btn_url   = esc_url(  $attributes['btnUrl']   ?? '#' );
		$image_url = esc_url(  $attributes['imageUrl'] ?? '' );
		$image_alt = esc_attr( $attributes['imageAlt'] ?? '' );
		$reversed  = ! empty( $attributes['reversed'] );

		ob_start();
		?>
		<section class="dks-text-image dks-section<?php echo $reversed ? ' dks-text-image--reversed' : ''; ?>">
			<div class="dks-container">
				<div class="dks-text-image__grid">
					<div class="dks-text-image__content">
						<?php if ( $eyebrow ) : ?><span class="dks-eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
						<?php if ( $heading ) : ?><h2 class="dks-text-image__heading"><?php echo $heading; ?></h2><?php endif; ?>
						<?php if ( $text )    : ?><p class="dks-text-image__text"><?php echo $text; ?></p><?php endif; ?>
						<?php if ( $btn_text ) : ?>
							<a href="<?php echo $btn_url; ?>" class="dks-btn dks-btn--primary"><?php echo $btn_text; ?></a>
						<?php endif; ?>
					</div>
					<div class="dks-text-image__media">
						<?php if ( $image_url ) : ?>
							<img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" class="dks-text-image__img" loading="lazy" />
						<?php else : ?>
							<div class="dks-text-image__img-placeholder">
								<span><?php esc_html_e( 'Select image in editor', 'dks-theme' ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Steps ─────────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_steps_block' ) ) :
	function dks_render_steps_block( $attributes ) {
		$eyebrow = esc_html( $attributes['eyebrow'] ?? '' );
		$heading = wp_kses_post( $attributes['heading'] ?? '' );
		$intro   = esc_html( $attributes['intro']   ?? '' );
		$steps   = $attributes['steps'] ?? [];

		ob_start();
		?>
		<section class="dks-steps dks-section">
			<div class="dks-container">
				<div class="dks-section-header">
					<?php if ( $eyebrow ) : ?><span class="dks-eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
					<?php if ( $heading ) : ?><h2 class="dks-section-heading"><?php echo $heading; ?></h2><?php endif; ?>
					<?php if ( $intro )   : ?><p class="dks-section-intro"><?php echo $intro; ?></p><?php endif; ?>
				</div>
				<div class="dks-steps__grid">
					<?php foreach ( $steps as $i => $step ) : ?>
						<div class="dks-step">
							<div class="dks-step__number" aria-hidden="true"><?php echo sprintf( '%02d', $i + 1 ); ?></div>
							<div class="dks-step__body">
								<h3 class="dks-step__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
								<p class="dks-step__desc"><?php echo esc_html( $step['desc'] ?? '' ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Testimonials ──────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_testimonials_block' ) ) :
	function dks_render_testimonials_block( $attributes ) {
		$eyebrow = esc_html( $attributes['eyebrow'] ?? '' );
		$heading = wp_kses_post( $attributes['heading'] ?? '' );
		$items   = $attributes['items'] ?? [];

		ob_start();
		?>
		<section class="dks-testimonials dks-section">
			<div class="dks-container">
				<div class="dks-section-header">
					<?php if ( $eyebrow ) : ?><span class="dks-eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
					<?php if ( $heading ) : ?><h2 class="dks-section-heading"><?php echo $heading; ?></h2><?php endif; ?>
				</div>
				<div class="dks-testimonials__grid">
					<?php foreach ( $items as $item ) : ?>
						<div class="dks-testimonial">
							<?php if ( ! empty( $item['rating'] ) ) : ?>
								<div class="dks-testimonial__rating" aria-label="<?php echo (int) $item['rating']; ?> sterren">
									<?php for ( $s = 0; $s < 5; $s++ ) : ?>
										<span class="dks-star<?php echo $s < (int) $item['rating'] ? ' dks-star--filled' : ''; ?>" aria-hidden="true">&#9733;</span>
									<?php endfor; ?>
								</div>
							<?php endif; ?>
							<blockquote class="dks-testimonial__quote"><?php echo wp_kses_post( $item['quote'] ?? '' ); ?></blockquote>
							<div class="dks-testimonial__author">
								<?php if ( ! empty( $item['imageUrl'] ) ) : ?>
									<img src="<?php echo esc_url( $item['imageUrl'] ); ?>" alt="<?php echo esc_attr( $item['name'] ?? '' ); ?>" class="dks-testimonial__avatar" loading="lazy" />
								<?php endif; ?>
								<div class="dks-testimonial__author-info">
									<span class="dks-testimonial__name"><?php echo esc_html( $item['name'] ?? '' ); ?></span>
									<?php if ( ! empty( $item['role'] ) ) : ?>
										<span class="dks-testimonial__role"><?php echo esc_html( $item['role'] ); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Logos ─────────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_logos_block' ) ) :
	function dks_render_logos_block( $attributes ) {
		$heading = esc_html( $attributes['heading'] ?? '' );
		$logos   = $attributes['logos'] ?? [];

		ob_start();
		?>
		<section class="dks-logos">
			<div class="dks-container">
				<?php if ( $heading ) : ?><p class="dks-logos__heading"><?php echo $heading; ?></p><?php endif; ?>
				<?php if ( ! empty( $logos ) ) : ?>
					<div class="dks-logos__grid">
						<?php foreach ( $logos as $logo ) :
							$img  = esc_url( $logo['imageUrl'] ?? '' );
							$alt  = esc_attr( $logo['imageAlt'] ?? '' );
							$link = esc_url( $logo['link']     ?? '' );
							if ( ! $img ) continue;
						?>
							<div class="dks-logos__item">
								<?php if ( $link ) : ?>
									<a href="<?php echo $link; ?>" target="_blank" rel="noopener noreferrer">
										<img src="<?php echo $img; ?>" alt="<?php echo $alt; ?>" loading="lazy" />
									</a>
								<?php else : ?>
									<img src="<?php echo $img; ?>" alt="<?php echo $alt; ?>" loading="lazy" />
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="dks-logos__placeholder"><?php esc_html_e( 'Voeg logo\'s toe in de editor.', 'dks-theme' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Process ───────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_process_block' ) ) :
	function dks_render_process_block( $attributes ) {
		$eyebrow = esc_html( $attributes['eyebrow'] ?? '' );
		$heading = wp_kses_post( $attributes['heading'] ?? '' );
		$intro   = esc_html( $attributes['intro']   ?? '' );
		$steps   = $attributes['steps'] ?? [];

		ob_start();
		?>
		<section class="dks-process dks-section">
			<div class="dks-container">
				<div class="dks-section-header">
					<?php if ( $eyebrow ) : ?><span class="dks-eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
					<?php if ( $heading ) : ?><h2 class="dks-section-heading"><?php echo $heading; ?></h2><?php endif; ?>
					<?php if ( $intro )   : ?><p class="dks-section-intro"><?php echo $intro; ?></p><?php endif; ?>
				</div>
				<div class="dks-process__steps">
					<?php foreach ( $steps as $i => $step ) : ?>
						<div class="dks-process-step">
							<div class="dks-process-step__marker">
								<span class="dks-process-step__num"><?php echo $i + 1; ?></span>
								<?php if ( $i < count( $steps ) - 1 ) : ?>
									<span class="dks-process-step__line" aria-hidden="true"></span>
								<?php endif; ?>
							</div>
							<div class="dks-process-step__body">
								<h3 class="dks-process-step__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
								<p class="dks-process-step__desc"><?php echo esc_html( $step['desc'] ?? '' ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── CTA ───────────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_cta_block' ) ) :
	function dks_render_cta_block( $attributes ) {
		$eyebrow    = esc_html( $attributes['eyebrow']        ?? '' );
		$heading    = wp_kses_post( $attributes['heading']    ?? '' );
		$subheading = esc_html( $attributes['subheading']     ?? '' );
		$btn1_text  = esc_html( $attributes['btnPrimaryText'] ?? '' );
		$btn1_url   = esc_url(  $attributes['btnPrimaryUrl']  ?? '#' );
		$btn2_text  = esc_html( $attributes['btnSecondText']  ?? '' );
		$btn2_url   = esc_url(  $attributes['btnSecondUrl']   ?? '#' );
		$variant    = in_array( $attributes['variant'] ?? '', [ 'dark', 'accent', 'light' ], true )
			? $attributes['variant'] : 'dark';

		ob_start();
		?>
		<section class="dks-cta dks-cta--<?php echo esc_attr( $variant ); ?> dks-section">
			<div class="dks-container">
				<div class="dks-cta__inner">
					<?php if ( $eyebrow ) : ?><span class="dks-eyebrow dks-cta__eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
					<?php if ( $heading ) : ?><h2 class="dks-cta__heading"><?php echo $heading; ?></h2><?php endif; ?>
					<?php if ( $subheading ) : ?><p class="dks-cta__subheading"><?php echo $subheading; ?></p><?php endif; ?>
					<?php if ( $btn1_text || $btn2_text ) : ?>
						<div class="dks-cta__actions">
							<?php if ( $btn1_text ) : ?>
								<a href="<?php echo $btn1_url; ?>" class="dks-btn dks-btn--primary"><?php echo $btn1_text; ?></a>
							<?php endif; ?>
							<?php if ( $btn2_text ) : ?>
								<a href="<?php echo $btn2_url; ?>" class="dks-btn dks-btn--outline"><?php echo $btn2_text; ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── Bonus Benefits ────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_bonus_block' ) ) :
	function dks_render_bonus_block( $attributes ) {
		$eyebrow = esc_html( $attributes['eyebrow'] ?? '' );
		$heading = wp_kses_post( $attributes['heading'] ?? '' );
		$intro   = esc_html( $attributes['intro']   ?? '' );
		$items   = $attributes['items'] ?? [];

		ob_start();
		?>
		<section class="dks-bonus dks-section">
			<div class="dks-container">
				<div class="dks-section-header">
					<?php if ( $eyebrow ) : ?><span class="dks-eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
					<?php if ( $heading ) : ?><h2 class="dks-section-heading"><?php echo $heading; ?></h2><?php endif; ?>
					<?php if ( $intro )   : ?><p class="dks-section-intro"><?php echo $intro; ?></p><?php endif; ?>
				</div>
				<div class="dks-bonus__grid">
					<?php foreach ( $items as $item ) : ?>
						<div class="dks-bonus-item">
							<div class="dks-bonus-item__icon" aria-hidden="true">
								<?php echo dks_svg_icon( $item['icon'] ?? 'star' ); ?>
							</div>
							<h3 class="dks-bonus-item__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
							<p class="dks-bonus-item__desc"><?php echo esc_html( $item['desc'] ?? '' ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;

// ── FAQ ───────────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_faq_block' ) ) :
	function dks_render_faq_block( $attributes ) {
		$eyebrow = esc_html( $attributes['eyebrow'] ?? '' );
		$heading = wp_kses_post( $attributes['heading'] ?? '' );
		$intro   = esc_html( $attributes['intro']   ?? '' );
		$items   = $attributes['items'] ?? [];

		ob_start();
		?>
		<section class="dks-faq dks-section">
			<div class="dks-container">
				<div class="dks-section-header">
					<?php if ( $eyebrow ) : ?><span class="dks-eyebrow"><?php echo $eyebrow; ?></span><?php endif; ?>
					<?php if ( $heading ) : ?><h2 class="dks-section-heading"><?php echo $heading; ?></h2><?php endif; ?>
					<?php if ( $intro )   : ?><p class="dks-section-intro"><?php echo $intro; ?></p><?php endif; ?>
				</div>
				<div class="dks-faq__list">
					<?php foreach ( $items as $i => $item ) :
						$btn_id = 'dks-faq-' . $i . '-btn';
						$ans_id = 'dks-faq-' . $i;
					?>
						<div class="dks-faq__item">
							<button
								class="dks-faq__question"
								aria-expanded="false"
								aria-controls="<?php echo esc_attr( $ans_id ); ?>"
								id="<?php echo esc_attr( $btn_id ); ?>"
							>
								<?php echo esc_html( $item['question'] ?? '' ); ?>
								<span class="dks-faq__icon" aria-hidden="true"></span>
							</button>
							<div
								class="dks-faq__answer"
								id="<?php echo esc_attr( $ans_id ); ?>"
								role="region"
								aria-labelledby="<?php echo esc_attr( $btn_id ); ?>"
								hidden
							>
								<div class="dks-faq__answer-inner">
									<?php echo wp_kses_post( $item['answer'] ?? '' ); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
endif;


// ══════════════════════════════════════════════════════════════════════════════
// HELPER FUNCTIONS (shared across all blocks)
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Returns the default placeholder listings data.
 * Child themes can replace the entire set via the filter above.
 */
if ( ! function_exists( 'dks_get_placeholder_listings' ) ) :
	function dks_get_placeholder_listings() {
		return [
			[
				'image'     => DKS_THEME_URI . '/assets/images/property-placeholder-1.jpg',
				'badge'     => __( 'Featured', 'dks-theme' ),
				'price'     => '€1,450,000',
				'location'  => __( 'Amsterdam, Prinsengracht District', 'dks-theme' ),
				'beds'      => 4,
				'baths'     => 3,
				'sqm'       => '185m²',
				'permalink' => '#',
			],
			[
				'image'     => DKS_THEME_URI . '/assets/images/property-placeholder-2.jpg',
				'badge'     => '',
				'price'     => '€985,000',
				'location'  => __( 'Utrecht, Historical Center', 'dks-theme' ),
				'beds'      => 3,
				'baths'     => 2,
				'sqm'       => '142m²',
				'permalink' => '#',
			],
			[
				'image'     => DKS_THEME_URI . '/assets/images/property-placeholder-3.jpg',
				'badge'     => '',
				'price'     => '€1,200,000',
				'location'  => __( 'Rotterdam, Kop van Zuid', 'dks-theme' ),
				'beds'      => 3,
				'baths'     => 2,
				'sqm'       => '160m²',
				'permalink' => '#',
			],
		];
	}
endif;

/**
 * Renders a single property card.
 * Can be called directly from templates or child themes.
 *
 * @param array $item Property data array.
 * @return string HTML.
 */
if ( ! function_exists( 'dks_render_property_card' ) ) :
	function dks_render_property_card( $item ) {
		$image     = esc_url( $item['image']    ?? '' );
		$badge     = esc_html( $item['badge']    ?? '' );
		$price     = esc_html( $item['price']    ?? '' );
		$location  = esc_html( $item['location'] ?? '' );
		$beds      = absint( $item['beds']     ?? 0 );
		$baths     = absint( $item['baths']    ?? 0 );
		$sqm       = esc_html( $item['sqm']      ?? '' );
		$permalink = esc_url( $item['permalink'] ?? '#' );

		ob_start();
		?>
		<article class="dks-property-card">
			<a href="<?php echo $permalink; ?>" class="dks-property-card__image-wrap" aria-label="<?php echo esc_attr( sprintf( __( 'View listing: %s', 'dks-theme' ), $price ) ); ?>">
				<?php if ( $image ) : ?>
					<img
						class="dks-property-card__image"
						src="<?php echo $image; ?>"
						alt="<?php echo esc_attr( $location ); ?>"
						loading="lazy"
					/>
				<?php endif; ?>
				<?php if ( $badge ) : ?>
					<span class="dks-property-card__badge"><?php echo $badge; ?></span>
				<?php endif; ?>
			</a>
			<div class="dks-property-card__info">
				<h3 class="dks-property-card__price">
					<a href="<?php echo $permalink; ?>"><?php echo $price; ?></a>
				</h3>
				<?php if ( $location ) : ?>
					<p class="dks-property-card__location">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.979-5.121 3.979-8.827a8.25 8.25 0 00-16.5 0c0 3.706 2.035 6.744 3.979 8.827a19.58 19.58 0 002.686 2.282 16.975 16.975 0 001.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
						<?php echo $location; ?>
					</p>
				<?php endif; ?>
				<div class="dks-property-card__meta">
					<?php if ( $beds ) : ?>
						<div class="dks-property-card__meta-item">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
							<?php echo sprintf( _n( '%d Bed', '%d Beds', $beds, 'dks-theme' ), $beds ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $baths ) : ?>
						<div class="dks-property-card__meta-item">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a6 6 0 0112 0v1H6v-1z"/></svg>
							<?php echo sprintf( _n( '%d Bath', '%d Baths', $baths, 'dks-theme' ), $baths ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $sqm ) : ?>
						<div class="dks-property-card__meta-item">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
							<?php echo $sqm; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
		return ob_get_clean();
	}
endif;

/**
 * Returns inline SVG for common icon slugs.
 * Extendable via the 'dks_svg_icon' filter.
 *
 * @param string $icon Icon slug.
 * @return string SVG HTML.
 */
if ( ! function_exists( 'dks_svg_icon' ) ) :
	function dks_svg_icon( $icon ) {
		$icons = [
			'home'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>',
			'handshake' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>',
			'verified'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>',
			'star'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>',
		];
		$svg = $icons[ $icon ] ?? $icons['star'];
		return apply_filters( 'dks_svg_icon', $svg, $icon );
	}
endif;
