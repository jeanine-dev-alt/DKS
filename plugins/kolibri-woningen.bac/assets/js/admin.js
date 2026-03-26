/**
 * Kolibri Woningen — Admin JS
 * Tabs + media gallery uploader.
 */
( function ( $ ) {
	'use strict';

	// ── Tab switching ─────────────────────────────────────────────────────────────

	const tabBtns   = document.querySelectorAll( '.kolibri-tab-btn' );
	const tabPanels = document.querySelectorAll( '.kolibri-tab-panel' );

	tabBtns.forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const target = btn.dataset.tab;

			tabBtns.forEach( ( b ) => b.classList.remove( 'kolibri-active' ) );
			tabPanels.forEach( ( p ) => p.classList.remove( 'kolibri-active' ) );

			btn.classList.add( 'kolibri-active' );
			const panel = document.getElementById( 'kolibri-tab-' + target );
			if ( panel ) panel.classList.add( 'kolibri-active' );
		} );
	} );

	// ── Gallery media uploader ────────────────────────────────────────────────────

	const addBtn     = document.getElementById( 'kolibri-gallery-add' );
	const hiddenIds  = document.getElementById( 'kolibri-gallery-ids' );
	const previewEl  = document.getElementById( 'kolibri-gallery-preview' );

	if ( addBtn && hiddenIds && previewEl ) {
		let frame;

		addBtn.addEventListener( 'click', () => {
			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title:    'Foto\'s selecteren',
				button:   { text: 'Toevoegen aan galerij' },
				multiple: true,
			} );

			frame.on( 'select', () => {
				const attachments = frame.state().get( 'selection' ).toJSON();
				const currentIds  = hiddenIds.value
					? hiddenIds.value.split( ',' ).map( Number )
					: [];

				attachments.forEach( ( att ) => {
					if ( currentIds.includes( att.id ) ) return;
					currentIds.push( att.id );

					const src  = att.sizes?.thumbnail?.url ?? att.url;
					const span = document.createElement( 'span' );
					span.className      = 'kolibri-gallery-item';
					span.dataset.id     = att.id;
					span.innerHTML      =
						`<img src="${ src }" alt="" width="60" height="60">` +
						`<button type="button" class="kolibri-remove-img" data-id="${ att.id }">&times;</button>`;
					previewEl.appendChild( span );
					span.querySelector( '.kolibri-remove-img' ).addEventListener( 'click', removeImg );
				} );

				hiddenIds.value = currentIds.join( ',' );
			} );

			frame.open();
		} );

		// Remove existing items.
		previewEl.querySelectorAll( '.kolibri-remove-img' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', removeImg );
		} );

		function removeImg() {
			const id   = parseInt( this.dataset.id, 10 );
			const item = this.closest( '.kolibri-gallery-item' );
			item.remove();

			const ids = hiddenIds.value
				? hiddenIds.value.split( ',' ).map( Number ).filter( ( i ) => i !== id )
				: [];
			hiddenIds.value = ids.join( ',' );
		}
	}

} )( jQuery );
