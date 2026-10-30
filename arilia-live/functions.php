<?php
/** Arilia block theme bootstrap. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_theme_file_path( 'inc/content.php' );
require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/audience.php' );
require_once get_theme_file_path( 'inc/about.php' );

function arilia_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/site.css' );
    add_editor_style( 'assets/refinement.css' );
    load_theme_textdomain( 'arilia', get_theme_file_path( 'languages' ) );
}
add_action( 'after_setup_theme', 'arilia_theme_setup' );

function arilia_enqueue_assets() {
    $file = get_theme_file_path( 'assets/site.css' );
    wp_enqueue_style( 'arilia-site', get_theme_file_uri( 'assets/site.css' ), array(), (string) filemtime( $file ) );
    wp_enqueue_style( 'arilia-refinement', get_theme_file_uri( 'assets/refinement.css' ), array( 'arilia-site' ), (string) filemtime( get_theme_file_path( 'assets/refinement.css' ) ) );
}
add_action( 'wp_enqueue_scripts', 'arilia_enqueue_assets' );

function arilia_patterns() {
    register_block_pattern_category( 'arilia', array( 'label' => __( 'Arilia pages', 'arilia' ) ) );
    foreach ( array( 'home' => 'Home', 'products' => 'Products', 'our-story' => 'Our Story', 'contact' => 'Contact & Distribution' ) as $key => $title ) {
        register_block_pattern( 'arilia/' . $key, array(
            'title' => 'Arilia — ' . $title, 'categories' => array( 'arilia' ),
            'content' => arilia_page_content( $key ), 'description' => __( 'Editable Arilia page built from core WordPress blocks.', 'arilia' ),
        ) );
    }
    register_block_pattern( 'arilia/header', array( 'title' => 'Arilia Header', 'content' => arilia_header_content(), 'inserter' => false ) );
    register_block_pattern( 'arilia/footer', array( 'title' => 'Arilia Footer', 'content' => arilia_footer_content(), 'inserter' => false ) );
    foreach ( arilia_products() as $slug => $product ) {
        register_block_style( 'core/image', array( 'name' => 'arilia-bottle-' . $slug, 'label' => 'Arilia bottle: ' . $product['name'] ) );
    }
}
add_action( 'init', 'arilia_patterns', 20 );

function arilia_meta_description() {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) { return; }
    if ( ! is_singular() ) { return; }
    $description = get_the_excerpt( get_queried_object_id() );
    if ( ! $description ) { $description = 'Discover Arilia’s colourful 250 ml collection, signature double-bulb bottle, and fruit flavour pairings.'; }
    echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '">' . "\n";
}
add_action( 'wp_head', 'arilia_meta_description', 5 );

/** Scope story layout to the existing page, without replacing its saved content. */
function arilia_story_body_class( $classes ) {
    if ( is_page( 'our-story' ) ) { $classes[] = 'arilia-story-page'; }
    return $classes;
}
add_filter( 'body_class', 'arilia_story_body_class' );

/** Update the brand line in previously saved header blocks as well as defaults. */
function arilia_brand_line_render( $html, $block ) {
    $classes = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
    if ( in_array( 'arilia-brand-line', preg_split( '/\s+/', $classes ), true ) ) {
        return ''; // Tagline is included in the supplied logo artwork.
    }
    return $html;
}
add_filter( 'render_block_core/paragraph', 'arilia_brand_line_render', 10, 2 );

/** Use the supplied brand artwork in default and previously saved wordmarks. */
function arilia_logo_markup() {
    return '<a class="arilia-logo-link" href="' . esc_url( home_url( '/' ) ) . '"><img class="arilia-logo-image" src="' . esc_url( get_theme_file_uri( 'assets/arilia-logo-v124.webp' ) ) . '" width="1536" height="1024" alt="Arilia — A Little Wonder. Home" decoding="async" /></a>';
}
function arilia_logo_render( $html, $block ) {
    $classes = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
    if ( in_array( 'arilia-wordmark', preg_split( '/\s+/', $classes ), true ) ) {
        return '<p class="arilia-wordmark">' . arilia_logo_markup() . '</p>';
    }
    return $html;
}
add_filter( 'render_block_core/paragraph', 'arilia_logo_render', 11, 2 );

