#!/usr/bin/env python3
"""
Builds content-import.xml (WXR) from data/*.json + p1_content.py.
Run after parse_xlsx.py. Output lands at the theme root: ../content-import.xml
"""
import json
import os
import re
import sys
from datetime import datetime, timezone
from xml.sax.saxutils import escape as xesc

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from p1_content import CONTENT  # noqa: E402

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # craftypuns-theme/
DATA_DIR = os.path.join(ROOT, "data")
OUT_PATH = os.path.join(ROOT, "content-import.xml")

SITE_URL = "https://craftypuns.net"
POST_DATE = "2026-10-05 00:00:00"
POST_DATE_GMT = "2026-10-05 00:00:00"

UNMATCHED_BRIDGE_LABELS = set()


def load(name):
    with open(os.path.join(DATA_DIR, name), encoding="utf-8") as f:
        return json.load(f)


def cdata(text):
    text = "" if text is None else str(text)
    text = text.replace("]]>", "]]]]><![CDATA[>")
    return f"<![CDATA[{text}]]>"


def slugify(name):
    s = name.lower()
    s = s.replace("&", "and")
    s = re.sub(r"[^a-z0-9]+", "-", s)
    s = re.sub(r"-+", "-", s).strip("-")
    return s


# ---------------------------------------------------------------------------
# Load data
# ---------------------------------------------------------------------------
topics = load("topics.json")
use_hubs = load("use-hubs.json")
outer = load("outer-section.json")
site_pages = load("site-pages.json")
h2_template = load("h2-template.json")

SECTIONS = h2_template["sections"]  # order 1..17

# ---------------------------------------------------------------------------
# Build a global name -> slug map (for bridge-link resolution) and
# a sub-hub name -> status map (for "Served by topic post" detection)
# ---------------------------------------------------------------------------
NAME_TO_SLUG = {}
for row in site_pages:
    if row["slug"]:
        NAME_TO_SLUG[row["page"]] = row["slug"]
for t in topics:
    NAME_TO_SLUG[t["page"]] = t["slug"]
for u in use_hubs:
    NAME_TO_SLUG[u["page"]] = u["slug"]
for o in outer:
    NAME_TO_SLUG[o["page"]] = o["slug"]

SUBHUB_SERVED_BY_POST = set(
    row["page"] for row in site_pages
    if row["type"] == "Sub-hub" and row["status"] == "Served by topic post"
)
HUB_SLUG = {row["page"]: row["slug"] for row in site_pages if row["type"] == "Hub"}
SUBHUB_SLUG = {row["page"]: row["slug"] for row in site_pages if row["type"] == "Sub-hub" and row["slug"]}

# ---------------------------------------------------------------------------
# Categories: 10 hubs + 29 non-served sub-hubs
# ---------------------------------------------------------------------------
hubs = sorted(set(t["hub"] for t in topics))
subhubs_by_hub = {}
for t in topics:
    subhubs_by_hub.setdefault(t["hub"], set()).add(t["sub_hub"])

categories = []  # list of dicts: name, slug, parent_slug, description

for hub in hubs:
    sub_names = sorted(
        sh for sh in subhubs_by_hub[hub] if sh not in SUBHUB_SERVED_BY_POST
    )
    desc = (
        f"{hub} covers {', '.join(sub_names[:-1]) + ' and ' + sub_names[-1] if len(sub_names) > 1 else (sub_names[0] if sub_names else 'every related topic')} "
        f"wordplay. Pick a sub-topic below for original one-liners, captions and jokes, or browse every {hub.lower()} page from here."
    )
    categories.append({
        "name": hub,
        "slug": HUB_SLUG.get(hub, slugify(hub)),
        "parent_slug": None,
        "description": desc,
    })

