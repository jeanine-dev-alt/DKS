/**
 * main.js — DKS Real Estate Theme
 *
 * Front-end JavaScript:
 *   - Mobile menu toggle
 *   - Sub-menu keyboard navigation
 *   - Hero parallax (subtle, respects prefers-reduced-motion)
 *   - Scroll-triggered fade-in for listing cards
 *   - Newsletter form AJAX submission
 *   - Sticky header shadow on scroll
 *
 * No build step required — vanilla ES2020, no dependencies.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

( function () {
	'use strict';

	/* ────────────────────────────────────────────────────────────────────
	 * 1. DOM-ready helper
	 * ──────────────────────────────────────────────────────────────────── */
	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 2. Mobile menu toggle
	 * ──────────────────────────────────────────────────────────────────── */
	function initMobileMenu() {
		const toggle = document.getElementById( 'menu-toggle' );
		const nav    = document.getElementById( 'site-navigation' );

		if ( ! toggle || ! nav ) return;

		toggle.addEventListener( 'click', function () {
			const isOpen = nav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', isOpen );

			// Update label from localised strings if available
			if ( window.dksTheme && window.dksTheme.i18n ) {
				toggle.setAttribute(
					'aria-label',
					isOpen ? dksTheme.i18n.menuClose : dksTheme.i18n.menuOpen
				);
			}
		} );

		// Close on outside click
		document.addEventListener( 'click', function ( e ) {
			if ( nav.classList.contains( 'is-open' ) &&
			     ! nav.contains( e.target ) &&
			     ! toggle.contains( e.target ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );

		// Close on ESC
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && nav.classList.contains( 'is-open' ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.focus();
			}
		} );
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 3. Sub-menu click toggle on mobile (touch-friendly)
	 * ──────────────────────────────────────────────────────────────────── */
	function initSubMenus() {
		const parents = document.querySelectorAll(
			'.primary-navigation .menu-item-has-children > a'
		);

		parents.forEach( function ( link ) {
			// On small screens (where hover doesn't work) toggle on click
			link.addEventListener( 'click', function ( e ) {
				if ( window.innerWidth <= 1024 ) {
					e.preventDefault();
					const parent = link.parentElement;
					parent.classList.toggle( 'is-open' );
				}
			} );
		} );
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 4. Sticky header: add shadow on scroll
	 * ──────────────────────────────────────────────────────────────────── */
	function initStickyHeader() {
		const header = document.getElementById( 'masthead' );
		if ( ! header ) return;

		const onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 40 );
		};

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 5. Hero parallax (skips if prefers-reduced-motion)
	 * ──────────────────────────────────────────────────────────────────── */
	function initHeroParallax() {
		if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;

		const bg = document.querySelector( '.dks-hero__bg' );
		if ( ! bg ) return;

		window.addEventListener( 'scroll', function () {
			const scrollY = window.scrollY;
			bg.style.transform = 'translateY(' + scrollY * 0.25 + 'px)';
		}, { passive: true } );
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 6. Scroll-triggered fade-in for property cards and feature items
	 * ──────────────────────────────────────────────────────────────────── */
	function initRevealOnScroll() {
		if ( ! window.IntersectionObserver ) return;

		const targets = document.querySelectorAll(
			'.dks-property-card, .dks-feature-item, .dks-post-card'
		);

		if ( ! targets.length ) return;

		// Set initial hidden state
		targets.forEach( function ( el, i ) {
			el.style.opacity    = '0';
			el.style.transform  = 'translateY(24px)';
			el.style.transition = 'opacity 0.6s ease ' + ( i % 3 ) * 0.1 + 's, transform 0.6s ease ' + ( i % 3 ) * 0.1 + 's';
		} );

		const observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.style.opacity   = '1';
						entry.target.style.transform = 'translateY(0)';
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.12 }
		);

		targets.forEach( function ( el ) { observer.observe( el ); } );
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 7. Newsletter form — AJAX submit with nonce
	 * ──────────────────────────────────────────────────────────────────── */
	function initNewsletterForm() {
		const form = document.querySelector( '.dks-newsletter__form' );
		if ( ! form ) return;

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();

			const emailInput = form.querySelector( '[name="dks_email"]' );
			const btn        = form.querySelector( '.dks-newsletter__submit' );

			if ( ! emailInput || ! emailInput.value ) return;

			const originalText = btn.textContent;
			btn.textContent    = window.dksTheme
				? ( dksTheme.i18n.sending || 'Sending…' )
				: 'Sending…';
			btn.disabled = true;

			const data = new FormData();
			data.append( 'action', 'dks_newsletter_subscribe' );
			data.append( 'email', emailInput.value );
			data.append( 'nonce', form.querySelector( '[name="dks_newsletter_nonce"]' )?.value || '' );

			fetch( window.dksTheme ? dksTheme.ajaxUrl : '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: data,
			} )
			.then( function ( res ) { return res.json(); } )
			.then( function ( response ) {
				btn.disabled = false;
				if ( response.success ) {
					emailInput.value = '';
					btn.textContent  = window.dksTheme
						? ( dksTheme.i18n.subscribed || 'Subscribed!' )
						: 'Subscribed!';
					setTimeout( function () { btn.textContent = originalText; }, 3000 );
				} else {
					btn.textContent = window.dksTheme
						? ( dksTheme.i18n.error || 'Error — try again' )
						: 'Error — try again';
					setTimeout( function () { btn.textContent = originalText; }, 3000 );
				}
			} )
			.catch( function () {
				btn.disabled    = false;
				btn.textContent = originalText;
			} );
		} );
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 8. FAQ accordion — keyboard-accessible, one-open-at-a-time per list
	 * ──────────────────────────────────────────────────────────────────── */
	function initFaqAccordion() {
		const buttons = document.querySelectorAll( '.dks-faq__question' );
		if ( ! buttons.length ) return;

		buttons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				const expanded  = btn.getAttribute( 'aria-expanded' ) === 'true';
				const answerId  = btn.getAttribute( 'aria-controls' );
				const answer    = document.getElementById( answerId );
				const list      = btn.closest( '.dks-faq__list' );

				// Close every other open item in the same list
				if ( list ) {
					list.querySelectorAll( '.dks-faq__question[aria-expanded="true"]' )
						.forEach( function ( other ) {
							if ( other === btn ) return;
							other.setAttribute( 'aria-expanded', 'false' );
							var otherId = other.getAttribute( 'aria-controls' );
							var otherAnswer = document.getElementById( otherId );
							if ( otherAnswer ) otherAnswer.hidden = true;
						} );
				}

				// Toggle current
				btn.setAttribute( 'aria-expanded', ! expanded );
				if ( answer ) answer.hidden = expanded;
			} );
		} );
	}

	/* ────────────────────────────────────────────────────────────────────
	 * 9. Bootstrap all
	 * ──────────────────────────────────────────────────────────────────── */
	ready( function () {
		initMobileMenu();
		initSubMenus();
		initStickyHeader();
		initHeroParallax();
		initRevealOnScroll();
		initNewsletterForm();
		initFaqAccordion();
	} );

} )();
