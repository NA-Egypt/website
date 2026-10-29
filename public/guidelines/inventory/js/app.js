/**
 * Main Application Logic for NA-Egypt Inventory & Literature System Documentation
 * Features:
 * - Dynamic Sidebar TOC grouped by Category
 * - Instant Arabic Search with Category Filter Pills (Desktop & Mobile)
 * - Injected Reference Tables (Glossary, Keyboard Shortcuts, Roles Matrix)
 * - Dual Print Modes:
 *   1. Print Current Section (🖨️ طباعة القسم)
 *   2. Print Full Manual Book (📚 طباعة الدليل الكامل) with Cover Page, TOC, and Chapter Page Breaks
 * - Dark/Light Theme Persistence & Dynamic Font Scaling
 */

document.addEventListener('DOMContentLoaded', () => {
  const searchEngine = new ArabicSearchEngine(GUIDELINE_SECTIONS);
  let currentSectionId = GUIDELINE_SECTIONS[0].id;
  let activeSearchCategory = 'all';
  let activeMobileCategory = 'all';

  // DOM Elements
  const sidebarNav = document.getElementById('sidebar-nav');
  const contentArea = document.getElementById('guideline-content');
  const fullManualPrintContainer = document.getElementById('full-manual-print-container');
  const searchInput = document.getElementById('search-input');
  const clearSearchBtn = document.getElementById('clear-search-btn');
  const searchResultsPane = document.getElementById('search-results-pane');
  const searchResultsList = document.getElementById('search-results-list');
  const searchCountBadge = document.getElementById('search-count-badge');
  const searchFilterPills = document.querySelectorAll('#search-results-pane .filter-pill');
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const themeBtnText = document.getElementById('theme-btn-text');
  const menuToggleBtn = document.getElementById('menu-toggle-btn');
  const sidebar = document.getElementById('sidebar');
  const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
  const sidebarBackdrop = document.getElementById('sidebar-backdrop');
  const breadcrumbCurrent = document.getElementById('breadcrumb-current');
  const fontIncreaseBtn = document.getElementById('font-increase-btn');
  const fontDecreaseBtn = document.getElementById('font-decrease-btn');
  const printSectionBtn = document.getElementById('print-section-btn');
  const printFullBookBtn = document.getElementById('print-full-book-btn');
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
  const bottomPrintBtn = document.getElementById('bottom-print-btn');
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
        item.href = `#${sec.id}`;
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
    history.replaceState(null, null, `#${id}`);

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

    // Find Previous & Next Sections
    const currentIndex = GUIDELINE_SECTIONS.findIndex(s => s.id === id);
    const prevSec = currentIndex > 0 ? GUIDELINE_SECTIONS[currentIndex - 1] : null;
    const nextSec = currentIndex < GUIDELINE_SECTIONS.length - 1 ? GUIDELINE_SECTIONS[currentIndex + 1] : null;

    let footerNavHtml = `
      <div class="section-nav-footer">
        ${prevSec ? `<button class="header-btn" onclick="window.appNavigate('${prevSec.id}')">→ السابق: ${prevSec.title}</button>` : '<div></div>'}
        ${nextSec ? `<button class="header-btn" onclick="window.appNavigate('${nextSec.id}')">التالي: ${nextSec.title} ←</button>` : '<div></div>'}
      </div>
    `;

    contentArea.innerHTML = `
      <article class="guideline-article">
        <div class="article-meta" style="margin-bottom: 1rem; display: flex; flex-wrap: wrap; gap: 0.4rem;">
          <span class="badge-tag">القسم: ${sec.category}</span>
          <span class="badge-tag">الصفحة المرجعية: ص ${sec.page}</span>
          ${tagsHtml}
        </div>
        ${html}
        ${footerNavHtml}
      </article>
    `;

    renderInjectedTables(contentArea);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  window.appNavigate = (id) => {
    navigateToSection(id);
    closeSidebar();
  };

  // 3. Render Injected Reference Tables inside a target root element
  function renderInjectedTables(rootEl = document) {
    // A. Roles Matrix Table
    const rolesContainer = rootEl.querySelector('#roles-matrix-table-container');
    if (rolesContainer && typeof ROLES_MATRIX_DATA !== 'undefined') {
      const rows = ROLES_MATRIX_DATA.map(item => `
        <tr>
          <td style="font-weight: 800; color: var(--primary-color); white-space: nowrap;">${item.role}</td>
          <td><span class="badge-code">${item.permission}</span></td>
          <td style="font-size: 0.82rem;">${item.screens}</td>
          <td>${item.responsibilities}</td>
        </tr>
      `).join('');

      rolesContainer.innerHTML = `
        <div class="responsive-table-wrapper">
          <div class="table-scroll-hint">↔ اسحب الجدول أفقياً للاطلاع على باقي الأعمدة</div>
          <table class="data-table">
            <thead>
              <tr>
                <th>الدور الوظيفي / الخدمي</th>
                <th>الصلاحية البرمجية</th>
                <th>الشاشات والمسارات المتاحة</th>
                <th>المهام والمسؤوليات الأساسية</th>
              </tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
      `;
    }

    // B. Keyboard Shortcuts Table
    const kbContainer = rootEl.querySelector('#keyboard-shortcuts-table-container');
    if (kbContainer && typeof KEYBOARD_SHORTCUTS_DATA !== 'undefined') {
      const rows = KEYBOARD_SHORTCUTS_DATA.map(item => `
        <tr>
          <td style="text-align: center; white-space: nowrap;"><span class="kbd-key">${item.key}</span></td>
          <td><span class="ui-badge blue">${item.scope}</span></td>
          <td style="font-weight: 600;">${item.action}</td>
        </tr>
      `).join('');

      kbContainer.innerHTML = `
        <div class="responsive-table-wrapper">
          <div class="table-scroll-hint">↔ اسحب الجدول أفقياً للاطلاع على باقي الاختصارات</div>
          <table class="data-table">
            <thead>
              <tr>
                <th style="width: 140px; text-align: center;">المفتاح / الاختصار</th>
                <th style="width: 160px;">نطاق العمل</th>
                <th>الوظيفة والإجراء الفوري</th>
              </tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
      `;
    }

    // C. Glossary Table
    const glossaryContainer = rootEl.querySelector('#glossary-table-container');
    if (glossaryContainer && typeof GLOSSARY_DATA !== 'undefined') {
      const rows = GLOSSARY_DATA.map(item => `
        <tr>
          <td style="font-weight: bold; text-align: center;">${item.sn}</td>
          <td><span class="badge-code">${item.code}</span></td>
          <td style="font-weight: 700; color: var(--primary-color);">${item.ar}</td>
          <td>${item.desc}</td>
        </tr>
      `).join('');

      glossaryContainer.innerHTML = `
        <div class="responsive-table-wrapper">
          <div class="table-scroll-hint">↔ اسحب الجدول أفقياً للاطلاع على باقي الأعمدة</div>
          <table class="data-table">
            <thead>
              <tr>
                <th style="width: 45px; text-align: center;">م</th>
                <th>الكود / الحقل البرمجي</th>
                <th>المصطلح بالعربية</th>
                <th>الشرح والوظيفة المحاسبية</th>
              </tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
      `;
    }
  }

  // 4. Build Full Manual Book DOM for Printing All Chapters at Once
  function buildFullBookForPrint() {
    if (!fullManualPrintContainer) return;

    const tocRows = GUIDELINE_SECTIONS.map((sec, idx) => `
      <div class="print-toc-row">
        <span><strong>${idx + 1}. ${sec.title}</strong> <small style="color: #64748b;">(${sec.category})</small></span>
        <span>صفحة ${sec.page}</span>
      </div>
    `).join('');

    const chaptersHtml = GUIDELINE_SECTIONS.map((sec, idx) => `
      <article class="guideline-article ${idx > 0 ? 'print-chapter-break' : ''}">
        <div style="margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #cbd5e1; padding-bottom: 0.4rem; font-size: 0.8rem; color: #475569;">
          <span>دليل نظام المخزون والأدبيات — الإقليم الخدمي لمصر (NA-Egypt)</span>
          <span>القسم: ${sec.category} | الصفحة المرجعية: ص ${sec.page}</span>
        </div>
        ${sec.content}
      </article>
    `).join('');

    fullManualPrintContainer.innerHTML = `
      <div class="print-toc-box">
        <h2 style="color: #184e77; margin-bottom: 0.75rem; text-align: center;">فهرس موضوعات دليل نظام المخزون والأدبيات</h2>
        ${tocRows}
      </div>
      ${chaptersHtml}
    `;

    renderInjectedTables(fullManualPrintContainer);
  }

  // 5. Live Search Handler (Desktop)
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
        <div style="text-align: center; padding: 1.5rem; color: var(--text-muted);">
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

  searchFilterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      searchFilterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      activeSearchCategory = pill.dataset.category;
      handleSearch();
    });
  });

  // 6. Mobile Search Modal Handler
  function openMobileSearch() {
    if (!mobileSearchModal) return;
    mobileSearchModal.classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
      if (mobileSearchInput) mobileSearchInput.focus();
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
      if (mobileSearchStats) mobileSearchStats.textContent = 'أدخل كلمة للبحث في المخزن، الجرد، الأذونات، أو طلبات المجموعات...';
      mobileSearchResultsList.innerHTML = `
        <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
          <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">🔍 ابحث بالكلمات أو الأكواد أو الاختصارات</p>
          <p style="font-size: 0.85rem;">مثال: "استلام خاطئ"، "يوم 19"، "جرد"، "TR"، "تالف"</p>
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
          <p style="font-size: 0.8rem; margin-top: 0.35rem;">جرب البحث بكلمات أخرى أو اختيار تصنيف "الكل".</p>
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

  if (mobileSearchBtn) mobileSearchBtn.addEventListener('click', openMobileSearch);
  if (bottomSearchBtn) bottomSearchBtn.addEventListener('click', openMobileSearch);
  if (closeSearchModalBtn) closeSearchModalBtn.addEventListener('click', closeMobileSearch);
  if (mobileSearchInput) mobileSearchInput.addEventListener('input', handleMobileSearch);

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

  // 7. Theme Toggle
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

  // 8. Font Sizing Controls
  function adjustFontSize(delta) {
    const newSize = currentFontSize + delta;
    if (newSize >= 13 && newSize <= 25) {
      currentFontSize = newSize;
      document.body.style.fontSize = currentFontSize + 'px';
    }
  }

  if (fontIncreaseBtn) fontIncreaseBtn.addEventListener('click', () => adjustFontSize(1));
  if (fontDecreaseBtn) fontDecreaseBtn.addEventListener('click', () => adjustFontSize(-1));
  if (bottomFontIncrease) bottomFontIncrease.addEventListener('click', () => adjustFontSize(1));
  if (bottomFontDecrease) bottomFontDecrease.addEventListener('click', () => adjustFontSize(-1));

  // 9. Dual Print Handlers (Current Section vs Full Book Manual)
  function printCurrentSection() {
    document.body.classList.remove('print-full-book');
    window.print();
  }

  function printFullManualBook() {
    buildFullBookForPrint();
    document.body.classList.add('print-full-book');
    window.print();
    setTimeout(() => {
      document.body.classList.remove('print-full-book');
    }, 800);
  }

  if (printSectionBtn) {
    printSectionBtn.addEventListener('click', printCurrentSection);
  }

  if (printFullBookBtn) {
    printFullBookBtn.addEventListener('click', printFullManualBook);
  }

  if (bottomPrintBtn) {
    bottomPrintBtn.addEventListener('click', printFullManualBook);
  }

  // 10. Copy Share Link
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

