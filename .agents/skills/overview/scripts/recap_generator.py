#!/usr/bin/env python3
"""
Recap Report Generator for Antigravity Overview Skill.
Synthesizes executive summaries, accomplishment milestones, file breakdowns,
and actionable continuation playbooks with ready-to-run prompt templates.
"""

import json
from typing import List, Dict, Any, Optional

try:
    from .session_analyzer import SessionAnalysis, analyze_session
except ImportError:
    from session_analyzer import SessionAnalysis, analyze_session


class RecapGenerator:
    def __init__(self, analysis: SessionAnalysis):
        self.analysis = analysis

    def generate_markdown(self, title: str = "Antigravity Session Overview & Recap") -> str:
        """Generates a structured GitHub-Flavored Markdown session summary."""
        ctx = self.analysis.context
        dom = self.analysis.domains
        
        status_badge = "🟢 **IN PROGRESS**" if not ctx.is_clean else "🔵 **CLEAN / COMMITTED**"
        
        lines = []
        lines.append(f"# 🛰️ {title}")
        lines.append("")
        lines.append(f"> **Status**: {status_badge} &nbsp;|&nbsp; **Branch**: `{ctx.branch}` &nbsp;|&nbsp; **Files Touched**: `{len(self.analysis.all_touched_files)}` &nbsp;|&nbsp; **Recent Commits**: `{len(ctx.recent_commits)}`")
        lines.append("")
        
        # 1. Executive Summary
        lines.append("## 📌 Executive Summary")
        lines.append("Here is the high-level synthesis of everything completed during this development stretch:")
        lines.append("")
        if self.analysis.completed_milestones:
            for m in self.analysis.completed_milestones[:6]:
                lines.append(f"- ✅ **{m}**")
        else:
            lines.append("- Active exploration and workspace file editing in progress.")
        lines.append("")

        # 2. Domain & Architectural Footprint
        lines.append("## 🏗️ Domain & Architectural Breakdown")
        lines.append("| Domain | Files Modified | Scope & Purpose |")
        lines.append("| :--- | :---: | :--- |")
        
        domain_rows = [
            ("⚙️ Backend & Core Logic", len(dom.backend_files), "Business logic, APIs, and algorithmic pipelines"),
            ("🎨 Frontend & UI", len(dom.frontend_files), "Components, views, styles, and templates"),
            ("🧪 Testing & QA", len(dom.test_files), "Unit, integration, and regression test suites"),
            ("🚀 DevOps & Infra", len(dom.devops_config_files), "CI/CD workflows, Dockerfiles, and configuration manifests"),
            ("📚 Docs & Knowledge", len(dom.documentation_files), "READMEs, references, guides, and skill specs")
        ]
        
        for name, count, desc in domain_rows:
            if count > 0:
                lines.append(f"| {name} | `{count}` | {desc} |")
        lines.append("")

        # 3. Workspace Files Changed
        if self.analysis.all_touched_files:
            lines.append("## 📁 Touched Files & Modifications")
            lines.append("<details>")
            lines.append(f"<summary><strong>Click to view all {len(self.analysis.all_touched_files)} modified files</strong></summary>\n")
            lines.append("| Status / Type | File Path |")
            lines.append("| :---: | :--- |")
            for f in self.analysis.all_touched_files[:30]:
                tag = "MODIFIED" if f in ctx.uncommitted_modified else ("UNTRACKED" if f in ctx.uncommitted_untracked else "COMMITTED")
                lines.append(f"| `{tag}` | [`{f}`]({f}) |")
            if len(self.analysis.all_touched_files) > 30:
                lines.append(f"| `...` | *and {len(self.analysis.all_touched_files) - 30} more files* |")
            lines.append("\n</details>")
            lines.append("")

        # 4. Detected Open TODOs (if any)
        if self.analysis.detected_todos:
            lines.append("## ⚠️ Detected Inline TODOs & Technical Debt")
            for t in self.analysis.detected_todos[:5]:
                lines.append(f"- [`{t['file']}:{t['line']}`]({t['file']}#L{t['line']}) — **{t['tag']}**: {t['text']}")
            lines.append("")

        # 5. Continuation Playbook (Next Steps)
        lines.append("## 🧭 Continuation Playbook: How to Proceed")
        lines.append("Choose one of the recommended next steps below or copy a prompt to continue seamlessly:")
        lines.append("")
        
        lines.append("### 🎯 Option 1: Verification & Code Review (Recommended)")
        lines.append("- Run `/review` to audit changes for security, performance, and clean code standards.")
        lines.append("- Execute the automated test suite to ensure zero regressions.")
        lines.append("")
        
        lines.append("### 📦 Option 2: Version Control & Commit")
        lines.append("- Stage remaining changes with `git add`.")
        lines.append("- Generate a structured conventional commit message.")
        lines.append("")
        
        lines.append("### 🚀 Option 3: Continue Feature Evolution")
        if self.analysis.pending_tasks:
            for idx, p in enumerate(self.analysis.pending_tasks, 1):
                lines.append(f"{idx}. {p}")
        else:
            lines.append("1. Continue implementing the next planned project milestone.")
        lines.append("")

        # 6. Copy-Paste Continuation Prompts
        lines.append("## 💬 Instant Continuation Prompts")
        lines.append("> Simply copy and send one of these prompts to Antigravity:")
        lines.append("")
        lines.append("```text")
        lines.append("Prompt 1 (Run Review): /review --staged")
        lines.append("Prompt 2 (Run Tests): Run all unit tests in the repository and fix any failing cases.")
        lines.append("Prompt 3 (Commit Changes): Review current git diff, stage all changes, and write a conventional commit message.")
        lines.append("Prompt 4 (Next Milestone): Based on the current overview, let's proceed with the next feature step.")
        lines.append("```")
        lines.append("")
        lines.append("---")
        lines.append("*Generated by [Antigravity Overview](https://github.com/Pikaswelt/antigravity-overview) · Context & Continuation Engine*")

        return "\n".join(lines)

    def to_json(self) -> str:
        """Serializes session summary into JSON."""
        ctx = self.analysis.context
        payload = {
            "session": {
                "branch": ctx.branch,
                "is_clean": ctx.is_clean,
                "files_touched_count": len(self.analysis.all_touched_files),
                "commits_count": len(ctx.recent_commits)
            },
            "milestones": self.analysis.completed_milestones,
            "domains": {
                "backend": self.analysis.domains.backend_files,
                "frontend": self.analysis.domains.frontend_files,
                "devops": self.analysis.domains.devops_config_files,
                "tests": self.analysis.domains.test_files,
                "documentation": self.analysis.domains.documentation_files
            },
            "pending_tasks": self.analysis.pending_tasks,
            "todos": self.analysis.detected_todos,
            "all_files": self.analysis.all_touched_files
        }
        return json.dumps(payload, indent=2)


if __name__ == "__main__":
    analysis = analyze_session()
    gen = RecapGenerator(analysis)
    print(gen.generate_markdown())
