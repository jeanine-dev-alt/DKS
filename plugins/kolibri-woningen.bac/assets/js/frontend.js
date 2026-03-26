/**
 * Kolibri Woningen — Frontend JS
 * AJAX filtering + sort + pagination.
 */
( function () {
	'use strict';

	if ( typeof kolibriData === 'undefined' ) return;

	const resultsEl  = document.getElementById( 'kolibri-results' );
	const filterForm = document.getElementById( 'kolibri-filter-form' );
	const sortSelect = document.getElementById( 'kolibri-sort' );

	if ( ! resultsEl ) return;

	// ── AJAX request ────────────────────────────────────────────────────────────

	let debounceTimer;

	function fetchResults( extraData = {} ) {
		clearTimeout( debounceTimer );
		debounceTimer = setTimeout( () => {
			_doFetch( extraData );
		}, 250 );
	}

	function _doFetch( extraData = {} ) {
		const data = new FormData();
		data.append( 'action', 'kolibri_filter' );
		data.append( 'nonce', kolibriData.nonce );

		// Collect form fields.
		if ( filterForm ) {
			new FormData( filterForm ).forEach( ( val, key ) => {
				data.append( key, val );
			} );
		}

		// Sort.
		if ( sortSelect ) {
			const [ orderby, order ] = sortSelect.value.split( '-' );
			data.append( 'orderby', orderby );
			data.append( 'order', order );
		}

		// Extra (page, etc.).
		Object.entries( extraData ).forEach( ( [ k, v ] ) => data.append( k, v ) );

		resultsEl.classList.add( 'is-loading' );

		fetch( kolibriData.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		} )
			.then( ( r ) => r.json() )
			.then( ( json ) => {
				if ( json.success ) {
					resultsEl.innerHTML = json.data.html;
					attachPaginationEvents();
				}
			} )
			.catch( console.error )
			.finally( () => {
				resultsEl.classList.remove( 'is-loading' );
				// Scroll to results top.
				resultsEl.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
			} );
	}

	// ── Filter form ──────────────────────────────────────────────────────────────

	if ( filterForm ) {
		// Instant AJAX on change for select / inputs with data-ajax-filter.
		filterForm.querySelectorAll( '[data-ajax-filter]' ).forEach( ( el ) => {
			el.addEventListener( 'change', () => fetchResults( { paged: 1 } ) );
		} );

		// Prevent default submit; use AJAX instead.
		filterForm.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			fetchResults( { paged: 1 } );
		} );

		// Reset.
		filterForm.addEventListener( 'reset', () => {
			setTimeout( () => fetchResults( { paged: 1 } ), 50 );
		} );
	}

	// ── Sort ─────────────────────────────────────────────────────────────────────

	if ( sortSelect ) {
		sortSelect.addEventListener( 'change', () => fetchResults( { paged: 1 } ) );
	}

	// ── Pagination ────────────────────────────────────────────────────────────────

	function attachPaginationEvents() {
		resultsEl.querySelectorAll( '.kolibri-page-btn' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', () => {
				const page = btn.dataset.page;
				fetchResults( { paged: page } );
			} );
		} );
	}

	attachPaginationEvents();

} )();

// ── Contact form (sticky CTA) ────────────────────────────────────────────────

( function () {
	'use strict';

	const form = document.querySelector( '.kolibri-contact-form' );
	if ( ! form ) return;

	form.addEventListener( 'submit', ( e ) => {
		e.preventDefault();
		const data = new FormData( form );
		data.append( 'action', 'kolibri_contact' );

		if ( typeof kolibriData !== 'undefined' ) {
			data.append( 'nonce', kolibriData.nonce );
		}

		fetch( ( typeof kolibriData !== 'undefined' ? kolibriData.ajaxUrl : '/wp-admin/admin-ajax.php' ), {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		} )
			.then( ( r ) => r.json() )
			.then( ( json ) => {
				const msg = document.createElement( 'p' );
				msg.style.cssText = 'margin-top:.5rem;font-size:.75rem;font-weight:600;';
				if ( json.success ) {
					msg.style.color = '#16a34a';
					msg.textContent = json.data?.message ?? 'Bericht verzonden!';
					form.reset();
				} else {
					msg.style.color = '#dc2626';
					msg.textContent = json.data?.message ?? 'Er ging iets mis.';
				}
				form.appendChild( msg );
				setTimeout( () => msg.remove(), 6000 );
			} )
			.catch( console.error );
	} );
} )();
