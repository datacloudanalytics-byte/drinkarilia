<?php
/**
 * Appearance → Arilia Setup: explicit, capability-checked starter import,
 * page layout upgrades and contact settings. Nothing runs on activation.
 *
 * @package Arilia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARILIA_CONTENT_VERSION', '2.0.2' );

function arilia_setup_menu() {
	add_theme_page( 'Arilia Setup', 'Arilia Setup', 'manage_options', 'arilia-setup', 'arilia_setup_screen' );
}
add_action( 'admin_menu', 'arilia_setup_menu' );

function arilia_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'arilia_starter_complete' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_arilia-setup' === $screen->id ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Welcome to Arilia. Create the editable starter pages when you are ready; activating this theme does not import or replace anything.', 'arilia' ) . ' <a href="' . esc_url( admin_url( 'themes.php?page=arilia-setup' ) ) . '">' . esc_html__( 'Open Arilia Setup', 'arilia' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'arilia_setup_notice' );

/**
 * Status of each starter page for the setup table.
 */
function arilia_page_status() {
	$rows = array();
	foreach ( arilia_page_manifest() as $slug => $title ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		$row  = array( 'slug' => $slug, 'title' => $title, 'id' => 0, 'status' => 'missing', 'content' => '', 'edit' => '' );
		if ( $page ) {
			$row['id']     = $page->ID;
			$row['status'] = $page->post_status;
			$row['edit']   = get_edit_post_link( $page->ID );
			$seeded        = get_post_meta( $page->ID, '_arilia_seeded_content', true );
			$hash          = get_post_meta( $page->ID, '_arilia_content_hash', true );
			if ( get_post_meta( $page->ID, '_arilia_starter_slug', true ) !== $slug ) {
				$row['content'] = 'foreign';
			} elseif ( ARILIA_CONTENT_VERSION === $seeded && $hash === md5( $page->post_content ) ) {
				$row['content'] = 'current';
			} elseif ( ARILIA_CONTENT_VERSION === $seeded ) {
				$row['content'] = 'edited';
			} else {
				$row['content'] = 'legacy';
			}
		}
		$rows[] = $row;
	}
	return $rows;
}

