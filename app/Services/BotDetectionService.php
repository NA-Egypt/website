<?php

namespace App\Services;

use Illuminate\Http\Request;

class BotDetectionService
{
    /**
     * Comprehensive list of search engine spiders, AI/LLM crawlers, and automated scrapers.
     *
     * @var array<int, string>
     */
    public const BOT_AND_AI_SIGNATURES = [
        // Search Engine Crawlers
        'googlebot',
        'bingbot',
        'slurp',
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'sogou',
        'exabot',
        'facebot',
        'ia_archiver',
        'applebot',
        'petalbot',
        'seznambot',
        'qwantify',
        'ecosia',
        'mj12bot',
        'ahrefsbot',
        'semrushbot',
        'dotbot',
        'rogerbot',

        // AI & LLM Training / Search Agents
        'gptbot',
        'chatgpt-user',
        'oai-searchbot',
        'openai',
        'claudebot',
        'claude-web',
        'anthropic-ai',
        'anthropic',
        'google-extended',
        'googleother',
        'perplexitybot',
        'perplexity-user',
        'perplexity',
        'ccbot',
        'bytespider',
        'bytedance',
        'meta-externalagent',
        'meta-externalfetcher',
        'facebookexternalhit',
        'facebookbot',
        'amazonbot',
        'applebot-extended',
        'cohere-ai',
        'youbot',
        'diffbot',
        'omgilibot',
        'omgili',
        'imagesiftbot',
        'timpibot',
        'velenpublicwebcrawler',
        'webzio-extended',
        'ai2bot',
        'friendlycrawler',
        'icc-crawler',
        'mistral',
        'deepseek',

        // Generic Crawlers, Headless Browsers & Automated Scrapers
        'crawler',
        'spider',
        'bot',
        'scraper',
        'headlesschrome',
        'phantomjs',
        'playwright',
        'puppeteer',
        'selenium',
        'python-requests',
        'python-urllib',
        'aiohttp',
        'httpx',
        'curl/',
        'wget/',
        'libwww-perl',
        'go-http-client',
        'okhttp',
        'node-fetch',
        'undici',
    ];

    /**
     * Determine whether the incoming request originates from a search engine, AI agent, or automated scraper.
     */
    public static function isBotOrAi(?Request $request = null): bool
    {
        $request = $request ?? request();
        $userAgent = (string) $request->header('User-Agent', '');

        if ($userAgent === '') {
            return false;
        }

        foreach (self::BOT_AND_AI_SIGNATURES as $signature) {
            if (stripos($userAgent, $signature) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Standard HTTP headers to strictly forbid search engine indexing, caching, and AI ingestion.
     *
     * @return array<string, string>
     */
    public static function noIndexAiHeaders(): array
    {
        return [
            'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet, noai, noimageai',
        ];
    }
}

