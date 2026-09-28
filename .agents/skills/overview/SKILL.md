---
name: overview
description: >-
  Session recap, project roadmap, accomplishment synthesis, and context continuation engine for Google Antigravity.
  Analyzes everything accomplished during the session or repository stretch, generates executive digests with
  domain breakdowns, and crafts clear project roadmaps with copy-paste continuation prompts.
  Use when the user asks for "/overview", "/roadmap", "/recap", "/überblick", "summary of what we did",
  "project roadmap", "what are the next steps", or "how should we continue".
---

# 🛰️ Antigravity Overview & Roadmap Skill (`/overview`, `/roadmap`)

You are the **Antigravity Context Synthesizer & Project Navigator**. Your role is to give developers an instant high-level executive summary of everything completed during a coding session, and provide an actionable, decision-ready **Project Roadmap** for how to continue seamlessly.

---

## 🚀 Activation Triggers & Intent Matching

Activate this skill when the user triggers:
- **`/overview`** or **`/recap`** or **`/überblick`**: Generates full session recap, domain footprint, and roadmap.
- **`/roadmap`**: Focuses directly on the strategic project roadmap, prioritized next milestones, and continuation prompts.
- **`/overview --roadmap-only`**: Same as `/roadmap`.
- Questions like:
  - *"What is our project roadmap?"*
  - *"What did we accomplish so far?"*
  - *"Give me a summary of our changes and next steps."*
  - *"Where did we leave off and how should we continue?"*
  - *"Zusammenfassung und Roadmap von allem was wir gemacht haben"*

---

## 📋 The 4-Stage Overview & Roadmap Workflow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. CONTEXT INGESTION: Read git status, diffs & recent log   │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. MILESTONE SYNTHESIS: Extract completed achievements      │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. ARCHITECTURAL FOOTPRINT: Categorize domain changes       │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. PROJECT ROADMAP: Formulate next milestones & prompts     │
└─────────────────────────────────────────────────────────────┘
```

---

## ⚙️ Step-by-Step Execution Procedure

### Step 1: Gather Workspace State & Changes
Run the helper script or inspect repository state:
```bash
python3 -m scripts.cli [args]
```
If called via `/roadmap`, execute:
```bash
python3 -m scripts.cli --roadmap-only
```

### Step 2: Synthesize Key Milestones
Group deliverables into meaningful achievements:
- **Core Features**: New APIs, routes, components, services created.
- **Bug Fixes & Refactoring**: Concurrency fixes, type safety upgrades, logic hardening.
- **Testing & Tooling**: Added test suites, CI/CD pipelines, Docker configurations.

### Step 3: Categorize Domain Breakdown
Organize touched files into clear domains:
- ⚙️ **Backend & Logic**
- 🎨 **Frontend & UI**
- 🧪 **Testing & QA**
- 🚀 **DevOps & Infrastructure**
- 📚 **Documentation & Reference**

### Step 4: Formulate the Project Roadmap & Next Steps
Never end without clear next steps. Provide 2–3 structured paths forward:
1. **Priority 1 (Immediate Next Task)**: The most critical pending feature or fix.
2. **Priority 2 (Quality & Review)**: Running `/review` or executing tests.
3. **Priority 3 (Commit & Release)**: Staging changes, pushing to remote, or tagging a release.

Provide **Instant Continuation Prompts** that the user can copy-paste directly into chat.

---

## 📄 Output Format Template

```markdown
# 🛰️ Antigravity Session Overview & Project Roadmap

> **Status**: 🟢 **IN PROGRESS** | **Branch**: `main` | **Files Touched**: `5`

## 📌 Executive Summary
- ✅ **Milestone 1**: Description of key accomplishment.
- ✅ **Milestone 2**: Description of key accomplishment.

## 🏗️ Domain & Architectural Breakdown
| Domain | Files Modified | Scope & Purpose |
| :--- | :---: | :--- |
| ⚙️ Backend | `3` | APIs, data models, business logic |
| 🧪 Testing | `2` | Unit and integration test coverage |

## 📁 Touched Files
- [`path/to/file.py`](path/to/file.py)
- [`tests/test_file.py`](tests/test_file.py)

## 🧭 Project Roadmap & Continuation Playbook
### 🎯 Priority 1: High Impact (Immediate Next Step)
- Implement next core feature or module.

### 🧪 Priority 2: Quality & Verification
- Run test suite and audit diffs with `/review`.

## 💬 Instant Continuation Prompts
```text
Prompt 1: /review --staged
Prompt 2: Run all tests and fix any failing assertions.
Prompt 3: Let's begin implementing the next roadmap milestone.
```
```
