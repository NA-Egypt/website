/**
 * Smart Arabic Client-Side AI/Search Engine for NA Egypt Guidelines
 * Features:
 * - Arabic text normalization (Alef variants, Taa Marbuta/Haa, Yaa/Alef Maqsura, diacritics/tashkeel removal, tatweel removal)
 * - Multi-keyword fuzzy and prefix matching
 * - Content scoring based on title matches, tags, category, and verbatim body relevance
 * - Dynamic keyword snippet extraction and highlighting
 */

class ArabicSearchEngine {
  constructor(sections) {
    this.sections = sections || [];
    this.index = [];
    this.buildIndex();
  }

  // Normalize Arabic string for accurate search
  static normalizeArabic(text) {
    if (!text) return '';
    return text
      .toString()
      .toLowerCase()
      // Remove diacritics (tashkeel)
      .replace(/[\u064B-\u065F\u0670]/g, '')
      // Remove tatweel (kashida)
      .replace(/\u0640/g, '')
      // Normalize Alef variants (أ, إ, آ -> ا)
      .replace(/[إأآٱ]/g, 'ا')
      // Normalize Yaa variants (ى -> ي, ئ -> ي)
      .replace(/[ىئ]/g, 'ي')
      // Normalize Taa Marbuta (ة -> ه)
      .replace(/ة/g, 'ه')
      // Normalize Persian/Urdu kaf/gaf
      .replace(/ك/g, 'ك')
      // Remove punctuation and extra spaces
      .replace(/[^\w\s\u0600-\u06FF]/gi, ' ')
      .replace(/\s+/g, ' ')
      .trim();
  }

  // Strip HTML tags for clean text indexing
  static stripHtml(html) {
    if (!html) return '';
    return html.replace(/<[^>]*>?/gm, ' ');
  }

  // Build searchable index
  buildIndex() {
    this.index = this.sections.map(sec => {
      const cleanContent = ArabicSearchEngine.stripHtml(sec.content);
      const normalizedTitle = ArabicSearchEngine.normalizeArabic(sec.title);
      const normalizedCategory = ArabicSearchEngine.normalizeArabic(sec.category);
      const normalizedTags = (sec.tags || []).map(t => ArabicSearchEngine.normalizeArabic(t)).join(' ');
      const normalizedContent = ArabicSearchEngine.normalizeArabic(cleanContent);

      return {
        id: sec.id,
        title: sec.title,
        page: sec.page,
        category: sec.category,
        tags: sec.tags || [],
        rawContent: cleanContent,
        normalizedTitle,
        normalizedCategory,
        normalizedTags,
        normalizedContent,
        fullNormalizedSearchBlob: `${normalizedTitle} ${normalizedCategory} ${normalizedTags} ${normalizedContent}`
      };
    });
  }

  // Perform search
  search(query, filterCategory = 'all') {
    if (!query || !query.trim()) {
      return filterCategory === 'all' 
        ? this.sections 
        : this.sections.filter(s => s.category === filterCategory);
    }

    const normalizedQuery = ArabicSearchEngine.normalizeArabic(query);
    const queryTokens = normalizedQuery.split(/\s+/).filter(t => t.length > 0);

    if (queryTokens.length === 0) return [];

    const results = [];

    this.index.forEach(item => {
      if (filterCategory !== 'all' && item.category !== filterCategory) {
        return;
      }

      let score = 0;
      let matchedTokens = 0;

      queryTokens.forEach(token => {
        let tokenScore = 0;
        
        // Exact match in title (High weight)
        if (item.normalizedTitle.includes(token)) {
          tokenScore += 150;
        }

        // Match in tags
        if (item.normalizedTags.includes(token)) {
          tokenScore += 80;
        }

        // Match in category
        if (item.normalizedCategory.includes(token)) {
          tokenScore += 40;
        }

        // Match in content
        const occurrences = (item.normalizedContent.match(new RegExp(token, 'g')) || []).length;
        if (occurrences > 0) {
          tokenScore += Math.min(occurrences * 10, 100);
        }

        if (tokenScore > 0) {
          matchedTokens++;
          score += tokenScore;
        }
      });

      // Include if at least one token matches (or prefer items matching more tokens)
      if (matchedTokens > 0) {
        // Boost if all query tokens matched
        if (matchedTokens === queryTokens.length) {
          score += 100;
        }

        // Extract relevant snippet
        const snippet = this.generateSnippet(item.rawContent, queryTokens);

        results.push({
          id: item.id,
          title: item.title,
          page: item.page,
          category: item.category,
          score,
          snippet
        });
      }
    });

    // Sort by score descending
    results.sort((a, b) => b.score - a.score);
    return results;
  }

  // Generate contextual preview snippet around matched tokens
  generateSnippet(rawContent, queryTokens, maxLength = 180) {
    if (!rawContent) return '';
    const normalizedRaw = ArabicSearchEngine.normalizeArabic(rawContent);

    // Find the first occurrence of any token
    let bestIndex = -1;
    for (const token of queryTokens) {
      const idx = normalizedRaw.indexOf(token);
      if (idx !== -1 && (bestIndex === -1 || idx < bestIndex)) {
        bestIndex = idx;
      }
    }

    if (bestIndex === -1) {
      return rawContent.slice(0, maxLength) + (rawContent.length > maxLength ? '...' : '');
    }

    const start = Math.max(0, bestIndex - 40);
    const end = Math.min(rawContent.length, start + maxLength);
    let snippet = rawContent.slice(start, end).trim();

    if (start > 0) snippet = '...' + snippet;
    if (end < rawContent.length) snippet = snippet + '...';

    return snippet;
  }

  // Highlight matches in text
  static highlight(text, query) {
    if (!query || !query.trim() || !text) return text;
    const tokens = ArabicSearchEngine.normalizeArabic(query).split(/\s+/).filter(Boolean);
    if (!tokens.length) return text;

    let highlighted = text;
    // We create a regex pattern for each token that matches Arabic variations
    tokens.forEach(token => {
      const pattern = token
        .split('')
        .map(char => {
          if (/[اأإآٱ]/.test(char)) return '[اأإآٱ]';
          if (/[يىئ]/.test(char)) return '[يىئ]';
          if (/[هة]/.test(char)) return '[هة]';
          return char;
        })
        .join('[\\u064B-\\u065F\\u0670\\u0640]*');

      const regex = new RegExp(`(${pattern})`, 'gi');
      highlighted = highlighted.replace(regex, '<mark class="search-highlight">$1</mark>');
    });

    return highlighted;
  }
}
