<?php
/**
 * Template tags — reusable helpers used in template files.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Posted-on / author / categories ──────────────────────────────────────

if ( ! function_exists( 'dks_posted_on' ) ) :
	function dks_posted_on() {
		$time = sprintf(
			'<time class="entry-date published" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);
		echo '<span class="posted-on">' . $time . '</span>';
	}
endif;

if ( ! function_exists( 'dks_posted_by' ) ) :
	function dks_posted_by() {
		echo '<span class="byline">' .
			sprintf(
				/* translators: %s: author display name */
				esc_html_x( 'by %s', 'post author', 'dks-theme' ),
				'<span class="author vcard"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' .
				esc_html( get_the_author() ) . '</a></span>'
			) .
		'</span>';
	}
endif;

if ( ! function_exists( 'dks_entry_footer' ) ) :
	function dks_entry_footer() {
		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'dks-theme' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged: %1$s', 'dks-theme' ) . '</span>', $tags_list );
		}
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'dks-theme' ),
					[ 'span' => [ 'class' => [] ] ]
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

// ── Thumbnail with fallback ────────────────────────────────────────────────
if ( ! function_exists( 'dks_post_thumbnail' ) ) :
	function dks_post_thumbnail( $size = 'post-thumbnail', $attr = [] ) {
		if ( post_password_required() || ! has_post_thumbnail() ) {
			return;
		}
		echo '<div class="post-thumbnail">';
		the_post_thumbnail( $size, $attr );
		echo '</div>';
	}
endif;

// ── Pagination ────────────────────────────────────────────────────────────
if ( ! function_exists( 'dks_numeric_posts_nav' ) ) :
	function dks_numeric_posts_nav() {
		the_posts_pagination( [
			'mid_size'  => 2,
			'prev_text' => __( '&laquo; Previous', 'dks-theme' ),
			'next_text' => __( 'Next &raquo;', 'dks-theme' ),
		] );
	}
endif;

// ── Comment callback ──────────────────────────────────────────────────────
if ( ! function_exists( 'dks_comment' ) ) :
	function dks_comment( $comment, $args, $depth ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
		?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'dks-comment', $comment ); ?> style="padding:1.5rem 0;border-bottom:1px solid rgba(26,26,26,.08);">
			<div style="display:flex;gap:1rem;align-items:flex-start;">
				<div style="flex-shrink:0;">
					<?php echo get_avatar( $comment, 48, '', '', [ 'style' => 'border-radius:50%;display:block;' ] ); ?>
				</div>
				<div style="flex:1;min-width:0;">
					<div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:rgba(26,26,26,.5);margin-bottom:.4rem;">
						<?php comment_author_link( $comment ); ?>
						&mdash;
						<time datetime="<?php comment_time( 'c' ); ?>"><?php comment_date(); ?></time>
						<?php edit_comment_link( __( '(bewerk)', 'dks-theme' ), ' ', '' ); ?>
					</div>
					<?php if ( '0' === $comment->comment_approved ) : ?>
						<p style="font-size:.75rem;color:rgba(26,26,26,.4);font-style:italic;"><?php esc_html_e( 'Je reactie wacht op goedkeuring.', 'dks-theme' ); ?></p>
					<?php endif; ?>
					<div class="comment-content entry-content" style="padding-inline:0;">
						<?php comment_text(); ?>
					</div>
					<?php
					comment_reply_link( array_merge( $args, [
						'add_below' => 'comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div style="margin-top:.5rem;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;">',
						'after'     => '</div>',
					] ) );
					?>
				</div>
			</div>
		</<?php echo $tag; ?>>
		<?php
	}
endif;

// ── Breadcrumbs (simple, no external dependency) ──────────────────────────
if ( ! function_exists( 'dks_breadcrumbs' ) ) :
	function dks_breadcrumbs() {
		if ( is_front_page() ) return;

		echo '<nav class="dks-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'dks-theme' ) . '">';
		echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'dks-theme' ) . '</a>';

		if ( is_singular() ) {
			echo '<span aria-hidden="true"> / </span>';
			echo '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
		} elseif ( is_archive() ) {
			echo '<span aria-hidden="true"> / </span>';
			echo '<span aria-current="page">' . esc_html( get_the_archive_title() ) . '</span>';
		}

		echo '</nav>';
	}
endif;
