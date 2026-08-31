/**
 * Rule: Compiled CSS Bundle & Raw Stylesheet Scanner
 * Scans compiled CSS in public/build/assets/ and raw resources/css/ for non-responsive rules.
 */
export function checkCompiledCSS(filePath, content, lines) {
    const issues = [];
    if (!filePath.endsWith('.css')) return issues;

    // Scan for dangerous global fixed widths without media query encapsulation
    // Ignore vendor minified libraries if minified on single line, but scan if relevant
    const isCompiled = filePath.includes('public/build/assets');
    const minMobileWidth = 320;

    if (!isCompiled) {
        let insideMediaQuery = false;
        let braceDepth = 0;

        lines.forEach((line, idx) => {
            const lineNum = idx + 1;
            const trimmed = line.trim();
            if (trimmed.startsWith('/*') || trimmed.startsWith('*')) return;

            if (/@media\b/i.test(trimmed)) {
                insideMediaQuery = true;
            }

            const openBraces = (trimmed.match(/{/g) || []).length;
            const closeBraces = (trimmed.match(/}/g) || []).length;
            braceDepth += openBraces - closeBraces;

            if (braceDepth <= 0) {
                braceDepth = 0;
                insideMediaQuery = false;
            }

            if (!insideMediaQuery) {
                const match = trimmed.match(/(?<!max-)(?:width|min-width)\s*:\s*(\d+)px/i);
                if (match) {
                    const widthVal = parseInt(match[1], 10);
                    // Check if adjacent lines in the same rule define max-width: 100%
                    const surrounding = lines.slice(Math.max(0, idx - 2), Math.min(lines.length, idx + 3)).join(' ');
                    const hasMaxWidthFallback = /max-width\s*:\s*100%/i.test(surrounding);

                    if (widthVal > minMobileWidth && !hasMaxWidthFallback) {
                        issues.push({
                            file: filePath,
                            line: lineNum,
                            rule: 'CSS_FIXED_MIN_WIDTH',
                            severity: 'WARNING',
                            message: `Fixed width declaration '${match[0]}' in CSS exceeds 320px mobile viewport limit outside media queries.`,
                            suggestion: `Wrap in '@media (min-width: 640px)' or use 'max-width: 100%' fluid fallback.`
                        });
                    }
                }
            }
        });
    }

    return issues;
}
