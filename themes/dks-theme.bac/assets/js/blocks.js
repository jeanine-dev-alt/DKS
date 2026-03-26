/**
 * blocks.js — DKS Real Estate Theme
 *
 * Native canvas editing for all custom Gutenberg blocks.
 * Text is editable directly on the canvas via styled <input> / <textarea>.
 * Images use MediaUpload on the canvas. Repeater items can be added/removed inline.
 *
 * PHP render_callback handles the actual frontend output.
 * save() returns null for all blocks (dynamic / server-side rendered).
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	const { registerBlockType }          = blocks;
	const { InspectorControls,
	        MediaUpload,
	        MediaUploadCheck }           = blockEditor;
	const { PanelBody, TextControl,
	        RangeControl, SelectControl,
	        Button, ToggleControl }      = components;
	const { createElement: el,
	        Fragment }                   = element;
	const { __ }                         = i18n;

	/* ── Design tokens ──────────────────────────────────────────────────── */
	var C = { accent: '#B85C38', dark: '#1A1A1A', light: '#F9F9F7' };

	/* ── Inline-editable input helpers ──────────────────────────────────── */

	/**
	 * inputOn( 'dark' | 'light', extraStyle )
	 * Returns a style object for a transparent input that blends into its section.
	 */
	function inputOn( bg, extra ) {
		var border = ( bg === 'dark' ) ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.2)';
		return Object.assign( {
			background:    'transparent',
			border:        'none',
			borderBottom:  '1px dashed ' + border,
			outline:       'none',
			color:         'inherit',
			fontFamily:    'inherit',
			fontSize:      'inherit',
			fontWeight:    'inherit',
			letterSpacing: 'inherit',
			lineHeight:    'inherit',
			width:         '100%',
			padding:       '2px 0',
			boxSizing:     'border-box',
			display:       'block',
		}, extra || {} );
	}

	function taOn( bg, extra ) {
		return Object.assign( inputOn( bg ), { resize: 'vertical', minHeight: '52px' }, extra || {} );
	}

	/* ── Small editor hint label above an inline field ─────────────────── */
	function hint( text, bg ) {
		return el( 'span', { style: {
			display:       'block',
			fontSize:      '9px',
			fontWeight:    '700',
			textTransform: 'uppercase',
			letterSpacing: '0.15em',
			color:         ( bg === 'dark' ) ? 'rgba(255,255,255,0.35)' : 'rgba(0,0,0,0.35)',
			marginBottom:  '3px',
			userSelect:    'none',
		} }, text );
	}

	/* ── Eyebrow preview row ────────────────────────────────────────────── */
	function eyebrowInput( value, onChange, bg ) {
		return el( 'div', { style: { marginBottom: '8px' } },
			hint( 'eyebrow', bg ),
			el( 'input', { type: 'text', value: value || '', onChange: function(e){ onChange(e.target.value); },
				style: inputOn( bg, { fontSize: '10px', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.35em', color: C.accent } )
			} )
		);
	}

	/* ── Canvas image upload ─────────────────────────────────────────────── */
	function canvasImg( opts ) {
		/* opts: { id, url, alt, onSelect, wrapStyle, imgStyle, phStyle } */
		return el( MediaUploadCheck, {},
			el( MediaUpload, {
				onSelect:     opts.onSelect,
				allowedTypes: [ 'image' ],
				value:        opts.id || 0,
				render: function ( ref ) {
					if ( opts.url ) {
						return el( 'div', { style: Object.assign( { position: 'relative' }, opts.wrapStyle || {} ) },
							el( 'img', { src: opts.url, alt: opts.alt || '', style: Object.assign( { width: '100%', height: '100%', objectFit: 'cover', display: 'block' }, opts.imgStyle || {} ) } ),
							el( 'button', { onClick: ref.open, style: { position: 'absolute', bottom: '8px', left: '8px', fontSize: '10px', padding: '3px 8px', background: 'rgba(0,0,0,.6)', color: '#fff', border: 'none', cursor: 'pointer', borderRadius: '2px' } },
								__( 'Vervangen', 'dks-theme' ) )
						);
					}
					return el( 'div', {
						onClick: ref.open,
						style: Object.assign( { display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', fontSize: '11px', color: '#aaa', border: '1px dashed #ccc', background: '#f0f0f0', minHeight: '160px' }, opts.phStyle || {} ),
					}, __( '+ Afbeelding selecteren', 'dks-theme' ) );
				},
			} )
		);
	}

	/* ── Inspector image panel ───────────────────────────────────────────── */
	function imgInspector( title, id, url, onSelect, onRemove ) {
		return el( PanelBody, { title: title, initialOpen: true },
			el( MediaUploadCheck, {},
				el( MediaUpload, {
					onSelect: onSelect,
					allowedTypes: [ 'image' ],
					value: id || 0,
					render: function ( ref ) {
						return el( 'div', {},
							url ? el( 'img', { src: url, alt: '', style: { width: '100%', marginBottom: '8px' } } ) : null,
							el( Button, { onClick: ref.open, variant: 'secondary', isSmall: true },
								url ? __( 'Vervangen', 'dks-theme' ) : __( 'Afbeelding kiezen', 'dks-theme' )
							),
							url ? el( Button, { onClick: onRemove, variant: 'link', isDestructive: true, isSmall: true, style: { display: 'block', marginTop: '6px' } }, __( 'Verwijderen', 'dks-theme' ) ) : null
						);
					}
				} )
			)
		);
	}

	/* ── Section-scoped repeater (canvas) ────────────────────────────────── */
	/**
	 * canvasRepeater( items, setFn, key, defaultItem, renderFn )
	 * renderFn( item, upd, rem ) — upd(field, val), rem()
	 */
	function canvasRepeater( items, setFn, key, defItem, renderFn ) {
		function upd( idx, field, val ) {
			var next = items.map( function( it, i ) {
				if ( i !== idx ) return it;
				var c = Object.assign( {}, it ); c[ field ] = val; return c;
			} );
			var o = {}; o[ key ] = next; setFn( o );
		}
		function batch( idx, fields ) {
			var next = items.map( function( it, i ) {
				if ( i !== idx ) return it;
				return Object.assign( {}, it, fields );
			} );
			var o = {}; o[ key ] = next; setFn( o );
		}
		function rem( idx ) {
			var o = {}; o[ key ] = items.filter( function( _, i ) { return i !== idx; } ); setFn( o );
		}
		function add() {
			var o = {}; o[ key ] = items.concat( [ Object.assign( {}, defItem ) ] ); setFn( o );
		}
		return el( Fragment, {},
			items.map( function( item, idx ) {
				return el( Fragment, { key: idx }, renderFn( item, function( f, v ) { upd( idx, f, v ); }, function() { rem( idx ); }, idx, function( fields ) { batch( idx, fields ); } ) );
			} ),
			el( 'button', {
				type: 'button', onClick: add,
				style: { marginTop: '12px', padding: '8px 18px', fontSize: '10px', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.1em', background: C.accent, color: '#fff', border: 'none', cursor: 'pointer', fontFamily: 'inherit' },
			}, '+ Item toevoegen' )
		);
	}

	/* ── Shared remove button ────────────────────────────────────────────── */
	function remBtn( onClick ) {
		return el( 'button', { type: 'button', onClick: onClick, style: { display: 'block', background: 'none', border: 'none', cursor: 'pointer', fontSize: '10px', color: '#cc1818', padding: '4px 0', fontFamily: 'inherit' } }, '✕ Verwijderen' );
	}

	/* ─────────────────────────────────────────────────────────────────────
	 * 1. dks/hero
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/hero', {
		title:       __( 'DKS Hero', 'dks-theme' ),
		description: __( 'Volledige breedte hero-sectie met achtergrondafbeelding.', 'dks-theme' ),
		category:    'dks-blocks',
		icon:        'cover-image',
		supports:    { align: [ 'full' ], anchor: true },
		attributes: {
			backgroundId:   { type: 'number', default: 0 },
			backgroundUrl:  { type: 'string', default: '' },
			backgroundAlt:  { type: 'string', default: '' },
			heading:        { type: 'string', default: 'A NEW BEGINNING IN YOUR DREAM HOME' },
			subheading:     { type: 'string', default: 'Expert guidance for buying and renting premium properties in the heart of the Netherlands.' },
			btnPrimaryText: { type: 'string', default: 'View Listings' },
			btnPrimaryUrl:  { type: 'string', default: '#' },
			btnSecondText:  { type: 'string', default: 'Contact Us' },
			btnSecondUrl:   { type: 'string', default: '#' },
			overlayOpacity: { type: 'number', default: 45 },
			minHeight:      { type: 'string', default: '90vh' },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},

				el( InspectorControls, {},
					imgInspector(
						__( 'Achtergrondafbeelding', 'dks-theme' ),
						a.backgroundId, a.backgroundUrl,
						function( m ) { set( { backgroundId: m.id, backgroundUrl: m.url, backgroundAlt: m.alt } ); },
						function()    { set( { backgroundId: 0, backgroundUrl: '', backgroundAlt: '' } ); }
					),
					el( PanelBody, { title: __( 'Overlay & hoogte', 'dks-theme' ), initialOpen: false },
						el( RangeControl, {
							label: __( 'Overlay donkerte (%)', 'dks-theme' ), value: a.overlayOpacity,
							onChange: function(v){ set({ overlayOpacity: v }); }, min: 0, max: 90, step: 5,
						} ),
						el( TextControl, {
							label: __( 'Min. hoogte (CSS)', 'dks-theme' ), value: a.minHeight,
							onChange: function(v){ set({ minHeight: v }); }, help: 'bijv. 90vh of 600px',
						} )
					),
					el( PanelBody, { title: __( 'Knop-URL\'s', 'dks-theme' ), initialOpen: false },
						el( TextControl, { label: 'Primaire knop URL', value: a.btnPrimaryUrl, onChange: function(v){ set({ btnPrimaryUrl: v }); } } ),
						el( TextControl, { label: 'Secundaire knop URL', value: a.btnSecondUrl, onChange: function(v){ set({ btnSecondUrl: v }); } } )
					)
				),

				/* Canvas */
				el( 'div', { style: {
					position:   'relative',
					minHeight:  '380px',
					background: a.backgroundUrl ? 'url(' + a.backgroundUrl + ') center/cover no-repeat' : C.dark,
					display:    'flex',
					alignItems: 'center',
					overflow:   'hidden',
					color:      '#fff',
				} },
					/* overlay */
					el( 'div', { style: { position: 'absolute', inset: 0, background: 'rgba(0,0,0,' + ( a.overlayOpacity / 100 ) + ')', pointerEvents: 'none' } } ),
					/* content */
					el( 'div', { style: { position: 'relative', zIndex: 1, padding: '3rem', width: '100%', maxWidth: '760px' } },
						el( 'div', { style: { marginBottom: '14px' } },
							hint( 'Koptekst', 'dark' ),
							el( 'input', { type: 'text', value: a.heading,
								onChange: function(e){ set({ heading: e.target.value }); },
								style: inputOn( 'dark', { fontSize: '2rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: '#fff' } ),
							} )
						),
						el( 'div', { style: { marginBottom: '24px' } },
							hint( 'Subtekst', 'dark' ),
							el( 'input', { type: 'text', value: a.subheading,
								onChange: function(e){ set({ subheading: e.target.value }); },
								style: inputOn( 'dark', { fontSize: '1rem', color: 'rgba(255,255,255,0.8)' } ),
							} )
						),
						el( 'div', { style: { display: 'flex', gap: '12px', flexWrap: 'wrap' } },
							el( 'input', { type: 'text', value: a.btnPrimaryText,
								onChange: function(e){ set({ btnPrimaryText: e.target.value }); },
								style: { background: C.accent, color: '#fff', border: 'none', padding: '11px 24px', fontFamily: 'inherit', fontWeight: '700', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.1em', outline: 'none', cursor: 'text', minWidth: '120px' },
							} ),
							el( 'input', { type: 'text', value: a.btnSecondText,
								onChange: function(e){ set({ btnSecondText: e.target.value }); },
								style: { background: 'transparent', color: '#fff', border: '2px solid rgba(255,255,255,0.55)', padding: '11px 24px', fontFamily: 'inherit', fontWeight: '700', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.1em', outline: 'none', cursor: 'text', minWidth: '120px' },
							} )
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 2. dks/listings — native canvas (property cards)
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/listings', {
		title:    __( 'DKS Premium Listings', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'building',
		supports: { anchor: true },
		attributes: {
			title:      { type: 'string', default: 'Premium Listings' },
			eyebrow:    { type: 'string', default: 'Selected Collection' },
			browseText: { type: 'string', default: 'Browse All Properties' },
			browseUrl:  { type: 'string', default: '#' },
			columns:    { type: 'number', default: 3 },
			cards: {
				type:    'array',
				default: [
					{ imageId: 0, imageUrl: '', price: '€1,450,000', location: 'Amsterdam, Prinsengracht District', beds: '4', baths: '3', sqm: '185m²', badge: 'Featured', permalink: '#' },
					{ imageId: 0, imageUrl: '', price: '€985,000',   location: 'Utrecht, Historical Center',        beds: '3', baths: '2', sqm: '142m²', badge: '',         permalink: '#' },
					{ imageId: 0, imageUrl: '', price: '€1,200,000', location: 'Rotterdam, Kop van Zuid',           beds: '3', baths: '2', sqm: '160m²', badge: '',         permalink: '#' },
				],
			},
		},

		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const cards = attributes.cards;

			function updateCard( index, field, value ) {
				const next = cards.map( function ( card, i ) {
					if ( i !== index ) return card;
					const updated = {};
					Object.keys( card ).forEach( function ( k ) { updated[ k ] = card[ k ]; } );
					updated[ field ] = value;
					return updated;
				} );
				setAttributes( { cards: next } );
			}

			function addCard() {
				setAttributes( { cards: cards.concat( [ { imageId: 0, imageUrl: '', price: '', location: '', beds: '', baths: '', sqm: '', badge: '', permalink: '#' } ] ) } );
			}
			function removeCard( index ) {
				setAttributes( { cards: cards.filter( function ( _, i ) { return i !== index; } ) } );
			}

			var gridStyle = {
				display:             'grid',
				gridTemplateColumns: 'repeat(' + Math.min( attributes.columns, cards.length ) + ', 1fr)',
				gap:                 '16px',
				marginTop:           '16px',
			};
			var cardStyle      = { border: '1px solid #e0e0e0', borderRadius: '2px', background: '#fff', overflow: 'hidden', fontFamily: 'inherit' };
			var fieldsetStyle  = { padding: '10px 12px', display: 'flex', flexDirection: 'column', gap: '6px' };
			var labelStyle     = { fontSize: '10px', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.1em', color: '#888', marginBottom: '2px', display: 'block' };
			var inputStyle     = { width: '100%', padding: '4px 6px', fontSize: '12px', border: '1px solid #ddd', borderRadius: '2px', fontFamily: 'inherit', boxSizing: 'border-box' };
			var rowStyle       = { display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '6px' };
			var thumbStyle     = { width: '100%', height: '120px', objectFit: 'cover', display: 'block' };
			var thumbPhStyle   = { width: '100%', height: '120px', background: '#f0f0f0', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '11px', color: '#aaa', cursor: 'pointer', fontFamily: 'inherit' };
			var removeBtnStyle = { display: 'block', width: '100%', padding: '4px', fontSize: '10px', background: 'none', border: 'none', color: '#cc0000', cursor: 'pointer', textAlign: 'center', fontFamily: 'inherit' };

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: __( 'Sectie-instellingen', 'dks-theme' ), initialOpen: true },
						el( TextControl, { label: __( 'Sectietitel', 'dks-theme' ),              value: attributes.title,      onChange: function(v){ setAttributes({ title: v }); } } ),
						el( TextControl, { label: __( 'Eyebrow label', 'dks-theme' ),            value: attributes.eyebrow,    onChange: function(v){ setAttributes({ eyebrow: v }); } } ),
						el( TextControl, { label: __( '"Alle woningen" linktekst', 'dks-theme' ), value: attributes.browseText, onChange: function(v){ setAttributes({ browseText: v }); } } ),
						el( TextControl, { label: __( '"Alle woningen" URL', 'dks-theme' ),       value: attributes.browseUrl,  onChange: function(v){ setAttributes({ browseUrl: v }); } } ),
						el( RangeControl, { label: __( 'Kolommen', 'dks-theme' ), value: attributes.columns, onChange: function(v){ setAttributes({ columns: v }); }, min: 1, max: 4 } )
					)
				),

				el( 'div', { style: { padding: '16px 0' } },

					el( 'div', { style: { marginBottom: '8px' } },
						el( 'p', { style: { fontSize: '10px', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.3em', color: C.accent, margin: '0 0 4px' } }, attributes.eyebrow ),
						el( 'h2', { style: { fontSize: '22px', fontWeight: '900', margin: '0', letterSpacing: '-0.02em', textTransform: 'uppercase' } }, attributes.title )
					),

					el( 'div', { style: gridStyle },
						cards.map( function ( card, index ) {
							return el( 'div', { key: index, style: cardStyle },

								el( MediaUploadCheck, {},
									el( MediaUpload, {
										onSelect: function(media){ updateCard( index, 'imageUrl', media.url ); updateCard( index, 'imageId', media.id ); },
										allowedTypes: [ 'image' ],
										value: card.imageId,
										render: function ( ref ) {
											return card.imageUrl
												? el( 'div', { style: { position: 'relative' } },
													el( 'img', { src: card.imageUrl, style: thumbStyle, alt: '' } ),
													el( 'button', { onClick: ref.open, style: { position: 'absolute', bottom: '6px', left: '6px', fontSize: '10px', padding: '3px 8px', background: 'rgba(0,0,0,.55)', color: '#fff', border: 'none', cursor: 'pointer', borderRadius: '2px' } }, __( 'Vervangen', 'dks-theme' ) )
												  )
												: el( 'div', { style: thumbPhStyle, onClick: ref.open }, __( '+ Afbeelding selecteren', 'dks-theme' ) );
										},
									} )
								),

								el( 'div', { style: fieldsetStyle },
									el( 'div', {}, el( 'label', { style: labelStyle }, __( 'Prijs', 'dks-theme' ) ), el( 'input', { type: 'text', value: card.price, placeholder: '€1,450,000', style: inputStyle, onChange: function(e){ updateCard(index,'price',e.target.value); } } ) ),
									el( 'div', {}, el( 'label', { style: labelStyle }, __( 'Adres / locatie', 'dks-theme' ) ), el( 'input', { type: 'text', value: card.location, placeholder: 'Amsterdam, Prinsengracht', style: inputStyle, onChange: function(e){ updateCard(index,'location',e.target.value); } } ) ),
									el( 'div', { style: rowStyle },
										el( 'div', {}, el( 'label', { style: labelStyle }, 'Slaapk.' ), el( 'input', { type: 'text', value: card.beds, placeholder: '4', style: inputStyle, onChange: function(e){ updateCard(index,'beds',e.target.value); } } ) ),
										el( 'div', {}, el( 'label', { style: labelStyle }, 'Badkamers' ), el( 'input', { type: 'text', value: card.baths, placeholder: '3', style: inputStyle, onChange: function(e){ updateCard(index,'baths',e.target.value); } } ) ),
										el( 'div', {}, el( 'label', { style: labelStyle }, 'Opp.' ), el( 'input', { type: 'text', value: card.sqm, placeholder: '185m²', style: inputStyle, onChange: function(e){ updateCard(index,'sqm',e.target.value); } } ) )
									),
									el( 'div', { style: rowStyle },
										el( 'div', {}, el( 'label', { style: labelStyle }, 'Badge' ), el( 'input', { type: 'text', value: card.badge, placeholder: 'Featured', style: inputStyle, onChange: function(e){ updateCard(index,'badge',e.target.value); } } ) ),
										el( 'div', { style: { gridColumn: 'span 2' } }, el( 'label', { style: labelStyle }, 'Link (URL)' ), el( 'input', { type: 'text', value: card.permalink, placeholder: '/woningen/naam', style: inputStyle, onChange: function(e){ updateCard(index,'permalink',e.target.value); } } ) )
									),
									cards.length > 1 ? el( 'button', { style: removeBtnStyle, onClick: function(){ removeCard(index); } }, __( '✕ Kaart verwijderen', 'dks-theme' ) ) : null
								)
							);
						} )
					),

					cards.length < 4
						? el( 'button', { onClick: addCard, style: { marginTop: '12px', padding: '8px 16px', fontSize: '11px', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.1em', background: C.dark, color: '#fff', border: 'none', cursor: 'pointer', fontFamily: 'inherit' } }, __( '+ Kaart toevoegen', 'dks-theme' ) )
						: null
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 3. dks/features — dark section, items grid
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/features', {
		title:    __( 'DKS Features', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'star-filled',
		supports: { align: [ 'full' ], anchor: true },
		attributes: {
			eyebrow: { type: 'string', default: 'DKS Distinction' },
			title:   { type: 'string', default: 'Why Choose DKS' },
			intro:   { type: 'string', default: 'We combine deep local market insights with a commitment to excellence, ensuring your real estate journey is seamless and successful.' },
			items:   { type: 'array', default: [
				{ icon: 'home',      title: 'Local Expertise', desc: 'In-depth knowledge of the Dutch housing market.' },
				{ icon: 'handshake', title: 'Personal Service', desc: 'A dedicated advisor for your unique needs.' },
				{ icon: 'verified',  title: 'Trusted Results',  desc: 'Over 20 years delivering premium real estate solutions.' },
			] },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			var itemCardStyle = {
				background:   'rgba(255,255,255,0.06)',
				border:       '1px solid rgba(255,255,255,0.1)',
				borderRadius: '2px',
				padding:      '18px 14px',
				position:     'relative',
			};

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Sectie-instellingen', initialOpen: true },
						el( TextControl, { label: 'Eyebrow', value: a.eyebrow, onChange: function(v){ set({ eyebrow: v }); } } ),
						el( TextControl, { label: 'Titel', value: a.title, onChange: function(v){ set({ title: v }); } } ),
						el( TextControl, { label: 'Introductietekst', value: a.intro, onChange: function(v){ set({ intro: v }); } } )
					)
				),

				/* Canvas — dark section */
				el( 'div', { style: { background: C.dark, padding: '3rem 2rem', color: '#fff' } },

					/* Section header */
					eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, 'dark' ),
					el( 'div', { style: { marginBottom: '8px' } },
						hint( 'Titel', 'dark' ),
						el( 'input', { type: 'text', value: a.title, onChange: function(e){ set({ title: e.target.value }); },
							style: inputOn( 'dark', { fontSize: '1.75rem', fontWeight: '800', letterSpacing: '-0.02em', textTransform: 'uppercase', color: '#fff' } ),
						} )
					),
					el( 'div', { style: { marginBottom: '2rem' } },
						hint( 'Intro', 'dark' ),
						el( 'textarea', { value: a.intro, onChange: function(e){ set({ intro: e.target.value }); },
							style: taOn( 'dark', { fontSize: '0.9rem', color: 'rgba(255,255,255,0.7)' } ),
						} )
					),

					/* Items grid */
					el( 'div', { style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px,1fr))', gap: '14px', marginBottom: '12px' } },
						canvasRepeater( a.items, set, 'items',
							{ icon: 'home', title: 'Nieuw voordeel', desc: '' },
							function( item, upd, rem, idx, batch ) {
								return el( 'div', { style: itemCardStyle },
									el( 'div', { style: { display: 'flex', gap: '8px', marginBottom: '8px', alignItems: 'center' } },
										el( 'div', { style: { width: '36px', height: '36px', background: C.accent, borderRadius: '50%', flexShrink: 0, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '11px', color: '#fff', fontWeight: '700' } }, item.icon ? item.icon.charAt(0).toUpperCase() : '?' ),
										el( 'div', { style: { flex: 1, minWidth: 0 } },
											hint( 'icoon-naam', 'dark' ),
											el( 'input', { type: 'text', value: item.icon || '', placeholder: 'home / star / verified', onChange: function(e){ upd('icon', e.target.value); }, style: inputOn( 'dark', { fontSize: '10px' } ) } )
										)
									),
									hint( 'Titel', 'dark' ),
									el( 'input', { type: 'text', value: item.title || '', onChange: function(e){ upd('title',e.target.value); }, style: inputOn( 'dark', { fontWeight: '700', fontSize: '0.875rem', marginBottom: '6px' } ) } ),
									hint( 'Beschrijving', 'dark' ),
									el( 'textarea', { value: item.desc || '', onChange: function(e){ upd('desc',e.target.value); }, style: taOn( 'dark', { fontSize: '0.8rem', color: 'rgba(255,255,255,0.65)', minHeight: '48px' } ) } ),
									remBtn( rem )
								);
							}
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 4. dks/newsletter — light section
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/newsletter', {
		title:    __( 'DKS Newsletter', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'email',
		supports: { anchor: true },
		attributes: {
			heading:     { type: 'string', default: 'Stay Informed' },
			description: { type: 'string', default: 'Receive exclusive market insights and premium listings directly to your inbox.' },
			buttonText:  { type: 'string', default: 'Subscribe' },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},
				el( InspectorControls, {},
					el( PanelBody, { title: 'Newsletter', initialOpen: true },
						el( TextControl, { label: 'Koptekst',    value: a.heading,     onChange: function(v){ set({ heading: v }); } } ),
						el( TextControl, { label: 'Beschrijving', value: a.description, onChange: function(v){ set({ description: v }); } } ),
						el( TextControl, { label: 'Knoptekst',   value: a.buttonText,  onChange: function(v){ set({ buttonText: v }); } } )
					)
				),

				el( 'div', { style: { background: C.light, padding: '3rem 2rem' } },
					el( 'div', { style: { maxWidth: '600px', margin: '0 auto', background: '#fff', borderRadius: '4px', padding: '2.5rem', border: '1px solid #e8e8e4' } },
						el( 'div', { style: { marginBottom: '10px' } },
							hint( 'Koptekst', 'light' ),
							el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
								style: inputOn( 'light', { fontSize: '1.5rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: C.dark } ),
							} )
						),
						el( 'div', { style: { marginBottom: '20px' } },
							hint( 'Beschrijving', 'light' ),
							el( 'input', { type: 'text', value: a.description, onChange: function(e){ set({ description: e.target.value }); },
								style: inputOn( 'light', { fontSize: '0.9rem', color: 'rgba(26,26,26,0.7)' } ),
							} )
						),
						el( 'div', { style: { display: 'flex', gap: '10px', alignItems: 'center' } },
							el( 'div', { style: { flex: 1, background: '#f5f5f3', border: '1px solid #ddd', padding: '11px 14px', fontSize: '13px', color: '#aaa' } }, 'jouwnaam@email.nl' ),
							el( 'input', { type: 'text', value: a.buttonText, onChange: function(e){ set({ buttonText: e.target.value }); },
								style: { background: C.accent, color: '#fff', border: 'none', padding: '11px 22px', fontFamily: 'inherit', fontWeight: '700', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.1em', outline: 'none', cursor: 'text', minWidth: '100px' },
							} )
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 5. dks/text-image — two-column, text left + image right
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/text-image', {
		title:    __( 'DKS Text + Afbeelding', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'align-pull-right',
		supports: { anchor: true, align: [ 'wide' ] },
		attributes: {
			eyebrow:  { type: 'string',  default: '' },
			heading:  { type: 'string',  default: 'Your Compelling Headline Here' },
			text:     { type: 'string',  default: 'Add your supporting text here. Describe the unique value you bring to the reader.' },
			btnText:  { type: 'string',  default: 'Learn More' },
			btnUrl:   { type: 'string',  default: '#' },
			imageId:  { type: 'number',  default: 0 },
			imageUrl: { type: 'string',  default: '' },
			imageAlt: { type: 'string',  default: '' },
			reversed: { type: 'boolean', default: false },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			var textColStyle = {
				padding:        '2rem',
				display:        'flex',
				flexDirection:  'column',
				justifyContent: 'center',
				order:          a.reversed ? 2 : 1,
			};
			var imgColStyle = {
				minHeight: '300px',
				order:     a.reversed ? 1 : 2,
				position:  'relative',
			};

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Lay-out', initialOpen: true },
						el( ToggleControl, {
							label:    __( 'Afbeelding links (omgekeerd)', 'dks-theme' ),
							checked:  a.reversed,
							onChange: function(v){ set({ reversed: v }); },
						} ),
						el( TextControl, { label: 'Knop URL', value: a.btnUrl, onChange: function(v){ set({ btnUrl: v }); } } )
					),
					imgInspector(
						__( 'Afbeelding', 'dks-theme' ),
						a.imageId, a.imageUrl,
						function(m){ set({ imageId: m.id, imageUrl: m.url, imageAlt: m.alt }); },
						function(){ set({ imageId: 0, imageUrl: '', imageAlt: '' }); }
					)
				),

				/* Canvas — white 2-column grid */
				el( 'div', { style: { background: '#fff', border: '1px solid #eee' } },
					el( 'div', { style: { display: 'grid', gridTemplateColumns: '1fr 1fr', alignItems: 'stretch' } },

						/* Text column */
						el( 'div', { style: textColStyle },
							eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, 'light' ),
							el( 'div', { style: { marginBottom: '12px' } },
								hint( 'Koptekst', 'light' ),
								el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
									style: inputOn( 'light', { fontSize: '1.6rem', fontWeight: '800', letterSpacing: '-0.02em', textTransform: 'uppercase', color: C.dark } ),
								} )
							),
							el( 'div', { style: { marginBottom: '18px' } },
								hint( 'Tekst', 'light' ),
								el( 'textarea', { value: a.text, onChange: function(e){ set({ text: e.target.value }); },
									style: taOn( 'light', { fontSize: '0.9375rem', color: 'rgba(26,26,26,0.75)', lineHeight: '1.7' } ),
								} )
							),
							el( 'input', { type: 'text', value: a.btnText, onChange: function(e){ set({ btnText: e.target.value }); },
								style: { display: 'inline-block', background: C.accent, color: '#fff', border: 'none', padding: '11px 24px', fontFamily: 'inherit', fontWeight: '700', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.1em', outline: 'none', cursor: 'text', width: 'auto', minWidth: '100px' },
							} )
						),

						/* Image column */
						el( 'div', { style: imgColStyle },
							canvasImg( {
								id: a.imageId, url: a.imageUrl, alt: a.imageAlt,
								onSelect: function(m){ set({ imageId: m.id, imageUrl: m.url, imageAlt: m.alt }); },
								wrapStyle: { height: '100%', minHeight: '300px' },
								imgStyle:  { height: '100%', minHeight: '300px' },
								phStyle:   { minHeight: '300px', color: '#aaa', border: 'none', background: '#f5f5f3', fontSize: '13px' },
							} )
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 6. dks/steps — numbered step cards
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/steps', {
		title:    __( 'DKS Stappen', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'list-view',
		supports: { anchor: true },
		attributes: {
			eyebrow: { type: 'string', default: 'How It Works' },
			heading: { type: 'string', default: 'Our Method' },
			intro:   { type: 'string', default: '' },
			steps:   { type: 'array',  default: [
				{ title: 'First Step',  desc: 'Describe what happens in this step.' },
				{ title: 'Second Step', desc: 'Describe what happens in this step.' },
				{ title: 'Third Step',  desc: 'Describe what happens in this step.' },
			] },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Sectie-instellingen', initialOpen: true },
						el( TextControl, { label: 'Eyebrow', value: a.eyebrow, onChange: function(v){ set({ eyebrow: v }); } } ),
						el( TextControl, { label: 'Titel',   value: a.heading, onChange: function(v){ set({ heading: v }); } } ),
						el( TextControl, { label: 'Intro',   value: a.intro,   onChange: function(v){ set({ intro: v }); } } )
					)
				),

				el( 'div', { style: { background: C.light, padding: '3rem 2rem' } },
					eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, 'light' ),
					el( 'div', { style: { marginBottom: a.intro ? '8px' : '2rem' } },
						hint( 'Titel', 'light' ),
						el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
							style: inputOn( 'light', { fontSize: '1.75rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: C.dark } ),
						} )
					),
					a.intro ? el( 'div', { style: { marginBottom: '2rem' } },
						el( 'textarea', { value: a.intro, onChange: function(e){ set({ intro: e.target.value }); }, style: taOn('light') } )
					) : null,

					el( 'div', { style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px,1fr))', gap: '16px', marginBottom: '12px' } },
						canvasRepeater( a.steps, set, 'steps',
							{ title: '', desc: '' },
							function( item, upd, rem, idx ) {
								return el( 'div', { style: { background: '#fff', border: '1px solid #e4e4e0', padding: '18px 14px', position: 'relative' } },
									el( 'div', { style: { fontSize: '2rem', fontWeight: '900', color: C.accent, lineHeight: 1, marginBottom: '10px', opacity: 0.5 } }, String( idx + 1 ).padStart( 2, '0' ) ),
									hint( 'Staptitel', 'light' ),
									el( 'input', { type: 'text', value: item.title || '', onChange: function(e){ upd('title',e.target.value); }, style: inputOn('light', { fontWeight: '700', marginBottom: '8px' }) } ),
									hint( 'Beschrijving', 'light' ),
									el( 'textarea', { value: item.desc || '', onChange: function(e){ upd('desc',e.target.value); }, style: taOn('light', { fontSize: '0.875rem', color: 'rgba(26,26,26,0.7)' }) } ),
									remBtn( rem )
								);
							}
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 7. dks/testimonials — review cards
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/testimonials', {
		title:    __( 'DKS Testimonials', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'format-quote',
		supports: { anchor: true },
		attributes: {
			eyebrow: { type: 'string', default: 'What Clients Say' },
			heading: { type: 'string', default: 'Client Testimonials' },
			items:   { type: 'array',  default: [
				{ quote: 'Exceptional service. They found us our dream home in under two months.', name: 'Jan de Vries', role: 'Buyer', imageUrl: '', imageId: 0, rating: 5 },
				{ quote: 'Professional, knowledgeable, and genuinely invested in our success.', name: 'Marieke Smit', role: 'Seller', imageUrl: '', imageId: 0, rating: 5 },
			] },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Sectie-instellingen', initialOpen: true },
						el( TextControl, { label: 'Eyebrow', value: a.eyebrow, onChange: function(v){ set({ eyebrow: v }); } } ),
						el( TextControl, { label: 'Titel',   value: a.heading, onChange: function(v){ set({ heading: v }); } } )
					)
				),

				el( 'div', { style: { background: C.light, padding: '3rem 2rem' } },
					eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, 'light' ),
					el( 'div', { style: { marginBottom: '2rem' } },
						hint( 'Titel', 'light' ),
						el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
							style: inputOn( 'light', { fontSize: '1.75rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: C.dark } ),
						} )
					),

					el( 'div', { style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(260px,1fr))', gap: '16px', marginBottom: '12px' } },
						canvasRepeater( a.items, set, 'items',
							{ quote: '', name: '', role: '', imageUrl: '', imageId: 0, rating: 5 },
							function( item, upd, rem, idx, batch ) {
								return el( 'div', { style: { background: '#fff', border: '1px solid #e4e4e0', padding: '20px 16px' } },
									/* Stars */
									el( 'div', { style: { color: '#F5A623', fontSize: '14px', marginBottom: '10px', letterSpacing: '2px' } }, '★★★★★' ),
									hint( 'Quote', 'light' ),
									el( 'textarea', { value: item.quote || '', onChange: function(e){ upd('quote',e.target.value); }, style: taOn('light', { fontSize: '0.9rem', color: 'rgba(26,26,26,0.8)', fontStyle: 'italic', marginBottom: '12px' }) } ),
									el( 'div', { style: { display: 'flex', alignItems: 'center', gap: '10px', marginTop: '12px' } },
										/* Avatar upload */
										el( MediaUploadCheck, {},
											el( MediaUpload, {
												onSelect: function(m){ batch({ imageUrl: m.url, imageId: m.id }); },
												allowedTypes: ['image'], value: item.imageId || 0,
												render: function( ref ) {
													return el( 'div', { onClick: ref.open, style: { width: '40px', height: '40px', borderRadius: '50%', overflow: 'hidden', cursor: 'pointer', background: '#ddd', flexShrink: 0, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '10px', color: '#999' } },
														item.imageUrl ? el( 'img', { src: item.imageUrl, alt: '', style: { width: '100%', height: '100%', objectFit: 'cover' } } ) : '+'
													);
												}
											} )
										),
										el( 'div', { style: { flex: 1, minWidth: 0 } },
											el( 'input', { type: 'text', value: item.name || '', placeholder: 'Naam', onChange: function(e){ upd('name',e.target.value); }, style: inputOn('light', { fontWeight: '700', fontSize: '0.875rem', marginBottom: '2px' }) } ),
											el( 'input', { type: 'text', value: item.role || '', placeholder: 'Rol', onChange: function(e){ upd('role',e.target.value); }, style: inputOn('light', { fontSize: '0.75rem', color: C.accent }) } )
										)
									),
									remBtn( rem )
								);
							}
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 8. dks/logos — logo/certification grid
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/logos', {
		title:    __( 'DKS Logo Grid', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'images-alt2',
		supports: { anchor: true },
		attributes: {
			heading: { type: 'string', default: '' },
			logos:   { type: 'array',  default: [] },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Sectie-instellingen', initialOpen: true },
						el( TextControl, { label: 'Heading (optioneel)', value: a.heading, onChange: function(v){ set({ heading: v }); } } )
					)
				),

				el( 'div', { style: { background: '#fff', padding: '2.5rem 2rem', borderTop: '1px solid #eee', borderBottom: '1px solid #eee' } },
					a.heading ? el( 'div', { style: { marginBottom: '1.5rem', textAlign: 'center' } },
						hint( 'Heading', 'light' ),
						el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
							style: inputOn('light', { fontSize: '1rem', fontWeight: '700', textAlign: 'center', textTransform: 'uppercase', letterSpacing: '0.2em', color: 'rgba(26,26,26,0.45)' } ),
						} )
					) : null,

					el( 'div', { style: { display: 'flex', flexWrap: 'wrap', gap: '16px', justifyContent: 'center', alignItems: 'center', marginBottom: '12px' } },
						canvasRepeater( a.logos, set, 'logos',
							{ imageUrl: '', imageId: 0, imageAlt: '', link: '' },
							function( item, upd, rem, idx, batch ) {
								return el( 'div', { style: { textAlign: 'center', width: '120px' } },
									el( MediaUploadCheck, {},
										el( MediaUpload, {
											onSelect: function(m){ batch({ imageUrl: m.url, imageId: m.id, imageAlt: m.alt || '' }); },
											allowedTypes: ['image'], value: item.imageId || 0,
											render: function( ref ) {
												return item.imageUrl
													? el( 'div', { style: { position: 'relative', marginBottom: '4px' } },
														el( 'img', { src: item.imageUrl, alt: item.imageAlt || '', style: { height: '48px', width: 'auto', maxWidth: '100%', objectFit: 'contain', display: 'block', margin: '0 auto', opacity: 0.6 } } ),
														el( 'button', { onClick: ref.open, style: { fontSize: '9px', padding: '2px 6px', background: 'rgba(0,0,0,.45)', color: '#fff', border: 'none', cursor: 'pointer', borderRadius: '2px', display: 'block', margin: '4px auto 0' } }, __( 'Vervangen', 'dks-theme' ) )
													)
													: el( 'div', { onClick: ref.open, style: { height: '48px', background: '#f0f0f0', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '10px', color: '#aaa', cursor: 'pointer', marginBottom: '4px' } }, '+' );
											}
										} )
									),
									el( 'input', { type: 'text', value: item.link || '', placeholder: 'Link (optioneel)', onChange: function(e){ upd('link',e.target.value); },
										style: inputOn('light', { fontSize: '9px', textAlign: 'center', color: '#aaa' }) } ),
									remBtn( rem )
								);
							}
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 9. dks/process — vertical timeline
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/process', {
		title:    __( 'DKS Proces', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'arrow-down-alt',
		supports: { anchor: true },
		attributes: {
			eyebrow: { type: 'string', default: 'Next Steps' },
			heading: { type: 'string', default: 'How We Work' },
			intro:   { type: 'string', default: '' },
			steps:   { type: 'array',  default: [
				{ title: 'Consultation', desc: 'We start with a free, no-obligation consultation.' },
				{ title: 'Analysis',     desc: 'We analyse the market and tailor a strategy for you.' },
				{ title: 'Execution',    desc: 'We guide you through every step until closing.' },
			] },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Sectie-instellingen', initialOpen: true },
						el( TextControl, { label: 'Eyebrow', value: a.eyebrow, onChange: function(v){ set({ eyebrow: v }); } } ),
						el( TextControl, { label: 'Titel',   value: a.heading, onChange: function(v){ set({ heading: v }); } } ),
						el( TextControl, { label: 'Intro',   value: a.intro,   onChange: function(v){ set({ intro: v }); } } )
					)
				),

				el( 'div', { style: { background: C.light, padding: '3rem 2rem' } },
					eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, 'light' ),
					el( 'div', { style: { marginBottom: a.intro ? '8px' : '2rem' } },
						hint( 'Titel', 'light' ),
						el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
							style: inputOn('light', { fontSize: '1.75rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: C.dark }) } )
					),
					a.intro ? el( 'div', { style: { marginBottom: '2rem' } },
						el( 'textarea', { value: a.intro, onChange: function(e){ set({ intro: e.target.value }); }, style: taOn('light') } )
					) : null,

					el( 'div', { style: { maxWidth: '640px', marginBottom: '12px' } },
						canvasRepeater( a.steps, set, 'steps',
							{ title: '', desc: '' },
							function( item, upd, rem, idx ) {
								return el( 'div', { style: { display: 'flex', gap: '16px', marginBottom: '20px', padding: '16px', background: '#fff', border: '1px solid #e4e4e0' } },
									el( 'div', { style: { width: '36px', height: '36px', borderRadius: '50%', background: C.accent, color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: '900', fontSize: '13px', flexShrink: 0 } },
										String( idx + 1 )
									),
									el( 'div', { style: { flex: 1, minWidth: 0 } },
										hint( 'Staptitel', 'light' ),
										el( 'input', { type: 'text', value: item.title || '', onChange: function(e){ upd('title',e.target.value); }, style: inputOn('light', { fontWeight: '700', marginBottom: '6px' }) } ),
										hint( 'Beschrijving', 'light' ),
										el( 'textarea', { value: item.desc || '', onChange: function(e){ upd('desc',e.target.value); }, style: taOn('light', { fontSize: '0.875rem', color: 'rgba(26,26,26,0.7)' }) } ),
										remBtn( rem )
									)
								);
							}
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 10. dks/cta — call-to-action section (dark / accent / light)
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/cta', {
		title:    __( 'DKS CTA', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'megaphone',
		supports: { anchor: true, align: [ 'wide', 'full' ] },
		attributes: {
			eyebrow:        { type: 'string', default: '' },
			heading:        { type: 'string', default: 'Ready to Find Your Dream Home?' },
			subheading:     { type: 'string', default: 'Get in touch today and let our experts guide you.' },
			btnPrimaryText: { type: 'string', default: 'Contact Us' },
			btnPrimaryUrl:  { type: 'string', default: '#' },
			btnSecondText:  { type: 'string', default: '' },
			btnSecondUrl:   { type: 'string', default: '#' },
			variant:        { type: 'string', default: 'dark' },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			var bg  = a.variant === 'accent' ? C.accent : ( a.variant === 'light' ? C.light : C.dark );
			var bg2 = a.variant === 'light'  ? 'light'  : 'dark';
			var fg  = a.variant === 'light'  ? C.dark   : '#fff';
			var btnBg  = a.variant === 'light' ? C.accent : '#fff';
			var btnFg  = a.variant === 'light' ? '#fff'   : ( a.variant === 'accent' ? C.accent : C.dark );

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'CTA-instellingen', initialOpen: true },
						el( SelectControl, {
							label: 'Stijl',
							value: a.variant,
							options: [
								{ label: 'Donker (zwart)',  value: 'dark' },
								{ label: 'Accent (oranje)', value: 'accent' },
								{ label: 'Licht',          value: 'light' },
							],
							onChange: function(v){ set({ variant: v }); },
						} ),
						el( TextControl, { label: 'Knop 1 URL', value: a.btnPrimaryUrl, onChange: function(v){ set({ btnPrimaryUrl: v }); } } ),
						el( TextControl, { label: 'Knop 2 URL', value: a.btnSecondUrl,  onChange: function(v){ set({ btnSecondUrl: v }); } } )
					)
				),

				el( 'div', { style: { background: bg, padding: '4rem 2rem', textAlign: 'center', color: fg } },
					a.eyebrow ? eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, bg2 ) : el( 'div', { style: { marginBottom: '6px' } },
						hint( 'Eyebrow (optioneel)', bg2 ),
						el( 'input', { type: 'text', value: a.eyebrow || '', placeholder: 'Eyebrow tekst…', onChange: function(e){ set({ eyebrow: e.target.value }); }, style: inputOn(bg2, { fontSize: '10px', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.35em', color: a.variant === 'light' ? C.accent : 'rgba(255,255,255,0.6)', textAlign: 'center' }) } )
					),
					el( 'div', { style: { marginBottom: '10px' } },
						hint( 'Koptekst', bg2 ),
						el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
							style: inputOn(bg2, { fontSize: '2rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: fg, textAlign: 'center' }),
						} )
					),
					el( 'div', { style: { marginBottom: '24px' } },
						hint( 'Subtekst', bg2 ),
						el( 'input', { type: 'text', value: a.subheading, onChange: function(e){ set({ subheading: e.target.value }); },
							style: inputOn(bg2, { fontSize: '1rem', color: a.variant === 'light' ? 'rgba(26,26,26,0.7)' : 'rgba(255,255,255,0.75)', textAlign: 'center' }),
						} )
					),
					el( 'div', { style: { display: 'flex', justifyContent: 'center', gap: '12px', flexWrap: 'wrap' } },
						el( 'input', { type: 'text', value: a.btnPrimaryText, onChange: function(e){ set({ btnPrimaryText: e.target.value }); },
							style: { background: btnBg, color: btnFg, border: 'none', padding: '12px 28px', fontFamily: 'inherit', fontWeight: '700', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.1em', outline: 'none', cursor: 'text', minWidth: '120px' },
						} ),
						a.btnSecondText
							? el( 'input', { type: 'text', value: a.btnSecondText, onChange: function(e){ set({ btnSecondText: e.target.value }); },
								style: { background: 'transparent', color: fg, border: '2px solid ' + ( a.variant === 'light' ? 'rgba(26,26,26,0.35)' : 'rgba(255,255,255,0.55)' ), padding: '12px 28px', fontFamily: 'inherit', fontWeight: '700', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.1em', outline: 'none', cursor: 'text', minWidth: '120px' },
							} )
							: el( 'button', { type: 'button', onClick: function(){ set({ btnSecondText: 'Meer info' }); }, style: { background: 'none', border: '1px dashed rgba(255,255,255,0.3)', color: 'rgba(255,255,255,0.4)', padding: '12px 20px', cursor: 'pointer', fontSize: '10px', fontFamily: 'inherit' } }, '+ Tweede knop' )
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 11. dks/bonus — benefit items
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/bonus', {
		title:    __( 'DKS Bonus Voordelen', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'awards',
		supports: { anchor: true },
		attributes: {
			eyebrow: { type: 'string', default: 'Included' },
			heading: { type: 'string', default: 'Extra Benefits' },
			intro:   { type: 'string', default: '' },
			items:   { type: 'array',  default: [
				{ icon: 'star',     title: 'Free Valuation',    desc: 'Professional property valuation at no cost.' },
				{ icon: 'verified', title: 'Legal Support',     desc: 'Full guidance through all legal requirements.' },
				{ icon: 'home',     title: 'After-Sale Service', desc: 'We remain available after the deal is closed.' },
			] },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Sectie-instellingen', initialOpen: true },
						el( TextControl, { label: 'Eyebrow', value: a.eyebrow, onChange: function(v){ set({ eyebrow: v }); } } ),
						el( TextControl, { label: 'Titel',   value: a.heading, onChange: function(v){ set({ heading: v }); } } ),
						el( TextControl, { label: 'Intro',   value: a.intro,   onChange: function(v){ set({ intro: v }); } } )
					)
				),

				el( 'div', { style: { background: C.light, padding: '3rem 2rem' } },
					eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, 'light' ),
					el( 'div', { style: { marginBottom: '2rem' } },
						hint( 'Titel', 'light' ),
						el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
							style: inputOn('light', { fontSize: '1.75rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: C.dark }) } )
					),

					el( 'div', { style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px,1fr))', gap: '14px', marginBottom: '12px' } },
						canvasRepeater( a.items, set, 'items',
							{ icon: 'star', title: '', desc: '' },
							function( item, upd, rem, idx, batch ) {
								return el( 'div', { style: { background: '#fff', border: '1px solid #e4e4e0', padding: '18px 14px' } },
									el( 'div', { style: { display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '10px' } },
										el( 'div', { style: { width: '36px', height: '36px', background: 'rgba(184,92,56,0.1)', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '12px', color: C.accent, fontWeight: '700', flexShrink: 0 } }, item.icon ? item.icon.charAt(0).toUpperCase() : '★' ),
										el( 'div', { style: { flex: 1, minWidth: 0 } },
											hint( 'icoon (home/star/verified)', 'light' ),
											el( 'input', { type: 'text', value: item.icon || '', placeholder: 'star', onChange: function(e){ upd('icon',e.target.value); }, style: inputOn('light', { fontSize: '10px' }) } )
										)
									),
									hint( 'Titel', 'light' ),
									el( 'input', { type: 'text', value: item.title || '', onChange: function(e){ upd('title',e.target.value); }, style: inputOn('light', { fontWeight: '700', marginBottom: '6px' }) } ),
									hint( 'Beschrijving', 'light' ),
									el( 'textarea', { value: item.desc || '', onChange: function(e){ upd('desc',e.target.value); }, style: taOn('light', { fontSize: '0.875rem', color: 'rgba(26,26,26,0.7)' }) } ),
									remBtn( rem )
								);
							}
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

	/* ─────────────────────────────────────────────────────────────────────
	 * 12. dks/faq — accordion
	 * ─────────────────────────────────────────────────────────────────────*/
	registerBlockType( 'dks/faq', {
		title:    __( 'DKS FAQ', 'dks-theme' ),
		category: 'dks-blocks',
		icon:     'info',
		supports: { anchor: true },
		attributes: {
			eyebrow: { type: 'string', default: 'FAQ' },
			heading: { type: 'string', default: 'Frequently Asked Questions' },
			intro:   { type: 'string', default: '' },
			items:   { type: 'array',  default: [
				{ question: 'What services do you offer?',              answer: 'We offer a full range of real estate services including buying, selling, and renting.' },
				{ question: 'How long does it take to buy a property?', answer: 'Typically 2–4 months from initial search to handover of keys.' },
			] },
		},

		edit: function ( props ) {
			var a   = props.attributes;
			var set = props.setAttributes;

			return el( Fragment, {},

				el( InspectorControls, {},
					el( PanelBody, { title: 'Sectie-instellingen', initialOpen: true },
						el( TextControl, { label: 'Eyebrow', value: a.eyebrow, onChange: function(v){ set({ eyebrow: v }); } } ),
						el( TextControl, { label: 'Titel',   value: a.heading, onChange: function(v){ set({ heading: v }); } } ),
						el( TextControl, { label: 'Intro',   value: a.intro,   onChange: function(v){ set({ intro: v }); } } )
					)
				),

				el( 'div', { style: { background: C.light, padding: '3rem 2rem' } },
					eyebrowInput( a.eyebrow, function(v){ set({ eyebrow: v }); }, 'light' ),
					el( 'div', { style: { marginBottom: '2rem' } },
						hint( 'Titel', 'light' ),
						el( 'input', { type: 'text', value: a.heading, onChange: function(e){ set({ heading: e.target.value }); },
							style: inputOn('light', { fontSize: '1.75rem', fontWeight: '800', textTransform: 'uppercase', letterSpacing: '-0.02em', color: C.dark }) } )
					),

					el( 'div', { style: { maxWidth: '760px', marginBottom: '12px' } },
						canvasRepeater( a.items, set, 'items',
							{ question: '', answer: '' },
							function( item, upd, rem, idx, batch ) {
								return el( 'div', { style: { borderBottom: '1px solid #e0e0da', padding: '16px 0' } },
									el( 'div', { style: { display: 'flex', alignItems: 'flex-start', gap: '10px', marginBottom: '8px' } },
										el( 'div', { style: { color: C.accent, fontSize: '16px', lineHeight: 1, marginTop: '2px', flexShrink: 0 } }, '▶' ),
										el( 'div', { style: { flex: 1, minWidth: 0 } },
											hint( 'Vraag', 'light' ),
											el( 'input', { type: 'text', value: item.question || '', onChange: function(e){ upd('question',e.target.value); },
												style: inputOn('light', { fontWeight: '700', fontSize: '1rem' }) } )
										)
									),
									el( 'div', { style: { paddingLeft: '26px' } },
										hint( 'Antwoord', 'light' ),
										el( 'textarea', { value: item.answer || '', onChange: function(e){ upd('answer',e.target.value); },
											style: taOn('light', { fontSize: '0.9rem', color: 'rgba(26,26,26,0.75)', lineHeight: '1.7' }) } )
									),
									el( 'div', { style: { paddingLeft: '26px' } }, remBtn( rem ) )
								);
							}
						)
					)
				)
			);
		},

		save: function () { return null; },
	} );

} )(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.element,
	window.wp.i18n
);