function arilia_setup_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$result = get_transient( 'arilia_setup_result_' . get_current_user_id() );
	if ( $result ) {
		delete_transient( 'arilia_setup_result_' . get_current_user_id() );
	}
	$contact = arilia_contact();
	$rows    = arilia_page_status();
	$labels  = array(
		'current' => 'Up to date',
		'edited'  => 'Edited by you',
		'legacy'  => 'Older layout (1.x)',
		'foreign' => 'Not an Arilia page',
		''        => '—',
	);
	?>
	<div class="wrap arilia-setup">
		<h1>Arilia Website Setup</h1>
		<?php if ( $result ) : ?>
			<div class="notice <?php echo ! empty( $result['error'] ) ? 'notice-error' : 'notice-success'; ?>"><p><?php echo esc_html( $result['message'] ); ?></p></div>
		<?php endif; ?>

		<h2>1. Contact details</h2>
		<p>Used by the footer, the enquiry form, the WhatsApp and call buttons in the header and the "Stock Arilia" band. Page copy created before a change keeps the old details; edit those pages or run the layout upgrade below.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="arilia_save_contact">
			<?php wp_nonce_field( 'arilia_save_contact' ); ?>
			<table class="form-table" role="presentation">
				<tr><th scope="row"><label for="arilia_email">Enquiry email</label></th><td><input name="contact[email]" id="arilia_email" type="email" class="regular-text" value="<?php echo esc_attr( $contact['email'] ); ?>" required> <p class="description">Enquiry form messages are sent here.</p></td></tr>
				<tr><th scope="row"><label for="arilia_phone">Phone (as displayed)</label></th><td><input name="contact[phone]" id="arilia_phone" type="text" class="regular-text" value="<?php echo esc_attr( $contact['phone'] ); ?>"></td></tr>
				<tr><th scope="row"><label for="arilia_whatsapp">WhatsApp number (digits only, with country code)</label></th><td><input name="contact[whatsapp]" id="arilia_whatsapp" type="text" class="regular-text" value="<?php echo esc_attr( $contact['whatsapp'] ); ?>" pattern="[0-9]{8,15}"></td></tr>
				<tr><th scope="row"><label for="arilia_legal">Copyright name</label></th><td><input name="contact[legal_name]" id="arilia_legal" type="text" class="regular-text" value="<?php echo esc_attr( $contact['legal_name'] ); ?>"></td></tr>
				<tr><th scope="row"><label for="arilia_instagram">Instagram URL</label></th><td><input name="contact[instagram]" id="arilia_instagram" type="url" class="regular-text" value="<?php echo esc_attr( $contact['instagram'] ); ?>" placeholder="https://www.instagram.com/…"></td></tr>
				<tr><th scope="row"><label for="arilia_facebook">Facebook URL</label></th><td><input name="contact[facebook]" id="arilia_facebook" type="url" class="regular-text" value="<?php echo esc_attr( $contact['facebook'] ); ?>" placeholder="https://www.facebook.com/…"></td></tr>
			</table>
			<?php submit_button( 'Save contact details' ); ?>
		</form>

		<h2>2. Starter pages</h2>
		<p>Creates Home, Products, Our Story, Contact &amp; Distribution and seven flavour pages from core WordPress blocks, and copies the artwork into the Media Library. Existing pages are never overwritten. If an unrelated page already uses one of these slugs, setup stops and tells you which one.</p>
		<table class="widefat striped" style="max-width:860px">
			<thead><tr><th>Page</th><th>Status</th><th>Layout</th><th></th></tr></thead>
			<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<tr>
					<td><?php echo esc_html( $row['title'] ); ?> <code>/<?php echo esc_html( $row['slug'] ); ?>/</code></td>
					<td><?php echo esc_html( 'missing' === $row['status'] ? 'Not created' : ucfirst( $row['status'] ) ); ?></td>
					<td><?php echo esc_html( $labels[ $row['content'] ] ); ?></td>
					<td><?php if ( $row['edit'] ) : ?><a href="<?php echo esc_url( $row['edit'] ); ?>">Edit</a><?php endif; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px">
			<input type="hidden" name="action" value="arilia_starter">
			<?php wp_nonce_field( 'arilia_starter' ); ?>
			<p><label><input type="checkbox" name="publish_pages" value="1"> Publish new starter pages now. Leave unchecked to create drafts for review.</label></p>
			<p><label><input type="checkbox" name="set_homepage" value="1"> Make Home the website homepage (only when publishing). Changes Settings → Reading.</label></p>
			<p><label><input type="checkbox" name="set_site_title" value="1"> Set the site title to “Arilia” and the tagline to “A little bottle. A world of flavour.”</label></p>
			<?php submit_button( 'Create missing starter pages' ); ?>
		</form>

		<h2>3. Upgrade page layouts to <?php echo esc_html( ARILIA_CONTENT_VERSION ); ?></h2>
		<p>Replaces the content of starter pages that still use the older 1.x layout (or that you want reset) with the new design. WordPress keeps the previous content as a <strong>revision</strong>, so you can restore it from the page editor. Pages marked “Edited by you” are only touched if you tick the box.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="arilia_upgrade">
			<?php wp_nonce_field( 'arilia_upgrade' ); ?>
			<p><label><input type="checkbox" name="include_edited" value="1"> Also reset pages I have edited (previous content stays available as a revision).</label></p>
			<?php submit_button( 'Upgrade page layouts', 'secondary' ); ?>
		</form>

		<h2>Editing the website</h2>
		<ul style="list-style:disc;padding-left:20px">
			<li><strong>Pages → All Pages:</strong> change copy, product descriptions, links and artwork with the block editor.</li>
			<li><strong>Appearance → Editor:</strong> edit the shared Header and Footer, upload a logo (Site Logo block) and adjust Styles.</li>
			<li><strong>Settings → General:</strong> upload a Site Icon (favicon). The theme shows its own pomegranate icon until you do.</li>
			<li><strong>Enquiry form:</strong> messages are emailed to the address above. Nothing is stored in the database. Test it once after launch and check spam folders; if mail does not arrive, install an SMTP plugin.</li>
			<li><strong>Patterns:</strong> Pages → Add New → Patterns → “Arilia pages” inserts another copy of any starter page or section.</li>
		</ul>
		<p><a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">Open Pages</a> <a class="button" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>">Open Site Editor</a></p>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 * Media import
 * ---------------------------------------------------------------------- */

/**
 * Copy a theme image into the Media Library once. Returns the attachment ID
 * or 0 if the upload directory is not writable.
 */
function arilia_import_image( $file, $title ) {
	$existing = arilia_attachment_for( $file );
	if ( $existing ) {
		return $existing;
	}
	$path = ARILIA_DIR . '/assets/images/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$bits = wp_upload_bits( 'arilia-' . $file, null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $bits['error'] ) ) {
		return 0;
	}
	$type = wp_check_filetype( $bits['file'] );
	$id   = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
			'meta_input'     => array( '_arilia_asset' => $file ),
		),
		$bits['file']
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $bits['file'] ) );
	return (int) $id;
}

