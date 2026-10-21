/* Arilia: progressive enhancements only. The site works without JavaScript. */
( function () {
	'use strict';
	var header = document.querySelector( '.arilia-header' );
	if ( header ) {
		var update = function () { header.classList.toggle( 'is-scrolled', window.scrollY > 8 ); };
		update();
		window.addEventListener( 'scroll', update, { passive: true } );
	}
	// Smooth-scroll the Products page filter chips, respecting reduced motion.
	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	document.querySelectorAll( '.arilia-filter a[href^="#"]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function ( e ) {
			var target = document.getElementById( link.getAttribute( 'href' ).slice( 1 ) );
			if ( ! target ) { return; }
			e.preventDefault();
			target.scrollIntoView( { behavior: reduce ? 'auto' : 'smooth', block: 'start' } );
			target.setAttribute( 'tabindex', '-1' );
			target.focus( { preventScroll: true } );
			history.replaceState( null, '', '#' + target.id );
		} );
	} );
} )();
