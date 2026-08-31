/**
 * Rule: Responsive Grid & Column Layouts
 * Ensures multi-column grid declarations adopt mobile-first responsive stacking.
 */
export function checkResponsiveGrids(filePath, content, lines) {
    const issues = [];

    lines.forEach((line, idx) => {
        const lineNum = idx + 1;
        const trimmed = line.trim();
        if (trimmed.startsWith('//') || trimmed.startsWith('/*') || trimmed.startsWith('<!--')) return;

        // Tailwind multi-column grids: grid-cols-[3-12] without breakpoint prefix
        const twGridColsRegex = /(?<![\w:-])grid-cols-([3-9]|1[0-2])(?![-\w])/g;
        let match;
        while ((match = twGridColsRegex.exec(line)) !== null) {
            const fullMatchIndex = match.index;
            const precedingPrefix = line.substring(Math.max(0, fullMatchIndex - 10), fullMatchIndex);
            
            if (!/(?:sm|md|lg|xl|2xl):/i.test(precedingPrefix)) {
                // Also check if line has grid-cols-1 or grid-cols-2 as base
                const hasMobileBase = /(?<![\w:-])grid-cols-(?:1|2)(?![\w:-])/.test(line);
                if (!hasMobileBase) {
                    issues.push({
                        file: filePath,
                        line: lineNum,
                        rule: 'NON_RESPONSIVE_GRID',
                        severity: 'WARNING',
                        message: `Multi-column grid '${match[0]}' is applied directly at base mobile without responsive prefix.`,
                        suggestion: `Use mobile-first layout: 'grid-cols-1 sm:grid-cols-2 md:${match[0]}'.`
                    });
                }
            }
        }

        // Bootstrap non-responsive columns: col-3, col-4 without col-12 or col-sm-*
        const bsColRegex = /(?<![\w:-])col-([1-6])(?![-\w])/g;
        while ((match = bsColRegex.exec(line)) !== null) {
            // Check if col-12 or col-sm/md/lg is present
            const hasMobileCol12 = /(?<![\w:-])col-12(?![\w:-])/.test(line);
            const hasResponsiveCol = /(?<![\w:-])col-(?:sm|md|lg|xl)-/i.test(line);
            
            if (!hasMobileCol12 && !hasResponsiveCol) {
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'BOOTSTRAP_NON_RESPONSIVE_COL',
                    severity: 'TIP',
                    message: `Bootstrap column '${match[0]}' may squish content on small mobile viewports.`,
                    suggestion: `Use 'col-12 col-md-${match[1]}' to stack vertically on mobile screens.`
                });
            }
        }
    });

    return issues;
}
