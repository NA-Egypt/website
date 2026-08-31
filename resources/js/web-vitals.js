/**
 * Web Vitals Real User Monitoring (RUM) & Performance Observer
 * Zero-dependency native PerformanceObserver utility for measuring Core Web Vitals (LCP, CLS, INP, FCP, TTFB).
 */

const THRESHOLDS = {
    LCP: { good: 2500, poor: 4000 },
    CLS: { good: 0.1, poor: 0.25 },
    INP: { good: 200, poor: 500 },
    FCP: { good: 1800, poor: 3000 },
    TTFB: { good: 800, poor: 1800 }
};

function getRating(name, value) {
    const thresh = THRESHOLDS[name];
    if (!thresh) return 'unknown';
    if (value <= thresh.good) return 'good';
    if (value <= thresh.poor) return 'needs-improvement';
    return 'poor';
}

const metrics = {
    LCP: null,
    CLS: 0,
    INP: null,
    FCP: null,
    TTFB: null
};

const listeners = new Set();

function emitMetric(name, value, entry = null) {
    const rating = getRating(name, value);
    const metricData = {
        name,
        value: Number(value.toFixed ? value.toFixed(name === 'CLS' ? 3 : 1) : value),
        rating,
        entry
    };
    metrics[name] = metricData.value;

    // Log to console if debug mode is active
    if (typeof window !== 'undefined' && (window.__DEBUG_WEB_VITALS || localStorage.getItem('debug_web_vitals') === 'true')) {
        const badgeColor = rating === 'good' ? '#10b981' : rating === 'needs-improvement' ? '#f59e0b' : '#ef4444';
        console.log(
            `%c[Web Vitals] ${name}: ${metricData.value}${name === 'CLS' ? '' : 'ms'} (${rating})`,
            `color: white; background: ${badgeColor}; padding: 2px 6px; border-radius: 4px; font-weight: bold;`
        );
    }

    listeners.forEach(fn => {
        try {
            fn(metricData);
        } catch (e) {
            console.error('Web Vitals listener error:', e);
        }
    });
}

/**
 * Initialize Web Vitals Performance Observers
 * @param {Function} [onReport] - Optional callback triggered on each metric measurement
 */
export function initWebVitals(onReport) {
    if (typeof window === 'undefined' || typeof PerformanceObserver === 'undefined') {
        return;
    }

    if (typeof onReport === 'function') {
        listeners.add(onReport);
    }

    // 1. TTFB (Time to First Byte)
    try {
        const navEntry = performance.getEntriesByType('navigation')[0];
        if (navEntry) {
            const ttfb = navEntry.responseStart - navEntry.requestStart;
            if (ttfb >= 0) emitMetric('TTFB', ttfb, navEntry);
        }
    } catch (_) {}

    // 2. FCP (First Contentful Paint)
    try {
        const fcpObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.name === 'first-contentful-paint') {
                    emitMetric('FCP', entry.startTime, entry);
                    fcpObserver.disconnect();
                }
            }
        });
        fcpObserver.observe({ type: 'paint', buffered: true });
    } catch (_) {}

    // 3. LCP (Largest Contentful Paint)
    try {
        let lastLcpEntry = null;
        const lcpObserver = new PerformanceObserver((list) => {
            const entries = list.getEntries();
            if (entries.length > 0) {
                lastLcpEntry = entries[entries.length - 1];
            }
        });
        lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });

        const reportLcp = () => {
            if (lastLcpEntry) {
                emitMetric('LCP', lastLcpEntry.startTime, lastLcpEntry);
            }
        };

        // Report LCP on first user interaction or visibility change
        ['keydown', 'click', 'visibilitychange'].forEach(type => {
            addEventListener(type, reportLcp, { once: true, capture: true });
        });
    } catch (_) {}

    // 4. CLS (Cumulative Layout Shift)
    try {
        let clsValue = 0;
        let sessionEntries = [];

        const clsObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                // Only count layout shifts without recent user input
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                    sessionEntries.push(entry);
                }
            }
            emitMetric('CLS', clsValue, sessionEntries);
        });
        clsObserver.observe({ type: 'layout-shift', buffered: true });
    } catch (_) {}

    // 5. INP (Interaction to Next Paint)
    try {
        let maxDuration = 0;
        let worstEntry = null;

        const inpObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                const duration = entry.duration;
                if (duration > maxDuration) {
                    maxDuration = duration;
                    worstEntry = entry;
                }
            }
            if (worstEntry) {
                emitMetric('INP', maxDuration, worstEntry);
            }
        });
        inpObserver.observe({ type: 'event', durationThreshold: 16, buffered: true });
    } catch (_) {}
}

export function getWebVitalsSnapshot() {
    return { ...metrics };
}

// Auto-initialize if running in browser
if (typeof window !== 'undefined') {
    window.initWebVitals = initWebVitals;
    window.getWebVitalsSnapshot = getWebVitalsSnapshot;
}
