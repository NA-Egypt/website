/**
 * Main Application Logic for NA Egypt RSC Guidelines
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize Search Engine
  const searchEngine = new ArabicSearchEngine(GUIDELINE_SECTIONS);
  let currentSectionId = GUIDELINE_SECTIONS[0].id;
  let activeSearchCategory = 'all';

  // DOM Elements
  const sidebarNav = document.getElementById('sidebar-nav');
  const contentArea = document.getElementById('guideline-content');
  const searchInput = document.getElementById('search-input');
  const clearSearchBtn = document.getElementById('clear-search-btn');
  const searchResultsPane = document.getElementById('search-results-pane');
  const searchResultsList = document.getElementById('search-results-list');
  const searchCountBadge = document.getElementById('search-count-badge');
  const searchFilterPills = document.querySelectorAll('.filter-pill');
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const menuToggleBtn = document.getElementById('menu-toggle-btn');
  const sidebar = document.getElementById('sidebar');
  const breadcrumbCurrent = document.getElementById('breadcrumb-current');
  const fontIncreaseBtn = document.getElementById('font-increase-btn');
  const fontDecreaseBtn = document.getElementById('font-decrease-btn');
  const printDocBtn = document.getElementById('print-doc-btn');
  const copyLinkBtn = document.getElementById('copy-link-btn');

  // Font Size state
  let currentFontSize = 17;

  // Initialize Theme from localStorage
  const savedTheme = localStorage.getItem('na_theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  // 1. Render Sidebar Table of Contents
  function renderSidebar() {
    sidebarNav.innerHTML = '';
    
    // Group sections by category
    const categories = {};
    GUIDELINE_SECTIONS.forEach(sec => {
      if (!categories[sec.category]) {
        categories[sec.category] = [];
      }
      categories[sec.category].push(sec);
    });

    Object.keys(categories).forEach(catName => {
      const catTitle = document.createElement('div');
      catTitle.className = 'nav-category-title';
      catTitle.textContent = catName;
      sidebarNav.appendChild(catTitle);

      categories[catName].forEach(sec => {
        const item = document.createElement('a');
        item.className = `nav-item ${sec.id === currentSectionId ? 'active' : ''}`;
        item.dataset.id = sec.id;
        item.innerHTML = `
          <span>${sec.title}</span>
          <span class="nav-page-badge">ص ${sec.page}</span>
        `;

        item.addEventListener('click', (e) => {
          e.preventDefault();
          navigateToSection(sec.id);
          if (window.innerWidth <= 900) {
            sidebar.classList.remove('open');
          }
        });

        sidebarNav.appendChild(item);
      });
    });
  }

  // 2. Render Main Section Content
  function navigateToSection(id, highlightQuery = '') {
    const sec = GUIDELINE_SECTIONS.find(s => s.id === id);
    if (!sec) return;

    currentSectionId = id;
    window.location.hash = id;

    // Update active state in sidebar
    document.querySelectorAll('.nav-item').forEach(item => {
      if (item.dataset.id === id) {
        item.classList.add('active');
        item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } else {
        item.classList.remove('active');
      }
    });

    // Update breadcrumbs
    breadcrumbCurrent.textContent = sec.title;

    // Build content with highlights if searched
    let html = sec.content;
    if (highlightQuery) {
      html = ArabicSearchEngine.highlight(html, highlightQuery);
    }

    contentArea.innerHTML = `
      <article class="guideline-article" id="article-${sec.id}">
        ${html}
        <div class="section-nav-footer">
          ${getPreviousSectionBtn(sec)}
          ${getNextSectionBtn(sec)}
        </div>
      </article>
    `;

    // Special Table Renderers
    if (sec.id === 'glossary-acronyms') {
      renderGlossaryTable();
    } else if (sec.id === 'roberts-rules') {
      renderRobertsRulesTable();
    }

    // Scroll to top of content
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Hide search result pane on full page view
    if (!highlightQuery) {
      searchResultsPane.style.display = 'none';
    }
  }

  function getPreviousSectionBtn(sec) {
    const idx = GUIDELINE_SECTIONS.findIndex(s => s.id === sec.id);
    if (idx > 0) {
      const prev = GUIDELINE_SECTIONS[idx - 1];
      return `<button class="header-btn" onclick="document.querySelector('[data-id=\\'${prev.id}\\']').click()">→ السابق: ${prev.title}</button>`;
    }
    return '<div></div>';
  }

  function getNextSectionBtn(sec) {
    const idx = GUIDELINE_SECTIONS.findIndex(s => s.id === sec.id);
    if (idx < GUIDELINE_SECTIONS.length - 1) {
      const next = GUIDELINE_SECTIONS[idx + 1];
      return `<button class="header-btn" onclick="document.querySelector('[data-id=\\'${next.id}\\']').click()">التالي: ${next.title} ←</button>`;
    }
    return '<div></div>';
  }

  // 3. Render Special Dynamic Tables
  function renderGlossaryTable() {
    const container = document.getElementById('glossary-table-container');
    if (!container || typeof GLOSSARY_DATA === 'undefined') return;

    let rows = GLOSSARY_DATA.map(item => `
      <tr>
        <td style="font-weight: bold; width: 60px; text-align: center;">${item.sn}</td>
        <td style="font-weight: bold; color: var(--primary-color);">${item.acronym}</td>
        <td style="direction: ltr; text-align: left; font-family: monospace;">${item.en}</td>
        <td>${item.ar}</td>
      </tr>
    `).join('');

    container.innerHTML = `
      <div class="responsive-table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 60px; text-align: center;">م</th>
              <th>الاختصار</th>
              <th style="text-align: left;">الكلمة بالإنجليزية</th>
              <th>الكلمة بالعربية</th>
            </tr>
          </thead>
          <tbody>
            ${rows}
          </tbody>
        </table>
      </div>
    `;
  }

  function renderRobertsRulesTable() {
    const container = document.getElementById('roberts-rules-table-container');
    if (!container || typeof ROBERTS_RULES_DATA === 'undefined') return;

    let rows = ROBERTS_RULES_DATA.map(item => `
      <tr>
        <td style="font-weight: bold; text-align: center;">${item.m}</td>
        <td style="font-weight: bold; color: var(--primary-color);">${item.type}</td>
        <td>${item.purpose}</td>
        <td style="text-align: center;">${item.interrupt}</td>
        <td style="text-align: center;">${item.second}</td>
        <td style="text-align: center;">${item.debatable}</td>
        <td style="text-align: center; font-weight: 600;">${item.majority}</td>
      </tr>
    `).join('');

    container.innerHTML = `
      <div class="responsive-table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th style="text-align: center;">م</th>
              <th>نوع الاقتراح</th>
              <th>الغرض منه</th>
              <th style="text-align: center;">هل يجوز المقاطعة؟</th>
              <th style="text-align: center;">هل يحتاج إلى تزكية؟</th>
              <th style="text-align: center;">قابل للمناقشة؟</th>
              <th style="text-align: center;">الأغلبية المطلوبة</th>
            </tr>
          </thead>
          <tbody>
            ${rows}
          </tbody>
        </table>
      </div>
    `;
  }

  // 4. Live Search Handler
  function handleSearch() {
    const query = searchInput.value.trim();
    if (!query) {
      clearSearchBtn.style.display = 'none';
      searchResultsPane.style.display = 'none';
      navigateToSection(currentSectionId);
      return;
    }

    clearSearchBtn.style.display = 'block';
    const results = searchEngine.search(query, activeSearchCategory);

    searchCountBadge.textContent = `${results.length} نتيجة`;
    searchResultsList.innerHTML = '';

    if (results.length === 0) {
      searchResultsList.innerHTML = `
        <div class="text-center p-4 text-muted">
          <p>لم يتم العثور على أي نتائج مطابقة لكلمة "<strong>${query}</strong>"</p>
        </div>
      `;
    } else {
      results.forEach(res => {
        const card = document.createElement('div');
        card.className = 'search-result-card';
        const highlightedTitle = ArabicSearchEngine.highlight(res.title, query);
        const highlightedSnippet = ArabicSearchEngine.highlight(res.snippet, query);

        card.innerHTML = `
          <div class="search-result-title">
            <span>${highlightedTitle}</span>
            <span class="nav-page-badge">ص ${res.page}</span>
          </div>
          <div class="search-result-snippet">${highlightedSnippet}</div>
        `;

        card.addEventListener('click', () => {
          navigateToSection(res.id, query);
          searchResultsPane.style.display = 'none';
        });

        searchResultsList.appendChild(card);
      });
    }

    searchResultsPane.style.display = 'block';
  }

  searchInput.addEventListener('input', handleSearch);

  clearSearchBtn.addEventListener('click', () => {
    searchInput.value = '';
    handleSearch();
    searchInput.focus();
  });

  // Search Filter Pills
  searchFilterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      searchFilterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      activeSearchCategory = pill.dataset.category;
      handleSearch();
    });
  });

  // 5. Theme Toggle
  themeToggleBtn.addEventListener('click', () => {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('na_theme', newTheme);
    updateThemeIcon(newTheme);
  });

  function updateThemeIcon(theme) {
    themeToggleBtn.innerHTML = theme === 'dark' ? '☀️ الوضع الفاتح' : '🌙 الوضع الداكن';
  }

  // 6. Mobile Menu Toggle
  menuToggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('open');
  });

  // 7. Font Sizing
  fontIncreaseBtn.addEventListener('click', () => {
    if (currentFontSize < 24) {
      currentFontSize += 1;
      document.body.style.fontSize = currentFontSize + 'px';
    }
  });

  fontDecreaseBtn.addEventListener('click', () => {
    if (currentFontSize > 14) {
      currentFontSize -= 1;
      document.body.style.fontSize = currentFontSize + 'px';
    }
  });

  // 8. Print & Copy Link
  printDocBtn.addEventListener('click', () => {
    window.print();
  });

  copyLinkBtn.addEventListener('click', () => {
    navigator.clipboard.writeText(window.location.href).then(() => {
      const originalText = copyLinkBtn.innerHTML;
      copyLinkBtn.innerHTML = '✓ تم النسخ';
      setTimeout(() => {
        copyLinkBtn.innerHTML = originalText;
      }, 2000);
    });
  });

  // Initial Load from URL Hash or default
  renderSidebar();
  const initialHash = window.location.hash.replace('#', '');
  if (initialHash && GUIDELINE_SECTIONS.find(s => s.id === initialHash)) {
    navigateToSection(initialHash);
  } else {
    navigateToSection(GUIDELINE_SECTIONS[0].id);
  }
});
