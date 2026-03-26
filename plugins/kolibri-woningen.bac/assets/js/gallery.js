/**
 * Kolibri Woningen — Gallery Lightbox
 *
 * Features:
 *  - Main image + thumbnail strip click → open lightbox
 *  - Keyboard: ArrowLeft / ArrowRight / Escape
 *  - Touch swipe (>50px threshold)
 *  - Image preloading (next + prev)
 *  - Focus trap inside lightbox
 */
( function () {
	'use strict';

	// ── Data ──────────────────────────────────────────────────────────────────────

	const dataEl = document.getElementById( 'kolibri-gallery-data' );
	if ( ! dataEl ) return;

	let images;
	try {
		images = JSON.parse( dataEl.textContent );
	} catch ( e ) {
		return;
	}

	if ( ! images || ! images.length ) return;

	// ── Elements ──────────────────────────────────────────────────────────────────

	const lightbox   = document.getElementById( 'kolibri-lightbox' );
	const lbImg      = lightbox.querySelector( '.kolibri-lb-img' );
	const lbClose    = lightbox.querySelector( '.kolibri-lb-close' );
	const lbPrev     = lightbox.querySelector( '.kolibri-lb-prev' );
	const lbNext     = lightbox.querySelector( '.kolibri-lb-next' );
	const lbCurrent  = document.getElementById( 'kolibri-lb-current' );
	const lbTotal    = document.getElementById( 'kolibri-lb-total' );
	const openBtns   = document.querySelectorAll( '.kolibri-gallery-open, .kolibri-gallery-thumb' );

	if ( ! lightbox ) return;

	let current   = 0;
	let lastFocus = null;

	// ── Open / close ──────────────────────────────────────────────────────────────

	function open( index ) {
		current   = clamp( index, 0, images.length - 1 );
		lastFocus = document.activeElement;
		show( current );
		lightbox.hidden = false;
		document.body.style.overflow = 'hidden';
		lbClose.focus();
	}

	function close() {
		lightbox.hidden = true;
		document.body.style.overflow = '';
		lbImg.src = '';
		if ( lastFocus ) lastFocus.focus();
	}

	// ── Show image ────────────────────────────────────────────────────────────────

	function show( index ) {
		current = clamp( index, 0, images.length - 1 );
		const img = images[ current ];
		lbImg.src = img.src;
		lbImg.alt = img.alt || '';
		if ( lbCurrent ) lbCurrent.textContent = current + 1;
		if ( lbTotal )   lbTotal.textContent   = images.length;

		// Hide prev/next when at boundaries.
		lbPrev.hidden = images.length <= 1;
		lbNext.hidden = images.length <= 1;

		// Preload adjacent.
		preload( current + 1 );
		preload( current - 1 );
	}

	function preload( index ) {
		if ( index < 0 || index >= images.length ) return;
		const img = new Image();
		img.src = images[ index ].src;
	}

	function clamp( n, min, max ) {
		return Math.max( min, Math.min( max, n ) );
	}

	// ── Button triggers ───────────────────────────────────────────────────────────

	openBtns.forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const idx = parseInt( btn.dataset.index ?? 0, 10 );
			open( idx );
		} );
	} );

	lbClose.addEventListener( 'click', close );
	lbPrev.addEventListener(  'click', () => show( current - 1 ) );
	lbNext.addEventListener(  'click', () => show( current + 1 ) );

	// Click outside image.
	lightbox.addEventListener( 'click', ( e ) => {
		if ( e.target === lightbox ) close();
	} );

	// ── Keyboard ──────────────────────────────────────────────────────────────────

	document.addEventListener( 'keydown', ( e ) => {
		if ( lightbox.hidden ) return;

		switch ( e.key ) {
			case 'Escape':
				close();
				break;
			case 'ArrowLeft':
			case 'ArrowUp':
				e.preventDefault();
				show( current - 1 );
				break;
			case 'ArrowRight':
			case 'ArrowDown':
				e.preventDefault();
				show( current + 1 );
				break;
		}
	} );

	// ── Touch swipe ───────────────────────────────────────────────────────────────

	let touchStartX = 0;

	lightbox.addEventListener( 'touchstart', ( e ) => {
		touchStartX = e.changedTouches[ 0 ].clientX;
	}, { passive: true } );

	lightbox.addEventListener( 'touchend', ( e ) => {
		const dx = e.changedTouches[ 0 ].clientX - touchStartX;
		if ( Math.abs( dx ) > 50 ) {
			show( dx < 0 ? current + 1 : current - 1 );
		}
	}, { passive: true } );

	// ── Focus trap ────────────────────────────────────────────────────────────────

	lightbox.addEventListener( 'keydown', ( e ) => {
		if ( e.key !== 'Tab' || lightbox.hidden ) return;

		const focusable = Array.from(
			lightbox.querySelectorAll( 'button, [tabindex]:not([tabindex="-1"])' )
		).filter( ( el ) => ! el.hidden && ! el.disabled );

		if ( ! focusable.length ) return;

		const first = focusable[ 0 ];
		const last  = focusable[ focusable.length - 1 ];

		if ( e.shiftKey && document.activeElement === first ) {
			e.preventDefault();
			last.focus();
		} else if ( ! e.shiftKey && document.activeElement === last ) {
			e.preventDefault();
			first.focus();
		}
	} );

} )();