for hub in hubs:
    for sub in sorted(subhubs_by_hub[hub]):
        if sub in SUBHUB_SERVED_BY_POST:
            continue
        pages_in_sub = sorted(t["page"] for t in topics if t["hub"] == hub and t["sub_hub"] == sub)
        desc = (
            f"{sub} gathers {', '.join(pages_in_sub[:-1]) + ' and ' + pages_in_sub[-1] if len(pages_in_sub) > 1 else pages_in_sub[0]} "
            f"on one page. Each one has its own one-liners, captions, kids' jokes and more."
        )
        categories.append({
            "name": sub,
            "slug": SUBHUB_SLUG.get(sub, slugify(sub)),
            "parent_slug": HUB_SLUG.get(hub, slugify(hub)),
            "description": desc,
        })

assert len(categories) == 10 + 29, f"Expected 39 categories, got {len(categories)}"


# ---------------------------------------------------------------------------
# Bridge link parsing: "Up: Pet Puns -> Animal Puns | Across: Dog Puns, Rabbit Puns | Seasonal: Easter hub"
# ---------------------------------------------------------------------------
# Shorthand labels used in the Contextual Bridges column that don't exactly
# match a Page/H1 title elsewhere in the sheets.
BRIDGE_LABEL_ALIASES = {
    "Back to School": "School Puns",
    "Birthday": "Birthday Puns",
    "Christmas": "Christmas Puns",
    "Easter": "Easter Puns",
    "Fall": "Fall Puns",
    "Father's Day": "Father's Day Puns",
    "Graduation": "Graduation Puns",
    "Halloween": "Halloween Puns",
    "July 4": "Fourth of July Puns",
    "May 4": "Star Wars Puns",
    "Mother's Day": "Mother's Day Puns",
    "New Year": "New Year Puns",
    "Sport Puns": "Sports Puns",
    "Spring": "Spring Puns",
    "St. Patrick's": "St. Patrick's Day Puns",
    "Summer": "Summer Puns",
    "Thanksgiving": "Thanksgiving Puns",
    "Valentine's": "Valentine's Day Puns",
    "Winter": "Winter Puns",
}


def parse_bridges(raw):
    if not raw:
        return []
    links = []
    seen_slugs = set()
    segments = raw.split("|")
    for seg in segments:
        seg = seg.strip()
        if ":" not in seg:
            continue
        _, rest = seg.split(":", 1)
        # split on arrow and comma
        parts = re.split(r"→|->|,", rest)
        for p in parts:
            label = p.strip()
            label = re.sub(r"\s+hub$", "", label, flags=re.IGNORECASE)
            if not label:
                continue
            label = BRIDGE_LABEL_ALIASES.get(label, label)
            slug = NAME_TO_SLUG.get(label)
            if not slug:
                UNMATCHED_BRIDGE_LABELS.add(label)
                continue
            if slug in seen_slugs:
                continue
            seen_slugs.add(slug)
            links.append({"label": label, "slug": slug})
    return links


# ---------------------------------------------------------------------------
# Generic FAQ generator (used when a topic has no hand-written CONTENT entry)
# ---------------------------------------------------------------------------
def generic_faqs(title):
    return [
        {
            "q": f"What are the best {title.lower()}?",
            "a": f"The best {title.lower()} use real, topic-specific wordplay rather than generic jokes dropped into a template. Look for one-liners, short lines and captions built from {title.lower().replace(' puns', '')}'s own vocabulary, not borrowed from somewhere else.",
        },
        {
            "q": f"Are {title.lower()} good for kids?",
            "a": f"Yes, {title.lower()} are easy to make kid-friendly: stick to the clean, simple lines and Q&A-style jokes rather than anything flirty or dark, and they work well for classrooms, cards and family game night.",
        },
        {
            "q": f"How do you make a {title.lower().split(' puns')[0]} pun?",
            "a": f"Start with a word tied to {title.lower().split(' puns')[0]}, then listen for a similar-sounding word or phrase you can swap in for a double meaning. See our guide on how to write a pun for the full step-by-step method.",
        },
    ]