function arilia_import_all_images() {
	$ids = array(
		'pomegranate.webp'       => arilia_import_image( 'pomegranate.webp', 'Arilia Pomegranate Juice 250 ml bottle' ),
		'pomegranate-apple.webp' => arilia_import_image( 'pomegranate-apple.webp', 'Arilia Pomegranate + Apple Drink 250 ml bottle' ),
		'collection.webp'        => arilia_import_image( 'collection.webp', 'The Arilia 250 ml collection' ),
	);
	foreach ( arilia_products() as $product ) {
		$ids[ $product['image'] ] = arilia_import_image( $product['image'], sprintf( 'Arilia %s %s 250 ml', $product['name'], $product['type'] ) );
	}
	return $ids;
}

/* -------------------------------------------------------------------------
 * Page import and upgrade
 * ---------------------------------------------------------------------- */

function arilia_seed_page( $id, $slug, $publish ) {
	$content = arilia_page_content( $slug );
	if ( ! $content ) {
		return new WP_Error( 'arilia_content_missing', 'A starter page could not be generated. Existing content was preserved.' );
	}
	$products = arilia_products();
	$excerpt  = isset( $products[ $slug ] ) ? $products[ $slug ]['tagline'] . ' ' . $products[ $slug ]['copy'] : 'Arilia: seven bright fruit flavours in a signature 250 ml double-bulb bottle. Pure fruit wellness for retail, hospitality and home.';
	$args     = array( 'ID' => $id, 'post_content' => $content, 'post_excerpt' => $excerpt );
	if ( null !== $publish ) {
		$args['post_status'] = $publish ? 'publish' : 'draft';
	}
	$updated = wp_update_post( wp_slash( $args ), true );
	if ( is_wp_error( $updated ) ) {
		return $updated;
	}
	update_post_meta( $id, '_arilia_seeded_content', ARILIA_CONTENT_VERSION );
	update_post_meta( $id, '_arilia_content_hash', md5( get_post_field( 'post_content', $id ) ) );
	if ( isset( $products[ $slug ] ) ) {
		$thumb = arilia_attachment_for( $products[ $slug ]['image'] );
		if ( $thumb ) {
			set_post_thumbnail( $id, $thumb );
		}
	}
	return true;
}