/** Refresh existing saved image blocks without rewriting page content. */
function arilia_refresh_bottle_images( $html, $block ) {
    if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) { return $html; }
    $classes = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
    $file = '';
    if ( preg_match( '/(?:^|\s)arilia-hero-image(?:\s|$)/', $classes ) ) { $file = 'pomegranate.webp'; }
    elseif ( preg_match( '/(?:^|\s)arilia-story-image(?:\s|$)/', $classes ) ) { $file = 'pomegranate-apple.webp'; }
    elseif ( preg_match( '/(?:^|\s)is-style-arilia-bottle-([a-z-]+)(?:\s|$)/', $classes, $bottle ) && file_exists( get_theme_file_path( 'assets/bottle-' . $bottle[1] . '-v126.webp' ) ) ) {
        // 1.20: whole-bottle cut-out instead of a masked crop of the group photo.
        $sizes = array( 'pomegranate' => array( 293, 560 ), 'pomegranate-apple' => array( 292, 560 ), 'beet-lime-ginger' => array( 198, 560 ), 'mango' => array( 197, 560 ), 'cranberry' => array( 198, 560 ), 'strawberry' => array( 198, 560 ), 'dragon-fruit' => array( 198, 560 ) );
        $dim = array( 1024, 1536 );
        $tags = new WP_HTML_Tag_Processor( $html );
        while ( $tags->next_tag( 'FIGURE' ) ) { $tags->add_class( 'arilia-bottle-whole' ); break; }
        $tags = new WP_HTML_Tag_Processor( $tags->get_updated_html() );
        while ( $tags->next_tag( 'IMG' ) ) {
            $tags->set_attribute( 'src', arilia_asset( 'bottle-' . $bottle[1] . '-v126.webp' ) );
            foreach ( array( 'srcset', 'sizes', 'data-src', 'data-srcset', 'data-lazy-src', 'data-lazy-srcset' ) as $attribute ) { $tags->remove_attribute( $attribute ); }
            $tags->set_attribute( 'width', (string) $dim[0] ); $tags->set_attribute( 'height', (string) $dim[1] );
        }
        return $tags->get_updated_html();
    }
    elseif ( preg_match( '/(?:^|\s)arilia-lineup(?:\s|$)/', $classes ) ) { $file = 'collection.webp'; }
    $tags = new WP_HTML_Tag_Processor( $html );
    while ( $tags->next_tag( 'IMG' ) ) {
        $target = $file;
        $src = $tags->get_attribute( 'src' );
        if ( ! $target && is_string( $src ) && preg_match( '~/themes/arilia/assets/(pomegranate|pomegranate-apple|collection)(?:-v[0-9]+)?\.webp(?:[?#]|$)~', $src, $match ) ) {
            $target = $match[1] . '.webp';
        }
        if ( ! $target ) { continue; }
        $tags->set_attribute( 'src', arilia_asset( $target ) );
        foreach ( array( 'srcset', 'sizes', 'data-src', 'data-srcset', 'data-lazy-src', 'data-lazy-srcset' ) as $attribute ) { $tags->remove_attribute( $attribute ); }
        $tags->set_attribute( 'width', 'collection.webp' === $target ? '1900' : '1024' );
        $tags->set_attribute( 'height', 'collection.webp' === $target ? '828' : '1536' );
    }
    return $tags->get_updated_html();
}
add_filter( 'render_block_core/image', 'arilia_refresh_bottle_images', 20, 2 );

