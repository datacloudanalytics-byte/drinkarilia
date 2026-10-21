<?php
/** Explicit, capability-checked starter import. No content is changed on activation. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function arilia_starter_manifest() {
    $pages = array( 'home' => 'Home', 'products' => 'Products', 'our-story' => 'Our Story', 'contact' => 'Contact & Distribution' );
    foreach ( arilia_products() as $slug => $product ) { $pages[ $slug ] = $product['name']; }
    return $pages;
}
function arilia_setup_menu() { add_theme_page( 'Arilia Setup', 'Arilia Setup', 'manage_options', 'arilia-setup', 'arilia_setup_screen' ); }
add_action( 'admin_menu', 'arilia_setup_menu' );

function arilia_setup_notice() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    if ( get_option( 'arilia_starter_complete' ) ) { return; }
    echo '<div class="notice notice-info"><p>' . esc_html__( 'Welcome to Arilia. Create editable starter pages when you are ready; activating this theme does not import or replace pages.', 'arilia' ) . ' <a href="' . esc_url( admin_url( 'themes.php?page=arilia-setup' ) ) . '">' . esc_html__( 'Open Arilia Setup', 'arilia' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'arilia_setup_notice' );

function arilia_setup_screen() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $result = get_transient( 'arilia_setup_result_' . get_current_user_id() );
    if ( $result ) { delete_transient( 'arilia_setup_result_' . get_current_user_id() ); }
    ?>
    <div class="wrap">
      <h1>Arilia Website Setup</h1>
      <p>Create Home, Products, Our Story, Contact &amp; Distribution, and seven flavour pages. All pages use WordPress core blocks; no paid page builder or commerce plugin is required.</p>
      <?php if ( $result ) : ?>
      <div class="notice <?php echo ! empty( $result['error'] ) ? 'notice-error' : 'notice-success'; ?>"><p><?php echo esc_html( $result['message'] ); ?></p></div>
      <?php endif; ?>
      <h2>Before importing</h2>
      <p>Existing pages are never overwritten. If an unrelated page already uses one of our starter slugs, setup stops and identifies it. Running setup again preserves all previously imported page content, including edits.</p>
      <p>The starter artwork is the supplied Arilia concept imagery. Review final labels and wording before public launch. Email, Call and WhatsApp links use refresh@pionorybeverages.com and +91 90329 54333.</p>
      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="arilia_starter">
        <?php wp_nonce_field( 'arilia_starter' ); ?>
        <p><label><input type="checkbox" name="publish_pages" value="1"> Publish new starter pages now. If this website is public, visitors can access these pages. Leave unchecked to create drafts for review.</label></p>
        <p><label><input type="checkbox" name="set_homepage" value="1"> Make the new Home page the website homepage (only when publishing). This changes Settings → Reading.</label></p>
        <p><label><input type="checkbox" name="set_site_title" value="1"> Set the site title to Arilia and its tagline to A little bottle. A world of flavour.</label></p>
        <?php submit_button( 'Create Arilia Starter Pages' ); ?>
      </form>
      <h2>Editing the website</h2>
      <ul>
        <li><strong>Pages → All Pages:</strong> change page copy, product descriptions, links and artwork.</li>
        <li><strong>Appearance → Editor:</strong> edit the shared Header, Footer, colours and typography.</li>
        <li><strong>Image replacement:</strong> for a standalone product photo, choose the image block’s Default style. The named Arilia bottle styles crop one bottle from the supplied group artwork.</li>
        <li><strong>Pages → Add New → Patterns → Arilia pages:</strong> insert another copy of a starter page, then edit it.</li>
      </ul>
      <p><a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">Open Pages</a> <a class="button" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>">Open Site Editor</a></p>
      <h2>GoDaddy launch</h2>
      <p>Use the temporary WordPress address while reviewing. Connect drinkarilia.com through your GoDaddy WordPress hosting dashboard when ready, enable HTTPS, and verify both www and root-domain routing. Preserve your email DNS records. In Settings → Reading, control search visibility deliberately. This theme does not alter DNS, privacy settings, hosting, or payments.</p>
    </div>
    <?php
}

function arilia_import_starter( $publish, $set_homepage, $set_title ) {
    $manifest = arilia_starter_manifest();
    $map = array();
    // Preflight every slug before creating anything.
    foreach ( $manifest as $slug => $title ) {
        $existing = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $existing && $slug !== get_post_meta( $existing->ID, '_arilia_starter_slug', true ) ) {
            return new WP_Error( 'arilia_slug_conflict', sprintf( 'An existing page already uses “%s”. Rename that page’s slug, or build manually with the Arilia patterns. No pages were overwritten.', $slug ) );
        }
        if ( $existing ) { $map[ $slug ] = (int) $existing->ID; }
    }
    $new_count = 0;
    foreach ( $manifest as $slug => $title ) {
        if ( isset( $map[ $slug ] ) ) { continue; }
        $id = wp_insert_post( array(
            'post_title' => $title, 'post_name' => $slug, 'post_type' => 'page', 'post_status' => 'draft',
            'post_content' => '', 'comment_status' => 'closed', 'ping_status' => 'closed',
            'meta_input' => array( '_arilia_starter_slug' => $slug, '_wp_page_template' => 'brand-page' ),
        ), true );
        if ( is_wp_error( $id ) ) { return $id; }
        $map[ $slug ] = (int) $id;
        ++$new_count;
    }
    // All page IDs now exist, so links work with both plain and pretty permalinks.
    foreach ( $manifest as $slug => $title ) {
        $id = $map[ $slug ];
        if ( get_post_meta( $id, '_arilia_seeded_content', true ) ) { continue; }
        if ( '' !== trim( get_post_field( 'post_content', $id ) ) ) {
            update_post_meta( $id, '_arilia_seeded_content', 'preserved' );
            continue;
        }
        $content = arilia_page_content( $slug );
        if ( ! $content ) { return new WP_Error( 'arilia_content_missing', 'A starter page could not be generated. Existing content was preserved.' ); }
        $description = isset( arilia_products()[ $slug ] ) ? arilia_products()[ $slug ]['copy'] : 'Explore Arilia’s colourful 250 ml collection and signature double-bulb bottle.';
        $updated = wp_update_post( wp_slash( array(
            'ID' => $id, 'post_content' => $content, 'post_excerpt' => $description,
            'post_status' => $publish ? 'publish' : 'draft',
        ) ), true );
        if ( is_wp_error( $updated ) ) { return $updated; }
        update_post_meta( $id, '_arilia_seeded_content', '1.0.0' );
    }
    if ( $set_homepage && 'publish' === get_post_status( $map['home'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $map['home'] );
    }
    if ( $set_title ) { update_option( 'blogname', 'Arilia' ); update_option( 'blogdescription', 'A little bottle. A world of flavour.' ); }
    update_option( 'arilia_starter_complete', '1.0.0' );
    return array( 'new_count' => $new_count, 'pages' => $map );
}

function arilia_handle_starter() {
    if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_pages' ) ) { wp_die( esc_html__( 'You do not have permission to create these pages.', 'arilia' ), '', array( 'response' => 403 ) ); }
    check_admin_referer( 'arilia_starter' );
    $publish = isset( $_POST['publish_pages'] ) && '1' === $_POST['publish_pages'];
    if ( $publish && ! current_user_can( 'publish_pages' ) ) { wp_die( esc_html__( 'You do not have permission to publish pages.', 'arilia' ), '', array( 'response' => 403 ) ); }
    if ( ! add_option( 'arilia_setup_lock', time(), '', false ) ) {
        $message = array( 'error' => true, 'message' => 'Setup is already running. Refresh after it finishes. If a request was interrupted, an administrator can remove the arilia_setup_lock option after confirming no setup is active.' );
    } else {
        try {
            $result = arilia_import_starter( $publish, isset( $_POST['set_homepage'] ) && '1' === $_POST['set_homepage'], isset( $_POST['set_site_title'] ) && '1' === $_POST['set_site_title'] );
            $message = is_wp_error( $result ) ? array( 'error' => true, 'message' => $result->get_error_message() ) : array( 'error' => false, 'message' => sprintf( 'Created %d new pages. Existing imported content was preserved. Open Pages to review the status of all 11 starter pages. Drafts can be published from Pages; repeating setup does not republish existing drafts.', $result['new_count'] ) );
        } catch ( Throwable $error ) {
            $message = array( 'error' => true, 'message' => 'Setup did not complete. Created drafts have been retained; retry setup to resume. Existing pages were not overwritten.' );
        } finally { delete_option( 'arilia_setup_lock' ); }
    }
    set_transient( 'arilia_setup_result_' . get_current_user_id(), $message, 120 );
    wp_safe_redirect( admin_url( 'themes.php?page=arilia-setup' ) );
    exit;
}
add_action( 'admin_post_arilia_starter', 'arilia_handle_starter' );