def generic_howto(title):
    topic = title.replace(" Puns", "").replace("'s", "")
    return (
        f"Use {title.lower()} in birthday cards, Instagram captions, classroom worksheets and party signs — "
        f"anywhere {topic.lower()} comes up and a quick laugh would land well."
    )


# ---------------------------------------------------------------------------
# Content builders
# ---------------------------------------------------------------------------
def render_list_section(heading, items):
    html = f"<h2>{xesc(heading)}</h2>\n"
    for item in items:
        html += f"<p>{xesc(item)}</p>\n"
    return html


def render_topic_content(topic):
    title = topic["page"]
    bridges = parse_bridges(topic.get("bridges_raw"))
    data = CONTENT.get(topic["slug"])

    if data:
        bridges = data.get("bridge_links", bridges)
        html = f"<p>{xesc(data['intro'])}</p>\n"
        section_map = [
            ("one_liners", f"{title} One-Liners"),
            ("short", f"Short {title}"),
            ("funny", f"Funny {title}"),
            ("cute", f"Cute {title}"),
            ("captions", f"{title} for Instagram Captions"),
            ("kids", f"{title} for Kids"),
            ("love", f"{title.replace(' Puns', '')} Love Puns"),
            ("birthday", f"{title.replace(' Puns', '')} Birthday Puns"),
            ("pickup_lines", f"{title.replace(' Puns', '')} Pickup Lines"),
            ("jokes", f"{title.replace(' Puns', '')} Jokes"),
        ]
        for key, heading in section_map:
            html += render_list_section(heading, data[key])
        html += f"<h2>How to Use {title}</h2>\n<p>{xesc(data['how_to_use'])}</p>\n"
        faqs = data["faqs"]
    else:
        headings = [
            f"{title} One-Liners", f"Short {title}", f"Funny {title}", f"Cute {title}",
            f"{title} for Instagram Captions", f"{title} for Kids",
            f"{title.replace(' Puns', '')} Love Puns", f"{title.replace(' Puns', '')} Birthday Puns",
            f"{title.replace(' Puns', '')} Pickup Lines", f"{title.replace(' Puns', '')} Jokes",
        ]
        html = f"<p>{xesc(title)} puns use {title.lower()}'s own words and sounds for quick wordplay you can use anywhere a laugh fits.</p>\n"
        for heading in headings:
            html += f"<h2>{xesc(heading)}</h2>\n<!-- TODO: puns content -->\n"
        html += f"<h2>How to Use {title}</h2>\n<p>{xesc(generic_howto(title))}</p>\n"
        faqs = generic_faqs(title)

    return html, faqs, bridges


def render_simple_content(title, target_query, descriptor):
    """Simpler structure for Use Hubs / Outer Section posts."""
    data = CONTENT.get(slugify(title))
    html = f"<p>{xesc(title)} brings together {xesc(descriptor)} in one place, built around the search \"{xesc(target_query)}.\"</p>\n"
    if data:
        html += render_list_section("Our Picks", data.get("items", []))
        faqs = data.get("faqs", generic_faqs(title))
        bridges = data.get("bridge_links", [])
    else:
        html += f"<h2>{xesc(title)}</h2>\n<!-- TODO: puns content -->\n"
        faqs = generic_faqs(title)
        bridges = []
    return html, faqs, bridges


# ---------------------------------------------------------------------------
# WXR assembly
# ---------------------------------------------------------------------------
def meta(key, value):
    return f"<wp:postmeta><wp:meta_key>{cdata(key)}</wp:meta_key><wp:meta_value>{cdata(value)}</wp:meta_value></wp:postmeta>\n"


def faq_meta(faqs):
    out = ""
    for i, faq in enumerate(faqs[:3], start=1):
        out += meta(f"_faq_q{i}", faq["q"])
        out += meta(f"_faq_a{i}", faq["a"])
    return out


def bridge_meta(bridges):
    if not bridges:
        return ""
    return meta("_bridge_links", json.dumps(bridges, ensure_ascii=False))