function arilia_import_starter( $publish, $set_homepage, $set_title ) {
	$manifest = arilia_page_manifest();
	$map      = array();
	foreach ( $manifest as $slug => $title ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing && $slug !== get_post_meta( $existing->ID, '_arilia_starter_slug', true ) ) {
			return new WP_Error( 'arilia_slug_conflict', sprintf( 'An existing page already uses “%s”. Rename that page’s slug, or build manually with the Arilia patterns. No pages were overwritten.', $slug ) );
		}
		if ( $existing ) {
			$map[ $slug ] = (int) $existing->ID;
		}
	}
	arilia_import_all_images();

	$new_count = 0;
	foreach ( $manifest as $slug => $title ) {
		if ( isset( $map[ $slug ] ) ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_title'     => $title,
				'post_name'      => $slug,
				'post_type'      => 'page',
				'post_status'    => 'draft',
				'post_content'   => '',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'meta_input'     => array( '_arilia_starter_slug' => $slug, '_wp_page_template' => 'brand-page' ),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$map[ $slug ] = (int) $id;
		++$new_count;
	}
	// All IDs exist now, so page links resolve with any permalink structure.
	foreach ( $manifest as $slug => $title ) {
		$id = $map[ $slug ];
		if ( get_post_meta( $id, '_arilia_seeded_content', true ) ) {
			continue;
		}
		if ( '' !== trim( get_post_field( 'post_content', $id ) ) ) {
			update_post_meta( $id, '_arilia_seeded_content', 'preserved' );
			continue;
		}
		$seeded = arilia_seed_page( $id, $slug, $publish );
		if ( is_wp_error( $seeded ) ) {
			return $seeded;
		}
	}
	if ( $set_homepage && 'publish' === get_post_status( $map['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $map['home'] );
	}
	if ( $set_title ) {
		update_option( 'blogname', 'Arilia' );
		update_option( 'blogdescription', 'A little bottle. A world of flavour.' );
	}
	update_option( 'arilia_starter_complete', ARILIA_CONTENT_VERSION );
	return array( 'new_count' => $new_count, 'pages' => $map );
}

function arilia_upgrade_layouts( $include_edited ) {
	arilia_import_all_images();
	$done = 0;
	foreach ( arilia_page_status() as $row ) {
		if ( ! $row['id'] || 'foreign' === $row['content'] || 'current' === $row['content'] ) {
			continue;
		}
		if ( 'edited' === $row['content'] && ! $include_edited ) {
			continue;
		}
		$seeded = arilia_seed_page( $row['id'], $row['slug'], null );
		if ( is_wp_error( $seeded ) ) {
			return $seeded;
		}
		++$done;
	}
	return $done;
}

/* -------------------------------------------------------------------------
 * Request handlers
 * ---------------------------------------------------------------------- */

function arilia_setup_lock() {
	$lock = get_option( 'arilia_setup_lock' );
	if ( $lock && (int) $lock > time() - 10 * MINUTE_IN_SECONDS ) {
		return false;
	}
	update_option( 'arilia_setup_lock', time(), false );
	return true;
}

function arilia_setup_redirect( $message ) {
	set_transient( 'arilia_setup_result_' . get_current_user_id(), $message, 120 );
	wp_safe_redirect( admin_url( 'themes.php?page=arilia-setup' ) );
	exit;
}

function arilia_handle_starter() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'You do not have permission to create these pages.', 'arilia' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'arilia_starter' );
	$publish = ! empty( $_POST['publish_pages'] );
	if ( $publish && ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'You do not have permission to publish pages.', 'arilia' ), '', array( 'response' => 403 ) );
	}
	if ( ! arilia_setup_lock() ) {
		arilia_setup_redirect( array( 'error' => true, 'message' => 'Setup is already running. Try again in a few minutes.' ) );
	}
	try {
		$result  = arilia_import_starter( $publish, ! empty( $_POST['set_homepage'] ), ! empty( $_POST['set_site_title'] ) );
		$message = is_wp_error( $result )
			? array( 'error' => true, 'message' => $result->get_error_message() )
			: array( 'error' => false, 'message' => sprintf( 'Created %d new page(s). Existing content was preserved. Review the table below, then publish drafts from Pages when you are happy.', $result['new_count'] ) );
	} catch ( Throwable $error ) {
		$message = array( 'error' => true, 'message' => 'Setup did not complete: ' . $error->getMessage() . ' Created drafts were kept; run setup again to resume.' );
	} finally {
		delete_option( 'arilia_setup_lock' );
	}
	arilia_setup_redirect( $message );
}
add_action( 'admin_post_arilia_starter', 'arilia_handle_starter' );

function arilia_handle_upgrade() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'You do not have permission to edit these pages.', 'arilia' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'arilia_upgrade' );
	if ( ! arilia_setup_lock() ) {
		arilia_setup_redirect( array( 'error' => true, 'message' => 'Setup is already running. Try again in a few minutes.' ) );
	}
	try {
		$result  = arilia_upgrade_layouts( ! empty( $_POST['include_edited'] ) );
		$message = is_wp_error( $result )
			? array( 'error' => true, 'message' => $result->get_error_message() )
			: array( 'error' => false, 'message' => sprintf( 'Upgraded %d page layout(s). Previous content is available under Revisions in each page editor.', $result ) );
	} catch ( Throwable $error ) {
		$message = array( 'error' => true, 'message' => 'Upgrade did not complete: ' . $error->getMessage() );
	} finally {
		delete_option( 'arilia_setup_lock' );
	}
	arilia_setup_redirect( $message );
}
add_action( 'admin_post_arilia_upgrade', 'arilia_handle_upgrade' );

function arilia_handle_save_contact() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to change settings.', 'arilia' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'arilia_save_contact' );
	$in    = isset( $_POST['contact'] ) && is_array( $_POST['contact'] ) ? wp_unslash( $_POST['contact'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$clean = array(
		'email'      => sanitize_email( $in['email'] ?? '' ),
		'phone'      => sanitize_text_field( $in['phone'] ?? '' ),
		'whatsapp'   => preg_replace( '/\D+/', '', $in['whatsapp'] ?? '' ),
		'legal_name' => sanitize_text_field( $in['legal_name'] ?? '' ),
		'instagram'  => esc_url_raw( $in['instagram'] ?? '' ),
		'facebook'   => esc_url_raw( $in['facebook'] ?? '' ),
	);
	if ( ! is_email( $clean['email'] ) ) {
		arilia_setup_redirect( array( 'error' => true, 'message' => 'Please enter a valid enquiry email address.' ) );
	}
	update_option( 'arilia_contact', wp_parse_args( $clean, arilia_contact_defaults() ) );
	arilia_setup_redirect( array( 'error' => false, 'message' => 'Contact details saved.' ) );
}
add_action( 'admin_post_arilia_save_contact', 'arilia_handle_save_contact' );
