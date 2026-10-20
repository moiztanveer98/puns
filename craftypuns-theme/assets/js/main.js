// Mobile nav toggle is handled inline in header.php.

// Header "Puns" mega-menu: the dropdown shows on :hover (desktop mouse), but
// touch devices, trackpads and keyboard users have no :hover state, so it was
// effectively unopenable for them. This adds a click/tap toggle on top of the
// existing hover behavior, plus outside-click and Escape to close.
(function () {
	var toggles = document.querySelectorAll( '.cp-dropdown-toggle' );
	toggles.forEach( function ( toggle ) {
		var item = toggle.closest( '.cp-dropdown-item' );
		if ( ! item ) {
			return;
		}
		toggle.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			var isOpen = item.classList.toggle( 'cp-open' );
			toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	} );

	document.addEventListener( 'click', function ( e ) {
		document.querySelectorAll( '.cp-dropdown-item.cp-open' ).forEach( function ( item ) {
			if ( ! item.contains( e.target ) ) {
				item.classList.remove( 'cp-open' );
				var btn = item.querySelector( '.cp-dropdown-toggle' );
				if ( btn ) {
					btn.setAttribute( 'aria-expanded', 'false' );
				}
			}
		} );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			document.querySelectorAll( '.cp-dropdown-item.cp-open' ).forEach( function ( item ) {
				item.classList.remove( 'cp-open' );
				var btn = item.querySelector( '.cp-dropdown-toggle' );
				if ( btn ) {
					btn.setAttribute( 'aria-expanded', 'false' );
				}
			} );
		}
	} );
})();

// Adds a "Copy" button to each pun line inside article content, so visitors
// can copy a single pun to their clipboard without selecting text manually.
(function () {
	function makeCopyButton( targetText ) {
		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'pun-copy-btn';
		btn.setAttribute( 'aria-label', 'Copy this pun' );
		btn.textContent = 'Copy';

		btn.addEventListener( 'click', function () {
			var text = targetText();
			var done = function () {
				btn.textContent = 'Copied!';
				btn.classList.add( 'is-copied' );
				setTimeout( function () {
					btn.textContent = 'Copy';
					btn.classList.remove( 'is-copied' );
				}, 1500 );
			};

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then( done, function () {
					fallbackCopy( text, done );
				} );
			} else {
				fallbackCopy( text, done );
			}
		} );

		return btn;
	}

	function fallbackCopy( text, done ) {
		var textarea = document.createElement( 'textarea' );
		textarea.value = text;
		textarea.style.position = 'fixed';
		textarea.style.opacity = '0';
		document.body.appendChild( textarea );
		textarea.select();
		try {
			document.execCommand( 'copy' );
			done();
		} catch ( e ) {
			// no-op: clipboard unavailable
		}
		document.body.removeChild( textarea );
	}

	function initPunCopyButtons( scope ) {
		var containers = ( scope || document ).querySelectorAll( '.prose' );
		containers.forEach( function ( container ) {
			var items = container.querySelectorAll( 'li' );
			items.forEach( function ( item ) {
				if ( item.querySelector( '.pun-copy-btn' ) ) {
					return;
				}
				var line = item.textContent.trim();
				if ( ! line ) {
					return;
				}
				item.classList.add( 'pun-line' );
				var btn = makeCopyButton( function () {
					return item.textContent.replace( /\s*Copy(ied!)?\s*$/, '' ).trim();
				} );
				item.appendChild( btn );
			} );
		} );
	}

	// Exposed so the Pun Generator tool can re-run this on freshly inserted results.
	window.craftypunsInitPunCopy = initPunCopyButtons;

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () { initPunCopyButtons(); } );
	} else {
		initPunCopyButtons();
	}
})();
