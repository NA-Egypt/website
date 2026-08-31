/**
 * Rule: Mobile Touch Targets & Accessibility (WCAG 2.5.5 / 2.5.8)
 * Ensures buttons, links, and touch triggers meet minimum 44px hitbox guidelines.
 */
export function checkTouchTargets(filePath, content, lines) {
    const issues = [];

    lines.forEach((line, idx) => {
        const lineNum = idx + 1;
        const trimmed = line.trim();
        if (trimmed.startsWith('//') || trimmed.startsWith('/*') || trimmed.startsWith('<!--')) return;

        // Detect <button> or <a href> with tiny explicit height/width without padding
        const isInteractiveElement = /<(?:button|a\s+[^>]*href)/i.test(line) || /class=["'][^"']*\bbtn\b/i.test(line);

        if (isInteractiveElement) {
            // Check for tiny fixed sizes: size-4, size-5, h-4, h-5, h-6, w-4, w-5, w-6
            const tinySizeMatch = line.match(/(?<![\w:-])(?:size-(?:[2-6]|\[[1-3]\dpx\])|(?:h|w)-(?:[2-6]|\[[1-3]\dpx\]))(?![-\w])/);
            
            // Check if there is padding or min-h/min-w to compensate
            const hasPadding = /(?<![\w:-])(?:p|py|px|size|min-h|min-w)-(?:[3-9]|\d{2,}|\[(?:4[4-9]|[5-9]\d|\d{3,})px\])/.test(line);
            
            if (tinySizeMatch && !hasPadding) {
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'TOUCH_TARGET_TOO_SMALL',
                    severity: 'TIP',
                    message: `Interactive element with '${tinySizeMatch[0]}' may be difficult to tap accurately on mobile screens (<44px).`,
                    suggestion: "Add padding (e.g. 'p-2.5') or minimum touch dimensions ('min-h-[44px] min-w-[44px]') to meet WCAG touch target standards."
                });
            }
        }
    });

    return issues;
}
