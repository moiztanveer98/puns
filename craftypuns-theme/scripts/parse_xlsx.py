#!/usr/bin/env python3
"""
Parses craftypuns-topical-map.xlsx into local JSON files under ../data/.
These JSON files are used only at theme-generation / WXR-build time.
They are NOT loaded at WordPress runtime.

Usage: python parse_xlsx.py
"""
import json
import os
import re
import sys
import openpyxl

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # craftypuns-theme/
DATA_DIR = os.path.join(ROOT, "data")
XLSX_PATH = os.path.join(os.path.dirname(ROOT), "craftypuns-topical-map.xlsx")


def slugify_parts(slug):
    """'/cat-puns/' -> 'cat-puns'. Absolute URLs (http/https) are returned unchanged."""
    if not slug or not isinstance(slug, str):
        return None
    slug = slug.strip()
    if slug.startswith("http://") or slug.startswith("https://"):
        return slug
    return slug.strip("/").strip()


def rows_after_header(ws, header_row_idx):
    """Yield (row_index, row_values) for all rows after header_row_idx that aren't fully empty."""
    all_rows = list(ws.iter_rows(values_only=True))
    for i in range(header_row_idx + 1, len(all_rows)):
        r = all_rows[i]
        if all(v is None for v in r):
            continue
        yield i, r


def parse_topical_map(wb):
    ws = wb["Topical Map"]
    rows = list(ws.iter_rows(values_only=True))
    header = rows[0]
    out = []
    for r in rows[1:]:
        if all(v is None for v in r):
            continue
        d = dict(zip(header, r))
        out.append({
            "num": d.get("#"),
            "hub": d.get("Hub"),
            "sub_hub": d.get("Sub-hub"),
            "page": d.get("Page"),
            "target_query": d.get("Target Query"),
            "slug": slugify_parts(d.get("URL Slug")),
            "url_slug": d.get("URL Slug"),
            "section": d.get("Section"),
            "priority": d.get("Priority"),
            "seasonality": d.get("Seasonality"),
            "status": d.get("Status on craftypuns.net"),
            "bridges_raw": d.get("Contextual Bridges"),
            "notes": d.get("Notes"),
            "h1": d.get("H1 Title"),
            "meta_title": d.get("Meta Title"),
            "meta_description": d.get("Meta Description"),
        })
    return out


def parse_simple_sheet(wb, sheet_name, kind):
    """Use Hubs / Outer Section share the same column shape."""
    ws = wb[sheet_name]
    rows = list(ws.iter_rows(values_only=True))
    header = rows[0]
    out = []
    for r in rows[1:]:
        if all(v is None for v in r):
            continue
        d = dict(zip(header, r))
        if kind == "use_hub":
            out.append({
                "page": d.get("Use Hub Page"),
                "target_query": d.get("Target Query"),
                "slug": slugify_parts(d.get("URL Slug")),
                "url_slug": d.get("URL Slug"),
                "priority": d.get("Priority"),
                "pulls_from": d.get("Pulls From / Links To"),
                "format_rule": d.get("Format Rule"),
                "h1": d.get("H1 Title"),
                "meta_title": d.get("Meta Title"),
                "meta_description": d.get("Meta Description"),
            })
        else:
            out.append({
                "page": d.get("Page (H1 draft)"),
                "target_query": d.get("Target Query"),
                "slug": slugify_parts(d.get("URL Slug")),
                "url_slug": d.get("URL Slug"),
                "priority": d.get("Priority"),
                "angle": d.get("Angle / Bridge"),
                "h1": d.get("H1 Title"),
                "meta_title": d.get("Meta Title"),
                "meta_description": d.get("Meta Description"),
            })
    return out


def parse_header_footer(wb):
    ws = wb["Header & Footer"]
    rows = list(ws.iter_rows(values_only=True))
    header_items = []
    footer_items = []
    rules = []
    mode = None
    col_header = None
    for r in rows:
        first = r[0]
        if first == "HEADER":
            mode = "header"
            col_header = None
            continue
        if first == "FOOTER":
            mode = "footer"
            col_header = None
            continue
        if first == "Rules":
            mode = "rules"
            continue
        if mode == "header":
            if first == "Label":
                col_header = r
                continue
            if first is None:
                continue
            label = first.strip()
            depth = 0
            while label.startswith("↳"):
                depth += 1
                label = label[1:].strip()
            header_items.append({
                "label": label,
                "depth": depth,
                "type": r[1],
                "slug": slugify_parts(r[2]) if r[2] else None,
                "url_slug": r[2],
                "notes": r[3],
            })
        elif mode == "footer":
            if first == "Column / Zone":
                col_header = r
                continue
            if first is None:
                continue
            footer_items.append({
                "zone": first,
                "label": r[1],
                "type": r[2],
                "slug": slugify_parts(r[3]) if r[3] else None,
                "url_slug": r[3],
                "notes": r[4],
            })
        elif mode == "rules":
            if first:
                rules.append(first.lstrip("- ").strip())
    return {"header": header_items, "footer": footer_items, "rules": rules}