def post_item(post_id, title, slug, content_html, meta_title, meta_desc, faqs, bridges,
              post_type="post", categories_slugs=None, excerpt=""):
    cats_xml = ""
    for cat_slug in (categories_slugs or []):
        cats_xml += f'<category domain="category" nicename="{xesc(cat_slug)}"><![CDATA[{cat_slug}]]></category>\n'

    return f"""<item>
<title>{cdata(title)}</title>
<link>{SITE_URL}/{slug}/</link>
<pubDate>{POST_DATE}</pubDate>
<dc:creator>{cdata('admin')}</dc:creator>
<guid isPermaLink="false">{SITE_URL}/?post_type={post_type}&#038;p={post_id}</guid>
<description></description>
<content:encoded>{cdata(content_html)}</content:encoded>
<excerpt:encoded>{cdata(excerpt)}</excerpt:encoded>
<wp:post_id>{post_id}</wp:post_id>
<wp:post_date>{POST_DATE}</wp:post_date>
<wp:post_date_gmt>{POST_DATE_GMT}</wp:post_date_gmt>
<wp:comment_status>closed</wp:comment_status>
<wp:ping_status>closed</wp:ping_status>
<wp:post_name>{cdata(slug)}</wp:post_name>
<wp:status>publish</wp:status>
<wp:post_parent>0</wp:post_parent>
<wp:menu_order>0</wp:menu_order>
<wp:post_type>{post_type}</wp:post_type>
<wp:post_password></wp:post_password>
<wp:is_sticky>0</wp:is_sticky>
{cats_xml}{meta('_seo_title', meta_title)}{meta('_seo_description', meta_desc)}{meta('rank_math_title', meta_title)}{meta('rank_math_description', meta_desc)}{meta('_yoast_wpseo_title', meta_title)}{meta('_yoast_wpseo_metadesc', meta_desc)}{faq_meta(faqs)}{bridge_meta(bridges)}</item>
"""


def page_item(post_id, title, slug, content_html, meta_title, meta_desc):
    return f"""<item>
<title>{cdata(title)}</title>
<link>{SITE_URL}/{slug}/</link>
<pubDate>{POST_DATE}</pubDate>
<dc:creator>{cdata('admin')}</dc:creator>
<guid isPermaLink="false">{SITE_URL}/?page_id={post_id}</guid>
<description></description>
<content:encoded>{cdata(content_html)}</content:encoded>
<excerpt:encoded>{cdata('')}</excerpt:encoded>
<wp:post_id>{post_id}</wp:post_id>
<wp:post_date>{POST_DATE}</wp:post_date>
<wp:post_date_gmt>{POST_DATE_GMT}</wp:post_date_gmt>
<wp:comment_status>closed</wp:comment_status>
<wp:ping_status>closed</wp:ping_status>
<wp:post_name>{cdata(slug)}</wp:post_name>
<wp:status>publish</wp:status>
<wp:post_parent>0</wp:post_parent>
<wp:menu_order>0</wp:menu_order>
<wp:post_type>page</wp:post_type>
<wp:post_password></wp:post_password>
<wp:is_sticky>0</wp:is_sticky>
{meta('_seo_title', meta_title)}{meta('_seo_description', meta_desc)}{meta('rank_math_title', meta_title)}{meta('rank_math_description', meta_desc)}{meta('_yoast_wpseo_title', meta_title)}{meta('_yoast_wpseo_metadesc', meta_desc)}</item>
"""


# ---------------------------------------------------------------------------
# Static pages (real, publishable content)
# ---------------------------------------------------------------------------
from static_pages import STATIC_PAGES  # noqa: E402

items_xml = []
post_id = 1000

for cat in categories:
    pass  # categories are emitted separately below (wp:category at channel level)

for page_def in STATIC_PAGES:
    post_id += 1
    items_xml.append(page_item(
        post_id, page_def["title"], page_def["slug"], page_def["content"],
        page_def["meta_title"], page_def["meta_description"],
    ))

