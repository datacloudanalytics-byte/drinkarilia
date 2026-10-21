/* global wp */
( function () {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'arilia/enquiry-form', {
		edit: function ( props ) {
			return el(
				'div',
				useBlockProps( { className: 'arilia-form-editor' } ),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Enquiry form' },
						el( TextControl, {
							label: 'Optional heading above the form',
							value: props.attributes.heading,
							onChange: function ( v ) { props.setAttributes( { heading: v } ); }
						} ),
						el( 'p', null, 'Messages are emailed to the address set under Appearance → Arilia Setup.' )
					)
				),
				el( ServerSideRender, { block: 'arilia/enquiry-form', attributes: props.attributes } )
			);
		},
		save: function () { return null; }
	} );
} )();
