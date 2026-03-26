<?php
/**
 * Custom meta boxes for property data.
 *
 * Registers a meta box on the 'page' post type (and optionally on a
 * 'property' CPT if a child theme registers one).
 * All fields are stored as post meta and are fully editable in the
 * WordPress admin without Gutenberg.
 *
 * Fields:
 *   _dks_price       — Asking price (free text, e.g. "€1,450,000")
 *   _dks_location    — Address / district
 *   _dks_beds        — Number of bedrooms
 *   _dks_baths       — Number of bathrooms
 *   _dks_sqm         — Floor area (e.g. "185m²")
 *   _dks_badge       — Optional badge label (e.g. "Featured")
 *   _dks_status      — Listing status: available | sold | rented
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Register meta keys for the REST API / block editor ────────────────────
if ( ! function_exists( 'dks_register_property_meta' ) ) :
	function dks_register_property_meta() {

		$meta_fields = [
			'_dks_price'    => 'string',
			'_dks_location' => 'string',
			'_dks_beds'     => 'integer',
			'_dks_baths'    => 'integer',
			'_dks_sqm'      => 'string',
			'_dks_badge'    => 'string',
			'_dks_status'   => 'string',
		];

		// Post types that use property meta. Child themes can add CPTs via the filter.
		$post_types = apply_filters( 'dks_property_post_types', [ 'page', 'post' ] );

		foreach ( $post_types as $post_type ) {
			foreach ( $meta_fields as $key => $type ) {
				register_post_meta( $post_type, $key, [
					'type'         => $type,
					'description'  => sprintf( __( 'DKS property field: %s', 'dks-theme' ), ltrim( $key, '_dks_' ) ),
					'single'       => true,
					'show_in_rest' => true, // Exposes to Gutenberg sidebar
					'auth_callback'=> function() {
						return current_user_can( 'edit_posts' );
					},
				] );
			}
		}
	}
endif;
add_action( 'init', 'dks_register_property_meta' );

// ── Add meta box ──────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_add_property_meta_box' ) ) :
	function dks_add_property_meta_box() {

		$post_types = apply_filters( 'dks_property_post_types', [ 'page', 'post' ] );

		add_meta_box(
			'dks_property_details',
			__( 'DKS Property Details', 'dks-theme' ),
			'dks_render_property_meta_box',
			$post_types,
			'side',
			'high'
		);
	}
endif;
add_action( 'add_meta_boxes', 'dks_add_property_meta_box' );

// ── Render meta box HTML ──────────────────────────────────────────────────
if ( ! function_exists( 'dks_render_property_meta_box' ) ) :
	function dks_render_property_meta_box( $post ) {

		wp_nonce_field( 'dks_save_property_meta', 'dks_property_nonce' );

		$price    = get_post_meta( $post->ID, '_dks_price',    true );
		$location = get_post_meta( $post->ID, '_dks_location', true );
		$beds     = get_post_meta( $post->ID, '_dks_beds',     true );
		$baths    = get_post_meta( $post->ID, '_dks_baths',    true );
		$sqm      = get_post_meta( $post->ID, '_dks_sqm',      true );
		$badge    = get_post_meta( $post->ID, '_dks_badge',    true );
		$status   = get_post_meta( $post->ID, '_dks_status',   true );

		?>
		<style>
			.dks-meta-row { margin-bottom: 12px; }
			.dks-meta-row label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; color: #50575e; }
			.dks-meta-row input, .dks-meta-row select { width: 100%; }
		</style>

		<div class="dks-meta-row">
			<label for="dks_price"><?php esc_html_e( 'Price', 'dks-theme' ); ?></label>
			<input type="text" id="dks_price" name="dks_price"
				value="<?php echo esc_attr( $price ); ?>"
				placeholder="€1,450,000" />
		</div>

		<div class="dks-meta-row">
			<label for="dks_location"><?php esc_html_e( 'Location', 'dks-theme' ); ?></label>
			<input type="text" id="dks_location" name="dks_location"
				value="<?php echo esc_attr( $location ); ?>"
				placeholder="<?php esc_attr_e( 'Amsterdam, Prinsengracht District', 'dks-theme' ); ?>" />
		</div>

		<div class="dks-meta-row">
			<label for="dks_beds"><?php esc_html_e( 'Bedrooms', 'dks-theme' ); ?></label>
			<input type="number" id="dks_beds" name="dks_beds"
				value="<?php echo esc_attr( $beds ); ?>"
				min="0" placeholder="4" />
		</div>

		<div class="dks-meta-row">
			<label for="dks_baths"><?php esc_html_e( 'Bathrooms', 'dks-theme' ); ?></label>
			<input type="number" id="dks_baths" name="dks_baths"
				value="<?php echo esc_attr( $baths ); ?>"
				min="0" placeholder="3" />
		</div>

		<div class="dks-meta-row">
			<label for="dks_sqm"><?php esc_html_e( 'Floor Area', 'dks-theme' ); ?></label>
			<input type="text" id="dks_sqm" name="dks_sqm"
				value="<?php echo esc_attr( $sqm ); ?>"
				placeholder="185m²" />
		</div>

		<div class="dks-meta-row">
			<label for="dks_badge"><?php esc_html_e( 'Badge Label', 'dks-theme' ); ?></label>
			<input type="text" id="dks_badge" name="dks_badge"
				value="<?php echo esc_attr( $badge ); ?>"
				placeholder="<?php esc_attr_e( 'Featured', 'dks-theme' ); ?>" />
		</div>

		<div class="dks-meta-row">
			<label for="dks_status"><?php esc_html_e( 'Status', 'dks-theme' ); ?></label>
			<select id="dks_status" name="dks_status">
				<option value="available" <?php selected( $status, 'available' ); ?>><?php esc_html_e( 'Available', 'dks-theme' ); ?></option>
				<option value="sold"      <?php selected( $status, 'sold'      ); ?>><?php esc_html_e( 'Sold',      'dks-theme' ); ?></option>
				<option value="rented"   <?php selected( $status, 'rented'    ); ?>><?php esc_html_e( 'Rented',    'dks-theme' ); ?></option>
			</select>
		</div>
		<?php
	}
endif;

// ── Save meta ─────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_save_property_meta' ) ) :
	function dks_save_property_meta( $post_id ) {

		// Verify nonce
		if ( ! isset( $_POST['dks_property_nonce'] )
			|| ! wp_verify_nonce( $_POST['dks_property_nonce'], 'dks_save_property_meta' ) ) {
			return;
		}

		// Don't save on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = [
			'dks_price'    => '_dks_price',
			'dks_location' => '_dks_location',
			'dks_beds'     => '_dks_beds',
			'dks_baths'    => '_dks_baths',
			'dks_sqm'      => '_dks_sqm',
			'dks_badge'    => '_dks_badge',
			'dks_status'   => '_dks_status',
		];

		foreach ( $fields as $post_key => $meta_key ) {
			if ( array_key_exists( $post_key, $_POST ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) );
				update_post_meta( $post_id, $meta_key, $value );
			}
		}

		// Numeric fields — ensure integers
		foreach ( [ 'dks_beds' => '_dks_beds', 'dks_baths' => '_dks_baths' ] as $pk => $mk ) {
			if ( isset( $_POST[ $pk ] ) ) {
				update_post_meta( $post_id, $mk, absint( $_POST[ $pk ] ) );
			}
		}
	}
endif;
add_action( 'save_post', 'dks_save_property_meta' );

// ── Contact page: shortcode meta box ──────────────────────────────────────
if ( ! function_exists( 'dks_add_contact_shortcode_meta_box' ) ) :
	/**
	 * Register the meta box only when the Contact page template is selected.
	 * The check runs on every edit screen load; switching templates requires
	 * saving once before the box appears / disappears.
	 */
	function dks_add_contact_shortcode_meta_box( string $post_type, WP_Post $post ): void {
		if ( 'page' !== $post_type ) {
			return;
		}

		// Show on the contact template, or on unsaved new pages (template not set yet).
		$template = get_page_template_slug( $post );
		if ( $template && 'page-contact.php' !== $template ) {
			return;
		}

		add_meta_box(
			'dks_contact_shortcode',
			__( 'Contact Form Shortcode', 'dks-theme' ),
			'dks_render_contact_shortcode_meta_box',
			'page',
			'normal',
			'high'
		);
	}
