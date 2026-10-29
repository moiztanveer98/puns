# -*- coding: utf-8 -*-
import sys, re, os
sys.path.insert(0, os.path.dirname(__file__))
from batch3_articles import ARTICLES

def typo(s):
    s = s.replace("'", "’")
    s = re.sub(r'"([^"]*)"', lambda m: "“" + m.group(1) + "”", s)
    return s

def topic_name(slug):
    return slug.replace('-', ' ').title().replace('Puns', 'Puns').strip()

TOPIC_DISPLAY = {
    "football-puns": "Football Puns",
    "basketball-puns": "Basketball Puns",
    "baseball-puns": "Baseball Puns",
    "soccer-puns": "Soccer Puns",
    "mothers-day-puns": "Mother's Day Puns",
    "fathers-day-puns": "Father's Day Puns",
    "mom-puns": "Mom Puns",
    "dad-puns": "Dad Puns",
    "unicorn-puns": "Unicorn Puns",
    "dragon-puns": "Dragon Puns",
    "duck-puns": "Duck Puns",
    "bee-puns": "Bee Puns",
    "breakfast-puns": "Breakfast Puns",
    "water-puns": "Water Puns",
    "computer-puns": "Computer Puns",
}

def build_html(slug, data):
    parts = []
    total = sum(len(items) for _, _, items in data['sections'])
    topic = TOPIC_DISPLAY[slug]
    title = f"{total}+ {topic}"
    parts.append(f"<p>{typo(data['intro'])}</p>")
    for heading, lead, items in data['sections']:
        parts.append(f"<h2>{typo(heading)}</h2>")
        parts.append(f"<p>{typo(lead)}</p>")
        parts.append("<ul>")
        for it in items:
            parts.append(f"<li>{typo(it)}</li>")
        parts.append("</ul>")
    parts.append(f"<h2>How to Use {typo(topic)}</h2>")
    parts.append(f"<p>{typo(data['how_to_use'])}</p>")
    parts.append("<h2>FAQ</h2>")
    for q, a in data['faq']:
        parts.append(f"<p><strong>{typo(q)}</strong><br />\n{typo(a)}</p>")
    parts.append("<h2>Related Puns</h2>")
    parts.append("<ul>")
    for href, text in data['related']:
        parts.append(f'<li><a href="{href}">{text}</a></li>')
    parts.append("</ul>")
    return title, "\n".join(parts), total

for slug, data in ARTICLES.items():
    title, html, total = build_html(slug, data)
    print(f"{slug}: {total} puns -> title: {title}")
    out_dir = os.path.join(os.path.dirname(__file__), "..", "content", "topical-map-batch-3")
    os.makedirs(out_dir, exist_ok=True)
    with open(os.path.join(out_dir, f"{slug}.html"), "w", encoding="utf-8") as f:
        f.write(f"<!-- TITLE: {title} -->\n")
        f.write(html)
