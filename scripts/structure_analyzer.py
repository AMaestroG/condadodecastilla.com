#!/usr/bin/env python3
"""Analyze repository structure for duplicates and misplaced CSS/JS.

This script scans PHP and HTML files to detect duplicated content and improper
placement of <link> and <script> tags.
"""

from __future__ import annotations

import hashlib
import os
from pathlib import Path
from collections import defaultdict
from bs4 import BeautifulSoup
import logging


def configure_logger() -> logging.Logger:
    """Return a logger configured once for this module."""
    logger = logging.getLogger(__name__)
    if not logger.handlers:
        logging.basicConfig(
            level=logging.INFO,
            format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        )
    return logger


logger = configure_logger()

REPO_ROOT = Path(__file__).resolve().parents[1]

FILE_PATTERNS = ["**/*.php", "**/*.html"]


def find_files() -> list[Path]:
    files: list[Path] = []
    for pattern in FILE_PATTERNS:
        files.extend(REPO_ROOT.glob(pattern))
    return files


def sha1_of(path: Path) -> str:
    data = path.read_bytes()
    return hashlib.sha1(data).hexdigest()


def detect_duplicates(paths: list[Path]) -> dict[str, list[Path]]:
    hashes: dict[str, list[Path]] = defaultdict(list)
    for p in paths:
        hashes[sha1_of(p)].append(p)
    return {h: ps for h, ps in hashes.items() if len(ps) > 1}


def repeated_header_footer(paths: list[Path]) -> tuple[list[Path], list[Path]]:
    header_file = REPO_ROOT / "_header.php"
    footer_file = REPO_ROOT / "_footer.php"
    header_content = header_file.read_text(encoding="utf-8", errors="ignore").strip()
    footer_content = footer_file.read_text(encoding="utf-8", errors="ignore").strip()

    repeated_headers: list[Path] = []
    repeated_footers: list[Path] = []
    for p in paths:
        if p in {header_file, footer_file}:
            continue
        text = p.read_text(encoding="utf-8", errors="ignore")
        if header_content and header_content in text and "_header.php" not in text:
            repeated_headers.append(p)
        if footer_content and footer_content in text and "_footer.php" not in text:
            repeated_footers.append(p)
    return repeated_headers, repeated_footers


def check_css_js_placement(paths: list[Path]) -> dict[str, list[str]]:
    issues: dict[str, list[str]] = defaultdict(list)
    for p in paths:
        content = p.read_text(encoding="utf-8", errors="ignore")
        soup = BeautifulSoup(content, "html.parser")
        head = soup.head
        body = soup.body
        if not head or not body:
            continue

        for link in soup.find_all("link"):
            if link.parent != head:
                issues[str(p)].append("<link> fuera de <head>")
                break

        for script in soup.find_all("script"):
            if script.parent == head:
                continue
            if script.parent != body:
                issues[str(p)].append("<script> fuera de <head> o <body>")
                break
            # must be last meaningful element in body
            siblings = []
            for s in script.next_siblings:
                if str(s).strip():
                    siblings.append(s)
            if siblings:
                issues[str(p)].append("<script> no al final de <body>")
                break
    return issues


def generate_report() -> str:
    paths = find_files()
    duplicates = detect_duplicates(paths)
    repeated_headers, repeated_footers = repeated_header_footer(paths)
    css_js = check_css_js_placement(paths)

    lines = ["== Duplicados =="]
    for h, ps in duplicates.items():
        lines.append(f"Hash {h[:7]}: {', '.join(str(p) for p in ps)}")

    lines.append("\n== Cabeceras repetidas ==")
    for p in repeated_headers:
        lines.append(str(p))

    lines.append("\n== Pies repetidos ==")
    for p in repeated_footers:
        lines.append(str(p))

    lines.append("\n== CSS/JS mal ubicados ==")
    for p, msgs in css_js.items():
        for m in msgs:
            lines.append(f"{p}: {m}")

    return "\n".join(lines)


def main() -> None:
    logger.info(generate_report())


if __name__ == "__main__":
    main()
