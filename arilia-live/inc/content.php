<?php
/** Core-block starter content. Once imported, all copy is stored in ordinary pages. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function arilia_products() {
    return array(
        'pomegranate' => array( 'name' => 'Pomegranate', 'family' => 'Fruit favourite', 'copy' => 'Ruby-red character. A familiar fruit with a wonderfully bold personality.' ),
        'mango' => array( 'name' => 'Mango', 'family' => 'Fruit favourite', 'copy' => 'A golden little moment. Meet the sunny centre of the Arilia collection.' ),
        'strawberry' => array( 'name' => 'Strawberry', 'family' => 'Fruit favourite', 'copy' => 'A berry favourite with a bright personality and a playful pink palette.' ),
        'cranberry' => array( 'name' => 'Cranberry', 'family' => 'Fruit favourite', 'copy' => 'A rich ruby colour and a berry character all its own.' ),
        'dragon-fruit' => array( 'name' => 'Dragon Fruit', 'family' => 'Fruit favourite', 'copy' => 'Tropical character. Vivid colour. A wonderfully different choice.' ),
        'pomegranate-apple' => array( 'name' => 'Pomegranate + Apple', 'family' => 'Creative blend', 'copy' => 'Two familiar favourites, brought together in one colourful pairing.' ),
        'beet-lime-ginger' => array( 'name' => 'Beet + Lime + Ginger', 'family' => 'Creative blend', 'copy' => 'An earthy root, a citrus note, and a little spice. A trio worth exploring.' ),
    );
}

function arilia_asset( $name ) {
    $map = array( 'pomegranate.webp' => 'pomegranate-v19.webp', 'pomegranate-apple.webp' => 'pomegranate-apple-v19.webp', 'collection.webp' => 'collection-v19.webp' );
    return get_theme_file_uri( 'assets/' . ( isset( $map[$name] ) ? $map[$name] : $name ) );
}
function arilia_url( $slug ) {
    if ( 'home' === $slug ) { return home_url( '/' ); }
    $page = get_page_by_path( $slug, OBJECT, 'page' );
    return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}
function arilia_b( $name, $attrs, $html ) {
    $json = $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
    return '<!-- wp:' . $name . $json . ' -->' . "\n" . $html . "\n" . '<!-- /wp:' . $name . ' -->' . "\n";
}
function arilia_dynamic( $name, $attrs ) {
    return '<!-- wp:' . $name . ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->' . "\n";
}
function arilia_group( $content, $class = '', $tag = 'div', $anchor = '' ) {
    $attrs = array();
    if ( 'div' !== $tag ) { $attrs['tagName'] = $tag; }
    if ( $class ) { $attrs['className'] = $class; }
    if ( $anchor ) { $attrs['anchor'] = $anchor; }
    return arilia_b( 'group', $attrs, '<' . $tag . ( $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '' ) . ' class="wp-block-group' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">' . $content . '</' . $tag . '>' );
}
function arilia_p( $text, $class = '' ) {
    return arilia_b( 'paragraph', $class ? array( 'className' => $class ) : array(), '<p' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . wp_kses_post( $text ) . '</p>' );
}
function arilia_h( $text, $level = 2, $class = '' ) {
    $attrs = array();
    if ( 2 !== $level ) { $attrs['level'] = $level; }
    if ( $class ) { $attrs['className'] = $class; }
    return arilia_b( 'heading', $attrs, '<h' . $level . ' class="wp-block-heading' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">' . wp_kses_post( $text ) . '</h' . $level . '>' );
}
function arilia_image( $file, $alt, $class = '' ) {
    $attrs = array( 'sizeSlug' => 'full', 'linkDestination' => 'none' );
    if ( $class ) { $attrs['className'] = $class; }
    return arilia_b( 'image', $attrs, '<figure class="wp-block-image size-full' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '"><img src="' . esc_url( arilia_asset( $file ) ) . '" alt="' . esc_attr( $alt ) . '"/></figure>' );
}
function arilia_buttons( $links, $class = '' ) {
    $content = '';
    foreach ( $links as $link ) {
        $content .= arilia_b( 'button', array(), '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $link[1] ) . '">' . esc_html( $link[0] ) . '</a></div>' );
    }
    return arilia_b( 'buttons', $class ? array( 'className' => $class ) : array(), '<div class="wp-block-buttons' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">' . $content . '</div>' );
}
function arilia_contact_buttons() {
    return arilia_buttons( array(
        array( 'WhatsApp enquiries ↗', 'https://wa.me/919032954333' ),
        array( 'Call +91 90329 54333', 'tel:+919032954333' ),
        array( 'refresh@pionorybeverages.com', 'mailto:refresh@pionorybeverages.com' ),
    ), 'arilia-contact-buttons' );
}
function arilia_contact_banner() {
    return arilia_group(
        arilia_p( 'RETAIL · DISTRIBUTION · HOSPITALITY', 'arilia-eyebrow' ) .
        arilia_h( 'LET’S MAKE<br>ROOM FOR ARILIA.' ) .
        arilia_p( 'Interested in bringing the collection to your shelves or tables?' ) . arilia_contact_buttons(),
        'arilia-connect', 'section', 'connect'
    );
}
function arilia_art( $slug, $name ) {
    return arilia_group( arilia_p( 'A LITTLE WONDER', 'arilia-art-caption' ) .
        arilia_image( 'collection.webp', 'Arilia ' . $name . ' 250 ml double-bulb bottle', 'is-style-arilia-bottle-' . $slug ),
        'arilia-product-art arilia-art--' . $slug
    );
}
function arilia_card( $slug, $product ) {
    return arilia_group(
        arilia_art( $slug, $product['name'] ) .
        arilia_p( '250 ml · ' . $product['family'], 'arilia-product-meta' ) .
        arilia_h( esc_html( $product['name'] ), 3 ) . arilia_p( esc_html( $product['copy'] ) ) .
        arilia_buttons( array( array( 'Explore ' . $product['name'] . ' ↗', arilia_url( $slug ) ) ), 'arilia-product-button' ),
        'arilia-product-card', 'article'
    );
}

function arilia_header_content() {
    $brand = arilia_group( arilia_p( arilia_logo_markup(), 'arilia-wordmark' ), 'arilia-brand' );
    $links = '';
    foreach ( array( 'products' => 'Flavours', 'our-story' => 'Our Story', 'contact' => 'Contact' ) as $slug => $label ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        $attrs = array( 'label' => $label, 'url' => arilia_url( $slug ) );
        if ( $page ) { $attrs['kind'] = 'post-type'; $attrs['type'] = 'page'; $attrs['id'] = (int) $page->ID; }
        $links .= arilia_dynamic( 'navigation-link', $attrs );
    }
    $links .= arilia_dynamic( 'navigation-link', array( 'label' => 'Stock Arilia', 'url' => arilia_url( 'contact' ) . '#connect', 'kind' => 'custom', 'className' => 'arilia-stock-link' ) );
    $navigation = '<!-- wp:navigation {"overlayMenu":"mobile","className":"arilia-navigation","ariaLabel":"Main navigation","layout":{"type":"flex","justifyContent":"right"}} -->' . $links . '<!-- /wp:navigation -->';
    return arilia_group( arilia_p( 'MEET THE ARILIA 250 ml COLLECTION', 'arilia-announcement' ) .
        arilia_group( $brand . $navigation, 'arilia-header-inner' ), 'arilia-header' );
}
function arilia_footer_content() {
    $links = '';
    foreach ( array( 'products' => 'Products', 'our-story' => 'Our Story', 'contact' => 'Contact & Distribution' ) as $slug => $label ) {
        $links .= arilia_p( '<a href="' . esc_url( arilia_url( $slug ) ) . '">' . esc_html( $label ) . '</a>' );
    }
    return arilia_group( arilia_group(
        arilia_group( arilia_p( arilia_logo_markup(), 'arilia-wordmark' ) . arilia_p( 'A little bottle. A world of flavour.' ), 'arilia-footer-brand' ) .
        arilia_group( $links, 'arilia-footer-links' ) .
        arilia_group( arilia_p( '<a href="mailto:refresh@pionorybeverages.com">refresh@pionorybeverages.com</a>' ) . arilia_p( '<a href="tel:+919032954333">+91 90329 54333</a>' ), 'arilia-footer-contact' ),
        'arilia-footer-inner' ) . arilia_p( '© Arilia. All rights reserved.', 'arilia-copyright' ), 'arilia-footer' );
}

function arilia_page_content( $slug ) {
    if ( 'home' === $slug ) {
        $hero = arilia_group( arilia_group(
            arilia_p( 'FRUIT FOR THE SPOTLIGHT.', 'arilia-eyebrow' ) . arilia_h( 'SMALL BOTTLE.<br>BIG FRUIT<br><em>ENERGY.</em>', 1 ) .
            arilia_p( 'From ruby-red pomegranate to golden mango. Meet a colourful collection with a character all its own.', 'arilia-intro' ) .
            arilia_buttons( array( array( 'Explore the collection ↗', arilia_url( 'products' ) ) ) ) . arilia_p( '250 ml · Our signature double-bulb bottle', 'arilia-hero-note' ),
            'arilia-hero-copy' ) . arilia_image( 'pomegranate.webp', 'Arilia pomegranate 250 ml bottle with ruby-red cap and ivory labels', 'arilia-hero-image' ), 'arilia-hero', 'section' );
        $collection = arilia_group( arilia_p( 'THE COLOUR OF YOUR NEXT SIP', 'arilia-eyebrow' ) . arilia_h( 'SEVEN WAYS TO<br>FIND YOUR WONDERFUL.' ) .
            arilia_p( 'Go bold. Go tropical. Try an unexpected pairing. There’s a whole spectrum to explore.' ) .
            arilia_image( 'collection.webp', 'Seven Arilia bottles with Mango in the centre', 'arilia-lineup' ) .
            arilia_buttons( array( array( 'Meet all seven flavours ↗', arilia_url( 'products' ) ) ) ), 'arilia-section arilia-centred', 'section' );
        $story = arilia_group( arilia_image( 'pomegranate-apple.webp', 'Arilia pomegranate and apple drink in its double-bulb bottle', 'arilia-story-image' ) .
            arilia_group( arilia_p( 'THE ARILIA SPIRIT', 'arilia-eyebrow' ) . arilia_h( 'FRUIT.<br>WITH A<br>POINT OF VIEW.' ) . arilia_p( 'Some moments deserve a little more colour. A pause in the afternoon. A conversation that keeps going. Something different at the table.' ) .
            arilia_buttons( array( array( 'Step into our world ↗', arilia_url( 'our-story' ) ) ) ), 'arilia-story-copy' ), 'arilia-story', 'section' );
        return $hero . arilia_group( arilia_p( 'POMEGRANATE · MANGO · CRANBERRY · STRAWBERRY · DRAGON FRUIT' ), 'arilia-ribbon' ) . $collection . $story . arilia_contact_banner();
    }
    if ( 'products' === $slug ) {
        $content = arilia_group( arilia_p( 'THE ARILIA COLLECTION', 'arilia-eyebrow' ) . arilia_h( 'FRUIT TAKES<br>CENTRE STAGE.', 1 ) . arilia_p( 'Colourful favourites. Curious combinations.<br>Find your next little wonderful.' ), 'arilia-products-hero', 'section' );
        $content .= arilia_group( arilia_p( 'JUMP TO', 'arilia-eyebrow' ) . arilia_buttons( array( array( 'Fruit favourites ↓', '#fruit-favourites' ), array( 'Creative blends ↓', '#creative-blends' ) ), 'arilia-jump-buttons' ), 'arilia-jump', 'nav' );
        foreach ( array( 'Fruit favourite' => array( 'fruit-favourites', 'FAMILIAR FRUIT.<br>FRESH PERSONALITY.', 'From ruby red to golden yellow. Five fruit flavours, each with a colour and character of its own.' ), 'Creative blend' => array( 'creative-blends', 'A LITTLE<br>UNEXPECTED.', 'Familiar ingredients. A different conversation. Meet two pairings that bring a twist to the collection.' ) ) as $family => $section ) {
            $cards = '';
            foreach ( arilia_products() as $key => $product ) { if ( $family === $product['family'] ) { $cards .= arilia_card( $key, $product ); } }
            $content .= arilia_group( arilia_group( arilia_p( strtoupper( $family ) . 'S', 'arilia-eyebrow' ) . arilia_h( $section[1] ) . arilia_p( $section[2] ), 'arilia-category-heading' ) .
                arilia_group( $cards, 'arilia-product-grid' . ( 'Creative blend' === $family ? ' arilia-grid-two' : '' ) ),
                'arilia-section' . ( 'Creative blend' === $family ? ' arilia-blends-section' : '' ), 'section', $section[0] );
        }
        return $content . arilia_contact_banner();
    }
    if ( 'our-story' === $slug ) {
        $points = '';
        foreach ( array( array( 'Two curves. One character.', 'A distinctive double-bulb silhouette makes Arilia recognisable at a glance.' ), array( 'The little details.', 'Leaf detailing at the neck, a ruby-red cap, and ivory labels bring the design together.' ), array( 'Your 250 ml moment.', 'A compact format for a colourful little break, wherever the day takes you.' ) ) as $point ) { $points .= arilia_group( arilia_h( $point[0], 3 ) . arilia_p( $point[1] ), 'arilia-bottle-point' ); }
        return arilia_group( arilia_p( 'THE ARILIA SPIRIT', 'arilia-eyebrow' ) . arilia_h( 'MAKE ROOM<br>FOR A LITTLE WONDER.', 1 ) . arilia_p( 'Colourful flavours. A bottle with character. A little curiosity in the everyday.' ), 'arilia-products-hero', 'section' ) .
            arilia_group( arilia_image( 'pomegranate-apple.webp', 'Arilia pomegranate and apple bottle', 'arilia-story-image' ) . arilia_group( arilia_h( 'A COLOURFUL<br>POINT OF VIEW.' ) . arilia_p( 'Arilia brings a playful point of view to the everyday, with a collection of fruit flavours and combinations made to be explored.' ) . arilia_p( 'From Pomegranate and Mango to Beet + Lime + Ginger, the collection gives familiar favourites a different setting.' ), 'arilia-story-copy' ), 'arilia-story', 'section' ) .
            arilia_group( arilia_h( 'IMPOSSIBLE TO MISS.' ) . arilia_group( $points, 'arilia-bottle-points' ), 'arilia-section', 'section' ) . arilia_contact_banner();
    }
    if ( 'contact' === $slug ) {
        return arilia_group( arilia_p( 'SAY HELLO TO ARILIA', 'arilia-eyebrow' ) . arilia_h( 'LET’S GROW<br>SOMETHING WONDERFUL.', 1 ) . arilia_p( 'For product questions, retail, distribution, and hospitality enquiries.' ), 'arilia-products-hero', 'section' ) .
            arilia_group( arilia_h( 'START A CONVERSATION.' ) . arilia_p( 'Tell us your name, city, business, and the flavours you’re interested in. Reach us using the contact option that works for you.' ) . arilia_contact_buttons() .
            arilia_p( 'Email: <a href="mailto:refresh@pionorybeverages.com">refresh@pionorybeverages.com</a><br>Phone / WhatsApp: <a href="tel:+919032954333">+91 90329 54333</a>', 'arilia-contact-copy' ), 'arilia-section arilia-centred', 'section' );
    }
    $products = arilia_products();
    if ( isset( $products[ $slug ] ) ) {
        $p = $products[ $slug ];
        $details = arilia_group( arilia_p( '<a href="' . esc_url( arilia_url( 'products' ) ) . '">← All flavours</a>', 'arilia-breadcrumb' ) . arilia_p( strtoupper( $p['family'] ) . ' · 250 ml', 'arilia-eyebrow' ) . arilia_h( esc_html( $p['name'] ), 1 ) . arilia_p( esc_html( $p['copy'] ), 'arilia-intro' ) .
            arilia_h( 'A BOTTLE WITH CHARACTER.', 2, 'arilia-product-subheading' ) . arilia_p( 'The Arilia 250 ml double-bulb bottle, with leaf detailing at the neck and signature ivory labels.' ) .
            arilia_p( 'Refer to the final product label for ingredients, nutrition, allergens, storage instructions, and product classification.', 'arilia-label-note' ) .
            arilia_buttons( array( array( 'Retail & distribution enquiries ↗', arilia_url( 'contact' ) ) ) ), 'arilia-product-copy' );
        return arilia_group( arilia_art( $slug, $p['name'] ) . $details, 'arilia-product-detail', 'section' ) . arilia_contact_banner();
    }
    return '';
}
