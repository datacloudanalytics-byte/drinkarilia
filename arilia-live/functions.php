<?php
/** Arilia block theme bootstrap. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_theme_file_path( 'inc/content.php' );
require_once get_theme_file_path( 'inc/setup.php' );

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
    return '<a class="arilia-logo-link" href="' . esc_url( home_url( '/' ) ) . '"><img class="arilia-logo-image" src="' . esc_url( get_theme_file_uri( 'assets/arilia-logo-v16.webp' ) ) . '" width="1536" height="1024" alt="Arilia — A Little Wonder. Home" decoding="async" /></a>';
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
    elseif ( preg_match( '/(?:^|\s)(?:arilia-lineup|is-style-arilia-bottle-[a-z-]+)(?:\s|$)/', $classes ) ) { $file = 'collection.webp'; }
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
        $tags->set_attribute( 'width', 'collection.webp' === $target ? '1774' : '1000' );
        $tags->set_attribute( 'height', 'collection.webp' === $target ? '887' : '1500' );
    }
    return $tags->get_updated_html();
}
add_filter( 'render_block_core/image', 'arilia_refresh_bottle_images', 20, 2 );

/** Display approved campaign posters in existing page hero blocks. */
function arilia_campaign_poster_render( $html, $block ) {
    $classes = preg_split( '/\s+/', isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '' );
    $home = in_array( 'arilia-hero', $classes, true );
    $flavours = in_array( 'arilia-products-hero', $classes, true ) && is_page( array( 'products', 'flavours', 'flavors' ) );
    if ( ! $home && ! $flavours ) { return $html; }
    $file = $home ? 'hero-poster-v111.webp' : 'flavours-poster-v110.webp';
    $title = $home ? 'A little wonder. A bold first sip.' : 'Seven flavours. Endless little wonders.';
    $alt = $home ? 'Arilia Pomegranate Juice. A Little Wonder. A Bold First Sip. Explore Arilia.' : 'Seven Arilia flavours: Pomegranate, Pomegranate Apple, Beet Lime Ginger, Mango, Cranberry, Strawberry and Dragon Fruit.';
    $img = '<img src="' . esc_url( arilia_asset( $file ) ) . '" width="' . ( $home ? '1983' : '1900' ) . '" height="' . ( $home ? '793' : '828' ) . '" alt="' . esc_attr( $alt ) . '" fetchpriority="high" decoding="async" />';
    if ( $home ) { $img = '<a href="' . esc_url( arilia_url( 'products' ) ) . '" aria-label="Explore Arilia flavours">' . $img . '</a>'; }
    return '<section class="arilia-campaign-poster"><h1 class="screen-reader-text">' . esc_html( $title ) . '</h1>' . $img . '</section>';
}
add_filter( 'render_block_core/group', 'arilia_campaign_poster_render', 20, 2 );
