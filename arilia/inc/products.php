<?php
/**
 * Product catalogue, page manifest and editable contact settings.
 *
 * Copy here is deliberately free of nutrition, health, origin or price
 * claims: the final product label remains the source of truth.
 *
 * @package Arilia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seven flavours. "type" mirrors the wording printed on each label
 * (always Drink) so the website never contradicts the pack.
 */
function arilia_products() {
	return array(
		'pomegranate'       => array(
			'name'    => 'Pomegranate',
			'type'    => 'Drink',
			'family'  => 'fruit-favourites',
			'tagline' => 'Ruby-red and unmistakably bold.',
			'copy'    => 'The flavour that started it all. Deep ruby colour and a bold, familiar fruit character.',
			'notes'   => array( 'Deep ruby colour', 'Bold, rounded fruit character', 'A clean, refreshing finish' ),
			'pairs'   => 'Serve well chilled. Lovely on its own, over ice, or alongside brunch.',
			'image'   => 'bottle-pomegranate.webp',
			'w'       => 293,
			'h'       => 560,
		),
		'mango'             => array(
			'name'    => 'Mango',
			'type'    => 'Drink',
			'family'  => 'fruit-favourites',
			'tagline' => 'Golden, sunny and smooth.',
			'copy'    => 'The sunny centre of the collection. Golden colour and a smooth, tropical sweetness.',
			'notes'   => array( 'Golden, sun-ripened colour', 'Smooth and tropical', 'Gently sweet' ),
			'pairs'   => 'Serve well chilled. A natural partner for spicy food and summer afternoons.',
			'image'   => 'bottle-mango.webp',
			'w'       => 197,
			'h'       => 560,
		),
		'strawberry'        => array(
			'name'    => 'Strawberry',
			'type'    => 'Drink',
			'family'  => 'fruit-favourites',
			'tagline' => 'Bright, pink and playful.',
			'copy'    => 'A berry favourite with a bright personality and a cheerful pink palette.',
			'notes'   => array( 'Bright strawberry colour', 'Soft berry sweetness', 'Light and easy-drinking' ),
			'pairs'   => 'Serve well chilled. Perfect for picnics, lunchboxes and dessert tables.',
			'image'   => 'bottle-strawberry.webp',
			'w'       => 198,
			'h'       => 560,
		),
		'cranberry'         => array(
			'name'    => 'Cranberry',
			'type'    => 'Drink',
			'family'  => 'fruit-favourites',
			'tagline' => 'Crisp, ruby and refreshing.',
			'copy'    => 'A rich ruby colour with a crisp berry character all of its own.',
			'notes'   => array( 'Rich ruby colour', 'Crisp, tart-leaning berry character', 'Refreshing finish' ),
			'pairs'   => 'Serve well chilled. A smart choice for mocktails and the dinner table.',
			'image'   => 'bottle-cranberry.webp',
			'w'       => 198,
			'h'       => 560,
		),
		'dragon-fruit'      => array(
			'name'    => 'Dragon Fruit',
			'type'    => 'Drink',
			'family'  => 'fruit-favourites',
			'tagline' => 'Vivid, tropical and different.',
			'copy'    => 'Tropical character and a vivid magenta colour. A wonderfully different choice.',
			'notes'   => array( 'Vivid magenta colour', 'Delicate tropical flavour', 'Light, clean finish' ),
			'pairs'   => 'Serve well chilled. The conversation starter of the collection.',
			'image'   => 'bottle-dragon-fruit.webp',
			'w'       => 198,
			'h'       => 560,
		),
		'pomegranate-apple' => array(
			'name'    => 'Pomegranate + Apple',
			'type'    => 'Drink',
			'family'  => 'creative-blends',
			'tagline' => 'Two favourites, one bottle.',
			'copy'    => 'Two familiar favourites brought together in one colourful, crowd-pleasing pairing.',
			'notes'   => array( 'Ruby pomegranate meets crisp apple', 'Balanced and rounded', 'Easy to love' ),
			'pairs'   => 'Serve well chilled. A family favourite for the breakfast table.',
			'image'   => 'bottle-pomegranate-apple.webp',
			'w'       => 292,
			'h'       => 560,
		),
		'beet-lime-ginger'  => array(
			'name'    => 'Beet + Lime + Ginger',
			'type'    => 'Drink',
			'family'  => 'creative-blends',
			'tagline' => 'Earthy, zesty, with a little spice.',
			'copy'    => 'An earthy root, a citrus note and a little spice. A trio worth exploring.',
			'notes'   => array( 'Deep beet colour', 'Zesty lime lift', 'A warm ginger finish' ),
			'pairs'   => 'Serve well chilled. Built for cafés, wellness menus and curious palates.',
			'image'   => 'bottle-beet-lime-ginger.webp',
			'w'       => 198,
			'h'       => 560,
		),
	);
}

