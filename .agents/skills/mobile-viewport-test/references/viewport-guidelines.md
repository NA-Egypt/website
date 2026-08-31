# Mobile Viewport & Core Web Vitals Guidelines

## 1. Target Mobile Viewports

All layouts must render seamlessly without horizontal scrolling across these standard viewport widths:

| Device Category | Viewport Width | Typical Devices |
| :--- | :--- | :--- |
| **Minimum Mobile** | `320px` | iPhone SE (1st gen), small Androids, split-screen views |
| **Standard Mobile** | `360px` – `393px` | iPhone 12/13/14/15, Galaxy S21/S22/S23, Pixel 7 |
| **Large Mobile / Plus**| `412px` – `430px` | iPhone Pro Max / Plus, Galaxy Ultra |
| **Small Tablet / Foldable** | `600px` – `768px` | iPad Mini, Galaxy Fold (unfolded) |

---

## 2. Core Web Vitals Thresholds (Mobile & Desktop)

| Metric | Good (Green) | Needs Improvement (Yellow) | Poor (Red) |
| :--- | :--- | :--- | :--- |
| **LCP** (Largest Contentful Paint) | $\le 2.5\text{s}$ | $2.5\text{s} - 4.0\text{s}$ | $> 4.0\text{s}$ |
| **CLS** (Cumulative Layout Shift) | $\le 0.10$ | $0.10 - 0.25$ | $> 0.25$ |
| **INP** (Interaction to Next Paint) | $\le 200\text{ms}$ | $200\text{ms} - 500\text{ms}$ | $> 500\text{ms}$ |
| **FCP** (First Contentful Paint) | $\le 1.8\text{s}$ | $1.8\text{s} - 3.0\text{s}$ | $> 3.0\text{s}$ |
| **TTFB** (Time to First Byte) | $\le 800\text{ms}$ | $800\text{ms} - 1800\text{ms}$ | $> 1800\text{ms}$ |

---

## 3. Key Layout Shift & Viewport Anti-Patterns

1. **Hardcoded Pixel Dimensions**:
   - ❌ `class="w-[360px]"` or `style="width: 400px"`
   - ✅ `class="w-full max-w-[360px]"` or `class="w-full md:w-[360px]"`
2. **Unwrapped Tables**:
   - ❌ Raw `<table>...</table>` directly inside a narrow card or grid.
   - ✅ `<div class="overflow-x-auto w-full"><table>...</table></div>`
3. **Unsized Media**:
   - ❌ `<img src="logo.png">`
   - ✅ `<img src="logo.png" width="160" height="48" class="h-auto w-auto max-w-full">`
4. **Touch Target Dimensions (WCAG 2.5.5 / 2.5.8)**:
   - Minimum target size: $44\text{px} \times 44\text{px}$ (or $24\text{px} \times 24\text{px}$ with surrounding spacing).
   - Use `min-h-[44px] min-w-[44px]` or adequate padding (`p-2.5` to `p-3`).
