/**
 * Rule: Viewport Meta Tag Validator
 * Ensures root HTML / Blade layout files contain a standard, accessible <meta name="viewport" ...>
 */
export function checkMetaViewport(filePath, content, lines) {
    const issues = [];
    
    // Ignore PDF export templates (Dompdf / print views)
    const isPdfTemplate = filePath.includes('/pdf/') || filePath.includes('/exports/') || filePath.includes('pdf.blade.php') || filePath.includes('_pdf.blade.php') || filePath.includes('-pdf.blade.php');
    if (isPdfTemplate) {
        return issues;
    }

    const isRootLayout = filePath.endsWith('.blade.php') && 
        (content.includes('<html') || content.includes('<!DOCTYPE') || filePath.includes('components/layout.blade.php') || filePath.includes('components/frontend/layout.blade.php') || filePath.includes('app.blade.php') || filePath.includes('welcome.blade.php'));

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
