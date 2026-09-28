#!/usr/bin/env python3
"""
Git Context Parser for Antigravity Overview Skill.
Extracts recent repository commits, staging status, branch activity, and touched files.
"""

import os
import subprocess
from dataclasses import dataclass, field
from typing import List, Dict, Any, Optional


@dataclass
class CommitInfo:
    hash: str
    author: str
    date: str
    subject: str
    files_changed: List[str] = field(default_factory=list)


@dataclass
class GitContext:
    branch: str = "main"
    remote_url: str = ""
    is_clean: bool = True
    uncommitted_modified: List[str] = field(default_factory=list)
    uncommitted_untracked: List[str] = field(default_factory=list)
    staged_files: List[str] = field(default_factory=list)
    recent_commits: List[CommitInfo] = field(default_factory=list)
    total_additions: int = 0
    total_deletions: int = 0

    def to_dict(self) -> Dict[str, Any]:
        return {
            "branch": self.branch,
            "remote_url": self.remote_url,
            "is_clean": self.is_clean,
            "uncommitted_modified": self.uncommitted_modified,
            "uncommitted_untracked": self.uncommitted_untracked,
            "staged_files": self.staged_files,
            "recent_commits": [
                {
                    "hash": c.hash,
                    "author": c.author,
                    "date": c.date,
                    "subject": c.subject,
                    "files_changed": c.files_changed
                }
                for c in self.recent_commits
            ],
            "total_additions": self.total_additions,
            "total_deletions": self.total_deletions
        }


def run_git(args: List[str], repo_path: str = ".") -> str:
    """Executes git commands safely."""
    try:
        res = subprocess.run(
            ["git"] + args,
            cwd=repo_path,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True,
            check=True
        )
        return res.stdout.strip()
    except Exception:
        return ""


def get_git_context(repo_path: str = ".", commit_limit: int = 10) -> GitContext:
    """Collects repository status and commit history."""
    ctx = GitContext()

    # 1. Branch name
    branch = run_git(["rev-parse", "--abbrev-ref", "HEAD"], repo_path)
    ctx.branch = branch if branch else "unknown"

    # 2. Remote URL
    remote = run_git(["config", "--get", "remote.origin.url"], repo_path)
    ctx.remote_url = remote

    # 3. Status
    status_output = run_git(["status", "--porcelain"], repo_path)
    if status_output:
        ctx.is_clean = False
        for line in status_output.splitlines():
            if len(line) < 3:
                continue
            code = line[:2]
            path = line[3:].strip()
            if code.startswith("?") or code == "??":
                ctx.uncommitted_untracked.append(path)
            elif code[0] in ["M", "A", "D", "R", "C"]:
                ctx.staged_files.append(path)
            elif code[1] in ["M", "D"]:
                ctx.uncommitted_modified.append(path)

    # 4. Recent Commits
    log_format = "%h|%an|%ad|%s"
    log_output = run_git(["log", f"-n{commit_limit}", f"--pretty=format:{log_format}", "--date=short"], repo_path)
    if log_output:
        for line in log_output.splitlines():
            parts = line.split("|", 3)
            if len(parts) == 4:
                c_hash, author, date, subject = parts
                # Get files changed for this commit
                files_out = run_git(["show", "--stat", "--name-only", "--pretty=format:", c_hash], repo_path)
                files_list = [f.strip() for f in files_out.splitlines() if f.strip()]
                ctx.recent_commits.append(
                    CommitInfo(hash=c_hash, author=author, date=date, subject=subject, files_changed=files_list)
                )

    # 5. Diff stats
    diff_stat = run_git(["diff", "--shortstat"], repo_path)
    if diff_stat:
        import re
        ins_match = re.search(r'(\d+)\s+insertion', diff_stat)
        del_match = re.search(r'(\d+)\s+deletion', diff_stat)
        if ins_match:
            ctx.total_additions += int(ins_match.group(1))
        if del_match:
            ctx.total_deletions += int(del_match.group(1))

    return ctx


if __name__ == "__main__":
    ctx = get_git_context()
    print(f"Branch: {ctx.branch} (Clean: {ctx.is_clean})")
    print(f"Commits: {len(ctx.recent_commits)}")
    print(f"Modified: {len(ctx.uncommitted_modified)}, Untracked: {len(ctx.uncommitted_untracked)}")
