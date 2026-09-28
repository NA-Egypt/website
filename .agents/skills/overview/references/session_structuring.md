# 📌 Session Structuring & Executive Synthesis Guide

When summarizing a long or complex coding session, clarity, high signal-to-noise ratio, and clear architectural context are vital.

---

## 1. The 4 Core Questions Every Overview Must Answer
1. **What was accomplished?** (Concrete deliverables, working features, resolved bugs).
2. **What changed in the codebase?** (Modified files, added dependencies, new architecture patterns).
3. **What is the current system state?** (Tests passing/failing, uncommitted diffs, environment requirements).
4. **How do we proceed from here?** (Immediate actionable next steps, roadmap options, copy-paste prompt templates).

---

## 2. Formatting Best Practices
- **Use Visual Hierarchies**: Lead with status badges (`🟢 IN PROGRESS` vs `🔵 COMMITTED`) and bold high-level takeaways.
- **Categorize by Domain**: Group touched files by functional area (Backend, Frontend, Tests, DevOps, Documentation) rather than flat file lists.
- **Surface Technical Decisions**: Highlight major architectural choices (e.g. choice of libraries, schema changes, database migrations) so developers resuming context understand the rationale.
- **Highlight Open Debt**: Call out any temporary placeholders, skipped tests, or inline `TODO` comments.
