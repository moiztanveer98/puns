// Puns Generator Tool: loads the pre-built pun dataset, lets the visitor
// pick a topic + count, and renders a random batch with copy buttons.
(function () {
	var categorySelect = document.getElementById( 'pg-category' );
	var countSelect = document.getElementById( 'pg-count' );
	var generateBtn = document.getElementById( 'pg-generate' );
	var results = document.getElementById( 'pg-results' );
	var empty = document.getElementById( 'pg-empty' );

	if ( ! categorySelect || ! generateBtn || ! window.craftypunsData ) {
		return;
	}

	var dataset = null;

	function populateCategories( data ) {
		var slugs = Object.keys( data ).sort( function ( a, b ) {
			return data[ a ].label.localeCompare( data[ b ].label );
		} );
		slugs.forEach( function ( slug ) {
			var opt = document.createElement( 'option' );
			opt.value = slug;
			opt.textContent = data[ slug ].label;
			categorySelect.appendChild( opt );
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

	function render( slug, count ) {
		var cat = dataset[ slug ];
		if ( ! cat || ! cat.puns.length ) {
			return;
		}
		var picks = shuffledSample( cat.puns, Math.min( count, cat.puns.length ) );
		var ul = document.createElement( 'ul' );
		picks.forEach( function ( text ) {
			var li = document.createElement( 'li' );
			li.textContent = text;
			ul.appendChild( li );
		} );
		results.innerHTML = '';
		results.appendChild( ul );
		empty.style.display = 'none';

		if ( window.craftypunsInitPunCopy ) {
			window.craftypunsInitPunCopy( results );
		}
	}

	fetch( window.craftypunsData.punsUrl )
		.then( function ( r ) { return r.json(); } )
		.then( function ( data ) {
			dataset = data;
			populateCategories( data );
			generateBtn.disabled = false;
		} )
		.catch( function () {
			empty.textContent = 'Could not load the puns data. Please refresh the page.';
		} );

	generateBtn.addEventListener( 'click', function () {
		if ( ! dataset ) {
			return;
		}
		var slug = categorySelect.value;
		var count = parseInt( countSelect.value, 10 ) || 10;
		render( slug, count );
	} );
})();