/** Display approved campaign posters in existing page hero blocks. The homepage is a 3-second slider. */
function arilia_poster_picture( $desktop, $portrait, $alt, $w, $h, $eager = false ) {
    $portrait_size = getimagesize( get_theme_file_path( 'assets/' . $portrait ) );
    $pw = $portrait_size ? (int) $portrait_size[0] : 941;
    $ph = $portrait_size ? (int) $portrait_size[1] : 1672;
    return '<picture><source media="(max-width: 1023px)" srcset="' . esc_url( arilia_asset( $portrait ) ) . '" width="' . $pw . '" height="' . $ph . '"><img src="' . esc_url( arilia_asset( $desktop ) ) . '" width="' . $w . '" height="' . $h . '" alt="' . esc_attr( $alt ) . '" ' . ( $eager ? 'fetchpriority="high"' : 'loading="lazy"' ) . ' decoding="async" /></picture>';
}
function arilia_home_slides() {
    return array(
        array( 'hero-poster-v123.webp', 'hero-pomegranate-portrait-v127.webp', 'A little wonder. A bold first sip. Discover Arilia Pomegranate Drink.', 'pomegranate' ),
        array( 'hero-beet-v123.webp', 'hero-beet-portrait-v123.webp', 'A little wonder. A bold first sip. Discover Arilia Beet + Lemon + Ginger Drink.', 'beet-lime-ginger' ),
        array( 'hero-cranberry-v123.webp', 'hero-cranberry-portrait-v123.webp', 'A little tart. A beautiful twist. Arilia Cranberry Drink, 250 ml. Discover Cranberry.', 'cranberry' ),
        array( 'hero-strawberry-v123.webp', 'hero-strawberry-portrait-v123.webp', 'Little berry. Beautiful moment. Arilia Strawberry Drink, 250 ml. Discover Strawberry.', 'strawberry' ),
        array( 'hero-dragon-fruit-v123.webp', 'hero-dragon-fruit-portrait-v123.webp', 'A little unexpected. A lot to discover. Arilia Dragon Fruit Drink, 250 ml. Discover Dragon Fruit.', 'dragon-fruit' ),
        array( 'hero-mango-v123.webp', 'hero-mango-portrait-v123.webp', 'Golden by nature. Wonderfully mango. Arilia Mango Drink, 250 ml. Discover Mango.', 'mango' ),
    );
}
function arilia_campaign_poster_render( $html, $block ) {
    $classes = preg_split( '/\s+/', isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '' );
    $home = in_array( 'arilia-hero', $classes, true );
    $flavours = in_array( 'arilia-products-hero', $classes, true ) && is_page( array( 'products', 'flavours', 'flavors' ) );
    if ( ! $home && ! $flavours ) { return $html; }
    if ( $flavours ) {
        $img = arilia_poster_picture( 'flavours-poster-v126.webp', 'flavours-portrait-v127.webp', 'Seven Arilia flavours: Pomegranate, Pomegranate Apple, Beet Lemon Ginger, Mango, Cranberry, Strawberry and Dragon Fruit.', 1900, 828, true );
        return '<section class="arilia-campaign-poster"><h1 class="screen-reader-text">Seven flavours. Endless little wonders.</h1>' . $img . '</section>';
    }
    $slides = ''; $dots = '';
    foreach ( arilia_home_slides() as $i => $s ) {
        $slides .= '<a class="arilia-slide' . ( 0 === $i ? ' is-active' : '' ) . '" href="' . esc_url( arilia_url( $s[3] ) ) . '" aria-label="' . esc_attr( $s[2] ) . '"' . ( 0 === $i ? '' : ' aria-hidden="true" tabindex="-1"' ) . '>' . arilia_poster_picture( $s[0], $s[1], $s[2], 1983, 793, 0 === $i ) . '</a>';
        $dots   .= '<button type="button" class="arilia-slider__dot' . ( 0 === $i ? ' is-active' : '' ) . '" data-slide="' . $i . '" aria-label="' . esc_attr( 'Show slide ' . ( $i + 1 ) ) . '"' . ( 0 === $i ? ' aria-current="true"' : '' ) . '></button>';
    }
    return '<section class="arilia-campaign-poster arilia-slider" data-interval="3000" aria-roledescription="carousel" aria-label="Arilia campaigns"><h1 class="screen-reader-text">A little wonder. A bold first sip.</h1><div class="arilia-slider__track">' . $slides . '</div><button type="button" class="arilia-slider__arrow arilia-slider__arrow--prev" data-dir="-1" aria-label="Previous slide"><svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><button type="button" class="arilia-slider__arrow arilia-slider__arrow--next" data-dir="1" aria-label="Next slide"><svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><div class="arilia-slider__dots" role="group" aria-label="Choose slide">' . $dots . '</div></section>';
}
add_filter( 'render_block_core/group', 'arilia_campaign_poster_render', 20, 2 );

