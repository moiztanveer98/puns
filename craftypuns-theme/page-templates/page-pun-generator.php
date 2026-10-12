<?php
/**
 * Template Name: Pun Generator Tool
 * Interactive tool: pick a category, generate a random batch of puns, copy them.
 * Data comes from assets/data/puns-dataset.json (built from the published articles).
 */
get_header();
?>
<main class="mx-auto max-w-3xl px-4 py-10">
	<?php craftypuns_breadcrumbs(); ?>

	<header class="text-center">
		<h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl"><?php the_title(); ?></h1>
		<p class="mt-3 text-slate-600">Pick a topic, choose how many you want, and generate instant puns. Click any pun to copy it.</p>
	</header>

	<div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6">
		<div class="flex flex-col gap-4 sm:flex-row sm:items-end">
			<div class="flex-1">
				<label for="pg-category" class="mb-1 block text-sm font-semibold text-slate-700">Topic</label>
				<select id="pg-category" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900"></select>
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
			<button id="pg-generate" type="button" disabled class="rounded-full bg-amber-400 px-6 py-2 font-semibold text-slate-900 shadow-sm transition hover:bg-amber-300">Generate Puns</button>
		</div>
	</div>

	<div id="pg-results" class="prose prose-slate mt-8 max-w-none"></div>
	<p id="pg-empty" class="mt-8 text-center text-slate-500">Choose a topic above and click “Generate Puns” to get started.</p>
</main>
<?php
get_footer();