topic_count = 0
for t in topics:
    post_id += 1
    content_html, faqs, bridges = render_topic_content(t)
    cats = [HUB_SLUG.get(t["hub"], slugify(t["hub"]))]
    if t["sub_hub"] not in SUBHUB_SERVED_BY_POST:
        cats.append(SUBHUB_SLUG.get(t["sub_hub"], slugify(t["sub_hub"])))
    items_xml.append(post_item(
        post_id, t["page"], t["slug"], content_html,
        t["meta_title"], t["meta_description"], faqs, bridges,
        categories_slugs=cats,
    ))
    topic_count += 1

use_hub_count = 0
for u in use_hubs:
    post_id += 1
    content_html, faqs, bridges = render_simple_content(u["page"], u["target_query"], u.get("pulls_from") or "")
    items_xml.append(post_item(
        post_id, u["page"], u["slug"], content_html,
        u["meta_title"], u["meta_description"], faqs, bridges,
    ))
    use_hub_count += 1

outer_count = 0
for o in outer:
    post_id += 1
    content_html, faqs, bridges = render_simple_content(o["page"], o["target_query"], o.get("angle") or "")
    items_xml.append(post_item(
        post_id, o["page"], o["slug"], content_html,
        o["meta_title"], o["meta_description"], faqs, bridges,
    ))
    outer_count += 1

# ---------------------------------------------------------------------------
# wp:category elements
# ---------------------------------------------------------------------------
categories_xml = ""
for cat in categories:
    parent = cat["parent_slug"] or ""
    categories_xml += f"""<wp:category>
<wp:term_id>0</wp:term_id>
<wp:category_nicename>{cdata(cat['slug'])}</wp:category_nicename>
<wp:category_parent>{cdata(parent)}</wp:category_parent>
<wp:cat_name>{cdata(cat['name'])}</wp:cat_name>
<wp:category_description>{cdata(cat['description'])}</wp:category_description>
</wp:category>
"""

wxr = f"""<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
<channel>
<title>Crafty Puns</title>
<link>{SITE_URL}</link>
<description>Crafty Puns content import</description>
<pubDate>{datetime.now(timezone.utc).strftime('%a, %d %b %Y %H:%M:%S +0000')}</pubDate>
<language>en-US</language>
<wp:wxr_version>1.2</wp:wxr_version>
<wp:base_site_url>{SITE_URL}</wp:base_site_url>
<wp:base_blog_url>{SITE_URL}</wp:base_blog_url>
<wp:author>
<wp:author_id>1</wp:author_id>
<wp:author_login><![CDATA[admin]]></wp:author_login>
<wp:author_email><![CDATA[placeholder@craftypuns.net]]></wp:author_email>
<wp:author_display_name><![CDATA[Crafty Puns]]></wp:author_display_name>
<wp:author_first_name><![CDATA[]]></wp:author_first_name>
<wp:author_last_name><![CDATA[]]></wp:author_last_name>
</wp:author>
{categories_xml}{''.join(items_xml)}</channel>
</rss>
"""

with open(OUT_PATH, "w", encoding="utf-8") as f:
    f.write(wxr)

print(f"Wrote {OUT_PATH}")
print(f"Categories : {len(categories)} (expected 39: 10 hub + 29 sub-hub)")
print(f"Pages      : {len(STATIC_PAGES)} (expected 9)")
print(f"Topic posts: {topic_count} (expected 245)")
print(f"Use hubs   : {use_hub_count} (expected 13)")
print(f"Outer posts: {outer_count} (expected 19)")
print(f"Total posts: {topic_count + use_hub_count + outer_count} (expected 277)")
if UNMATCHED_BRIDGE_LABELS:
    print(f"\nWARNING: {len(UNMATCHED_BRIDGE_LABELS)} bridge labels did not resolve to a known slug:")
    for label in sorted(UNMATCHED_BRIDGE_LABELS):
        print("  -", label)
