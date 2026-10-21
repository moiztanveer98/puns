<?php
/**
 * Template Name: Pun Generator Tool
 * Interactive tool: pick a topic (or a second one to combine), filter by
 * length, or search by keyword across every topic. Generates a random batch
 * of puns with per-pun copy / favorite / regenerate / share / image-export,
 * a "Copy All" action, a Favorites panel (localStorage) and a Recently
 * Generated panel (sessionStorage). Data comes from assets/data/puns-dataset.json.
 */
get_header();
?>
<main class="mx-auto max-w-3xl px-4 py-10">
	<?php craftypuns_breadcrumbs(); ?>

	<header class="text-center">
		<h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl"><?php the_title(); ?></h1>
		<p class="mt-3 text-slate-600">Pick a topic, search by keyword, or combine two topics — then generate, copy, save, share or download your puns as an image.</p>
	</header>

	<div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6">
		<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:flex-wrap">
			<div class="flex-1 pg-field-min">
				<label for="pg-category" class="mb-1 block text-sm font-semibold text-slate-700">Topic</label>
				<select id="pg-category" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900"></select>
			</div>
			<div class="flex-1 pg-field-min">
				<label for="pg-category-2" class="mb-1 block text-sm font-semibold text-slate-700">Combine with (optional)</label>
				<select id="pg-category-2" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900">
					<option value="">None</option>
				</select>
			</div>
			<div>
				<label for="pg-count" class="mb-1 block text-sm font-semibold text-slate-700">How many</label>
				<select id="pg-count" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900">
					<option value="5">5</option>
					<option value="10" selected>10</option>
					<option value="20">20</option>
					<option value="50">50</option>
				</select>
			</div>
			<div>
				<label for="pg-length" class="mb-1 block text-sm font-semibold text-slate-700">Length</label>
				<select id="pg-length" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900">
					<option value="any" selected>Any length</option>
					<option value="short">Short (one-liners)</option>
					<option value="medium">Medium</option>
					<option value="long">Long</option>
				</select>
			</div>
		</div>

		<div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-end">
			<div class="flex-1">
				<label for="pg-search" class="mb-1 block text-sm font-semibold text-slate-700">Or search by keyword (searches every topic)</label>
				<input id="pg-search" type="text" placeholder="e.g. birthday, coffee, pizza…" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900" />
			</div>
			<button id="pg-voice" type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 transition hover:border-amber-300">🎤 Speak</button>
		</div>

		<div class="mt-4">
			<button id="pg-generate" type="button" disabled class="w-full rounded-full bg-amber-400 px-6 py-2 font-semibold text-slate-900 shadow-sm transition hover:bg-amber-300 sm:w-auto">Generate Puns</button>
			<button id="pg-copy-all" type="button" hidden class="rounded-full border border-slate-300 bg-white px-6 py-2 font-semibold text-slate-700 transition hover:border-amber-300">Copy All</button>
		</div>
	</div>

	<div id="pg-results-wrap" class="mt-8">
		<div id="pg-results"></div>
		<p id="pg-empty" class="mt-8 text-center text-slate-500">Choose a topic above and click "Generate Puns" to get started.</p>
	</div>

	<section id="pg-favorites" class="mt-10 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6" hidden>
		<div class="flex items-center justify-between gap-3">
			<h2 class="text-lg font-bold">My Favorites (<span id="pg-favorites-count">0</span>)</h2>
			<button id="pg-favorites-copy-all" type="button" class="rounded-full border border-amber-300 bg-white px-4 py-1.5 text-sm font-semibold text-amber-800 transition hover:bg-amber-100">Copy All</button>
		</div>
		<ul id="pg-favorites-list" class="pg-pun-list mt-4"></ul>
	</section>

	<section id="pg-history" class="mt-10 rounded-2xl border border-slate-200 p-5 sm:p-6" hidden>
		<h2 class="text-lg font-bold">Recently Generated</h2>
		<p class="mt-1 text-sm text-slate-500">This session only — click any pun to copy it.</p>
		<ul id="pg-history-list" class="mt-4 space-y-2 text-sm"></ul>
	</section>
</main>
<?php
get_footer();
