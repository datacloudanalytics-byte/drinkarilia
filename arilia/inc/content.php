<?php
/**
 * Starter page content built from core blocks.
 *
 * Every page is generated once by the setup screen and then stored as an
 * ordinary editable page. Class names here are matched in assets/css/arilia.css.
 *
 * @package Arilia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Block builders
 * ---------------------------------------------------------------------- */

function arilia_b( $name, $attrs, $html ) {
	$json = $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
	return '<!-- wp:' . $name . $json . ' -->' . "\n" . $html . "\n" . '<!-- /wp:' . $name . ' -->' . "\n";
}

function arilia_dynamic( $name, $attrs = array() ) {
	$json = $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
	return '<!-- wp:' . $name . $json . ' /-->' . "\n";
}

function arilia_group( $content, $class = '', $tag = 'div', $anchor = '' ) {
	$attrs = array();
	if ( 'div' !== $tag ) {
		$attrs['tagName'] = $tag;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$open = '<' . $tag . ( $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '' ) . ' class="wp-block-group' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">';
	return arilia_b( 'group', $attrs, $open . $content . '</' . $tag . '>' );
}

function arilia_p( $text, $class = '' ) {
	$attrs = $class ? array( 'className' => $class ) : array();
	return arilia_b( 'paragraph', $attrs, '<p' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . wp_kses_post( $text ) . '</p>' );
}

function arilia_h( $text, $level = 2, $class = '' ) {
	$attrs = array();
	if ( 2 !== $level ) {
		$attrs['level'] = $level;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	return arilia_b( 'heading', $attrs, '<h' . $level . ' class="wp-block-heading' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">' . wp_kses_post( $text ) . '</h' . $level . '>' );
}

function arilia_list( $items, $class = '' ) {
	$li = '';
	foreach ( $items as $item ) {
		$li .= arilia_b( 'list-item', array(), '<li>' . wp_kses_post( $item ) . '</li>' );
	}
	$attrs = $class ? array( 'className' => $class ) : array();
	return arilia_b( 'list', $attrs, '<ul class="wp-block-list' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">' . $li . '</ul>' );
}

/**
 * Image block. Uses the Media Library attachment when the importer created
 * one, otherwise the theme file with explicit dimensions.
 */
function arilia_img( $file, $alt, $class = '', $w = 0, $h = 0 ) {
	$src   = arilia_image_src( $file );
	$attrs = array( 'sizeSlug' => 'full', 'linkDestination' => 'none' );
	if ( $src['id'] ) {
		$attrs['id'] = $src['id'];
	} elseif ( $w && $h ) {
		$attrs['arilia'] = array( 'w' => $w, 'h' => $h );
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$img = '<img src="' . esc_url( $src['url'] ) . '" alt="' . esc_attr( $alt ) . '"' . ( $src['id'] ? ' class="wp-image-' . (int) $src['id'] . '"' : '' ) . '/>';
	return arilia_b( 'image', $attrs, '<figure class="wp-block-image size-full' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">' . $img . '</figure>' );
}

/**
 * Buttons. Each link is [label, url, style] where style is '' or 'outline'.
 */
function arilia_buttons( $links, $class = '' ) {
	$content = '';
	foreach ( $links as $link ) {
		$style   = ! empty( $link[2] ) ? $link[2] : '';
		$b_attrs = array();
		$b_class = 'wp-block-button';
		if ( 'outline' === $style ) {
			$b_attrs['className'] = 'is-style-outline';
			$b_class             .= ' is-style-outline';
		}
		$content .= arilia_b( 'button', $b_attrs, '<div class="' . $b_class . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $link[1] ) . '">' . esc_html( $link[0] ) . '</a></div>' );
	}
	$attrs = $class ? array( 'className' => $class ) : array();
	return arilia_b( 'buttons', $attrs, '<div class="wp-block-buttons' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">' . $content . '</div>' );
}

function arilia_details( $summary, $content ) {
	return arilia_b( 'details', array( 'summary' => $summary ), '<details class="wp-block-details"><summary>' . esc_html( $summary ) . '</summary>' . arilia_p( $content ) . '</details>' );
}

/* -------------------------------------------------------------------------
 * Shared sections
 * ---------------------------------------------------------------------- */

function arilia_section_head( $eyebrow, $heading, $copy = '', $align = 'centre' ) {
	$inner = arilia_p( $eyebrow, 'arilia-eyebrow' ) . arilia_h( $heading );
	if ( $copy ) {
		$inner .= arilia_p( $copy, 'arilia-section__copy' );
	}
	return arilia_group( $inner, 'arilia-section__head' . ( 'left' === $align ? ' arilia-section__head--left' : '' ) );
}

function arilia_card( $slug, $product ) {
	$art  = arilia_group( arilia_img( $product['image'], sprintf( 'Arilia %s %s, 250 ml bottle', $product['name'], $product['type'] ), 'arilia-card__image', $product['w'], $product['h'] ), 'arilia-card__art' );
	$body = arilia_group(
		arilia_p( esc_html( $product['type'] ) . ' · 250 ml', 'arilia-card__meta' ) .
		arilia_h( esc_html( $product['name'] ), 3, 'arilia-card__title' ) .
		arilia_p( esc_html( $product['tagline'] ), 'arilia-card__copy' ) .
		arilia_buttons( array( array( 'Discover ' . $product['name'], arilia_url( $slug ) ) ), 'arilia-card__link' ),
		'arilia-card__body'
	);
	return arilia_group( $art . $body, 'arilia-card arilia-card--' . $slug, 'article' );
}

function arilia_collection_grid( $only = array(), $extra_class = '' ) {
	$cards = '';
	foreach ( arilia_products() as $slug => $product ) {
		if ( $only && ! in_array( $slug, $only, true ) ) {
			continue;
		}
		$cards .= arilia_card( $slug, $product );
	}
	return arilia_group( $cards, trim( 'arilia-grid arilia-grid--cards ' . $extra_class ) );
}

function arilia_cta_band() {
	return arilia_group(
		arilia_group(
			arilia_p( 'Retail · Distribution · Hospitality', 'arilia-eyebrow' ) .
			arilia_h( 'Let’s make room for Arilia.' ) .
			arilia_p( 'Bringing the collection to your shelves, menus or events? Tell us about your business and the flavours you’re interested in.' ) .
			arilia_buttons(
				array(
					array( 'Enquire now', arilia_url( 'contact' ) ),
					array( 'WhatsApp us', arilia_whatsapp_href(), 'outline' ),
					array( 'Call ' . arilia_contact( 'phone' ), arilia_tel_href(), 'outline' ),
				),
				'arilia-connect__buttons'
			),
			'arilia-connect__inner'
		),
		'arilia-connect',
		'section',
		'connect'
	);
}

function arilia_page_hero( $eyebrow, $heading, $copy ) {
	return arilia_group(
		arilia_group( arilia_p( $eyebrow, 'arilia-eyebrow' ) . arilia_h( $heading, 1 ) . arilia_p( $copy, 'arilia-lead' ), 'arilia-page-hero__inner' ),
		'arilia-page-hero',
		'section'
	);
}

/* -------------------------------------------------------------------------
 * Pages
 * ---------------------------------------------------------------------- */

function arilia_page_content( $slug ) {
	$products = arilia_products();

	if ( 'home' === $slug ) {
		$hero = arilia_group(
			arilia_group(
				arilia_p( 'Fruit for the spotlight.', 'arilia-eyebrow' ) .
				arilia_h( 'Small bottle.<br>Big fruit<br><em>energy.</em>', 1 ) .
				arilia_p( 'From ruby-red pomegranate to golden mango. Meet a colourful collection with a character all its own.', 'arilia-lead' ) .
				arilia_buttons( array( array( 'Explore the collection', arilia_url( 'products' ) ) ), 'arilia-hero__buttons' ) .
				arilia_p( '250 ml · Our signature double-bulb bottle', 'arilia-hero__meta' ),
				'arilia-hero__copy'
			) .
			arilia_group( arilia_img( 'pomegranate.webp', 'Arilia Pomegranate Drink in the signature 250 ml double-bulb bottle', 'arilia-hero__image', 1000, 1500 ), 'arilia-hero__art' ),
			'arilia-hero',
			'section'
		);

		$ribbon = arilia_group( arilia_p( 'Pomegranate · Mango · Strawberry · Cranberry · Dragon Fruit · Pomegranate + Apple · Beet + Lime + Ginger', 'arilia-ribbon__text' ), 'arilia-ribbon', 'section' );

		$collection = arilia_group(
			arilia_section_head( 'The collection', 'Seven ways to find your wonderful.', 'Go bold. Go tropical. Try an unexpected pairing. There’s a whole spectrum to explore, and every flavour comes in the same little 250 ml bottle.' ) .
			arilia_collection_grid() .
			arilia_buttons( array( array( 'See all flavours', arilia_url( 'products' ) ) ), 'arilia-section__cta' ),
			'arilia-section arilia-collection',
			'section',
			'flavours'
		);

		$features = '';
		$points   = array(
			array( '01', 'Seven flavours', 'Five fruit favourites and two creative blends. A colour for every mood and menu.' ),
			array( '02', 'The 250 ml moment', 'A compact, single-serve format that fits a lunch break, a car cup holder or a café fridge.' ),
			array( '03', 'A bottle with character', 'The double-bulb silhouette, leaf detailing at the neck and ivory labels are recognisable at a glance.' ),
			array( '04', 'Made for shelves and tables', 'Designed to stand out in retail, hospitality and events. Talk to us about distribution.' ),
		);
		foreach ( $points as $point ) {
			$features .= arilia_group( arilia_p( $point[0], 'arilia-feature__index' ) . arilia_h( $point[1], 3 ) . arilia_p( $point[2] ), 'arilia-feature' );
		}
		$why = arilia_group(
			arilia_section_head( 'Why Arilia', 'Small bottle. Big personality.' ) .
			arilia_group( $features, 'arilia-grid arilia-grid--features' ),
			'arilia-section arilia-section--sand',
			'section'
		);

		$story = arilia_group(
			arilia_group( arilia_img( 'pomegranate-apple.webp', 'Arilia Pomegranate + Apple Drink beside fresh pomegranate and apple', 'arilia-story__image', 1000, 1500 ), 'arilia-story__art' ) .
			arilia_group(
				arilia_p( 'Our story', 'arilia-eyebrow' ) .
				arilia_h( 'Fruit. With a point of view.' ) .
				arilia_p( 'Some moments deserve a little more colour. A pause in the afternoon. A conversation that keeps going. Something different at the table. Arilia was made for those moments.' ) .
				arilia_buttons( array( array( 'Read our story', arilia_url( 'our-story' ) ) ) ),
				'arilia-story__copy'
			),
			'arilia-story',
			'section'
		);

		return $hero . $ribbon . $collection . $why . $story . arilia_cta_band();
	}

	if ( 'products' === $slug ) {
		$content  = arilia_page_hero( 'The Arilia collection', 'Fruit takes centre stage.', 'Colourful favourites. Curious combinations. Seven flavours, one signature 250 ml bottle. Find your next little wonderful.' );
		$content .= arilia_group(
			arilia_buttons(
				array(
					array( 'Fruit favourites', '#fruit-favourites', 'outline' ),
					array( 'Creative blends', '#creative-blends', 'outline' ),
					array( 'Stock Arilia', arilia_url( 'contact' ) ),
				),
				'arilia-filter__buttons'
			),
			'arilia-filter',
			'nav'
		);
		$i = 0;
		foreach ( arilia_families() as $family => $meta ) {
			$slugs = array_keys( array_filter( $products, fn( $p ) => $p['family'] === $family ) );
			$content .= arilia_group(
				arilia_section_head( $meta['name'], $meta['heading'], $meta['copy'] ) .
				arilia_collection_grid( $slugs, 2 === count( $slugs ) ? 'arilia-grid--two' : '' ),
				'arilia-section' . ( $i % 2 ? ' arilia-section--sand' : '' ),
				'section',
				$family
			);
			++$i;
		}
		return $content . arilia_cta_band();
	}

	if ( 'our-story' === $slug ) {
		$content  = arilia_page_hero( 'Our story', 'Make room for a little wonder.', 'Colourful flavours. A bottle with character. A little curiosity in the everyday.' );
		$content .= arilia_group(
			arilia_group( arilia_img( 'pomegranate-apple.webp', 'Arilia Pomegranate + Apple Drink in its double-bulb bottle', 'arilia-story__image', 1000, 1500 ), 'arilia-story__art' ) .
			arilia_group(
				arilia_p( 'A colourful point of view', 'arilia-eyebrow' ) .
				arilia_h( 'Flavour first. Colour you can see.' ) .
				arilia_p( 'Arilia brings a playful point of view to the everyday, with a collection of fruit flavours and combinations made to be explored.' ) .
				arilia_p( 'From Pomegranate and Mango to Beet + Lime + Ginger, the collection gives familiar favourites a brand-new setting: a bright little bottle that looks as good on a café counter as it does in your hand.' ) .
				arilia_buttons( array( array( 'Explore the flavours', arilia_url( 'products' ) ) ) ),
				'arilia-story__copy'
			),
			'arilia-story',
			'section'
		);

		$points = '';
		foreach ( array(
			array( 'Two curves. One character.', 'A distinctive double-bulb silhouette makes Arilia recognisable from across the room.' ),
			array( 'The little details.', 'Leaf detailing at the neck, a ruby-red cap and ivory labels bring the design together.' ),
			array( 'Your 250 ml moment.', 'A compact format for a colourful little break, wherever the day takes you.' ),
		) as $point ) {
			$points .= arilia_group( arilia_h( $point[0], 3 ) . arilia_p( $point[1] ), 'arilia-point' );
		}
		$content .= arilia_group(
			arilia_section_head( 'The bottle', 'Impossible to miss.' ) .
			arilia_group( $points, 'arilia-grid arilia-grid--three' ),
			'arilia-section arilia-section--sand',
			'section'
		);

		$values = '';
		foreach ( array(
			array( '01', 'Flavour you can picture', 'Every bottle is named after what’s inside, and the colour tells you before the first sip.' ),
			array( '02', 'Made to be shared', 'A collection built for retailers, cafés, hotels and events, as well as the fridge at home.' ),
			array( '03', 'Honest on the label', 'Ingredients, nutrition and storage details live on the pack, where they belong.' ),
		) as $point ) {
			$values .= arilia_group( arilia_p( $point[0], 'arilia-feature__index' ) . arilia_h( $point[1], 3 ) . arilia_p( $point[2] ), 'arilia-feature' );
		}
		$content .= arilia_group(
			arilia_section_head( 'What we stand for', 'Simple things, done brightly.' ) .
			arilia_group( $values, 'arilia-grid arilia-grid--three' ),
			'arilia-section',
			'section'
		);
		return $content . arilia_cta_band();
	}

	if ( 'contact' === $slug ) {
		$content  = arilia_page_hero( 'Say hello to Arilia', 'Let’s grow something wonderful.', 'Product questions, retail, distribution and hospitality enquiries. Reach us however suits you best.' );
		$content .= arilia_group(
			arilia_group(
				arilia_h( 'Start a conversation.' ) .
				arilia_p( 'Tell us your name, city, business and the flavours you’re interested in. We read every message and reply by email or phone.' ) .
				arilia_dynamic( 'arilia/enquiry-form' ),
				'arilia-contact__form'
			) .
			arilia_group(
				arilia_group( arilia_p( 'WhatsApp', 'arilia-contact-card__label' ) . arilia_p( '<a href="' . esc_url( arilia_whatsapp_href() ) . '">' . esc_html( arilia_contact( 'phone' ) ) . '</a>', 'arilia-contact-card__value' ) . arilia_p( 'Quickest for trade enquiries and samples.' ), 'arilia-contact-card' ) .
				arilia_group( arilia_p( 'Call', 'arilia-contact-card__label' ) . arilia_p( '<a href="' . esc_url( arilia_tel_href() ) . '">' . esc_html( arilia_contact( 'phone' ) ) . '</a>', 'arilia-contact-card__value' ) . arilia_p( 'Leave a message if we miss you and we’ll call back.' ), 'arilia-contact-card' ) .
				arilia_group( arilia_p( 'Email', 'arilia-contact-card__label' ) . arilia_p( '<a href="mailto:' . esc_attr( arilia_contact( 'email' ) ) . '">' . esc_html( arilia_contact( 'email' ) ) . '</a>', 'arilia-contact-card__value' ) . arilia_p( 'For price lists, distribution and press.' ), 'arilia-contact-card' ),
				'arilia-contact__cards',
				'aside'
			),
			'arilia-section arilia-contact',
			'section'
		);
		$content .= arilia_group(
			arilia_section_head( 'Good to know', 'Frequently asked.' ) .
			arilia_group(
				arilia_details( 'Where can I buy Arilia?', 'Arilia is sold through retail and hospitality partners. Send us your city and we’ll point you to the nearest stockist, or help you become one.' ) .
				arilia_details( 'What sizes are available?', 'Every flavour comes in the signature 250 ml double-bulb bottle, ideal for single-serve retail, cafés and events.' ) .
				arilia_details( 'Do you supply hotels, restaurants and events?', 'Yes. Hospitality is a big part of what we do. Tell us about your venue and volumes and we’ll come back with options.' ) .
				arilia_details( 'How do I become a distributor?', 'Use the form above or WhatsApp us with your company name, region and current portfolio. We’ll follow up with next steps.' ),
				'arilia-faq'
			),
			'arilia-section arilia-section--sand',
			'section'
		);
		return $content;
	}

	if ( isset( $products[ $slug ] ) ) {
		$p      = $products[ $slug ];
		$family = arilia_families()[ $p['family'] ];
		$hero   = arilia_group(
			arilia_group( arilia_img( $p['image'], sprintf( 'Arilia %s %s, 250 ml bottle', $p['name'], $p['type'] ), 'arilia-flavour-hero__image', $p['w'], $p['h'] ), 'arilia-flavour-hero__art' ) .
			arilia_group(
				arilia_p( '<a href="' . esc_url( arilia_url( 'products' ) ) . '">← All flavours</a>', 'arilia-breadcrumb' ) .
				arilia_p( esc_html( $family['name'] ) . ' · ' . esc_html( $p['type'] ) . ' · 250 ml', 'arilia-eyebrow' ) .
				arilia_h( esc_html( $p['name'] ), 1 ) .
				arilia_p( esc_html( $p['tagline'] ) . ' ' . esc_html( $p['copy'] ), 'arilia-lead' ) .
				arilia_buttons(
					array(
						array( 'Stock this flavour', arilia_url( 'contact' ) ),
						array( 'WhatsApp us', arilia_whatsapp_href(), 'outline' ),
					)
				),
				'arilia-flavour-hero__copy'
			),
			'arilia-flavour-hero arilia-flavour--' . $slug,
			'section'
		);

		$details = arilia_group(
			arilia_group(
				arilia_group( arilia_h( 'Tasting notes', 3 ) . arilia_list( $p['notes'] ), 'arilia-detail' ) .
				arilia_group( arilia_h( 'How to serve', 3 ) . arilia_p( esc_html( $p['pairs'] ) ), 'arilia-detail' ) .
				arilia_group( arilia_h( 'At a glance', 3 ) . arilia_list( array( '<strong>Size:</strong> 250 ml', '<strong>Type:</strong> ' . esc_html( $p['type'] ), '<strong>Family:</strong> ' . esc_html( $family['name'] ), '<strong>Bottle:</strong> Signature double-bulb' ) ), 'arilia-detail' ),
				'arilia-grid arilia-grid--three'
			) .
			arilia_p( 'Refer to the product label for ingredients, nutrition, allergens, storage instructions and product classification.', 'arilia-label-note' ),
			'arilia-section arilia-flavour-details',
			'section'
		);

		$others = array_keys( $products );
		$pos    = array_search( $slug, $others, true );
		$next   = array();
		for ( $i = 1; $i <= 3; $i++ ) {
			$next[] = $others[ ( $pos + $i ) % count( $others ) ];
		}
		$more = arilia_group(
			arilia_section_head( 'More to explore', 'Three more little wonders.' ) .
			arilia_collection_grid( $next, 'arilia-grid--three' ) .
			arilia_buttons( array( array( 'See all seven flavours', arilia_url( 'products' ), 'outline' ) ), 'arilia-section__cta' ),
			'arilia-section arilia-section--sand',
			'section'
		);
		return $hero . $details . $more . arilia_cta_band();
	}

	return '';
}
