<?php
/**
 * Server render for arilia/enquiry-form.
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo arilia_enquiry_form_html( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the builder.