function arilia_enqueue_slider() {
    if ( ! is_front_page() ) { return; }
    wp_enqueue_script( 'arilia-slider', get_theme_file_uri( 'assets/slider.js' ), array(), (string) filemtime( get_theme_file_path( 'assets/slider.js' ) ), array( 'strategy' => 'defer', 'in_footer' => true ) );
}
add_action( 'wp_enqueue_scripts', 'arilia_enqueue_slider' );

/** Headings keep a space before each <br>, so hiding the break on phones never glues words together. */
function arilia_heading_break_space( $html, $block ) {
    return preg_replace( '/\s*<br\s*\/?>/i', ' <br>', $html );
}
add_filter( 'render_block_core/heading', 'arilia_heading_break_space', 5, 2 );

/** 1.19: the homepage lineup section ("Seven ways to find your wonderful") duplicates the audience section, so it is not shown. */
function arilia_hide_home_lineup( $html, $block ) {
    if ( ! is_front_page() || is_admin() ) { return $html; }
    $classes = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
    if ( false !== strpos( $classes, 'arilia-centred' ) && false !== strpos( $html, 'arilia-lineup' ) ) { return ''; }
    return $html;
}
add_filter( 'render_block_core/group', 'arilia_hide_home_lineup', 15, 2 );

function arilia_refresh_v124_images( $html ) {
 $map = array( 'arilia-logo-v16.webp' => 'arilia-logo-v124.webp', 'audience-seniors-v120.webp' => 'audience-seniors-v124.webp', 'audience-women-v120.webp' => 'audience-women-v124.webp', 'audience-kids-v120.webp' => 'audience-kids-v124.webp', 'audience-energy-v120.webp' => 'audience-energy-v124.webp', 'pomegranate-v19.webp' => 'pomegranate-v124.webp', 'pomegranate-apple-v19.webp' => 'pomegranate-apple-v124.webp', 'bottle-pomegranate-v114.webp' => 'bottle-pomegranate-v126.webp', 'bottle-pomegranate-apple-v114.webp' => 'bottle-pomegranate-apple-v126.webp', 'bottle-cranberry-v114.webp' => 'bottle-cranberry-v126.webp', 'bottle-dragon-fruit-v114.webp' => 'bottle-dragon-fruit-v126.webp', 'bottle-strawberry-v114.webp' => 'bottle-strawberry-v126.webp', 'bottle-beet-lime-ginger-v114.webp' => 'bottle-beet-lime-ginger-v126.webp', 'bottle-mango-v114.webp' => 'bottle-mango-v126.webp' );

 foreach ( array( 'pomegranate', 'pomegranate-apple', 'cranberry', 'dragon-fruit', 'strawberry', 'beet-lime-ginger', 'mango' ) as $slug ) {
  $map['bottle-' . $slug . '-v124.webp'] = 'bottle-' . $slug . '-v126.webp';
 }
 $map['pomegranate-v124.webp'] = 'bottle-pomegranate-v126.webp';
 $map['pomegranate-apple-v124.webp'] = 'bottle-pomegranate-apple-v126.webp';
 $map['pomegranate-v19.webp'] = 'bottle-pomegranate-v126.webp';
 $map['pomegranate-apple-v19.webp'] = 'bottle-pomegranate-apple-v126.webp';
 $map['collection-v19.webp'] = 'flavours-poster-v126.webp';
 $tags = new WP_HTML_Tag_Processor( $html );
 while ( $tags->next_tag( 'IMG' ) ) {
  $src = $tags->get_attribute( 'src' );
  if ( ! is_string( $src ) ) { continue; }
  $name = basename( (string) parse_url( $src, PHP_URL_PATH ) );
  if ( ! isset( $map[$name] ) ) { continue; }
  $tags->set_attribute( 'src', arilia_asset( $map[$name] ) );
  foreach ( array('srcset','sizes','data-src','data-srcset','data-lazy-src','data-lazy-srcset') as $attr ) { $tags->remove_attribute( $attr ); }
  $tags->remove_attribute('width'); $tags->remove_attribute('height');
 }
 return $tags->get_updated_html();
}
add_filter( 'render_block', 'arilia_refresh_v124_images', 40 );