def parse_site_pages(wb):
    ws = wb["Site Pages"]
    rows = list(ws.iter_rows(values_only=True))
    header = rows[0]
    out = []
    for r in rows[1:]:
        if all(v is None for v in r):
            continue
        d = dict(zip(header, r))
        out.append({
            "type": d.get("Type"),
            "page": d.get("Page"),
            "slug": slugify_parts(d.get("URL Slug")) if d.get("URL Slug") not in (None, "(topic post)") else None,
            "url_slug": d.get("URL Slug"),
            "wp_type": d.get("WP Type"),
            "status": d.get("Status"),
            "notes": d.get("Notes"),
        })
    return out


def parse_h2_template(wb):
    ws = wb["H2 Template"]
    rows = list(ws.iter_rows(values_only=True))
    out = []
    h1_formula = None
    for r in rows:
        if isinstance(r[0], str) and r[0].startswith("H1 formula"):
            h1_formula = r[0]
        if r[0] == "Order":
            continue
        if isinstance(r[0], int):
            out.append({
                "order": r[0],
                "heading": r[1],
                "zone": r[2],
                "rule": r[3],
            })
    return {"h1_formula": h1_formula, "sections": out}


def parse_seasonal(wb):
    ws = wb["Seasonal Calendar"]
    rows = list(ws.iter_rows(values_only=True))
    header = rows[0]
    out = []
    for r in rows[1:]:
        if all(v is None for v in r):
            continue
        if not isinstance(r[0], str):
            continue
        d = dict(zip(header, r))
        if d.get("Event") in (None, "Rule"):
            continue
        out.append({
            "event": d.get("Event"),
            "event_date": str(d.get("Event Date")) if d.get("Event Date") else None,
            "publish_by": str(d.get("Publish / Refresh By")) if d.get("Publish / Refresh By") else None,
            "pages": d.get("Pages"),
            "flag": d.get("Flag"),
        })
    return out


def parse_summary(wb):
    """Returns the per-hub breakdown rows only (stops at the 'Total topic pages' row)."""
    ws = wb["Summary"]
    rows = list(ws.iter_rows(values_only=True))
    out = []
    header = None
    for r in rows:
        if r[0] == "Hub":
            header = r
            continue
        if header is None:
            continue
        if not isinstance(r[0], str):
            continue
        if r[0].startswith("Total"):
            break
        if all(isinstance(v, int) for v in r[1:]):
            d = dict(zip(header, r))
            out.append(d)
    return out


def main():
    if not os.path.exists(XLSX_PATH):
        print(f"ERROR: xlsx not found at {XLSX_PATH}", file=sys.stderr)
        sys.exit(1)

    wb = openpyxl.load_workbook(XLSX_PATH, data_only=True)
    os.makedirs(DATA_DIR, exist_ok=True)

    topics = parse_topical_map(wb)
    use_hubs = parse_simple_sheet(wb, "Use Hubs", "use_hub")
    outer_section = parse_simple_sheet(wb, "Outer Section", "outer")
    header_footer = parse_header_footer(wb)
    site_pages = parse_site_pages(wb)
    h2_template = parse_h2_template(wb)
    seasonal = parse_seasonal(wb)
    summary = parse_summary(wb)

    files = {
        "topics.json": topics,
        "use-hubs.json": use_hubs,
        "outer-section.json": outer_section,
        "header-footer.json": header_footer,
        "site-pages.json": site_pages,
        "h2-template.json": h2_template,
        "seasonal.json": seasonal,
        "summary.json": summary,
    }
    for fname, payload in files.items():
        with open(os.path.join(DATA_DIR, fname), "w", encoding="utf-8") as f:
            json.dump(payload, f, indent=2, ensure_ascii=False)

    # ---- Validation against Summary sheet ----
    print("Parsed row counts:")
    print(f"  topics.json        : {len(topics)}")
    print(f"  use-hubs.json      : {len(use_hubs)}")
    print(f"  outer-section.json : {len(outer_section)}")
    print(f"  site-pages.json    : {len(site_pages)}")
    print(f"  header items       : {len(header_footer['header'])}")
    print(f"  footer items       : {len(header_footer['footer'])}")
    print(f"  h2 sections        : {len(h2_template['sections'])}")
    print(f"  seasonal events    : {len(seasonal)}")

    summary_total_pages = sum(row["Pages"] for row in summary)
    summary_p1 = sum(row["P1"] for row in summary)
    print("\nSummary sheet totals:")
    print(f"  Sum of 'Pages' across hubs : {summary_total_pages}")
    print(f"  Sum of 'P1' across hubs    : {summary_p1}")

    ok = True
    if summary_total_pages != len(topics):
        print(f"  MISMATCH: Summary Pages total ({summary_total_pages}) != Topical Map rows ({len(topics)})")
        ok = False
    p1_count = len([t for t in topics if t["priority"] == "P1"])
    if summary_p1 != p1_count:
        print(f"  MISMATCH: Summary P1 total ({summary_p1}) != Topical Map P1 rows ({p1_count})")
        ok = False

    total_posts = len(topics) + len(use_hubs) + len(outer_section)
    print(f"\nTotal posts (topics + use-hubs + outer-section): {total_posts}")

    if ok:
        print("\nRow counts reconcile with the Summary sheet.")
    else:
        print("\nRow-count mismatches found above — review before continuing.")
        sys.exit(2)


if __name__ == "__main__":
    main()
