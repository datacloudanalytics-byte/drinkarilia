<?php
/**
 * 1.14: "Discover the power of all seven flavours" — benefits strip and
 * audience cards shown directly below the homepage hero poster.
 *
 * The section is built from core blocks so new starter pages can edit it.
 * Existing saved homepages receive it at render time (like the campaign
 * poster) until their content is edited to include it.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function arilia_audience_copy() {
    return array(
        'eyebrow' => 'Discover the power of all seven flavours',
        'heading' => 'A flavour for every age and every lifestyle.',
        'intro'   => 'At Arilia, every bottle is crafted with nature’s finest fruits and ingredients to help support your wellness journey. Whether you’re looking for daily nourishment, natural energy, hydration or antioxidant-rich refreshment, there’s an Arilia flavour made for you.',
        'benefits' => array(
            array( 'antioxidant', 'Antioxidant-rich fruit' ),
            array( 'vitamins', 'Natural vitamins' ),
            array( 'hydration', 'Refreshing hydration' ),
            array( 'family', 'A flavour for every age' ),
        ),
        'groups' => array(
            array(
                'icon'  => 'women',
                'title' => 'For women’s wellness',
                'items' => array(
                    array( 'cranberry', 'Cranberry Drink', 'Known for its antioxidant content, cranberry is a popular choice among women seeking a refreshing drink to support an active and healthy lifestyle. Its naturally tangy flavour makes wellness delicious.' ),
                    array( 'pomegranate', 'Pomegranate Juice', 'Pomegranate is rich in antioxidants that help protect cells from oxidative stress and support overall well-being. A perfect daily companion for vibrant living.' ),
                    array( 'pomegranate-apple', 'Pomegranate + Apple Drink', 'Combining the antioxidant goodness of pomegranate with the natural vitamins found in apples, this blend offers a delicious way to stay refreshed and energised throughout the day.' ),
                ),
            ),
            array(
                'icon'  => 'men',
                'title' => 'For men’s active lifestyle',
                'items' => array(
                    array( 'beet-lime-ginger', 'Beet + Lime + Ginger Juice', 'This powerful combination brings together the natural vitality of beets, the freshness of lime and the warming kick of ginger. Ideal for supporting an active lifestyle and maintaining energy throughout the day.' ),
                    array( 'pomegranate', 'Pomegranate Juice', 'A favourite among fitness enthusiasts, pomegranate provides naturally occurring antioxidants that support overall wellness and daily performance.' ),
                ),
            ),
            array(
                'icon'  => 'kids',
                'title' => 'For growing kids',
                'items' => array(
                    array( 'mango', 'Mango Juice', 'Loaded with delicious tropical flavour, mango is a natural source of vitamins and nutrients that help children stay refreshed and energised.' ),
                    array( 'strawberry', 'Strawberry Juice', 'Sweet, fruity and packed with naturally occurring antioxidants, strawberry juice is a kid-friendly favourite that’s both tasty and refreshing.' ),
                    array( 'dragon-fruit', 'Dragon Fruit Juice', 'With its vibrant colour and unique flavour, dragon fruit offers a fun and exciting way for kids to enjoy fruit-based nutrition.' ),
                ),
            ),
            array(
                'icon'  => 'energy',
                'title' => 'For daily energy & vitality',
                'items' => array(
                    array( 'beet-lime-ginger', 'Beet + Lime + Ginger Juice', 'A naturally invigorating blend that helps you feel refreshed and ready to take on the day.' ),
                    array( 'mango', 'Mango Juice', 'Provides natural fruit goodness and refreshing hydration for people with busy lifestyles.' ),
                    array( 'dragon-fruit', 'Dragon Fruit Juice', 'A colourful superfruit beverage that supports hydration and everyday wellness.' ),
                ),
            ),
            array(
                'icon'  => 'seniors',
                'title' => 'Wellness for seniors',
                'lead'  => 'As we age, maintaining hydration and energy becomes increasingly important. Arilia’s fruit-based beverages provide natural goodness, antioxidants and refreshing flavours that can be enjoyed as part of a balanced lifestyle.',
                'items' => array(
                    array( 'pomegranate', 'Pomegranate Juice', '' ),
                    array( 'cranberry', 'Cranberry Drink', '' ),
                    array( 'beet-lime-ginger', 'Beet + Lime + Ginger Juice', 'These nutrient-rich favourites help seniors stay refreshed, active and energised throughout the day.' ),
                ),
            ),
        ),
        'close_heading' => 'One family. Seven flavours. Endless little wonders.',
        'close_copy'    => 'From energetic kids to health-conscious adults and active seniors, Arilia offers a flavour for every age and every lifestyle. Enjoy the perfect balance of taste, nutrition and refreshment in every sip.',
        'close_line'    => 'Arilia — A Little Wonder in every bottle.',
        'note'          => 'Individual health benefits may vary. Arilia beverages are intended to complement a balanced diet and healthy lifestyle.',
    );
}

/** Core-block markup for the section (used by the starter import and by the render-time injection). */
function arilia_audience_blocks() {
    $c = arilia_audience_copy();

    $benefits = '';
    foreach ( $c['benefits'] as $b ) {
        $benefits .= arilia_p( esc_html( $b[1] ), 'arilia-benefit arilia-benefit--' . $b[0] );
    }
    $strip = arilia_group( arilia_group( $benefits, 'arilia-benefits' ), 'arilia-benefits-wrap', 'section' );

    $cards = '';
    foreach ( $c['groups'] as $g ) {
        $inner = arilia_p( '', 'arilia-audience-icon arilia-audience-icon--' . $g['icon'] ) . arilia_h( esc_html( $g['title'] ), 3, 'arilia-audience-title' );
        if ( ! empty( $g['lead'] ) ) { $inner .= arilia_p( esc_html( $g['lead'] ), 'arilia-audience-lead' ); }
        foreach ( $g['items'] as $it ) {
            $product = arilia_products()[ $it[0] ];
            $thumb = arilia_image( 'bottle-' . $it[0] . '-v114.webp', 'Arilia ' . $it[1] . ' 250 ml', 'arilia-audience-thumb' );
            $name  = arilia_p( '<a href="' . esc_url( arilia_url( $it[0] ) ) . '">' . esc_html( $it[1] ) . '</a>', 'arilia-audience-name' );
            $copy  = $it[2] ? arilia_p( esc_html( $it[2] ), 'arilia-audience-copy' ) : '';
            $inner .= arilia_group( $thumb . arilia_group( $name . $copy, 'arilia-audience-text' ), 'arilia-audience-item' );
        }
        $cards .= arilia_group( $inner, 'arilia-audience-card arilia-audience-card--' . $g['icon'], 'article' );
    }

    $head  = arilia_group( arilia_p( esc_html( $c['eyebrow'] ), 'arilia-eyebrow' ) . arilia_h( esc_html( $c['heading'] ), 2, 'arilia-audience-heading' ) . arilia_p( esc_html( $c['intro'] ), 'arilia-audience-intro' ), 'arilia-audience-head' );
    $close = arilia_group(
        arilia_h( esc_html( $c['close_heading'] ), 2, 'arilia-audience-close-heading' ) .
        arilia_p( esc_html( $c['close_copy'] ) ) .
        arilia_p( esc_html( $c['close_line'] ), 'arilia-audience-signature' ) .
        arilia_buttons( array( array( 'Explore all seven flavours', arilia_url( 'products' ) ), array( 'Stock Arilia', arilia_url( 'contact' ) ) ), 'arilia-audience-buttons' ) .
        arilia_p( esc_html( $c['note'] ), 'arilia-audience-note' ),
        'arilia-audience-close'
    );
    return $strip . arilia_group( $head . arilia_group( $cards, 'arilia-audience-grid' ) . $close, 'arilia-audience', 'section', 'for-everyone' );
}

/** Rendered HTML, for injection into already saved homepages. */
function arilia_audience_html() {
    static $html = null;
    if ( null === $html ) { $html = do_blocks( arilia_audience_blocks() ); }
    return $html;
}

/** Append the section after the homepage campaign poster when the saved page does not already contain it. */
function arilia_audience_inject( $html, $block ) {
    $classes = preg_split( '/\s+/', isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '' );
    if ( ! in_array( 'arilia-hero', $classes, true ) || ! is_front_page() || is_admin() ) { return $html; }
    $content = get_post_field( 'post_content', get_queried_object_id() );
    if ( is_string( $content ) && false !== strpos( $content, 'arilia-audience' ) ) { return $html; }
    return $html . arilia_audience_html();
}
add_filter( 'render_block_core/group', 'arilia_audience_inject', 30, 2 );
