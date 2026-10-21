<?php
/**
 * Arilia block theme bootstrap.
 *
 * @package Arilia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARILIA_VERSION', '2.0.1' );
define( 'ARILIA_DIR', get_theme_file_path() );
define( 'ARILIA_URI', get_theme_file_uri() );

require_once ARILIA_DIR . '/inc/products.php';
require_once ARILIA_DIR . '/inc/content.php';
require_once ARILIA_DIR . '/inc/parts.php';
require_once ARILIA_DIR . '/inc/setup.php';
require_once ARILIA_DIR . '/inc/enquiry.php';
require_once ARILIA_DIR . '/inc/seo.php';

/**
 * Theme supports.
 */
function arilia_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 72,
			'width'                => 220,
			'flex-width'           => true,
			'flex-height'          => true,
			'unlink-homepage-logo' => true,
		)
	);
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/arilia.css' );
	load_theme_textdomain( 'arilia', ARILIA_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'arilia_theme_setup' );

/**
 * Front-end assets. One stylesheet, one tiny script.
 */
function arilia_enqueue_assets() {
	$css = ARILIA_DIR . '/assets/css/arilia.css';
	wp_enqueue_style( 'arilia', ARILIA_URI . '/assets/css/arilia.css', array(), (string) filemtime( $css ) );

	$js = ARILIA_DIR . '/assets/js/site.js';
	wp_enqueue_script( 'arilia', ARILIA_URI . '/assets/js/site.js', array(), (string) filemtime( $js ), array( 'strategy' => 'defer', 'in_footer' => true ) );
}
add_action( 'wp_enqueue_scripts', 'arilia_enqueue_assets' );

/**
 * Preload the two web fonts so the first paint uses them.
 */
function arilia_preload_fonts() {
	foreach ( array( 'fraunces-latin.woff2', 'manrope-latin.woff2' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( ARILIA_URI . '/assets/fonts/' . $font ) );
	}
}
add_action( 'wp_head', 'arilia_preload_fonts', 1 );

/**
 * Patterns: full pages, plus header/footer used by the template parts.
 */
function arilia_register_patterns() {
	register_block_pattern_category( 'arilia', array( 'label' => __( 'Arilia pages', 'arilia' ) ) );

	foreach ( arilia_page_manifest() as $slug => $title ) {
		register_block_pattern(
			'arilia/' . $slug,
			array(
				'title'       => sprintf( 'Arilia — %s', $title ),
				'categories'  => array( 'arilia' ),
				'description' => __( 'Editable Arilia page built from core WordPress blocks.', 'arilia' ),
				'content'     => arilia_page_content( $slug ),
			)
		);
	}

	register_block_pattern( 'arilia/header', array( 'title' => 'Arilia Header', 'inserter' => false, 'content' => arilia_header_content() ) );
	register_block_pattern( 'arilia/footer', array( 'title' => 'Arilia Footer', 'inserter' => false, 'content' => arilia_footer_content() ) );
	register_block_pattern( 'arilia/cta-band', array( 'title' => 'Arilia — Stock Arilia band', 'categories' => array( 'arilia' ), 'content' => arilia_cta_band() ) );
	register_block_pattern( 'arilia/collection-grid', array( 'title' => 'Arilia — Seven flavour cards', 'categories' => array( 'arilia' ), 'content' => arilia_collection_grid() ) );

	// Legacy image styles from theme 1.x so previously imported pages keep rendering.
	foreach ( arilia_products() as $slug => $product ) {
		register_block_style( 'core/image', array( 'name' => 'arilia-bottle-' . $slug, 'label' => sprintf( 'Arilia bottle: %s (legacy crop)', $product['name'] ) ) );
	}
}
add_action( 'init', 'arilia_register_patterns', 20 );

/**
 * Body classes used for flavour tinting and the sticky header.
 */
function arilia_body_class( $classes ) {
	if ( is_singular( 'page' ) ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( isset( arilia_products()[ $slug ] ) ) {
			$classes[] = 'arilia-flavour-page';
			$classes[] = 'arilia-flavour--' . $slug;
		}
	}
	return $classes;
}
add_filter( 'body_class', 'arilia_body_class' );

/**
 * Give images width/height attributes when the importer could not sideload
 * them (theme-hosted artwork), so there is no layout shift.
 */
function arilia_image_dimensions( $block_content, $block ) {
	if ( empty( $block['attrs']['arilia'] ) || ! is_array( $block['attrs']['arilia'] ) ) {
		return $block_content;
	}
	$dims = $block['attrs']['arilia'];
	if ( empty( $dims['w'] ) || empty( $dims['h'] ) || preg_match( '/<img[^>]+\swidth=/', $block_content ) ) {
		return $block_content;
	}
	return preg_replace( '/<img\s/', sprintf( '<img width="%d" height="%d" ', (int) $dims['w'], (int) $dims['h'] ), $block_content, 1 );
}
add_filter( 'render_block_core/image', 'arilia_image_dimensions', 10, 2 );

/**
 * Default favicon when no Site Icon has been uploaded.
 */
function arilia_default_site_icon( $url, $size ) {
	if ( '' === $url ) {
		return ARILIA_URI . '/assets/images/favicon.svg';
	}
	return $url;
}
add_filter( 'get_site_icon_url', 'arilia_default_site_icon', 10, 2 );

/**
 * Copyright year placeholder used in the footer pattern.
 */
function arilia_footer_year( $content, $block ) {
	if ( empty( $block['attrs']['className'] ) || false === strpos( $block['attrs']['className'], 'arilia-copyright' ) ) {
		return $content;
	}
	return str_replace( '{year}', gmdate( 'Y' ), $content );
}
add_filter( 'render_block_core/paragraph', 'arilia_footer_year', 10, 2 );
