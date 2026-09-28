#!/usr/bin/env python3
"""
Antigravity Overview CLI Runner.
Provides fast session summaries, domain breakdowns, and continuation roadmaps.
"""

import sys
import argparse
from typing import Optional

try:
    from .session_analyzer import analyze_session
    from .recap_generator import RecapGenerator
except ImportError:
    from session_analyzer import analyze_session
    from recap_generator import RecapGenerator


def run_overview(
    commit_limit: int = 10,
    json_mode: bool = False,
    output_path: Optional[str] = None,
    roadmap_only: bool = False,
    repo_path: str = "."
) -> int:
    """Runs session analysis and outputs recap."""
    try:
        analysis = analyze_session(repo_path=repo_path, commit_limit=commit_limit)
        generator = RecapGenerator(analysis)

        if json_mode:
            output = generator.to_json()
        elif roadmap_only:
            lines = [
                "# 🧭 Antigravity Continuation Roadmap",
                "",
                "### 🎯 Recommended Next Steps:"
            ]
            for idx, p in enumerate(analysis.pending_tasks, 1):
                lines.append(f"{idx}. {p}")
            output = "\n".join(lines)
        else:
            output = generator.generate_markdown()

        if output_path:
            with open(output_path, "w", encoding="utf-8") as f:
                f.write(output)
            print(f"✅ Session overview saved to: {output_path}")
        else:
            print(output)

        return 0
    except Exception as e:
        print(f"Error generating overview: {e}", file=sys.stderr)
        return 1


def main():
    parser = argparse.ArgumentParser(
        prog="agy-overview",
        description="Antigravity Overview - Session Recap & Context Continuation Suite"
    )
    parser.add_argument("-n", "--commits", type=int, default=10, help="Number of recent commits to analyze (default: 10)")
    parser.add_argument("--json", action="store_true", help="Output session digest in JSON format")
    parser.add_argument("-o", "--output", type=str, default=None, help="Save report to file path")
    parser.add_argument("--roadmap-only", action="store_true", help="Output only the continuation roadmap")
    parser.add_argument("--repo", type=str, default=".", help="Repository root directory")

    args = parser.parse_args()
    code = run_overview(
        commit_limit=args.commits,
        json_mode=args.json,
        output_path=args.output,
        roadmap_only=args.roadmap_only,
        repo_path=args.repo
    )
    sys.exit(code)


if __name__ == "__main__":
    main()
