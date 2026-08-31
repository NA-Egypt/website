/**
 * Rule: Viewport Meta Tag Validator
 * Ensures root HTML / Blade layout files contain a standard, accessible <meta name="viewport" ...>
 */
export function checkMetaViewport(filePath, content, lines) {
    const issues = [];
    
    // Ignore PDF export templates (Dompdf / print views)
    if (filePath.includes('/pdf/') || filePath.endsWith('.pdf.blade.php') || filePath.includes('pdf.blade.php')) {
        return issues;
    }

    const isRootLayout = filePath.endsWith('.blade.php') && 
        (content.includes('<html') || content.includes('<!DOCTYPE') || filePath.includes('layout') || filePath.includes('app.blade.php') || filePath.includes('dashborad') || filePath.includes('welcome'));

    if (!isRootLayout) {
        return issues;
    }

    const metaViewportRegex = /<meta\s+name=["']viewport["']\s+content=["']([^"']+)["']/i;
    const match = content.match(metaViewportRegex);

    if (!match) {
        // Find <head> line number
        let headLine = 1;
        lines.forEach((line, idx) => {
            if (line.includes('<head')) headLine = idx + 1;
        });

        issues.push({
            file: filePath,
            line: headLine,
            rule: 'VIEWPORT_META_MISSING',
            severity: 'CRITICAL',
            message: 'Root HTML template is missing <meta name="viewport" content="width=device-width, initial-scale=1">.',
            suggestion: 'Add <meta name="viewport" content="width=device-width, initial-scale=1"> inside <head>.'
        });
    } else {
        const viewportContent = match[1];
        const lineIdx = lines.findIndex(l => l.includes(match[0])) + 1;

        if (/user-scalable\s*=\s*no/i.test(viewportContent) || /maximum-scale\s*=\s*1(\.0)?(?!\d)/i.test(viewportContent)) {
            issues.push({
                file: filePath,
                line: lineIdx || 1,
                rule: 'VIEWPORT_ZOOM_DISABLED',
                severity: 'WARNING',
                message: 'Viewport disables pinch-to-zoom (user-scalable=no or maximum-scale=1), violating accessibility standards.',
                suggestion: 'Remove user-scalable=no and maximum-scale=1 from the viewport meta tag.'
            });
        }
    }

    return issues;
}
