#!/usr/bin/env python3
"""
Normalize YAML frontmatter in Antigravity rule Markdown files (.md):
1. Flattens multi-line YAML block sequences (e.g. `globs:\n  - a\n  - b`)
   and inline flow sequences (`globs: [a, b]`) into comma-separated scalar strings.
2. Strips surrounding single or double quotes from individual sequence items.
3. Ensures `trigger: glob` is present in frontmatter whenever `globs:` is defined.
"""

import argparse
import glob
import os
import re
import sys
from typing import List, Tuple


def _clean_item(raw: str) -> str:
    """Strip whitespace and surrounding single/double quotes from a sequence item."""
    val = raw.strip()
    if len(val) >= 2 and ((val[0] == '"' and val[-1] == '"') or (val[0] == "'" and val[-1] == "'")):
        val = val[1:-1].strip()
    return val


def fix_yaml_sequences(file_path: str, dry_run: bool = False) -> bool:
    """
    Inspect and normalize YAML frontmatter in a Markdown file.
    Returns True if changes were needed (and written unless dry_run=True).
    """
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()

    # Isolate YAML frontmatter between opening and closing --- delimiters
    fm_pattern = re.compile(r"^(---\r?\n)(.*?\r?\n)(---\r?\n)(.*)", re.DOTALL)
    match = fm_pattern.match(content)
    if not match:
        return False

    header_prefix, fm_body, header_suffix, rest = match.groups()
    line_ending = "\r\n" if "\r\n" in header_prefix else "\n"
    lines = fm_body.splitlines()
    new_lines: List[str] = []
    i = 0

    has_globs = False
    has_trigger = False
    globs_line_idx = -1

    while i < len(lines):
        line = lines[i]

        # Check for existing trigger key
        if re.match(r"^trigger\s*:", line):
            has_trigger = True

        # Case 1: Key followed by indented block sequence items
        key_match = re.match(r"^([a-zA-Z0-9_-]+):\s*$", line)
        if key_match:
            key = key_match.group(1)
            list_items: List[str] = []
            j = i + 1
            while j < len(lines):
                item_match = re.match(r"^\s+-\s+(.*)", lines[j])
                if not item_match:
                    break
                cleaned = _clean_item(item_match.group(1))
                if cleaned:
                    list_items.append(cleaned)
                j += 1

            if list_items:
                comma_str = ", ".join(list_items)
                if key == "globs":
                    has_globs = True
                    globs_line_idx = len(new_lines)
                new_lines.append(f"{key}: {comma_str}")
                i = j
                continue

        # Case 2: Inline flow sequence on globs (e.g., `globs: [a, b]`)
        inline_globs_match = re.match(r"^(globs):\s*\[(.*)\]\s*$", line)
        if inline_globs_match:
            key = inline_globs_match.group(1)
            raw_inner = inline_globs_match.group(2)
            items = [_clean_item(part) for part in raw_inner.split(",") if _clean_item(part)]
            comma_str = ", ".join(items)
            has_globs = True
            globs_line_idx = len(new_lines)
            new_lines.append(f"{key}: {comma_str}")
            i += 1
            continue

        # Case 3: Existing scalar globs line
        if re.match(r"^globs\s*:\s*\S+", line):
            has_globs = True
            globs_line_idx = len(new_lines)

        new_lines.append(line)
        i += 1

    # Ensure `trigger: glob` is present when `globs:` is defined
    if has_globs and not has_trigger:
        insert_pos = globs_line_idx if globs_line_idx >= 0 else len(new_lines)
        new_lines.insert(insert_pos, "trigger: glob")

    new_fm_body = line_ending.join(new_lines) + line_ending
    new_content = header_prefix + new_fm_body + header_suffix + rest

    if new_content != content:
        if not dry_run:
            with open(file_path, "w", encoding="utf-8") as f:
                f.write(new_content)
        return True
    return False


def scan_and_fix(directory: str, dry_run: bool = False) -> Tuple[int, int]:
    """Recursively scan `directory` for `.md` files and fix YAML frontmatter sequences."""
    pattern = os.path.join(directory, "**", "*.md")
    modified_count = 0
    total_count = 0

    for file_path in sorted(glob.glob(pattern, recursive=True)):
        total_count += 1
        if fix_yaml_sequences(file_path, dry_run=dry_run):
            status = "[DRY-RUN FIXED]" if dry_run else "[FIXED]"
            print(f"{status} {file_path}")
            modified_count += 1
        else:
            print(f"[OK]    {file_path}")

    action_word = "Would modify" if dry_run else "Modified"
    print(f"\nOperation complete. {action_word} {modified_count} of {total_count} file(s).")
    return modified_count, total_count


def main() -> None:
    parser = argparse.ArgumentParser(
        description="Flatten YAML sequences in Antigravity Markdown rule frontmatter into comma-separated strings."
    )
    parser.add_argument(
        "target_dir",
        nargs="?",
        default=None,
        help="Path to the rules directory (defaults to .agents/rules, or prompts interactively if omitted in a TTY).",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Preview files that would be modified without writing changes to disk.",
    )
    args = parser.parse_args()

    target_dir = args.target_dir
    if target_dir is None:
        default_dir = ".agents/rules" if os.path.isdir(".agents/rules") else "."
        if sys.stdin.isatty():
            try:
                user_input = input(
                    f"Enter the path to your rules directory (default {default_dir}): "
                ).strip()
            except EOFError:
                user_input = ""
            target_dir = user_input or default_dir
        else:
            target_dir = default_dir

    if not os.path.exists(target_dir):
        print(f"Error: Directory '{target_dir}' does not exist.", file=sys.stderr)
        sys.exit(1)

    scan_and_fix(target_dir, dry_run=args.dry_run)


if __name__ == "__main__":
    main()