endif;
add_action( 'add_meta_boxes', 'dks_add_contact_shortcode_meta_box', 10, 2 );

if ( ! function_exists( 'dks_render_contact_shortcode_meta_box' ) ) :
	function dks_render_contact_shortcode_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'dks_save_contact_shortcode', 'dks_contact_shortcode_nonce' );
		$shortcode = get_post_meta( $post->ID, '_dks_contact_shortcode', true );
		?>
		<p style="margin:0 0 8px;font-size:12px;color:#50575e;">
			<?php esc_html_e( 'Voer hier een shortcode in, bijv. van Contact Form 7. Deze shortcode wordt in de formulierkolom van de contactpagina gerenderd.', 'dks-theme' ); ?>
		</p>
		<input
			type="text"
			id="dks_contact_shortcode"
			name="dks_contact_shortcode"
			value="<?php echo esc_attr( $shortcode ); ?>"
			placeholder="[contact-form-7 id=&quot;123&quot; title=&quot;Contact&quot;]"
			style="width:100%;font-family:monospace;font-size:13px;"
		>
		<?php
	}
endif;

if ( ! function_exists( 'dks_save_contact_shortcode' ) ) :
	function dks_save_contact_shortcode( int $post_id ): void {
		if ( ! isset( $_POST['dks_contact_shortcode_nonce'] )
			|| ! wp_verify_nonce( $_POST['dks_contact_shortcode_nonce'], 'dks_save_contact_shortcode' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( array_key_exists( 'dks_contact_shortcode', $_POST ) ) {
			// Strip tags but allow [ ] chars for shortcode syntax.
			$value = wp_kses( wp_unslash( $_POST['dks_contact_shortcode'] ), [] );
			update_post_meta( $post_id, '_dks_contact_shortcode', $value );
		}
	}
endif;
add_action( 'save_post_page', 'dks_save_contact_shortcode' );
