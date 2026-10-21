// Puns Generator Tool.
// Loads the pre-built pun dataset and renders a random batch with:
// copy / copy-all, per-pun favorite, regenerate, share, download-as-image,
// a keyword search across all topics, an optional second topic to combine,
// a length filter, voice input for the search box, and a small history panel.
(function () {
	var categorySelect = document.getElementById( 'pg-category' );
	var category2Select = document.getElementById( 'pg-category-2' );
	var countSelect = document.getElementById( 'pg-count' );
	var lengthSelect = document.getElementById( 'pg-length' );
	var searchInput = document.getElementById( 'pg-search' );
	var voiceBtn = document.getElementById( 'pg-voice' );
	var generateBtn = document.getElementById( 'pg-generate' );
	var copyAllBtn = document.getElementById( 'pg-copy-all' );
	var results = document.getElementById( 'pg-results' );
	var empty = document.getElementById( 'pg-empty' );
	var favPanel = document.getElementById( 'pg-favorites' );
	var favList = document.getElementById( 'pg-favorites-list' );
	var favCount = document.getElementById( 'pg-favorites-count' );
	var favCopyAllBtn = document.getElementById( 'pg-favorites-copy-all' );
	var historyPanel = document.getElementById( 'pg-history' );
	var historyList = document.getElementById( 'pg-history-list' );

	if ( ! categorySelect || ! generateBtn || ! window.craftypunsData ) {
		return;
	}

	var FAV_KEY = 'craftypuns_favorites';
	var HIST_KEY = 'craftypuns_history';
	var dataset = null;
	var currentPicks = [];

	/* ---------- storage helpers ---------- */

	function readList( storage, key ) {
		try {
			var raw = storage.getItem( key );
			var parsed = raw ? JSON.parse( raw ) : [];
			return Array.isArray( parsed ) ? parsed : [];
		} catch ( e ) {
			return [];
		}
	}

	function writeList( storage, key, list ) {
		try {
			storage.setItem( key, JSON.stringify( list ) );
		} catch ( e ) {
			// storage unavailable (private mode, quota) — fail silently
		}
	}

	function getFavorites() {
		return readList( window.localStorage, FAV_KEY );
	}

	function isFavorite( text ) {
		return getFavorites().indexOf( text ) !== -1;
	}

	function toggleFavorite( text ) {
		var list = getFavorites();
		var idx = list.indexOf( text );
		if ( idx === -1 ) {
			list.unshift( text );
		} else {
			list.splice( idx, 1 );
		}
		writeList( window.localStorage, FAV_KEY, list );
		renderFavorites();
		return idx === -1;
	}

	function addToHistory( text ) {
		var list = readList( window.sessionStorage, HIST_KEY );
		var idx = list.indexOf( text );
		if ( idx !== -1 ) {
			list.splice( idx, 1 );
		}
		list.unshift( text );
		list = list.slice( 0, 10 );
		writeList( window.sessionStorage, HIST_KEY, list );
		renderHistory();
	}

	/* ---------- clipboard ---------- */

	function copyText( text, onDone ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( text ).then( onDone, function () {
				fallbackCopy( text, onDone );
			} );
		} else {
			fallbackCopy( text, onDone );
		}
	}

	function fallbackCopy( text, onDone ) {
		var textarea = document.createElement( 'textarea' );
		textarea.value = text;
		textarea.style.position = 'fixed';
		textarea.style.opacity = '0';
		document.body.appendChild( textarea );
		textarea.select();
		try {
			document.execCommand( 'copy' );
			onDone();
		} catch ( e ) {
			// clipboard unavailable
		}
		document.body.removeChild( textarea );
	}

	function flashButton( btn, label ) {
		var original = btn.textContent;
		btn.textContent = label;
		btn.classList.add( 'is-copied' );
		setTimeout( function () {
			btn.textContent = original;
			btn.classList.remove( 'is-copied' );
		}, 1500 );
	}

	/* ---------- share ---------- */

	function sharePun( text ) {
		var shareText = text + ' — crafty-puns.com';
		if ( navigator.share ) {
			navigator.share( { text: shareText } ).catch( function () {} );
			return;
		}
		openShareMenu( shareText );
	}

	function openShareMenu( text ) {
		var encoded = encodeURIComponent( text );
		var win = window.open( 'https://wa.me/?text=' + encoded, '_blank', 'noopener' );
		if ( ! win ) {
			window.location.href = 'sms:?body=' + encoded;
		}
	}

	/* ---------- image export ---------- */

	function wrapCanvasText( ctx, text, x, y, maxWidth, lineHeight ) {
		var words = text.split( ' ' );
		var line = '';
		var lines = [];
		for ( var n = 0; n < words.length; n++ ) {
			var testLine = line + words[ n ] + ' ';
			if ( ctx.measureText( testLine ).width > maxWidth && n > 0 ) {
				lines.push( line.trim() );
				line = words[ n ] + ' ';
			} else {
				line = testLine;
			}
		}
		lines.push( line.trim() );
		lines.forEach( function ( l, i ) {
			ctx.fillText( l, x, y + i * lineHeight );
		} );
		return lines.length;
	}

	function downloadPunImage( text ) {
		var canvas = document.createElement( 'canvas' );
		canvas.width = 1200;
		canvas.height = 630;
		var ctx = canvas.getContext( '2d' );

		var grad = ctx.createLinearGradient( 0, 0, 1200, 630 );
		grad.addColorStop( 0, '#fffbeb' );
		grad.addColorStop( 1, '#ffffff' );
		ctx.fillStyle = grad;
		ctx.fillRect( 0, 0, 1200, 630 );
		ctx.fillStyle = '#f59e0b';
		ctx.fillRect( 0, 0, 1200, 14 );

		ctx.fillStyle = '#0f172a';
		ctx.font = 'bold 36px system-ui, sans-serif';
		ctx.textAlign = 'left';
		ctx.fillText( 'Crafty Puns', 60, 90 );

		ctx.font = 'bold 54px system-ui, sans-serif';
		wrapCanvasText( ctx, text, 60, 260, 1080, 68 );

		ctx.font = '26px system-ui, sans-serif';
		ctx.fillStyle = '#64748b';
		ctx.fillText( 'crafty-puns.com', 60, 580 );

		canvas.toBlob( function ( blob ) {
			if ( ! blob ) {
				return;
			}
			var url = URL.createObjectURL( blob );
			var a = document.createElement( 'a' );
			a.href = url;
			a.download = 'crafty-puns.png';
			document.body.appendChild( a );
			a.click();
			document.body.removeChild( a );
			setTimeout( function () { URL.revokeObjectURL( url ); }, 1000 );
		} );
	}

	/* ---------- dataset helpers ---------- */

	function populateCategories( data ) {
		var slugs = Object.keys( data ).sort( function ( a, b ) {
			return data[ a ].label.localeCompare( data[ b ].label );
		} );
		[ categorySelect, category2Select ].forEach( function ( select ) {
			if ( ! select ) {
				return;
			}
			slugs.forEach( function ( slug ) {
				var opt = document.createElement( 'option' );
				opt.value = slug;
				opt.textContent = data[ slug ].label;
				select.appendChild( opt );
			} );
		} );
	}

	function shuffledSample( arr, n ) {
		var copy = arr.slice();
		for ( var i = copy.length - 1; i > 0; i-- ) {
			var j = Math.floor( Math.random() * ( i + 1 ) );
			var tmp = copy[ i ];
			copy[ i ] = copy[ j ];
			copy[ j ] = tmp;
		}
		return copy.slice( 0, n );
	}

	function lengthMatches( text, bucket ) {
		if ( bucket === 'any' || ! bucket ) {
			return true;
		}
		var len = text.length;
		if ( bucket === 'short' ) {
			return len <= 45;
		}
		if ( bucket === 'medium' ) {
			return len > 45 && len <= 90;
		}
		return len > 90;
	}

	// A handful of articles' bullet lists accidentally swept up a trailing
	// "Related Puns" markdown link (e.g. "[Orange Puns](/orange-puns/)")
	// during dataset extraction — filter those out rather than show raw
	// markdown syntax as a "pun".
	var MARKDOWN_LINK_RE = /^\[.*\]\(.*\)$/;

	function isRealPun( text ) {
		return ! MARKDOWN_LINK_RE.test( text.trim() );
	}

	function allPunsFlat() {
		var flat = [];
		Object.keys( dataset ).forEach( function ( slug ) {
			dataset[ slug ].puns.forEach( function ( text ) {
				if ( isRealPun( text ) ) {
					flat.push( text );
				}
			} );
		} );
		return flat;
	}

	function buildPool() {
		var keyword = searchInput && searchInput.value.trim().toLowerCase();
		var bucket = lengthSelect ? lengthSelect.value : 'any';
		var pool;

		if ( keyword ) {
			pool = allPunsFlat().filter( function ( text ) {
				return text.toLowerCase().indexOf( keyword ) !== -1;
			} );
		} else {
			var slug1 = categorySelect.value;
			var cat1 = dataset[ slug1 ];
			pool = cat1 ? cat1.puns.slice() : [];
			var slug2 = category2Select ? category2Select.value : '';
			if ( slug2 && slug2 !== slug1 && dataset[ slug2 ] ) {
				pool = pool.concat( dataset[ slug2 ].puns );
			}
		}

		return pool.filter( function ( text ) {
			return isRealPun( text ) && lengthMatches( text, bucket );
		} );
	}

	/* ---------- rendering ---------- */

	function makeActionButton( label, title, className, onClick ) {
		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'pg-action-btn ' + className;
		btn.title = title;
		btn.setAttribute( 'aria-label', title );
		btn.textContent = label;
		btn.addEventListener( 'click', onClick );
		return btn;
	}

	function renderPunItem( text ) {
		var li = document.createElement( 'li' );
		li.className = 'pg-pun-item';

		var span = document.createElement( 'span' );
		span.className = 'pg-pun-text';
		span.textContent = text;
		li.appendChild( span );

		var actions = document.createElement( 'span' );
		actions.className = 'pg-pun-actions';

		var copyBtn = makeActionButton( 'Copy', 'Copy this pun', 'pg-copy', function () {
			copyText( text, function () { flashButton( copyBtn, 'Copied!' ); } );
		} );
		actions.appendChild( copyBtn );

		var favBtn = makeActionButton( isFavorite( text ) ? '♥ Saved' : '♡ Save', 'Save to favorites', 'pg-fav', function () {
			var nowFav = toggleFavorite( text );
			favBtn.textContent = nowFav ? '♥ Saved' : '♡ Save';
			favBtn.classList.toggle( 'is-fav', nowFav );
		} );
		favBtn.classList.toggle( 'is-fav', isFavorite( text ) );
		actions.appendChild( favBtn );

		var regenBtn = makeActionButton( '⟳ New', 'Swap for another pun', 'pg-regen', function () {
			var pool = buildPool().filter( function ( t ) {
				return currentPicks.indexOf( t ) === -1;
			} );
			if ( ! pool.length ) {
				return;
			}
			var replacement = pool[ Math.floor( Math.random() * pool.length ) ];
			var posInPicks = currentPicks.indexOf( text );
			if ( posInPicks !== -1 ) {
				currentPicks[ posInPicks ] = replacement;
			}
			var newLi = renderPunItem( replacement );
			li.replaceWith( newLi );
			addToHistory( replacement );
		} );
		actions.appendChild( regenBtn );

		var shareBtn = makeActionButton( '⤴ Share', 'Share this pun', 'pg-share', function () {
			sharePun( text );
		} );
		actions.appendChild( shareBtn );

		var imageBtn = makeActionButton( '⬇ Image', 'Download as an image', 'pg-image', function () {
			downloadPunImage( text );
		} );
		actions.appendChild( imageBtn );

		li.appendChild( actions );
		return li;
	}

	function render() {
		var count = parseInt( countSelect.value, 10 ) || 10;
		var pool = buildPool();

		if ( ! pool.length ) {
			results.innerHTML = '';
			empty.style.display = '';
			empty.textContent = 'No puns match those filters — try a different topic, keyword or length.';
			currentPicks = [];
			return;
		}

		currentPicks = shuffledSample( pool, Math.min( count, pool.length ) );
		var ul = document.createElement( 'ul' );
		ul.className = 'pg-pun-list';
		currentPicks.forEach( function ( text ) {
			ul.appendChild( renderPunItem( text ) );
			addToHistory( text );
		} );

		results.innerHTML = '';
		results.appendChild( ul );
		empty.style.display = 'none';
		if ( copyAllBtn ) {
			copyAllBtn.hidden = false;
		}
	}

	/* ---------- favorites panel ---------- */

	function renderFavorites() {
		if ( ! favPanel || ! favList ) {
			return;
		}
		var list = getFavorites();
		favList.innerHTML = '';
		if ( favCount ) {
			favCount.textContent = list.length;
		}
		favPanel.hidden = list.length === 0;

		list.forEach( function ( text ) {
			var li = document.createElement( 'li' );
			li.className = 'pg-pun-item';

			var span = document.createElement( 'span' );
			span.className = 'pg-pun-text';
			span.textContent = text;
			li.appendChild( span );

			var actions = document.createElement( 'span' );
			actions.className = 'pg-pun-actions';

			var copyBtn = makeActionButton( 'Copy', 'Copy this pun', 'pg-copy', function () {
				copyText( text, function () { flashButton( copyBtn, 'Copied!' ); } );
			} );
			actions.appendChild( copyBtn );

			var removeBtn = makeActionButton( '✕ Remove', 'Remove from favorites', 'pg-fav is-fav', function () {
				toggleFavorite( text );
			} );
			actions.appendChild( removeBtn );

			li.appendChild( actions );
			favList.appendChild( li );
		} );
	}

	if ( favCopyAllBtn ) {
		favCopyAllBtn.addEventListener( 'click', function () {
			var text = getFavorites().join( '\n' );
			if ( ! text ) {
				return;
			}
			copyText( text, function () { flashButton( favCopyAllBtn, 'Copied!' ); } );
		} );
	}

	/* ---------- history panel ---------- */

	function renderHistory() {
		if ( ! historyPanel || ! historyList ) {
			return;
		}
		var list = readList( window.sessionStorage, HIST_KEY );
		historyList.innerHTML = '';
		historyPanel.hidden = list.length === 0;
		list.forEach( function ( text ) {
			var li = document.createElement( 'li' );
			li.className = 'pg-history-item';
			li.textContent = text;
			li.title = 'Click to copy';
			li.addEventListener( 'click', function () {
				copyText( text, function () {
					var original = li.textContent;
					li.textContent = 'Copied!';
					setTimeout( function () { li.textContent = original; }, 1200 );
				} );
			} );
			historyList.appendChild( li );
		} );
	}

	/* ---------- copy all ---------- */

	if ( copyAllBtn ) {
		copyAllBtn.addEventListener( 'click', function () {
			if ( ! currentPicks.length ) {
				return;
			}
			copyText( currentPicks.join( '\n' ), function () { flashButton( copyAllBtn, 'Copied!' ); } );
		} );
	}

	/* ---------- voice input ---------- */

	var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
	if ( voiceBtn ) {
		if ( ! SpeechRecognition || ! searchInput ) {
			voiceBtn.disabled = true;
			voiceBtn.title = 'Voice input is not supported in this browser';
		} else {
			voiceBtn.addEventListener( 'click', function () {
				var recognition = new SpeechRecognition();
				recognition.lang = 'en-US';
				recognition.interimResults = false;
				recognition.maxAlternatives = 1;
				voiceBtn.classList.add( 'is-listening' );
				voiceBtn.textContent = '● Listening…';
				recognition.onresult = function ( event ) {
					var transcript = event.results[ 0 ][ 0 ].transcript;
					searchInput.value = transcript;
					render();
				};
				recognition.onerror = function () {
					voiceBtn.classList.remove( 'is-listening' );
					voiceBtn.textContent = '🎤 Speak';
				};
				recognition.onend = function () {
					voiceBtn.classList.remove( 'is-listening' );
					voiceBtn.textContent = '🎤 Speak';
				};
				recognition.start();
			} );
		}
	}

	/* ---------- boot ---------- */

	fetch( window.craftypunsData.punsUrl )
		.then( function ( r ) { return r.json(); } )
		.then( function ( data ) {
			dataset = data;
			populateCategories( data );
			generateBtn.disabled = false;
			renderFavorites();
			renderHistory();
		} )
		.catch( function () {
			empty.textContent = 'Could not load the puns data. Please refresh the page.';
		} );

	generateBtn.addEventListener( 'click', function () {
		if ( dataset ) {
			render();
		}
	} );

	if ( searchInput ) {
		searchInput.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Enter' && dataset ) {
				e.preventDefault();
				render();
			}
		} );
	}
})();
