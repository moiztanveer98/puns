# Document Template: Topic Pun Page (CORE)

Applies to every `/[topic]-puns/` page. Per Koray's query/document template method
(§5.4): the same contextual vector is reused for every topic entity in this class,
so coverage stays efficient even where a specific topic has lower demand for one
sub-question. Each page's own brief fills in only what's topic-specific:
competitors, word bank, bridge links, and FAQ answers.

## Competitor slots (generic, per-topic pages adjust slot 1/2 where a better match exists)

1. **Top-ranking, comprehensive page** — Punpedia's equivalent topic page (e.g.
   punpedia.org/[topic]-puns/) — heaviest weight on question generation.
2. **Source to outrank** — Punscrazy or a similar mixed-list competitor — context
   terms and consensus; this is the format CraftyPuns is positioned against (puns
   mixed with jokes/slang with no topic separation).
3. **Phrase-taxonomy source** — Flick's caption pages (flick.social/captions/...) —
   used for the Captions section's phrase sequences and internal-link anchors.

## Contextual vector (macro → border → micro)

| # | Heading | Level | Type | Domain | Preceding question | Links |
|---|---|---|---|---|---|---|
| 1 | [Number]+ [Topic] Puns for [Top Use] That Are [Quality] | H1 | — | macro | — | — |
| 2 | (intro, no heading) | — | definitional | macro | — | — |
| 3 | [Topic] Pun One-Liners | H2 | grouper | macro | what are [topic] puns | — |
| 4 | Short [Topic] Puns | H2 | grouper | macro | one-liners given | — |
| 5 | Funny [Topic] Puns | H2 | comparative | macro | short puns given | — |
| 6 | Cute [Topic] Puns | H2 | comparative | macro | funny puns given | — |
| 7 | [Topic] Puns for Instagram Captions | H2 | grouper | **border** | cute puns given | → `/puns-for-instagram-captions/` |
| 8 | [Topic] Puns for Kids | H2 | grouper | micro | captions given | → `/puns-for-kids/` |
| 9 | [Topic] Love Puns | H2 | grouper | micro | kids puns given | → `/love-puns/` |
| 10 | [Topic] Birthday Puns | H2 | grouper | micro | love puns given | → `/puns-for-cards/` (if relevant) |
| 11 | [Topic] Pickup Lines | H2 | grouper | micro | birthday puns given | → `/pun-pickup-lines/` |
| 12 | [Topic] Jokes | H2 | grouper | micro | pickup lines given | → `/dad-joke-puns/` (if relevant) |
| 13 | How to Use [Topic] Puns | H2 | definitional | micro | jokes given | — |
| 14 | FAQ: What are the best [topic] puns? | H3 | definitional | micro | how-to given | — |
| 15 | FAQ: Are [topic] puns good for kids? | H3 | boolean | micro | Q14 answered | → `/puns-for-kids/` |
| 16 | FAQ: How do you make a [topic] pun? | H3 | definitional | micro | Q15 answered | → `/how-to-write-a-pun/` |
| 17 | Related Puns | H2 | — | micro | Q16 answered | → sub-hub + 2 sibling topics |

Contextual border = heading 7 (Captions): this is where the page pivots from
pure macro (topic depth) to cross-topic micro content that links out.

## Contextual structure (applies to every page using this template)

- **Intro**: definitional-first. Sentence 1 states what [topic] puns are
  (double meaning/sound-alike + the topic's own vocabulary). Sentence 2 states
  where to use them (captions, cards, classroom). No story opener, no "in this
  article". Mention every H2 in the same order they appear in the vector.
- **One-Liners**: format = list, each item a complete sentence with a twist at
  the end. Context term ("[topic]" or a topic-specific noun) must appear in
  every item.
- **Short**: format = list, 1–5 words per item. No verb required.
- **Funny**: format = list, the strongest/most groan-worthy lines — order by
  how direct the wordplay is, boldest first.
- **Cute**: format = list, soft/sweet tone, present tense, no innuendo.
- **Captions (border)**: format = list, every item ≤ 8 words, standalone
  (works with no topic context attached, since it will be copied onto a photo).
- **Kids**: format = Q&A list. Every item: `Q: ... A: ...` punchline. Vocabulary
  restricted to words a child would know.
- **Love**: format = list, second person ("you"), present tense, sweet not crude.
- **Birthday**: format = list, occasion-specific, present tense.
- **Pickup Lines**: format = list, flirty, one sentence, clean (no innuendo).
- **Jokes**: format = Q&A list, setup + punchline, present tense in the setup.
- **How to Use**: format = prose, 2–3 sentences, names concrete use cases (cards,
  captions, party signs, classroom) matching whichever use-hubs this topic's
  bridge links point to.
- **FAQ Q1 (definitional)**: answer starts with the direct claim in present
  tense, no modality, ≤ 4 sentences, mentions 2 of the section names above by
  the same words used in their H2s.
- **FAQ Q2 (boolean)**: answer starts "Yes," or "No," — present tense, ≤ 4
  sentences, links to `/puns-for-kids/` using "puns for kids" as anchor text.
- **FAQ Q3 (definitional)**: answer gives the 2-step method (pick a topic word →
  find a sound-alike), ≤ 4 sentences, links to `/how-to-write-a-pun/` using
  "how to write a pun" as anchor text.
- **Related Puns**: 3 links — the sub-hub (or parent hub if served-by-post) plus
  2 sibling topic pages from the Contextual Bridges column. Descriptive anchor
  text = the target page's own title, never "click here".

## Writing rules enforced on every article built from this template

- Answer-first, present tense, no modality (§6.2) in every FAQ and in the intro.
- List intros are complete sentences ending in a period, never a colon fragment (§6.3).
- Every list item carries the topic's context term (§6.3) — a cat-puns list item
  must contain "cat"/feline vocabulary, not a generic line that could belong to
  any animal.
- No external links (§6.5) — bridge links are internal only.
- One link per heading maximum, concentrated in the micro section (§5.6) — the
  macro section (One-Liners through Captions) carries zero internal links.
- No section allowed to balloon past its sibling sections — if a section needs
  more depth than this, it becomes its own OUTER page, not a longer section here.
