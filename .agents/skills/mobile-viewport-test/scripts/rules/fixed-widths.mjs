/**
 * Rule: Fixed Widths & Oversizing Hazards
 * Detects hardcoded width/min-width declarations > 320px that lack responsive breakpoint modifiers.
 */
export function checkFixedWidths(filePath, content, lines) {
    const issues = [];
    const minMobileWidth = 320;

    lines.forEach((line, idx) => {
        const lineNum = idx + 1;
        const trimmed = line.trim();
        if (trimmed.startsWith('//') || trimmed.startsWith('/*') || trimmed.startsWith('*') || trimmed.startsWith('<!--')) {
            return;
        }

        // 1. Tailwind arbitrary fixed width classes: w-[350px], min-w-[400px] without responsive prefix
        // Matches e.g. "w-[350px]" but NOT "sm:w-[350px]" or "md:w-[350px]"
        const twFixedRegex = /(?<![\w:-])(?:w|min-w)-\[(\d+)px\]/g;
        let match;
        while ((match = twFixedRegex.exec(line)) !== null) {
            const widthVal = parseInt(match[1], 10);
            if (widthVal > minMobileWidth) {
                // Check if preceded by a breakpoint prefix on the same token
                const fullToken = line.substring(Math.max(0, match.index - 10), match.index + match[0].length);
                if (!/(?:sm|md|lg|xl|2xl):/i.test(fullToken)) {
                    issues.push({
                        file: filePath,
                        line: lineNum,
                        rule: 'OVERSIZED_FIXED_WIDTH',
                        severity: 'CRITICAL',
                        message: `Fixed width '${match[0]}' (${widthVal}px) exceeds minimum mobile viewport (${minMobileWidth}px) without responsive prefix.`,
                        suggestion: `Replace with fluid width 'w-full max-w-[${widthVal}px]' or add responsive breakpoint (e.g. 'w-full md:${match[0]}').`
                    });
                }
            }
        }

        // 2. Inline styles: style="width: 400px" or style="min-width: 350px"
        const inlineStyleRegex = /style=["'][^"']*(?:(?<!max-)width|min-width)\s*:\s*(\d+)px[^"']*["']/gi;
        while ((match = inlineStyleRegex.exec(line)) !== null) {
            const widthVal = parseInt(match[1], 10);
            if (widthVal > minMobileWidth) {
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'INLINE_STYLE_OVERSIZED_WIDTH',
                    severity: 'CRITICAL',
                    message: `Hardcoded inline width '${widthVal}px' exceeds minimum mobile viewport (${minMobileWidth}px).`,
                    suggestion: `Use responsive CSS classes or 'max-width: 100%; width: ${widthVal}px' to avoid horizontal scrollbars on mobile.`
                });
            }
        }

        // 3. Massive fixed padding on base mobile: p-[40px], px-20, px-24 without breakpoint
        const massivePaddingRegex = /(?<![\w:-])(?:p|px)-(?:20|24|28|32|\[(?:[4-9]\d|\d{3,})px\])/g;
        while ((match = massivePaddingRegex.exec(line)) !== null) {
            const fullToken = line.substring(Math.max(0, match.index - 10), match.index + match[0].length);
            if (!/(?:sm|md|lg|xl|2xl):/i.test(fullToken)) {
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'MASSIVE_MOBILE_PADDING',
                    severity: 'WARNING',
                    message: `Excessive base padding '${match[0]}' severely constrains content area on narrow mobile screens.`,
                    suggestion: `Use smaller mobile padding with responsive step-up (e.g. 'px-4 sm:${match[0]}').`
                });
            }
        }
    });

    return issues;
}
