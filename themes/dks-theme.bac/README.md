# DKS Real Estate — WordPress Theme

**Version:** 1.0.0  
**Requires WordPress:** 6.3+  
**Requires PHP:** 8.1+  
**Text Domain:** `dks-theme`  
**License:** GPL-2.0-or-later

---

## Bestandsstructuur

```
dks-theme/
├── style.css                   ← Thema-header + volledige front-end stijlen
├── functions.php               ← Bootstrap (laadt /inc/)
├── header.php                  ← Sticky header, logo, wp_nav_menu
├── footer.php                  ← Footer grid, contact, navigatie
├── front-page.php              ← Homepage (content via Gutenberg blocks)
├── page.php                    ← Standaard pagina template
├── single.php                  ← Enkelvoudig bericht
├── archive.php                 ← Categorie / tag / auteur archief
├── index.php                   ← Blogoverzicht (fallback)
│
├── inc/
│   ├── setup.php               ← Theme supports, menus, image sizes
│   ├── enqueue.php             ← Scripts & styles (incl. child-theme support)
│   ├── blocks.php              ← Block registratie + PHP render callbacks
│   ├── block-filters.php       ← Block patterns (homepage, hero-only)
│   ├── meta-boxes.php          ← Property metabox (prijs, locatie, etc.)
│   ├── template-tags.php       ← Helper functies (posted_on, thumbnail …)
│   ├── widgets.php             ← Sidebar / footer widget areas
│   └── listings-hooks.php      ← Documentatie + child-theme integratiepunten
│
├── template-parts/
│   ├── content.php             ← Post card (blog loop)
│   ├── content-page.php        ← Pagina content
│   └── content-none.php        ← "Geen resultaten"
│
├── assets/
│   ├── css/
│   │   ├── editor-style.css    ← Gutenberg editor stijlen
│   │   └── post-card.css       ← Post card + paginering stijlen (merge in style.css)
│   ├── js/
│   │   ├── main.js             ← Mobiel menu, parallax, scroll-reveal, newsletter
│   │   └── blocks.js           ← Gutenberg block editor controls
│   └── images/                 ← Placeholder property afbeeldingen (zelf toevoegen)
│
├── languages/
│   └── dks-theme.pot           ← Vertaaltemplate voor alle strings
│
└── screenshot.png              ← 1200×900 thema screenshot (zelf toevoegen)
```

---

## Installatie

1. Kopieer de map `dks-theme/` naar `wp-content/themes/`.
2. Activeer het thema via **Weergave → Thema's**.
3. Maak een statische homepage aan: **Instellingen → Lezen → Statische pagina**.
4. Voeg het `dks/hero` block toe aan de homepage via de Gutenberg editor.
5. Wijs navigatiemenus toe via **Weergave → Menu's**:
   - *Primary Navigation* (hoofdmenu)
   - *Footer Navigation* (footermenu)
6. Stel contactgegevens in via **Weergave → Aanpassen**:
   - `dks_contact_address`, `dks_contact_phone`, `dks_contact_email`
   - `dks_hours_weekdays`, `dks_hours_saturday`

---

## Gutenberg Blocks

### `dks/hero`
Volledige-breedte hero-sectie. Werkt op **elke pagina**, niet alleen de homepage.

**Instellingen in de editor (Inspector):**
- Achtergrondafbeelding selecteren / vervangen
- Overlay-dekking (%)
- Minimale hoogte (CSS waarde)
- Koptekst (HTML toegestaan voor `<span class="accent">`)
- Subtekst
- Twee CTA-knoppen (tekst + URL)

### `dks/listings`
Premium listings grid. Toont standaard HTML-placeholder kaarten.

**Uitbreiden via child theme:**
```php
add_filter( 'dks_listings_items', function( $items, $attrs ) {
    // Retourneer array van property-objecten uit WP_Query
    return $mijn_aanbod;
}, 10, 2 );
```

### `dks/features`
"Why Choose DKS" sectie met icon-items.

### `dks/newsletter`
E-mail subscribe sectie. Swappable met plugin-shortcode:
```php
add_filter( 'dks_newsletter_form_html', function() {
    return do_shortcode( '[mailchimp_form]' );
} );
```

---

## Child Theme

Maak een child theme met minimaal twee bestanden:

**`dks-child/style.css`**
```css
/*
Theme Name:   DKS Child
Template:     dks-theme
Text Domain:  dks-child
*/
```

**`dks-child/functions.php`**
```php
<?php
// Eigen CSS laden
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'dks-child-style',
        get_stylesheet_uri(),
        [ 'dks-style' ],
        '1.0.0'
    );
} );
```

Elke `if ( ! function_exists() )` bewaker in het parent theme maakt het mogelijk om functies te overschrijven door ze eerder te declareren.

---

## Meertaligheid

Alle strings gebruiken `__( 'tekst', 'dks-theme' )`.

**Vertaling aanmaken met WP-CLI:**
```bash
wp i18n make-pot . languages/dks-theme.pot
```

**Nederlandse vertaling activeren:**
1. Kopieer `dks-theme.pot` naar `dks-theme-nl_NL.po`
2. Vertaal met Poedit
3. Compileer naar `dks-theme-nl_NL.mo`
4. Stel site-taal in op Nederlands via **Instellingen → Algemeen**

---

## Property Meta Velden

Elke pagina (en optioneel een `property` CPT via child theme) heeft een sidebar-metabox **DKS Property Details** met:

| Veld | Meta key | Type |
|------|----------|------|
| Prijs | `_dks_price` | string |
| Locatie | `_dks_location` | string |
| Slaapkamers | `_dks_beds` | integer |
| Badkamers | `_dks_baths` | integer |
| Oppervlakte | `_dks_sqm` | string |
| Badge | `_dks_badge` | string |
| Status | `_dks_status` | string (available/sold/rented) |

Velden zijn ook beschikbaar via de REST API (`show_in_rest: true`).

---

## Child Theme: Property CPT toevoegen

```php
// child theme functions.php

// 1. Registreer het CPT
add_action( 'init', function() {
    register_post_type( 'property', [
        'label'       => __( 'Properties', 'dks-child' ),
        'public'      => true,
        'has_archive' => true,
        'supports'    => [ 'title', 'editor', 'thumbnail' ],
        'show_in_rest'=> true,
        'rewrite'     => [ 'slug' => 'properties' ],
    ] );
} );

// 2. Voeg CPT toe aan de property meta boxes
add_filter( 'dks_property_post_types', function( $types ) {
    $types[] = 'property';
    return $types;
} );

// 3. Laad echte listings in het dks/listings block
add_filter( 'dks_listings_items', function( $default, $attrs ) {
    $q = new WP_Query([
        'post_type'      => 'property',
        'posts_per_page' => $attrs['columns'] ?? 3,
    ]);
    $items = [];
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            $id = get_the_ID();
            $items[] = [
                'image'     => get_the_post_thumbnail_url( $id, 'dks-property-card' ),
                'badge'     => get_post_meta( $id, '_dks_badge',    true ),
                'price'     => get_post_meta( $id, '_dks_price',    true ),
                'location'  => get_post_meta( $id, '_dks_location', true ),
                'beds'      => (int) get_post_meta( $id, '_dks_beds', true ),
                'baths'     => (int) get_post_meta( $id, '_dks_baths', true ),
                'sqm'       => get_post_meta( $id, '_dks_sqm',  true ),
                'permalink' => get_permalink(),
            ];
        }
        wp_reset_postdata();
    }
    return $items;
}, 10, 2 );
```
