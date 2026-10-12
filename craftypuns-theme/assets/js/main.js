// Mobile nav toggle is handled inline in header.php.

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
