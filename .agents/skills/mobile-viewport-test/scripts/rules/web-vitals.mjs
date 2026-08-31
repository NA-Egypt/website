/**
 * Rule: Core Web Vitals Static Layout & Performance Validator (CLS, LCP, INP)
 * Detects layout shift culprits, font loading hazards, and unoptimized paint triggers.
 */
export function checkWebVitals(filePath, content, lines) {
    const issues = [];

    // 1. Font Display Swap Check (CLS prevention)
    if (filePath.endsWith('.blade.php') || filePath.endsWith('.vue') || filePath.endsWith('.html')) {
        const fontLinkRegex = /<link[^>]+href=["'](https?:\/\/fonts\.(?:bunny\.net|googleapis\.com)[^"']+)["']/gi;
        let match;
        while ((match = fontLinkRegex.exec(content)) !== null) {
            const fontUrl = match[1];
            if (!fontUrl.includes('display=swap')) {
                const lineNum = content.substring(0, match.index).split('\n').length;
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'CLS_FONT_DISPLAY_MISSING',
                    severity: 'WARNING',
                    message: 'Web font stylesheet link is missing `display=swap`, which causes layout shifts (FOIT/FOUT) during font load.',
                    suggestion: 'Append `&display=swap` to the font URL (e.g. `href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"`).'
                });
            }
        }
    }

    // 2. Line-by-line checks for CLS, LCP, and INP
    lines.forEach((line, idx) => {
        const lineNum = idx + 1;
        const trimmed = line.trim();
        if (trimmed.startsWith('//') || trimmed.startsWith('/*') || trimmed.startsWith('<!--')) return;

        // Image Sizing & Layout Shift (CLS)
        const imgTagMatch = line.match(/<img\s+[^>]*>/i);
        if (imgTagMatch) {
            const imgTag = imgTagMatch[0];
            const hasWidthHeight = /\bwidth=["']\d+["']/i.test(imgTag) && /\bheight=["']\d+["']/i.test(imgTag);
            const hasAspectClass = /class=["'][^"']*(?:aspect-(?:video|square|\[\d+\/\d+\])|h-\d+\s+w-\d+|w-\d+\s+h-\d+)[^"']*["']/i.test(imgTag);
            
            // Exclude icons or dynamic template bindings if specified
            const isDynamic = /:src|{{/i.test(imgTag);

            if (!hasWidthHeight && !hasAspectClass && !isDynamic) {
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'CLS_UNSIZED_IMAGE',
                    severity: 'WARNING',
                    message: 'Image element `<img>` lacks explicit width/height attributes or an aspect-ratio bounding box, risking Cumulative Layout Shift (CLS).',
                    suggestion: 'Add `width="..." height="..."` attributes or Tailwind aspect class (e.g. `aspect-video` or `w-full h-auto`) to reserve layout space.'
                });
            }

            // Hero banner image lazy loading anti-pattern (LCP)
            if (/id=["'](?:hero|background|banner)["']|class=["'][^"']*(?:hero|banner)[^"']*["']/i.test(imgTag)) {
                if (/loading=["']lazy["']/i.test(imgTag)) {
                    issues.push({
                        file: filePath,
                        line: lineNum,
                        rule: 'LCP_LAZY_ON_HERO',
                        severity: 'CRITICAL',
                        message: 'Hero/banner image is set to `loading="lazy"`, which directly degrades Largest Contentful Paint (LCP).',
                        suggestion: 'Change to `loading="eager"` and add `fetchpriority="high"` on hero images.'
                    });
                }
            }
        }

        // Video elements missing aspect ratio (CLS)
        const videoTagMatch = line.match(/<video\s+[^>]*>/i);
        if (videoTagMatch) {
            const videoTag = videoTagMatch[0];
            const hasVideoDimensions = /\bwidth=["']\d+["']/i.test(videoTag) && /\bheight=["']\d+["']/i.test(videoTag);
            const hasVideoAspect = /class=["'][^"']*aspect-[^"']*["']/i.test(videoTag);
            if (!hasVideoDimensions && !hasVideoAspect) {
                issues.push({
                    file: filePath,
                    line: lineNum,
                    rule: 'CLS_UNSIZED_VIDEO',
                    severity: 'WARNING',
                    message: '`<video>` element lacks defined dimensions or aspect-ratio class, causing layout shifts when metadata loads.',
                    suggestion: 'Add `width="..." height="..."` or `aspect-video` container class.'
                });
            }
        }
    });

    return issues;
}
