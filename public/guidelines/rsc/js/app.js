/**
 * Main Application Logic for NA Egypt RSC Guidelines
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize Search Engine
  const searchEngine = new ArabicSearchEngine(GUIDELINE_SECTIONS);
  let currentSectionId = GUIDELINE_SECTIONS[0].id;
  let activeSearchCategory = 'all';
  let activeMobileCategory = 'all';

  // DOM Elements
  const sidebarNav = document.getElementById('sidebar-nav');
  const contentArea = document.getElementById('guideline-content');
  const searchInput = document.getElementById('search-input');
  const clearSearchBtn = document.getElementById('clear-search-btn');
  const searchResultsPane = document.getElementById('search-results-pane');
  const searchResultsList = document.getElementById('search-results-list');
  const searchCountBadge = document.getElementById('search-count-badge');
  const searchFilterPills = document.querySelectorAll('.sidebar-search-box ~ * .filter-pill, #search-results-pane .filter-pill');
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const themeBtnText = document.getElementById('theme-btn-text');
  const menuToggleBtn = document.getElementById('menu-toggle-btn');
  const sidebar = document.getElementById('sidebar');
  const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
  const sidebarBackdrop = document.getElementById('sidebar-backdrop');
  const breadcrumbCurrent = document.getElementById('breadcrumb-current');
  const fontIncreaseBtn = document.getElementById('font-increase-btn');
  const fontDecreaseBtn = document.getElementById('font-decrease-btn');
  const printDocBtn = document.getElementById('print-doc-btn');
  const copyLinkBtn = document.getElementById('copy-link-btn');

  // Mobile Search Elements
  const mobileSearchBtn = document.getElementById('mobile-search-btn');
  const mobileSearchModal = document.getElementById('mobile-search-modal');
  const mobileSearchInput = document.getElementById('mobile-search-input');
  const mobileClearSearchBtn = document.getElementById('mobile-clear-search-btn');
  const closeSearchModalBtn = document.getElementById('close-search-modal-btn');
  const mobileSearchFilterPills = document.querySelectorAll('.mobile-search-filter-pills .filter-pill');
  const mobileSearchStats = document.getElementById('mobile-search-stats');
  const mobileSearchResultsList = document.getElementById('mobile-search-results-list');

  // Bottom Navigation Elements
  const bottomMenuBtn = document.getElementById('bottom-menu-btn');
  const bottomSearchBtn = document.getElementById('bottom-search-btn');
  const bottomFontIncrease = document.getElementById('bottom-font-increase');
  const bottomFontDecrease = document.getElementById('bottom-font-decrease');
  const bottomShareBtn = document.getElementById('bottom-share-btn');
  const scrollToTopBtn = document.getElementById('scroll-to-top-btn');

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
          closeSidebar();
        });

        sidebarNav.appendChild(item);
      });
    });
  }

  // Sidebar Controls
  function openSidebar() {
    sidebar.classList.add('open');
    if (sidebarBackdrop) {
      sidebarBackdrop.classList.add('active');
    }
    if (window.innerWidth <= 900) {
      document.body.style.overflow = 'hidden';
    }
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    if (sidebarBackdrop) {
      sidebarBackdrop.classList.remove('active');
    }
    document.body.style.overflow = '';
  }

  if (menuToggleBtn) {
    menuToggleBtn.addEventListener('click', () => {
      if (sidebar.classList.contains('open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });
  }

  if (sidebarCloseBtn) {
    sidebarCloseBtn.addEventListener('click', closeSidebar);
  }

  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', closeSidebar);
  }

  if (bottomMenuBtn) {
    bottomMenuBtn.addEventListener('click', openSidebar);
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
    if (breadcrumbCurrent) {
      breadcrumbCurrent.textContent = sec.title;
    }

    // Build content with highlights if searched
    let html = sec.content;
    if (highlightQuery) {
      html = ArabicSearchEngine.highlight(html, highlightQuery);
    }

    // Render Tag Badges
    const tagsHtml = (sec.tags || []).map(t => `<span class="badge-tag">${t}</span>`).join(' ');

    // Find Previous & Next Sections for easy navigation
    const currentIndex = GUIDELINE_SECTIONS.findIndex(s => s.id === id);
    const prevSec = currentIndex > 0 ? GUIDELINE_SECTIONS[currentIndex - 1] : null;
    const nextSec = currentIndex < GUIDELINE_SECTIONS.length - 1 ? GUIDELINE_SECTIONS[currentIndex + 1] : null;

    let footerNavHtml = `
      <div class="section-nav-footer">
        ${prevSec ? `<button class="header-btn" onclick="window.appNavigate('${prevSec.id}')">← السابق: ${prevSec.title}</button>` : '<div></div>'}
        ${nextSec ? `<button class="header-btn" onclick="window.appNavigate('${nextSec.id}')">التالي: ${nextSec.title} →</button>` : '<div></div>'}
      </div>
    `;

    // Final Assembly
    contentArea.innerHTML = `
      <article class="guideline-article">
        <div class="article-meta mb-3">
          <span class="badge-tag">القسم: ${sec.category}</span>
          <span class="badge-tag">الصفحة المطبوعة: ${sec.page}</span>
          ${tagsHtml}
        </div>
        ${html}
        ${footerNavHtml}
      </article>
    `;

    // Render interactive data tables if container exists in HTML
    renderGlossaryTable();
    renderRobertsRulesTable();

    // Scroll smoothly to top of content
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Expose global navigator for footer buttons
  window.appNavigate = (id) => {
    navigateToSection(id);
    closeSidebar();
  };

  // 3. Render Injected Tables
  function renderGlossaryTable() {
    const container = document.getElementById('glossary-table-container');
    if (!container || typeof GLOSSARY_DATA === 'undefined') return;

    let rows = GLOSSARY_DATA.map(item => `
      <tr>
        <td style="font-weight: bold; text-align: center;">${item.sn}</td>
        <td><span class="badge-code">${item.acronym}</span></td>
        <td style="direction: ltr; text-align: left; font-family: monospace;">${item.en}</td>
        <td style="font-weight: 600;">${item.ar}</td>
      </tr>
    `).join('');

    container.innerHTML = `
      <div class="responsive-table-wrapper">
        <div class="table-scroll-hint">↔ اسحب الجدول أفقياً للاطلاع على باقي الأعمدة</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="width: 50px; text-align: center;">م</th>
              <th>المصطلح / الاختصار</th>
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
        <div class="table-scroll-hint">↔ اسحب الجدول أفقياً للاطلاع على باقي التفاصيل والقواعد</div>
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

  // 4. Live Search Handler (Desktop)
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

  if (searchInput) {
    searchInput.addEventListener('input', handleSearch);
  }

  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', () => {
      searchInput.value = '';
      handleSearch();
      searchInput.focus();
    });
  }

  // Search Filter Pills (Desktop)
  searchFilterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      searchFilterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      activeSearchCategory = pill.dataset.category;
      handleSearch();
    });
  });

  // 5. Mobile Search Modal Handler
  function openMobileSearch() {
    if (!mobileSearchModal) return;
    mobileSearchModal.classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
      if (mobileSearchInput) {
        mobileSearchInput.focus();
      }
    }, 100);
    handleMobileSearch();
  }

  function closeMobileSearch() {
    if (!mobileSearchModal) return;
    mobileSearchModal.classList.remove('active');
    document.body.style.overflow = '';
  }

  function handleMobileSearch() {
    if (!mobileSearchInput || !mobileSearchResultsList) return;
    const query = mobileSearchInput.value.trim();

    if (!query) {
      if (mobileClearSearchBtn) mobileClearSearchBtn.style.display = 'none';
      if (mobileSearchStats) mobileSearchStats.textContent = 'أدخل كلمة للبحث في الأقسام، السياسات، وقواعد روبرت...';
      mobileSearchResultsList.innerHTML = `
        <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
          <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">🔍 ابحث بالكلمات أو العناوين</p>
          <p style="font-size: 0.85rem;">مثال: "الرئيس"، "قواعد روبرت"، "النصاب"، "المفاهيم"</p>
        </div>
      `;
      return;
    }

    if (mobileClearSearchBtn) mobileClearSearchBtn.style.display = 'block';
    const results = searchEngine.search(query, activeMobileCategory);

    if (mobileSearchStats) {
      mobileSearchStats.textContent = `تم العثور على ${results.length} نتيجة مطابقة`;
    }
    mobileSearchResultsList.innerHTML = '';

    if (results.length === 0) {
      mobileSearchResultsList.innerHTML = `
        <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
          <p style="font-size: 1rem;">لا توجد نتائج مطابقة لـ "<strong>${query}</strong>"</p>
          <p style="font-size: 0.8rem; margin-top: 0.35rem;">جرب البحث بكلمات أخرى أو تغيير التصنيف أعلاه.</p>
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
          closeMobileSearch();
        });

        mobileSearchResultsList.appendChild(card);
      });
    }
  }

  if (mobileSearchBtn) {
    mobileSearchBtn.addEventListener('click', openMobileSearch);
  }

  if (bottomSearchBtn) {
    bottomSearchBtn.addEventListener('click', openMobileSearch);
  }

  if (closeSearchModalBtn) {
    closeSearchModalBtn.addEventListener('click', closeMobileSearch);
  }

  if (mobileSearchInput) {
    mobileSearchInput.addEventListener('input', handleMobileSearch);
  }

  if (mobileClearSearchBtn) {
    mobileClearSearchBtn.addEventListener('click', () => {
      mobileSearchInput.value = '';
      handleMobileSearch();
      mobileSearchInput.focus();
    });
  }

  mobileSearchFilterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      mobileSearchFilterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      activeMobileCategory = pill.dataset.category;
      handleMobileSearch();
    });
  });

  // 6. Theme Toggle
  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('na_theme', newTheme);
      updateThemeIcon(newTheme);
    });
  }

  function updateThemeIcon(theme) {
    const text = theme === 'dark' ? '☀️ الوضع الفاتح' : '🌙 الوضع الداكن';
    if (themeBtnText) {
      themeBtnText.textContent = text;
    } else if (themeToggleBtn) {
      themeToggleBtn.innerHTML = text;
    }
  }

  // 7. Font Sizing Controls (Desktop + Mobile Bottom Bar)
  function adjustFontSize(delta) {
    const newSize = currentFontSize + delta;
    if (newSize >= 13 && newSize <= 25) {
      currentFontSize = newSize;
      document.body.style.fontSize = currentFontSize + 'px';
    }
  }

  if (fontIncreaseBtn) {
    fontIncreaseBtn.addEventListener('click', () => adjustFontSize(1));
  }
  if (fontDecreaseBtn) {
    fontDecreaseBtn.addEventListener('click', () => adjustFontSize(-1));
  }
  if (bottomFontIncrease) {
    bottomFontIncrease.addEventListener('click', () => adjustFontSize(1));
  }
  if (bottomFontDecrease) {
    bottomFontDecrease.addEventListener('click', () => adjustFontSize(-1));
  }

  // 8. Print & Copy Link
  if (printDocBtn) {
    printDocBtn.addEventListener('click', () => {
      window.print();
    });
  }

  function handleCopyShare(btn) {
    navigator.clipboard.writeText(window.location.href).then(() => {
      const originalHtml = btn.innerHTML;
      btn.innerHTML = '✓ تم النسخ';
      setTimeout(() => {
        btn.innerHTML = originalHtml;
      }, 2000);
    });
  }

  if (copyLinkBtn) {
    copyLinkBtn.addEventListener('click', () => handleCopyShare(copyLinkBtn));
  }

  if (bottomShareBtn) {
    bottomShareBtn.addEventListener('click', () => handleCopyShare(bottomShareBtn));
  }

  // 9. Scroll to Top Button
  if (scrollToTopBtn) {
    scrollToTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Initial Load from URL Hash or default
  renderSidebar();
  const initialHash = window.location.hash.replace('#', '');
  if (initialHash && GUIDELINE_SECTIONS.find(s => s.id === initialHash)) {
    navigateToSection(initialHash);
  } else {
    navigateToSection(GUIDELINE_SECTIONS[0].id);
  }
});
