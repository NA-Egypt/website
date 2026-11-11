/**
 * Main Application Logic for NA Egypt RSC Guidelines
 * Modern Documentation Hub with Alyamama typography and Emerald/Gold theme
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize Search Engine
  const searchEngine = new ArabicSearchEngine(GUIDELINE_SECTIONS);
  let currentSectionId = GUIDELINE_SECTIONS[0].id;
  let activeSearchCategory = 'all';
  let activeMobileCategory = 'all';
  let toastTimeout = null;

  // DOM Elements
  const sidebarNav = document.getElementById('sidebar-nav');
  const contentArea = document.getElementById('guideline-content');
  const searchInput = document.getElementById('search-input');
  const clearSearchBtn = document.getElementById('clear-search-btn');
  const searchResultsPane = document.getElementById('search-results-pane');
  const searchResultsList = document.getElementById('search-results-list');
  const searchCountBadge = document.getElementById('search-count-badge');
  const closeDesktopSearchBtn = document.getElementById('close-desktop-search-btn');
  const searchFilterPills = document.querySelectorAll('#search-results-pane .filter-pill');
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const themeBtnText = document.getElementById('theme-btn-text');
  const bottomThemeBtn = document.getElementById('bottom-theme-btn');
  const bottomThemeIcon = document.getElementById('bottom-theme-icon');
  const bottomThemeLabel = document.getElementById('bottom-theme-label');
  const menuToggleBtn = document.getElementById('menu-toggle-btn');
  const sidebar = document.getElementById('sidebar');
  const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
  const sidebarBackdrop = document.getElementById('sidebar-backdrop');
  const breadcrumbCurrent = document.getElementById('breadcrumb-current');
  const fontIncreaseBtn = document.getElementById('font-increase-btn');
  const fontDecreaseBtn = document.getElementById('font-decrease-btn');
  const fontSizeBadge = document.getElementById('font-size-badge');
  const printDocBtn = document.getElementById('print-doc-btn');
  const copyLinkBtn = document.getElementById('copy-link-btn');
  const readingProgressBar = document.getElementById('reading-progress-bar');
  const toastNotification = document.getElementById('toast-notification');

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
  let currentFontSize = parseInt(localStorage.getItem('na_font_size')) || 17;
  document.body.style.fontSize = currentFontSize + 'px';
  if (fontSizeBadge) fontSizeBadge.textContent = currentFontSize + 'px';

  // Initialize Theme from localStorage
  const savedTheme = localStorage.getItem('na_theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  // Toast Notification Function
  function showToast(message) {
    if (!toastNotification) return;
    toastNotification.innerHTML = message;
    toastNotification.classList.add('active');
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
      toastNotification.classList.remove('active');
    }, 2500);
  }

  // Reading Progress Bar
  function updateProgressBar() {
    if (!readingProgressBar) return;
    const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
    readingProgressBar.style.width = scrolled + '%';
  }
  window.addEventListener('scroll', updateProgressBar, { passive: true });

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
      const catHeader = document.createElement('div');
      catHeader.className = 'nav-category-header';
      catHeader.innerHTML = `
        <span>${catName}</span>
        <span class="nav-category-count">${categories[catName].length}</span>
      `;
      sidebarNav.appendChild(catHeader);

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
    if (window.innerWidth <= 960) {
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
    history.replaceState(null, null, '#' + id);

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
        ${prevSec ? `
          <button class="nav-card-btn prev-btn" onclick="window.appNavigate('${prevSec.id}')">
            <span class="nav-card-label">← القسم السابق (ص ${prevSec.page})</span>
            <span class="nav-card-title">${prevSec.title}</span>
          </button>
        ` : '<div></div>'}
        ${nextSec ? `
          <button class="nav-card-btn next-btn" onclick="window.appNavigate('${nextSec.id}')">
            <span class="nav-card-label">القسم التالي (ص ${nextSec.page}) →</span>
            <span class="nav-card-title">${nextSec.title}</span>
          </button>
        ` : '<div></div>'}
      </div>
    `;

    // Final Assembly
    contentArea.innerHTML = `
      <article class="guideline-article">
        <div class="article-meta">
          <span class="badge-tag">📁 ${sec.category}</span>
          <span class="badge-tag">📄 الصفحة المطبوعة: ${sec.page}</span>
          ${tagsHtml}
        </div>
        <div class="article-body">
          ${html}
        </div>
        ${footerNavHtml}
      </article>
    `;

    // Enhance and structure content blocks
    enhanceContentUI();

    // Wire up interactive email click-to-copy
    setupEmailCopyListeners();

    // Render interactive data tables if containers exist
    renderGlossaryTable();
    renderRobertsRulesTable();

    // Scroll smoothly to top of content
    window.scrollTo({ top: 0, behavior: 'smooth' });
    updateProgressBar();
  }

  // Dynamic Content Enhancer (Converts servant requirements & tasks and study questions into structured cards)
  function enhanceContentUI() {
    // 1. Transform requirements and tasks into structured cards
    const headings = contentArea.querySelectorAll('.article-body h3, .article-body h4');
    headings.forEach(heading => {
      const text = heading.textContent.trim();
      const nextUl = heading.nextElementSibling;
      if (nextUl && nextUl.tagName === 'UL') {
        if (text.includes('المتطلبات')) {
          const wrapper = document.createElement('div');
          wrapper.className = 'requirements-box';
          heading.parentNode.insertBefore(wrapper, heading);
          wrapper.appendChild(heading);
          wrapper.appendChild(nextUl);
          if (!heading.innerHTML.includes('📋')) {
            heading.innerHTML = '📋 ' + heading.innerHTML;
          }
        } else if (text.includes('مهام') || text.includes('المهام')) {
          const wrapper = document.createElement('div');
          wrapper.className = 'tasks-box';
          heading.parentNode.insertBefore(wrapper, heading);
          wrapper.appendChild(heading);
          wrapper.appendChild(nextUl);
          if (!heading.innerHTML.includes('⚡')) {
            heading.innerHTML = '⚡ ' + heading.innerHTML;
          }
        }
      }
    });

    // 2. Transform Study questions in Concept study-boxes into beautiful cards
    const studyBoxes = contentArea.querySelectorAll('.study-box');
    studyBoxes.forEach(box => {
      const ol = box.querySelector('ol');
      if (ol) {
        const items = Array.from(ol.querySelectorAll('li'));
        const containerDiv = document.createElement('div');
        containerDiv.className = 'study-questions-list';
        items.forEach((li, idx) => {
          const qCard = document.createElement('div');
          qCard.className = 'study-question-item';
          qCard.innerHTML = `
            <div class="question-number">${idx + 1}</div>
            <div class="question-text">${li.innerHTML}</div>
          `;
          containerDiv.appendChild(qCard);
        });
        ol.parentNode.replaceChild(containerDiv, ol);
      }
    });
  }

  // Email copy-to-clipboard listeners
  function setupEmailCopyListeners() {
    const emails = contentArea.querySelectorAll('.copyable-email, .badge-code');
    emails.forEach(el => {
      const text = el.textContent.trim();
      if (text.includes('@naegypt.org')) {
        el.classList.add('copyable-email');
        el.title = 'اضغط لنسخ البريد الإلكتروني';
        el.onclick = (e) => {
          e.stopPropagation();
          navigator.clipboard.writeText(text).then(() => {
            showToast(`📋 تم نسخ البريد: <strong>${text}</strong>`);
          });
        };
      }
    });
  }

  // Expose global navigator for footer buttons
  window.appNavigate = (id) => {
    navigateToSection(id);
    closeSidebar();
  };

  // 3. Render Injected Tables (Glossary & Robert's Rules)
  function renderGlossaryTable() {
    const container = document.getElementById('glossary-table-container');
    if (!container || typeof GLOSSARY_DATA === 'undefined') return;

    function buildRows(filteredData) {
      if (filteredData.length === 0) {
        return `<tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">لا توجد مصطلحات مطابقة للبحث</td></tr>`;
      }
      return filteredData.map(item => `
        <tr>
          <td style="font-weight: 800; text-align: center; color: var(--text-muted);">${item.sn}</td>
          <td><span class="badge-code">${item.acronym}</span></td>
          <td style="direction: ltr; text-align: left; font-family: monospace; font-size: 0.9em;">${item.en}</td>
          <td style="font-weight: 700; color: var(--text-main);">${item.ar}</td>
        </tr>
      `).join('');
    }

    container.innerHTML = `
      <div class="table-toolbar">
        <input type="text" id="glossary-search" class="table-search-input" placeholder="🔍 ابحث في المصطلحات والاختصارات (عربي / إنجليزي)...">
      </div>
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
          <tbody id="glossary-tbody">
            ${buildRows(GLOSSARY_DATA)}
          </tbody>
        </table>
      </div>
    `;

    const searchInput = document.getElementById('glossary-search');
    const tbody = document.getElementById('glossary-tbody');
    if (searchInput && tbody) {
      searchInput.addEventListener('input', () => {
        const q = ArabicSearchEngine.normalizeArabic(searchInput.value.trim());
        const filtered = GLOSSARY_DATA.filter(item => {
          const ar = ArabicSearchEngine.normalizeArabic(item.ar);
          const en = (item.en || '').toLowerCase();
          const ac = (item.acronym || '').toLowerCase();
          return ar.includes(q) || en.includes(q) || ac.includes(q);
        });
        tbody.innerHTML = buildRows(filtered);
      });
    }
  }

  function renderRobertsRulesTable() {
    const container = document.getElementById('roberts-rules-table-container');
    if (!container || typeof ROBERTS_RULES_DATA === 'undefined') return;

    let activeFilter = 'all';

    function getMajorityBadge(maj) {
      if (maj.includes('بسيطة')) {
        return `<span class="motion-badge-simple">${maj}</span>`;
      } else if (maj.includes('ثلثين')) {
        return `<span class="motion-badge-two-thirds">${maj}</span>`;
      } else if (maj.includes('إجماع')) {
        return `<span class="motion-badge-consensus">${maj}</span>`;
      }
      return `<span class="motion-badge-none">${maj}</span>`;
    }

    function buildRows(filteredData) {
      if (filteredData.length === 0) {
        return `<tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">لا توجد قواعد مطابقة للبحث أو التصفية</td></tr>`;
      }
      return filteredData.map(item => `
        <tr>
          <td style="font-weight: 800; text-align: center; color: var(--text-muted);">${item.m}</td>
          <td style="font-weight: 800; color: var(--primary-color);">${item.type}</td>
          <td>${item.purpose}</td>
          <td style="text-align: center; font-weight: 600;">${item.interrupt === 'نعم' ? '✅ نعم' : (item.interrupt === 'لا' ? '❌ لا' : item.interrupt)}</td>
          <td style="text-align: center; font-weight: 600;">${item.second === 'نعم' ? '✅ نعم' : '❌ لا'}</td>
          <td style="text-align: center; font-weight: 600;">${item.debatable === 'نعم' ? '✅ نعم' : '❌ لا'}</td>
          <td style="text-align: center;">${getMajorityBadge(item.majority)}</td>
        </tr>
      `).join('');
    }

    container.innerHTML = `
      <div class="table-toolbar">
        <input type="text" id="roberts-search" class="table-search-input" placeholder="🔍 ابحث في اسم الاقتراح أو الغرض منه...">
        <div class="table-filter-chips">
          <button class="table-chip active" data-maj="all">الكل (${ROBERTS_RULES_DATA.length})</button>
          <button class="table-chip" data-maj="بسيطة">أغلبية بسيطة</button>
          <button class="table-chip" data-maj="ثلثين">ثلثين (2/3)</button>
          <button class="table-chip" data-maj="إجماع">إجماع</button>
          <button class="table-chip" data-maj="لا يصوت عليه">لا يصوت عليه</button>
        </div>
      </div>
      <div class="responsive-table-wrapper">
        <div class="table-scroll-hint">↔ اسحب الجدول أفقياً للاطلاع على باقي التفاصيل والقواعد</div>
        <table class="data-table">
          <thead>
            <tr>
              <th style="text-align: center; width: 45px;">م</th>
              <th>نوع الاقتراح</th>
              <th>الغرض منه</th>
              <th style="text-align: center;">هل يجوز المقاطعة؟</th>
              <th style="text-align: center;">يحتاج لتزكية؟</th>
              <th style="text-align: center;">قابل للمناقشة؟</th>
              <th style="text-align: center;">الأغلبية المطلوبة</th>
            </tr>
          </thead>
          <tbody id="roberts-tbody">
            ${buildRows(ROBERTS_RULES_DATA)}
          </tbody>
        </table>
      </div>
    `;

    const searchInput = document.getElementById('roberts-search');
    const tbody = document.getElementById('roberts-tbody');
    const chips = container.querySelectorAll('.table-chip');

    function updateTable() {
      const q = ArabicSearchEngine.normalizeArabic(searchInput.value.trim());
      const filtered = ROBERTS_RULES_DATA.filter(item => {
        const matchesQuery = !q ||
          ArabicSearchEngine.normalizeArabic(item.type).includes(q) ||
          ArabicSearchEngine.normalizeArabic(item.purpose).includes(q);
        const matchesMaj = activeFilter === 'all' || item.majority.includes(activeFilter);
        return matchesQuery && matchesMaj;
      });
      tbody.innerHTML = buildRows(filtered);
    }

    if (searchInput) {
      searchInput.addEventListener('input', updateTable);
    }

    chips.forEach(chip => {
      chip.addEventListener('click', () => {
        chips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        activeFilter = chip.dataset.maj;
        updateTable();
      });
    });
  }

  // 4. Live Search Handler (Desktop)
  function handleSearch() {
    const query = searchInput.value.trim();
    if (!query) {
      clearSearchBtn.style.display = 'none';
      searchResultsPane.style.display = 'none';
      return;
    }

    clearSearchBtn.style.display = 'block';
    const results = searchEngine.search(query, activeSearchCategory);

    searchCountBadge.textContent = `${results.length} نتيجة`;
    searchResultsList.innerHTML = '';

    if (results.length === 0) {
      searchResultsList.innerHTML = `
        <div class="text-center p-4 text-muted" style="text-align: center; padding: 2rem;">
          <p>لم يتم العثور على أي نتائج مطابقة لكلمة "<strong>${query}</strong>"</p>
          <p style="font-size: 0.85rem; margin-top: 0.5rem;">جرب كتابة جزء من الكلمة أو اختيار تصنيف مختلف.</p>
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
      clearSearchBtn.style.display = 'none';
      searchResultsPane.style.display = 'none';
      searchInput.focus();
    });
  }

  if (closeDesktopSearchBtn) {
    closeDesktopSearchBtn.addEventListener('click', () => {
      searchResultsPane.style.display = 'none';
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
    }, 120);
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
        <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
          <p style="font-size: 1.2rem; margin-bottom: 0.5rem;">🔍 ابحث بالكلمات أو العناوين</p>
          <p style="font-size: 0.88rem;">مثال: "الرئيس"، "قواعد روبرت"، "النصاب"، "المفاهيم"</p>
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
          <p style="font-size: 0.82rem; margin-top: 0.4rem;">جرب البحث بكلمات أخرى أو تغيير التصنيف أعلاه.</p>
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

    // 6. Theme Toggle Handler
    function toggleTheme() {
      const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('na_theme', newTheme);
      updateThemeIcon(newTheme);
      showToast(newTheme === 'dark' ? '🌙 تم تفعيل الوضع الداكن' : '☀️ تم تفعيل الوضع الفاتح');
    }

    function updateThemeIcon(theme) {
      const isDark = theme === 'dark';
      const headerText = isDark ? '☀️ الوضع الفاتح' : '🌙 الوضع الداكن';
      if (themeBtnText) {
        themeBtnText.textContent = headerText;
      } else if (themeToggleBtn) {
        themeToggleBtn.innerHTML = headerText;
      }

      if (bottomThemeIcon) {
        bottomThemeIcon.textContent = isDark ? '☀️' : '🌙';
      }
      if (bottomThemeLabel) {
        bottomThemeLabel.textContent = isDark ? 'فاتح' : 'داكن';
      }
    }

    if (themeToggleBtn) {
      themeToggleBtn.addEventListener('click', toggleTheme);
    }
    if (bottomThemeBtn) {
      bottomThemeBtn.addEventListener('click', toggleTheme);
    }

    // 7. Font Sizing Controls
    function adjustFontSize(delta) {
      const newSize = currentFontSize + delta;
      if (newSize >= 13 && newSize <= 25) {
        currentFontSize = newSize;
        document.body.style.fontSize = currentFontSize + 'px';
        localStorage.setItem('na_font_size', currentFontSize);
        if (fontSizeBadge) fontSizeBadge.textContent = currentFontSize + 'px';
        showToast(`🔤 حجم الخط: ${currentFontSize}px`);
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
      const currentUrl = window.location.origin + window.location.pathname + '#' + currentSectionId;
      navigator.clipboard.writeText(currentUrl).then(() => {
        showToast('🔗 تم نسخ رابط المقطع إلى الحافظة!');
        if (btn) {
          const originalHtml = btn.innerHTML;
          btn.innerHTML = '✓ تم النسخ';
          setTimeout(() => {
            btn.innerHTML = originalHtml;
          }, 2000);
        }
      }).catch(() => {
        showToast('⚠️ تعذر النسخ المباشر');
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

    // 10. Global Keyboard Shortcuts (Ctrl+K or '/' to focus search, Esc to dismiss)
    window.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        if (window.innerWidth <= 768) {
          openMobileSearch();
        } else {
          if (searchInput) {
            searchInput.focus();
            searchInput.select();
          }
        }
      } else if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault();
        if (window.innerWidth <= 768) {
          openMobileSearch();
        } else {
          if (searchInput) {
            searchInput.focus();
            searchInput.select();
          }
        }
      } else if (e.key === 'Escape') {
        closeMobileSearch();
        closeSidebar();
        if (searchResultsPane) searchResultsPane.style.display = 'none';
      }
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
