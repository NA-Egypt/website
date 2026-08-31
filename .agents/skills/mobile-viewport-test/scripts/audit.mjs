#!/usr/bin/env node

/**
 * Mobile Viewport & Core Web Vitals Browserless Audit Engine
 * Evaluates Blade views, Vue components, and CSS stylesheets for mobile responsiveness and Web Vitals hazards.
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

import { checkMetaViewport } from './rules/meta-viewport.mjs';
import { checkFixedWidths } from './rules/fixed-widths.mjs';
import { checkOverflowHazards } from './rules/overflow-hazards.mjs';
import { checkResponsiveGrids } from './rules/responsive-grids.mjs';
import { checkTouchTargets } from './rules/touch-targets.mjs';
import { checkWebVitals } from './rules/web-vitals.mjs';
import { checkCompiledCSS } from './rules/compiled-css.mjs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.resolve(__dirname, '../../../../');

// Parse CLI flags
const args = process.argv.slice(2);
const isStrict = args.includes('--strict');
const isJsonOutput = args.includes('--format=json');
const targetArg = args.find(a => a.startsWith('--target='));
const targetPath = targetArg ? targetArg.split('=')[1] : null;

// ANSI Colors for terminal output
const colors = {
    reset: '\x1b[0m',
    bold: '\x1b[1m',
    dim: '\x1b[2m',
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
    magenta: '\x1b[35m',
    cyan: '\x1b[36m',
    gray: '\x1b[90m',
};

// Discover files to scan
function getTargetFiles(dir, fileList = []) {
    if (!fs.existsSync(dir)) return fileList;
    const entries = fs.readdirSync(dir, { withFileTypes: true });

    for (const entry of entries) {
        const fullPath = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            if (['node_modules', 'vendor', '.git', 'storage', 'db-backup'].includes(entry.name)) {
                continue;
            }
            getTargetFiles(fullPath, fileList);
        } else if (entry.isFile()) {
            if (
                entry.name.endsWith('.blade.php') ||
                entry.name.endsWith('.vue') ||
                (entry.name.endsWith('.css') && !entry.name.includes('.min.'))
            ) {
                fileList.push(fullPath);
            }
        }
    }
    return fileList;
}

// Collect target files
let filesToScan = [];
if (targetPath) {
    const resolvedTarget = path.resolve(projectRoot, targetPath);
    if (fs.existsSync(resolvedTarget)) {
        if (fs.statSync(resolvedTarget).isDirectory()) {
            filesToScan = getTargetFiles(resolvedTarget);
        } else {
            filesToScan = [resolvedTarget];
        }
    } else {
        console.error(`${colors.red}Target path does not exist: ${targetPath}${colors.reset}`);
        process.exit(1);
    }
} else {
    const scanDirs = [
        path.join(projectRoot, 'resources/views'),
        path.join(projectRoot, 'resources/js'),
        path.join(projectRoot, 'resources/css'),
        path.join(projectRoot, 'public/build/assets')
    ];
    scanDirs.forEach(dir => getTargetFiles(dir, filesToScan));
}

// Execute rules across all files
const allIssues = [];
let scannedCount = 0;

for (const filePath of filesToScan) {
    try {
        const content = fs.readFileSync(filePath, 'utf8');
        const lines = content.split('\n');
        const relativePath = path.relative(projectRoot, filePath);

        scannedCount++;

        const fileIssues = [
            ...checkMetaViewport(relativePath, content, lines),
            ...checkFixedWidths(relativePath, content, lines),
            ...checkOverflowHazards(relativePath, content, lines),
            ...checkResponsiveGrids(relativePath, content, lines),
            ...checkTouchTargets(relativePath, content, lines),
            ...checkWebVitals(relativePath, content, lines),
            ...checkCompiledCSS(relativePath, content, lines)
        ];

        allIssues.push(...fileIssues);
    } catch (err) {
        // Skip unreadable binary/corrupted files gracefully
    }
}

// Categorize issues
const criticalIssues = allIssues.filter(i => i.severity === 'CRITICAL');
const warningIssues = allIssues.filter(i => i.severity === 'WARNING');
const tipIssues = allIssues.filter(i => i.severity === 'TIP');

// Save machine-readable JSON report
const reportDir = path.join(projectRoot, '.agents/reports');
if (!fs.existsSync(reportDir)) {
    fs.mkdirSync(reportDir, { recursive: true });
}
const reportPath = path.join(reportDir, 'mobile-viewport-report.json');
const reportData = {
    timestamp: new Date().toISOString(),
    scannedFiles: scannedCount,
    totalIssues: allIssues.length,
    summary: {
        critical: criticalIssues.length,
        warning: warningIssues.length,
        tip: tipIssues.length
    },
    issues: allIssues
};
fs.writeFileSync(reportPath, JSON.stringify(reportData, null, 2), 'utf8');

// Render output
if (isJsonOutput) {
    console.log(JSON.stringify(reportData, null, 2));
} else {
    console.log(`\n${colors.cyan}${colors.bold}==============================================================${colors.reset}`);
    console.log(`${colors.cyan}${colors.bold}   📱 MOBILE VIEWPORT & CORE WEB VITALS AUDIT REPORT${colors.reset}`);
    console.log(`${colors.cyan}${colors.bold}==============================================================${colors.reset}\n`);

    console.log(`${colors.bold}Files Scanned:${colors.reset} ${scannedCount}`);
    console.log(`${colors.bold}Summary:${colors.reset} ` +
        `${colors.red}${criticalIssues.length} Critical${colors.reset} | ` +
        `${colors.yellow}${warningIssues.length} Warnings${colors.reset} | ` +
        `${colors.blue}${tipIssues.length} Tips${colors.reset}\n`);

    if (allIssues.length === 0) {
        console.log(`${colors.green}${colors.bold}✨ Excellent! All mobile viewport and Web Vitals checks passed!${colors.reset}\n`);
    } else {
        console.log(`${colors.bold}Findings by File:${colors.reset}\n`);

        // Group by file
        const grouped = {};
        for (const issue of allIssues) {
            if (!grouped[issue.file]) grouped[issue.file] = [];
            grouped[issue.file].push(issue);
        }

        for (const [file, issues] of Object.entries(grouped)) {
            console.log(`${colors.bold}${colors.cyan}📄 ${file}${colors.reset}`);
            issues.forEach(iss => {
                const badge = iss.severity === 'CRITICAL'
                    ? `${colors.red}[CRITICAL]${colors.reset}`
                    : iss.severity === 'WARNING'
                    ? `${colors.yellow}[WARNING]${colors.reset}`
                    : `${colors.blue}[TIP]${colors.reset}`;

                console.log(`  ${badge} ${colors.gray}Line ${iss.line}:${colors.reset} ${colors.bold}${iss.rule}${colors.reset}`);
                console.log(`     ${iss.message}`);
                console.log(`     ${colors.green}↳ Fix: ${iss.suggestion}${colors.reset}`);
            });
            console.log('');
        }

        console.log(`${colors.dim}Report saved to: ${path.relative(projectRoot, reportPath)}${colors.reset}\n`);
    }
}

// Exit code handling
if (isStrict && criticalIssues.length > 0) {
    console.error(`${colors.red}${colors.bold}❌ Audit failed in --strict mode with ${criticalIssues.length} critical issue(s).${colors.reset}\n`);
    process.exit(1);
}

process.exit(0);