/**
 * Product families shown on the Products page.
 */
function arilia_families() {
	return array(
		'fruit-favourites' => array(
			'name'    => 'Fruit favourites',
			'heading' => 'Familiar fruit. Fresh personality.',
			'copy'    => 'Five single-fruit flavours, from ruby red to golden yellow, each with a colour and character of its own.',
		),
		'creative-blends'  => array(
			'name'    => 'Creative blends',
			'heading' => 'A little unexpected.',
			'copy'    => 'Familiar ingredients in a new conversation. Two pairings that bring a twist to the collection.',
		),
	);
}

/**
 * Pages the setup screen creates. Slug => title.
 */
function arilia_page_manifest() {
	$pages = array(
		'home'      => 'Home',
		'products'  => 'Products',
		'our-story' => 'Our Story',
		'contact'   => 'Contact & Distribution',
	);
	foreach ( arilia_products() as $slug => $product ) {
		$pages[ $slug ] = $product['name'];
	}
	return $pages;
}

/**
 * Contact details, editable under Appearance → Arilia Setup.
 */
function arilia_contact_defaults() {
	return array(
		'email'      => 'refresh@pionorybeverages.com',
		'phone'      => '+91 90329 54333',
		'whatsapp'   => '919032954333',
		'legal_name' => 'Arilia',
		'instagram'  => '',
		'facebook'   => '',
	);
}

function arilia_contact( $key = null ) {
	$saved = get_option( 'arilia_contact', array() );
	$all   = wp_parse_args( is_array( $saved ) ? $saved : array(), arilia_contact_defaults() );
	if ( null === $key ) {
		return $all;
	}
	return isset( $all[ $key ] ) ? $all[ $key ] : '';
}

function arilia_tel_href() {
	return 'tel:+' . preg_replace( '/\D+/', '', arilia_contact( 'phone' ) );
}

function arilia_whatsapp_href() {
	return 'https://wa.me/' . preg_replace( '/\D+/', '', arilia_contact( 'whatsapp' ) );
}

/**
 * Permalink for a starter page, falling back to a pretty URL guess.
 */
function arilia_url( $slug ) {
	if ( 'home' === $slug ) {
		return home_url( '/' );
	}
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * URL (and attachment ID if imported) for a theme image.
 */
function arilia_image_src( $file ) {
	$id = arilia_attachment_for( $file );
	if ( $id ) {
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( $src ) {
			return array( 'id' => $id, 'url' => $src[0], 'w' => $src[1], 'h' => $src[2] );
		}
	}
	return array( 'id' => 0, 'url' => ARILIA_URI . '/assets/images/' . $file, 'w' => 0, 'h' => 0 );
}

/**
 * Attachment that the importer created for a theme image, if any.
 */
function arilia_attachment_for( $file ) {
	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_arilia_asset',
			'meta_value'     => $file,
			'no_found_rows'  => true,
		)
	);
	return $found ? (int) $found[0] : 0;
}
