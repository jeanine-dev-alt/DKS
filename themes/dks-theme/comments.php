<?php
/**
 * comments.php — Comment list and form template.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="dks-comments dks-container" style="padding-top:3rem;padding-bottom:4rem;">

	<?php if ( have_comments() ) : ?>

		<h2 class="dks-comments__title" style="font-size:clamp(1.25rem,3vw,1.75rem);font-weight:800;letter-spacing:-0.02em;margin-bottom:2rem;">
			<?php
			printf(
				esc_html(
					_nx(
						'%1$s reactie op &ldquo;%2$s&rdquo;',
						'%1$s reacties op &ldquo;%2$s&rdquo;',
						get_comments_number(),
						'comments title',
						'dks-theme'
					)
				),
				number_format_i18n( get_comments_number() ),
				get_the_title()
			);
			?>
		</h2>

		<ol class="dks-comment-list" style="list-style:none;padding:0;margin:0 0 3rem;">
			<?php
			wp_list_comments( [
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 48,
				'callback'   => 'dks_comment',
			] );
			?>
		</ol>

		<?php the_comments_pagination( [
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
		] ); ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p style="font-size:.75rem;text-transform:uppercase;letter-spacing:.15em;color:rgba(26,26,26,.4);">
			<?php esc_html_e( 'Reacties zijn gesloten.', 'dks-theme' ); ?>
		</p>
	<?php endif; ?>

	<?php
	comment_form( [
		'title_reply'          => __( 'Laat een reactie achter', 'dks-theme' ),
		'title_reply_to'       => __( 'Reageer op %s', 'dks-theme' ),
		'cancel_reply_link'    => __( 'Annuleren', 'dks-theme' ),
		'label_submit'         => __( 'Reactie plaatsen', 'dks-theme' ),
		'class_submit'         => 'dks-btn dks-btn--primary',
		'comment_field'        => '<p><label for="comment" style="display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.5rem;">' . __( 'Reactie', 'dks-theme' ) . '</label><textarea id="comment" name="comment" cols="45" rows="6" required style="width:100%;padding:.75rem;border:1px solid rgba(26,26,26,.15);font-family:inherit;font-size:1rem;resize:vertical;"></textarea></p>',
		'fields'               => [
			'author' => '<p><label for="author" style="display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.5rem;">' . __( 'Naam', 'dks-theme' ) . ' <span>*</span></label><input id="author" name="author" type="text" required style="width:100%;padding:.75rem;border:1px solid rgba(26,26,26,.15);font-family:inherit;font-size:1rem;"></p>',
			'email'  => '<p><label for="email" style="display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.5rem;">' . __( 'E-mail', 'dks-theme' ) . ' <span>*</span></label><input id="email" name="email" type="email" required style="width:100%;padding:.75rem;border:1px solid rgba(26,26,26,.15);font-family:inherit;font-size:1rem;"></p>',
			'url'    => '',
		],
	] );
	?>

</div>
