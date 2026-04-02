# -*- coding: utf-8 -*-
"""Convert rolex-market-report.source.md (fetch-style markdown) to HTML for Beike page editor."""
import re
import html
from pathlib import Path

ROOT = Path(__file__).resolve().parent
SRC = ROOT / "rolex-market-report.source.md"
OUT = ROOT / "rolex-market-report-content.html"


def inline_fmt(s: str) -> str:
    s = html.escape(s)
    s = re.sub(r"\[([^\]]+)\]\(([^)]+)\)", r'<a href="\2">\1</a>', s)
    s = re.sub(r"\*\*([^*]+)\*\*", r"<strong>\1</strong>", s)
    return s


def parse_table(lines: list[str], i: int) -> tuple[str, int]:
    rows = []
    j = i
    while j < len(lines) and "|" in lines[j] and lines[j].strip().startswith("|"):
        rows.append(lines[j])
        j += 1
    if len(rows) < 2:
        return "", i
    header = [c.strip() for c in rows[0].strip("|").split("|")]
    sep = rows[1]
    if not re.match(r"^\|\s*[-:]+\s*(\|\s*[-:]+\s*)+\|?\s*$", sep.replace(" ", "")):
        # not a markdown table separator
        return "", i
    body_rows = rows[2:]
    th = "".join(f"<th>{inline_fmt(h)}</th>" for h in header)
    tbs = []
    for br in body_rows:
        cells = [c.strip() for c in br.strip("|").split("|")]
        if len(cells) != len(header):
            continue
        tbs.append("<tr>" + "".join(f"<td>{inline_fmt(c)}</td>" for c in cells) + "</tr>")
    table_html = '<table class="table table-bordered table-sm my-3"><thead><tr>' + th + "</tr></thead><tbody>" + "".join(tbs) + "</tbody></table>"
    return table_html, j


def main():
    text = SRC.read_text(encoding="utf-8")
    lines = text.splitlines()
    out: list[str] = ['<div class="lux-import-rolex-report">']

    i = 0
    in_ul = False
    in_ol = False

    def close_lists():
        nonlocal in_ul, in_ol
        s = ""
        if in_ul:
            s += "</ul>"
            in_ul = False
        if in_ol:
            s += "</ol>"
            in_ol = False
        return s

    while i < len(lines):
        line = lines[i]
        stripped = line.strip()

        if not stripped:
            out.append(close_lists())
            i += 1
            continue

        if stripped.startswith("|") and "|" in stripped[1:]:
            out.append(close_lists())
            tbl, ni = parse_table(lines, i)
            if tbl:
                out.append(tbl)
                i = ni
                continue

        m = re.match(r"^(#{1,6})\s+(.*)$", stripped)
        if m:
            out.append(close_lists())
            level = len(m.group(1))
            tag = f"h{min(level + 1, 6)}"  # # in file -> h2 (page title is h2 in theme)
            if level == 1:
                tag = "h2"
            elif level == 2:
                tag = "h3"
            elif level == 3:
                tag = "h4"
            content = inline_fmt(m.group(2).strip())
            out.append(f"<{tag}>{content}</{tag}>")
            i += 1
            continue

        if stripped.startswith("- "):
            if not in_ul:
                out.append(close_lists())
                out.append("<ul>")
                in_ul = True
            item = inline_fmt(stripped[2:].strip())
            out.append(f"<li>{item}</li>")
            i += 1
            continue

        if re.match(r"^\d+\.\s+", stripped):
            if not in_ol:
                out.append(close_lists())
                out.append("<ol>")
                in_ol = True
            item = re.sub(r"^\d+\.\s+", "", stripped)
            out.append(f"<li>{inline_fmt(item)}</li>")
            i += 1
            continue

        out.append(close_lists())
        out.append(f"<p>{inline_fmt(stripped)}</p>")
        i += 1

    out.append(close_lists())
    out.append("</div>")
    OUT.write_text("\n".join(out), encoding="utf-8")
    print(f"Wrote {OUT} ({len(OUT.read_text(encoding='utf-8'))} chars)")


if __name__ == "__main__":
    main()
