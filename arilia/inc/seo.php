<?php
/**
 * Lightweight SEO: meta description, Open Graph / Twitter cards and
 * JSON-LD. Steps aside when a dedicated SEO plugin is active.
 *
 * @package Arilia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arilia_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

function arilia_seo_description() {
	if ( is_singular() ) {
		$excerpt = get_the_excerpt( get_queried_object_id() );
		if ( $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
	}
	$tagline = get_bloginfo( 'description' );
	return $tagline ? $tagline . ' Seven fruit flavours in a signature 250 ml bottle.' : 'Arilia: seven bright fruit flavours in a signature 250 ml double-bulb bottle.';
}

function arilia_seo_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		if ( $src && $src[1] >= 600 ) {
			return $src;
		}
	}
	$src = arilia_image_src( 'collection.webp' );
	return array( $src['url'], $src['w'] ? $src['w'] : 1774, $src['h'] ? $src['h'] : 887 );
}

function arilia_seo_head() {
	if ( arilia_seo_plugin_active() ) {
		return;
	}
	$description = arilia_seo_description();
	$image       = arilia_seo_image();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_front_page() ? 'website' : 'article' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $image[0] ) . '">' . "\n";
	if ( ! empty( $image[1] ) ) {
		echo '<meta property="og:image:width" content="' . (int) $image[1] . '">' . "\n";
		echo '<meta property="og:image:height" content="' . (int) $image[2] . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="theme-color" content="#a4122e">' . "\n";

	$schema = array();
	if ( is_front_page() ) {
		$org = array(
			'@type'       => 'Organization',
			'name'        => get_bloginfo( 'name' ),
			'url'         => home_url( '/' ),
			'logo'        => has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : ARILIA_URI . '/assets/images/favicon.svg',
			'email'       => arilia_contact( 'email' ),
			'telephone'   => arilia_contact( 'phone' ),
			'contactPoint' => array( '@type' => 'ContactPoint', 'contactType' => 'sales', 'email' => arilia_contact( 'email' ), 'telephone' => arilia_contact( 'phone' ) ),
		);
		$same = array_filter( array( arilia_contact( 'instagram' ), arilia_contact( 'facebook' ) ) );
		if ( $same ) {
			$org['sameAs'] = array_values( $same );
		}
		$schema[] = $org;
		$schema[] = array( '@type' => 'WebSite', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) );
	} elseif ( is_singular( 'page' ) ) {
		$slug     = get_post_field( 'post_name', get_queried_object_id() );
		$products = arilia_products();
		if ( isset( $products[ $slug ] ) ) {
			$p        = $products[ $slug ];
			$schema[] = array(
				'@type'       => 'Product',
				'name'        => 'Arilia ' . $p['name'] . ' ' . $p['type'],
				'description' => $p['tagline'] . ' ' . $p['copy'],
				'image'       => $image[0],
				'brand'       => array( '@type' => 'Brand', 'name' => 'Arilia' ),
				'size'        => '250 ml',
				'url'         => get_permalink(),
			);
		}
	}
	if ( $schema ) {
		foreach ( $schema as $item ) {
			$item = array_merge( array( '@context' => 'https://schema.org' ), $item );
			echo '<script type="application/ld+json">' . wp_json_encode( $item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		}
	}
}
add_action( 'wp_head', 'arilia_seo_head', 5 );
