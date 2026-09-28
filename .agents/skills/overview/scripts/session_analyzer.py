#!/usr/bin/env python3
"""
Session Analyzer for Antigravity Overview Skill.
Synthesizes repository context, task completions, architectural changes, and unfinished work.
"""

import os
import re
from dataclasses import dataclass, field
from typing import List, Dict, Any, Set

try:
    from .git_context_parser import GitContext, get_git_context
except ImportError:
    from git_context_parser import GitContext, get_git_context


@dataclass
class DomainBreakdown:
    backend_files: List[str] = field(default_factory=list)
    frontend_files: List[str] = field(default_factory=list)
    devops_config_files: List[str] = field(default_factory=list)
    test_files: List[str] = field(default_factory=list)
    documentation_files: List[str] = field(default_factory=list)
    other_files: List[str] = field(default_factory=list)


@dataclass
class SessionAnalysis:
    context: GitContext
    domains: DomainBreakdown
    all_touched_files: List[str]
    completed_milestones: List[str]
    pending_tasks: List[str]
    detected_todos: List[Dict[str, Any]] = field(default_factory=list)


def categorize_file(path: str) -> str:
    """Categorizes a file into its functional domain."""
    p_lower = path.lower()
    
    # Tests
    if "test" in p_lower or "spec" in p_lower:
        return "test"
    
    # Documentation
    if p_lower.endswith(".md") or p_lower.startswith("docs/") or "reference" in p_lower or p_lower.endswith(".txt"):
        return "doc"
    
    # DevOps & Config
    if p_lower.endswith((".yml", ".yaml", ".json", ".toml", ".ini", ".env", ".gitignore", ".dockerignore")) or "docker" in p_lower or "makefile" in p_lower or ".github" in p_lower:
        return "devops"
        
    # Frontend / UI
    if p_lower.endswith((".html", ".css", ".scss", ".jsx", ".tsx", ".vue", ".svelte", ".svg", ".png", ".jpg")):
        return "frontend"
        
    # Backend & Core Code
    if p_lower.endswith((".py", ".go", ".rs", ".java", ".kt", ".c", ".cpp", ".cs", ".rb", ".php", ".js", ".ts", ".sh")):
        return "backend"
        
    return "other"


def find_todos_in_file(file_path: str, repo_path: str = ".") -> List[Dict[str, Any]]:
    """Scans a file for leftover TODO / FIXME comments."""
    full_path = os.path.join(repo_path, file_path)
    todos = []
    if not os.path.isfile(full_path):
        return todos

    todo_pattern = re.compile(r'(?i)(?://|#|/\*|<!--)\s*(TODO|FIXME|NOTE|HACK)\s*:\s*(.+)$')
    try:
        with open(full_path, "r", encoding="utf-8", errors="ignore") as f:
            for idx, line in enumerate(f, 1):
                m = todo_pattern.search(line)
                if m:
                    todos.append({
                        "file": file_path,
                        "line": idx,
                        "tag": m.group(1).upper(),
                        "text": m.group(2).strip()
                    })
    except Exception:
        pass
    return todos


def analyze_session(repo_path: str = ".", commit_limit: int = 10) -> SessionAnalysis:
    """Performs deep session analysis on repo context."""
    ctx = get_git_context(repo_path=repo_path, commit_limit=commit_limit)
    
    touched_set: Set[str] = set()
    touched_set.update(ctx.uncommitted_modified)
    touched_set.update(ctx.uncommitted_untracked)
    touched_set.update(ctx.staged_files)

    for c in ctx.recent_commits:
        touched_set.update(c.files_changed)

    domains = DomainBreakdown()
    todos = []

    for f in sorted(touched_set):
        cat = categorize_file(f)
        if cat == "test":
            domains.test_files.append(f)
        elif cat == "doc":
            domains.documentation_files.append(f)
        elif cat == "devops":
            domains.devops_config_files.append(f)
        elif cat == "frontend":
            domains.frontend_files.append(f)
        elif cat == "backend":
            domains.backend_files.append(f)
        else:
            domains.other_files.append(f)

        # Scan for TODOs
        file_todos = find_todos_in_file(f, repo_path=repo_path)
        todos.extend(file_todos)

    # Derive completed milestones from commits & file footprints
    milestones = []
    for c in ctx.recent_commits:
        milestones.append(f"[{c.hash}] {c.subject}")

    if not milestones and touched_set:
        milestones.append(f"Modified/created {len(touched_set)} workspace files across {len(domains.backend_files)} backend and {len(domains.test_files)} test files.")

    # Derive pending tasks
    pending = []
    if ctx.uncommitted_modified or ctx.uncommitted_untracked:
        pending.append(f"Review and commit uncommitted changes ({len(ctx.uncommitted_modified)} modified, {len(ctx.uncommitted_untracked)} untracked).")
    
    if domains.test_files:
        pending.append(f"Execute test suite covering {len(domains.test_files)} test files.")
    else:
        pending.append("Write automated unit/integration tests for recently modified logic.")

    if todos:
        pending.append(f"Resolve {len(todos)} inline TODO/FIXME comments.")

    return SessionAnalysis(
        context=ctx,
        domains=domains,
        all_touched_files=sorted(list(touched_set)),
        completed_milestones=milestones,
        pending_tasks=pending,
        detected_todos=todos
    )


if __name__ == "__main__":
    analysis = analyze_session()
    print(f"Session Touched Files: {len(analysis.all_touched_files)}")
    print(f"Backend: {len(analysis.domains.backend_files)}, Tests: {len(analysis.domains.test_files)}")
    print(f"Milestones: {len(analysis.completed_milestones)}")
    print(f"Pending: {len(analysis.pending_tasks)}")
