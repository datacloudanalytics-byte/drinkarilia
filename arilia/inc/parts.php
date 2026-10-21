<?php
/**
 * Header and footer patterns used by the template parts.
 *
 * These are generated on every request until an editor customises the part
 * in Appearance → Editor, so navigation links and contact details stay in
 * sync with the pages and settings.
 *
 * @package Arilia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arilia_nav_link( $slug, $label ) {
	$page  = get_page_by_path( $slug, OBJECT, 'page' );
	$attrs = array( 'label' => $label, 'url' => arilia_url( $slug ) );
	if ( $page ) {
		$attrs['kind'] = 'post-type';
		$attrs['type'] = 'page';
		$attrs['id']   = (int) $page->ID;
	}
	return arilia_dynamic( 'navigation-link', $attrs );
}

function arilia_brand_block( $class = 'arilia-brand' ) {
	return arilia_group(
		arilia_dynamic( 'site-logo', array( 'width' => 180, 'shouldSyncIcon' => false ) ) .
		arilia_p( '<a href="' . esc_url( home_url( '/' ) ) . '">Arilia</a>', 'arilia-wordmark' ) .
		arilia_p( 'Wonderful.', 'arilia-brand-line' ),
		$class
	);
}

function arilia_header_content() {
	$links  = arilia_nav_link( 'products', 'Flavours' );
	$links .= arilia_nav_link( 'our-story', 'Our Story' );
	$links .= arilia_nav_link( 'contact', 'Contact' );

	$navigation = '<!-- wp:navigation {"overlayMenu":"mobile","className":"arilia-nav","ariaLabel":"Main navigation","layout":{"type":"flex","justifyContent":"right"}} -->' . "\n" . $links . '<!-- /wp:navigation -->' . "\n";
	$cta        = arilia_buttons( array( array( 'Stock Arilia', arilia_url( 'contact' ) ) ), 'arilia-header__cta' );

	return arilia_group(
		arilia_group( arilia_brand_block() . $navigation . $cta, 'arilia-header__inner' ),
		'arilia-header'
	);
}

function arilia_footer_content() {
	$explore = '';
	foreach ( array( 'products' => 'Flavours', 'our-story' => 'Our Story', 'contact' => 'Contact & Distribution' ) as $slug => $label ) {
		$explore .= arilia_p( '<a href="' . esc_url( arilia_url( $slug ) ) . '">' . esc_html( $label ) . '</a>' );
	}
	$flavours = '';
	foreach ( arilia_products() as $slug => $product ) {
		$flavours .= arilia_p( '<a href="' . esc_url( arilia_url( $slug ) ) . '">' . esc_html( $product['name'] ) . '</a>' );
	}
	$contact  = arilia_p( '<a href="mailto:' . esc_attr( arilia_contact( 'email' ) ) . '">' . esc_html( arilia_contact( 'email' ) ) . '</a>' );
	$contact .= arilia_p( '<a href="' . esc_url( arilia_tel_href() ) . '">' . esc_html( arilia_contact( 'phone' ) ) . '</a>' );
	$contact .= arilia_p( '<a href="' . esc_url( arilia_whatsapp_href() ) . '">WhatsApp</a>' );
	foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook' ) as $key => $label ) {
		if ( arilia_contact( $key ) ) {
			$contact .= arilia_p( '<a href="' . esc_url( arilia_contact( $key ) ) . '" rel="noopener">' . $label . '</a>' );
		}
	}

	$columns = arilia_group(
		arilia_group( arilia_brand_block( 'arilia-footer__brand' ) . arilia_p( 'Pure fruit wellness in a little 250 ml bottle. Seven flavours, one signature silhouette.', 'arilia-footer__tagline' ), 'arilia-footer__col arilia-footer__col--brand' ) .
		arilia_group( arilia_p( 'Explore', 'arilia-footer__title' ) . $explore, 'arilia-footer__col' ) .
		arilia_group( arilia_p( 'Flavours', 'arilia-footer__title' ) . $flavours, 'arilia-footer__col' ) .
		arilia_group( arilia_p( 'Contact', 'arilia-footer__title' ) . $contact, 'arilia-footer__col' ),
		'arilia-footer__inner'
	);
	$legal   = arilia_group(
		arilia_p( '© {year} ' . esc_html( arilia_contact( 'legal_name' ) ) . '. All rights reserved.', 'arilia-copyright' ) .
		arilia_p( 'Refer to each product label for ingredients, nutrition, allergens and storage information.', 'arilia-footer__note' ),
		'arilia-footer__legal'
	);
	return arilia_group( $columns . $legal, 'arilia-footer' );
}
