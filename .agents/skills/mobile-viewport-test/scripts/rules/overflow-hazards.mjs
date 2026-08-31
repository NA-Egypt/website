/**
 * Rule: Horizontal Overflow Hazards
 * Detects elements and patterns prone to causing unwanted horizontal scrolling on mobile devices.
 */
export function checkOverflowHazards(filePath, content, lines) {
    const issues = [];

    // Exclude PDF export views (Dompdf does not use scroll wrappers on printed paper)
    const isPdfTemplate = filePath.includes('/pdf/') || filePath.includes('/exports/') || filePath.includes('pdf.blade.php') || filePath.includes('_pdf.blade.php') || filePath.includes('-pdf.blade.php');

    // 1. Check for unwrapped <table> elements in templates
    if (!isPdfTemplate && (filePath.endsWith('.blade.php') || filePath.endsWith('.vue'))) {
        const tableTagRegex = /<table(?:\s+[^>]*)?>/gi;
        let match;
        while ((match = tableTagRegex.exec(content)) !== null) {
            const tablePos = match.index;
            // Inspect the preceding ~2000 characters to check for an overflow container
            const precedingChunk = content.substring(Math.max(0, tablePos - 2000), tablePos);
            const hasOverflowWrapper = /class=["'][^"']*(?:overflow-x-auto|overflow-auto|table-responsive|overflow-x-scroll)[^"']*["']/i.test(precedingChunk);
            
            if (!hasOverflowWrapper) {
                // Determine line number
                const lineNum = content.substring(0, tablePos).split('\n').length;
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'UNWRAPPED_TABLE_OVERFLOW',
                    severity: 'WARNING',
                    message: 'HTML <table> element found without an overflow-x-auto or responsive container wrapper.',
                    suggestion: 'Wrap the <table> in `<div class="w-full overflow-x-auto"><table>...</table></div>` to prevent page-level horizontal blowouts.'
                });
            }
        }
    }

    // 2. Line-by-line checks for w-screen and flex-nowrap
    lines.forEach((line, idx) => {
        const lineNum = idx + 1;
        const trimmed = line.trim();
        if (trimmed.startsWith('//') || trimmed.startsWith('/*') || trimmed.startsWith('<!--')) return;

        // w-screen class bug (100vw exceeds document width if vertical scrollbar is present)
        if (/(?<![\w:-])w-screen(?![\w:-])/.test(line)) {
            issues.push({
                file: filePath,
                line: lineNum,
                rule: 'W_SCREEN_SCROLLBAR_BUG',
                severity: 'WARNING',
                message: "'w-screen' (100vw) causes horizontal scrollbars on mobile and desktop due to scrollbar widths.",
                suggestion: "Replace 'w-screen' with 'w-full' or 'max-w-full'."
            });
        }

        // flex-nowrap on base mobile
        if (/(?<![\w:-])flex-nowrap(?![\w:-])/.test(line)) {
            // Check if there is a responsive prefix
            const tokenMatch = line.match(/(?:sm|md|lg|xl|2xl):flex-nowrap/);
            if (!tokenMatch) {
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'FLEX_NOWRAP_OVERFLOW',
                    severity: 'TIP',
                    message: "'flex-nowrap' without responsive prefixes can cause child elements to overflow narrow viewports.",
                    suggestion: "Consider using 'flex-wrap sm:flex-nowrap' or 'flex-col sm:flex-row'."
                });
            }
        }
    });

    return issues;
}
