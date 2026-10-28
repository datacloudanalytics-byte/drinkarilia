/* Arilia homepage hero slider: auto-advances every 3 s, pauses on hover/focus, honours reduced motion. */
( function () {
	'use strict';
	var root = document.querySelector( '.arilia-slider' );
	if ( ! root ) { return; }
	var slides = root.querySelectorAll( '.arilia-slide' ), dots = root.querySelectorAll( '.arilia-slider__dot' );
	if ( slides.length < 2 ) { return; }
	var i = 0, timer = null, interval = parseInt( root.getAttribute( 'data-interval' ), 10 ) || 3000;
	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	function show( n ) {
		i = ( n + slides.length ) % slides.length;
		slides.forEach( function ( s, k ) {
			var on = k === i; s.classList.toggle( 'is-active', on );
			if ( on ) { s.removeAttribute( 'aria-hidden' ); s.removeAttribute( 'tabindex' ); } else { s.setAttribute( 'aria-hidden', 'true' ); s.setAttribute( 'tabindex', '-1' ); }
		} );
		dots.forEach( function ( d, k ) { d.classList.toggle( 'is-active', k === i ); if ( k === i ) { d.setAttribute( 'aria-current', 'true' ); } else { d.removeAttribute( 'aria-current' ); } } );
	}
	function start() { if ( reduce || timer ) { return; } timer = setInterval( function () { show( i + 1 ); }, interval ); }
	function stop() { clearInterval( timer ); timer = null; }
	dots.forEach( function ( d ) { d.addEventListener( 'click', function () { stop(); show( parseInt( d.getAttribute( 'data-slide' ), 10 ) ); start(); } ); } );
	root.querySelectorAll( '.arilia-slider__arrow' ).forEach( function ( b ) { b.addEventListener( 'click', function () { stop(); show( i + parseInt( b.getAttribute( 'data-dir' ), 10 ) ); start(); } ); } );
	root.addEventListener( 'keydown', function ( e ) { if ( e.key === 'ArrowLeft' ) { stop(); show( i - 1 ); start(); } else if ( e.key === 'ArrowRight' ) { stop(); show( i + 1 ); start(); } } );
	root.addEventListener( 'mouseenter', stop ); root.addEventListener( 'mouseleave', start );
	root.addEventListener( 'focusin', stop ); root.addEventListener( 'focusout', start );
	document.addEventListener( 'visibilitychange', function () { document.hidden ? stop() : start(); } );
	var sx = 0; root.addEventListener( 'touchstart', function ( e ) { sx = e.touches[0].clientX; stop(); }, { passive: true } );
	root.addEventListener( 'touchend', function ( e ) { var dx = e.changedTouches[0].clientX - sx; if ( Math.abs( dx ) > 40 ) { show( dx < 0 ? i + 1 : i - 1 ); } start(); }, { passive: true } );
	start();
} )();
