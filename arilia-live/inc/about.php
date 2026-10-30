<?php
/**
 * 1.28: "Our Story" page built on the structure of POM Wonderful's Our Story page
 * (hero, origin story, tree-to-table process, headline numbers, "more than meets
 * the rind" benefits, call to action), told for Arilia.
 *
 * Saved Our Story pages receive this layout at render time; new starter imports
 * get it as editable core blocks. Copy lives here so it can be edited in one place.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function arilia_about_copy() {
    return array(
        'eyebrow'   => 'Our story',
        'heading'   => 'A little wonder, made for every day.',
        'lead'      => 'Arilia began with a simple idea: real fruit, a bottle you want to hold, and a size that fits the moment. Seven flavours later, that idea is still what gets us up in the morning.',
        'origin'    => array(
            'eyebrow' => 'Where it started',
            'heading' => 'Rubies in a little bottle.',
            'body'    => array(
                'Pomegranate was our first love: ruby-red, bold and unmistakable. It set the tone for everything that followed, from the colour of our cap to the name on the label.',
                'We wanted a fruit drink that looked as good as it tasted, in a size you could finish in one sitting. So we drew a bottle with two curves instead of one, kept the label ivory so the fruit could do the talking, and filled it with 250 ml of flavour.',
                'Today Arilia is made by Pionory Beverages and shared across cafés, shops and family tables. The bottle is the same little wonder it was on day one.',
            ),
        ),
        'process'   => array(
            'eyebrow' => 'Fruit to bottle',
            'heading' => 'How a little wonder is made.',
            'intro'   => 'Every Arilia drink follows the same four steps, so a Mango in one city tastes like a Mango in the next.',
            'steps'   => array(
                array( 'Choose the fruit', 'Each flavour starts with the fruit it is named after. Pomegranate, mango, strawberry, cranberry, dragon fruit, apple, beet, lime and ginger: nothing on the label that is not in the bottle.' ),
                array( 'Blend for balance', 'Our blends are tuned for a clean, refreshing finish: bold enough to notice, light enough to reach for again tomorrow.' ),
                array( 'Fill the double-bulb bottle', 'Every flavour is filled into the same 250 ml signature bottle, so the range sits together on a shelf and in the hand.' ),
                array( 'Chill and share', 'Arilia is best served cold. From the fridge to the lunchbox, the café counter or the picnic table, it is made to be shared.' ),
            ),
        ),
        'numbers'   => array(
            array( '7', 'flavours', 'Five fruit favourites and two creative blends.' ),
            array( '250', 'ml', 'One single-serve size for every flavour.' ),
            array( '1', 'bottle', 'The double-bulb silhouette on every Arilia.' ),
            array( '0', 'guesswork', 'Ingredients, nutrition and storage are printed on every label.' ),
        ),
        'rind'      => array(
            'eyebrow' => 'More than meets the label',
            'heading' => 'Small details. Big difference.',
            'points'  => array(
                array( 'Two curves. One character.', 'The double-bulb shape is ours alone. You can spot an Arilia from across the room, and it sits comfortably in a child’s hand or a gym bag.' ),
                array( 'Fruit-forward flavour.', 'Each drink is built around one fruit or one pairing, so the first sip tells you exactly what you are drinking.' ),
                array( 'A colour you can trust.', 'Ruby, gold, pink, magenta, crimson: the drink inside is the colour on the label.' ),
                array( 'Honest on the pack.', 'Ingredients, nutrition, allergens and storage live on the label, where they belong, in plain words.' ),
            ),
        ),
        'gallery'   => array(
            'eyebrow' => 'Made for every table',
            'heading' => 'Shared by every generation.',
            'intro'   => 'From a morning run to a family weekend away, Arilia turns up where people gather.',
        ),
        'values'    => array(
            'eyebrow' => 'What we stand for',
            'heading' => 'Simple things, done brightly.',
            'items'   => array(
                array( 'Real fruit first', 'Flavour comes from the fruit on the label, and every bottle is named after what is inside.' ),
                array( 'A size that makes sense', '250 ml is one serving: no half-finished bottles, no waste, no second-guessing.' ),
                array( 'Made to be shared', 'Built for retailers, cafés, hotels and events as much as for the fridge at home.' ),
            ),
        ),
        'cta'       => array(
            'heading' => 'Make room for a little wonder.',
            'copy'    => 'Explore all seven flavours, or talk to us about bringing Arilia to your shelves and tables.',
        ),
    );
}

/** Core-block markup for the whole page. */
function arilia_about_blocks() {
    $c = arilia_about_copy();

    $hero = arilia_group(
        arilia_group( arilia_p( esc_html( $c['eyebrow'] ), 'arilia-eyebrow' ) . arilia_h( esc_html( $c['heading'] ), 1, 'arilia-about-title' ) . arilia_p( esc_html( $c['lead'] ), 'arilia-about-lead' ), 'arilia-about-hero__copy' ) .
        arilia_group( arilia_image( 'bottle-pomegranate-v126.webp', 'Arilia Pomegranate Drink, the first Arilia flavour, in the 250 ml double-bulb bottle', 'arilia-about-hero__image' ), 'arilia-about-hero__art' ),
        'arilia-about-hero', 'section'
    );

    $o = $c['origin'];
    $origin_copy = arilia_p( esc_html( $o['eyebrow'] ), 'arilia-eyebrow' ) . arilia_h( esc_html( $o['heading'] ), 2 );
    foreach ( $o['body'] as $para ) { $origin_copy .= arilia_p( esc_html( $para ) ); }
    $origin = arilia_group(
        arilia_group( arilia_image( 'bottle-pomegranate-apple-v126.webp', 'Arilia Pomegranate + Apple Drink beside fresh pomegranate and apple', 'arilia-about-origin__image' ), 'arilia-about-origin__art' ) .
        arilia_group( $origin_copy, 'arilia-about-origin__copy' ),
        'arilia-about-origin', 'section'
    );

    $p = $c['process']; $steps = '';
    foreach ( $p['steps'] as $i => $st ) {
        $steps .= arilia_group( arilia_p( sprintf( '%02d', $i + 1 ), 'arilia-about-step__num' ) . arilia_h( esc_html( $st[0] ), 3 ) . arilia_p( esc_html( $st[1] ) ), 'arilia-about-step' );
    }
    $process = arilia_group(
        arilia_group( arilia_p( esc_html( $p['eyebrow'] ), 'arilia-eyebrow' ) . arilia_h( esc_html( $p['heading'] ), 2 ) . arilia_p( esc_html( $p['intro'] ), 'arilia-about-intro' ), 'arilia-about-head' ) .
        arilia_group( $steps, 'arilia-about-steps' ),
        'arilia-about-process', 'section'
    );

    $nums = '';
    foreach ( $c['numbers'] as $n ) {
        $nums .= arilia_group( arilia_p( '<strong>' . esc_html( $n[0] ) . '</strong> <span>' . esc_html( $n[1] ) . '</span>', 'arilia-about-number__value' ) . arilia_p( esc_html( $n[2] ), 'arilia-about-number__copy' ), 'arilia-about-number' );
    }
    $numbers = arilia_group( arilia_group( $nums, 'arilia-about-numbers__grid' ), 'arilia-about-numbers', 'section' );

    $r = $c['rind']; $points = '';
    foreach ( $r['points'] as $pt ) { $points .= arilia_group( arilia_h( esc_html( $pt[0] ), 3 ) . arilia_p( esc_html( $pt[1] ) ), 'arilia-about-point' ); }
    $rind = arilia_group(
        arilia_group( arilia_image( 'bottle-mango-v126.webp', 'Arilia Mango Drink in the 250 ml double-bulb bottle', 'arilia-about-rind__image' ), 'arilia-about-rind__art' ) .
        arilia_group( arilia_p( esc_html( $r['eyebrow'] ), 'arilia-eyebrow' ) . arilia_h( esc_html( $r['heading'] ), 2 ) . arilia_group( $points, 'arilia-about-points' ), 'arilia-about-rind__copy' ),
        'arilia-about-rind', 'section'
    );

    $g = $c['gallery'];
    $photos = array(
        array( 'audience-seniors-v124.webp', 'Three generations enjoying Arilia on a camping trip', 'wide' ),
        array( 'audience-women-v124.webp', 'Friends sharing Arilia drinks', 'tall' ),
        array( 'audience-men-v126.webp', 'Arilia as part of a gym routine', 'tall' ),
        array( 'audience-kids-v124.webp', 'Children enjoying Arilia drinks at a picnic', 'wide' ),
    );
    $tiles = '';
    foreach ( $photos as $ph ) { $tiles .= arilia_image( $ph[0], $ph[1], 'arilia-about-tile arilia-about-tile--' . $ph[2] ); }
    $gallery = arilia_group(
        arilia_group( arilia_p( esc_html( $g['eyebrow'] ), 'arilia-eyebrow' ) . arilia_h( esc_html( $g['heading'] ), 2 ) . arilia_p( esc_html( $g['intro'] ), 'arilia-about-intro' ), 'arilia-about-head' ) .
        arilia_group( $tiles, 'arilia-about-gallery__grid' ),
        'arilia-about-gallery', 'section'
    );

    $v = $c['values']; $vals = '';
    foreach ( $v['items'] as $it ) { $vals .= arilia_group( arilia_h( esc_html( $it[0] ), 3 ) . arilia_p( esc_html( $it[1] ) ), 'arilia-about-value' ); }
    $values = arilia_group(
        arilia_group( arilia_p( esc_html( $v['eyebrow'] ), 'arilia-eyebrow' ) . arilia_h( esc_html( $v['heading'] ), 2 ), 'arilia-about-head' ) .
        arilia_group( $vals, 'arilia-about-values__grid' ),
        'arilia-about-values', 'section'
    );

    $cta = arilia_group(
        arilia_p( 'Retail · Distribution · Hospitality', 'arilia-eyebrow' ) . arilia_h( esc_html( $c['cta']['heading'] ), 2 ) . arilia_p( esc_html( $c['cta']['copy'] ) ) .
        arilia_buttons( array( array( 'Explore the flavours', arilia_url( 'products' ) ), array( 'Stock Arilia', arilia_url( 'contact' ) ) ), 'arilia-contact-buttons' ),
        'arilia-connect arilia-about-cta', 'section', 'connect'
    );

    return arilia_group( $hero . $origin . $process . $numbers . $rind . $gallery . $values . $cta, 'arilia-about' );
}

/** Replace the saved Our Story page content at render time. */
function arilia_about_the_content( $content ) {
    if ( is_admin() || ! in_the_loop() || ! is_main_query() || ! is_page( 'our-story' ) ) { return $content; }
    if ( false !== strpos( $content, 'arilia-about' ) ) { return $content; }
    return do_blocks( arilia_about_blocks() );
}
add_filter( 'the_content', 'arilia_about_the_content', 9 );
