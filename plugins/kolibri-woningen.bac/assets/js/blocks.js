/**
 * Kolibri Woningen — Gutenberg block editor scripts.
 *
 * Registers editor UI for:
 *   kolibri/woningen-grid        — grid met modus (laatste / selecteren) + kolommen
 *   kolibri/woningen-zoekformulier — zoekformulier met AJAX-filters
 *
 * Render is server-side; editor toont een placeholder.
 * Enqueued via enqueue_block_editor_assets (class-assets.php).
 */
( function ( blocks, element, blockEditor, components, apiFetch ) {
	'use strict';

	if ( ! blocks || ! element || ! blockEditor || ! components ) return;

	const { registerBlockType }                       = blocks;
	const { createElement: el, useState, useEffect, Fragment } = element;
	const { InspectorControls, useBlockProps }        = blockEditor;
	const {
		PanelBody,
		RangeControl,
		SelectControl,
		RadioControl,
		TextControl,
		Spinner,
		CheckboxControl,
	} = components;

	// ── Woningen Grid ─────────────────────────────────────────────────────────

	registerBlockType( 'kolibri/woningen-grid', {

		edit: function ( { attributes, setAttributes } ) {
			const { mode, count, columns, selectedIds } = attributes;

			const blockProps = useBlockProps( { className: 'kolibri-block-editor-placeholder' } );

			// Search state — only used in 'selected' mode
			const [ search,        setSearch        ] = useState( '' );
			const [ searchResults, setSearchResults ] = useState( [] );
			const [ searching,     setSearching     ] = useState( false );
			const [ selectedPosts, setSelectedPosts ] = useState( [] );

			// Load titles of already-selected posts on first render / when IDs change
			useEffect( function () {
				if ( ! selectedIds || selectedIds.length === 0 ) {
					setSelectedPosts( [] );
					return;
				}
				if ( ! apiFetch ) return;
				apiFetch( {
					path: '/wp/v2/kolibri_woning?include=' + selectedIds.join( ',' )
						+ '&per_page=100&_fields=id,title&status=publish',
				} ).then( function ( posts ) {
					// Preserve the order the editor selected them in
					const ordered = selectedIds
						.map( function ( id ) {
							return posts.find( function ( p ) { return p.id === id; } );
						} )
						.filter( Boolean );
					setSelectedPosts( ordered );
				} ).catch( function () {} );
			}, [ ( selectedIds || [] ).join( ',' ) ] );

			// Debounced REST search when typing
			useEffect( function () {
				if ( ! apiFetch || search.length < 2 ) {
					setSearchResults( [] );
					return;
				}
				setSearching( true );
				const timer = setTimeout( function () {
					apiFetch( {
						path: '/wp/v2/kolibri_woning?search='
							+ encodeURIComponent( search )
							+ '&per_page=20&_fields=id,title&status=publish',
					} ).then( function ( posts ) {
						setSearchResults( posts );
						setSearching( false );
					} ).catch( function () {
						setSearching( false );
					} );
				}, 350 );
				return function () { clearTimeout( timer ); };
			}, [ search ] );

			function toggleId( id ) {
				const current = selectedIds || [];
				const updated = current.includes( id )
					? current.filter( function ( i ) { return i !== id; } )
					: [ ...current, id ];
				setAttributes( { selectedIds: updated } );
			}

			function removeId( id ) {
				setAttributes( {
					selectedIds: ( selectedIds || [] ).filter( function ( i ) { return i !== id; } ),
				} );
			}

			// Build the preview label shown on the block canvas
			const previewLabel = mode === 'selected'
				? 'Woningen Grid \u2014 ' + ( selectedIds || [] ).length + ' geselecteerd, ' + columns + ' kolommen'
				: 'Woningen Grid \u2014 ' + count + ' laatste woningen, ' + columns + ' kolommen';

			return el(
				Fragment,
				null,

				// ── Inspector sidebar ─────────────────────────────────────────
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Instellingen', initialOpen: true },

						// Mode switcher
						el( RadioControl, {
							label: 'Weergave',
							selected: mode,
							options: [
								{ label: 'Laatste woningen tonen', value: 'latest' },
								{ label: 'Woningen handmatig selecteren', value: 'selected' },
							],
							onChange: function ( v ) { setAttributes( { mode: v } ); },
						} ),

						// Aantal — only in latest mode
						mode === 'latest' && el( RangeControl, {
							label: 'Aantal woningen',
							value: count,
							onChange: function ( v ) { setAttributes( { count: v } ); },
							min: 1,
							max: 12,
						} ),

						// Kolommen
						el( SelectControl, {
							label: 'Kolommen',
							value: String( columns ),
							options: [
								{ label: '2 kolommen', value: '2' },
								{ label: '3 kolommen', value: '3' },
								{ label: '4 kolommen', value: '4' },
							],
							onChange: function ( v ) { setAttributes( { columns: parseInt( v, 10 ) } ); },
						} ),
					),

					// ── Woningen selecteren — separate panel ──────────────────
					mode === 'selected' && el(
						PanelBody,
						{ title: 'Woningen selecteren', initialOpen: true },

						// List of already-selected posts
						selectedPosts.length > 0 && el(
							'div',
							{ className: 'kolibri-selected-posts' },
							el( 'p', { style: { fontWeight: 600, marginBottom: '6px', fontSize: '12px' } }, 'Geselecteerd:' ),
							selectedPosts.map( function ( post ) {
								return el(
									'div',
									{ key: post.id, className: 'kolibri-selected-item' },
									el( 'span', null, post.title.rendered ),
									el( 'button', {
										type: 'button',
										className: 'kolibri-remove-btn',
										onClick: function () { removeId( post.id ); },
										'aria-label': 'Verwijder ' + post.title.rendered,
									}, '\u00d7' ),
								);
							} ),
						),

						// Search input
						el( TextControl, {
							label: 'Woning zoeken',
							value: search,
							onChange: setSearch,
							placeholder: 'Typ een adres of titel\u2026',
						} ),

						// Spinner
						searching && el( Spinner, null ),

						// Search results
						! searching && searchResults.length > 0 && el(
							'div',
							{ className: 'kolibri-search-results' },
							searchResults.map( function ( post ) {
								return el( CheckboxControl, {
									key: post.id,
									label: post.title.rendered,
									checked: ( selectedIds || [] ).includes( post.id ),
									onChange: function () { toggleId( post.id ); },
								} );
							} ),
						),

						! searching && search.length >= 2 && searchResults.length === 0 && el(
							'p',
							{ style: { fontSize: '12px', color: '#757575', marginTop: '8px' } },
							'Geen woningen gevonden.'
						),
					),
				),

				// ── Block canvas placeholder ───────────────────────────────────
				el(
					'div',
					blockProps,
					el( 'span', {
						className: 'dashicons dashicons-building',
						style: { fontSize: '2rem', color: '#B85C38' },
					} ),
					el( 'p', null, previewLabel ),
				),
			);
		},

		save: function () { return null; },
	} );

	// ── Woningen Zoekformulier ─────────────────────────────────────────────────

	registerBlockType( 'kolibri/woningen-zoekformulier', {

		edit: function ( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'kolibri-block-editor-placeholder' } );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Instellingen', initialOpen: true },
						el( RangeControl, {
							label: 'Woningen per pagina',
							value: attributes.perPage,
							onChange: function ( v ) { setAttributes( { perPage: v } ); },
							min: 1,
							max: 48,
						} ),
						el( SelectControl, {
							label: 'Kolommen',
							value: String( attributes.columns ),
							options: [
								{ label: '2 kolommen', value: '2' },
								{ label: '3 kolommen', value: '3' },
								{ label: '4 kolommen', value: '4' },
							],
							onChange: function ( v ) { setAttributes( { columns: parseInt( v, 10 ) } ); },
						} ),
					),
				),
				el(
					'div',
					blockProps,
					el( 'span', {
						className: 'dashicons dashicons-search',
						style: { fontSize: '2rem', color: '#B85C38' },
					} ),
					el( 'p', null, 'Woningen Zoekformulier \u2014 met AJAX-filters' ),
				),
			);
		},

		save: function () { return null; },
	} );

} )(
	window.wp && window.wp.blocks,
	window.wp && window.wp.element,
	window.wp && window.wp.blockEditor,
	window.wp && window.wp.components,
	window.wp && window.wp.apiFetch,
);
