/**
 * Comprehensive Manual Data for NA-Egypt Inventory & Literature Management System
 * Designed for Electronic Viewing (/guidelines/inventory/) and Physical A4 Book Printing
 */

const GUIDELINE_SECTIONS = [
  // =========================================================================
  // 1. الغلاف والنظرة العامة
  // =========================================================================
  {
    id: "cover",
    title: "الغلاف والبيانات التمهيدية",
    page: 1,
    category: "مقدمة وهيكلة النظام",
    tags: ["غلاف", "مقدمة", "دليل النظام", "المخزون", "الأدبيات", "الطباعة"],
    content: `
      <div class="document-header">
        <div class="badge-tag">الإصدار الرسمي المحدث – 2026</div>
        <h1 class="main-doc-title">الدليل الشامل لنظام إدارة المخزون والأدبيات</h1>
        <p class="subtitle">الإقليم الخدمي لزمالة المدمنين المجهولين – مصر (NA-Egypt)</p>
        <div style="margin-top: 1.25rem; display: flex; justify-content: center; gap: 0.6rem; flex-wrap: wrap;">
          <span class="ui-badge blue">المخزن الرئيسي (Store)</span>
          <span class="ui-badge cyan">لجنة الأدبيات (Lit)</span>
          <span class="ui-badge green">الجرد الفعلي (Stocktaking)</span>
          <span class="ui-badge amber">الأذونات والدفتر الشهري (Slips & Ledger)</span>
          <span class="ui-badge red">طلبات المجموعات واللجان (Requests)</span>
        </div>
      </div>

      <h2>مرحباً بكم في دليل نظام المخزون والأدبيات</h2>
      <p>صُمم هذا الدليل المرجعي ليكون المرجع الإلكتروني والمطبوع لكافة الخدام والمسؤولين المتعاملين مع منظومة المستودع والأدبيات في إقليم مصر، بدءاً من استلام الشحنات في المخزن الرئيسي، ومروراً بالتحويل إلى لجنة الأدبيات، وحتى صرف طلبات المجموعات الشهرية وتوزيع مطبوعات اللجان الفرعية وإجراء الجرد الفعلي والتسويات.</p>

      <div class="alert-box alert-info">
        <span class="alert-icon">💡</span>
        <div>
          <strong>مُهيأ للاستخدام الإلكتروني والطباعة الورقية (A4):</strong><br>
          يمكنك تصفح هذا الدليل إلكترونياً مع البحث الذكي الفوري وتكبير الخط والوضع الليلي، أو الضغط على زر <strong>«🖨️ طباعة الدليل الكامل»</strong> في الشريط العلوي لاستخراج نسخة كتاب مطبوعة (أو ملف PDF) مقسمة بالفصول وأرقام الصفحات وجاهزة للتوزيع في ورش العمل واجتماعات التسليم والتسلم.
        </div>
      </div>

      <div class="service-card">
        <h3>ماذا يتضمن هذا الدليل؟</h3>
        <ul>
          <li><strong>هيكلة النظام والأدوار والصلاحيات:</strong> شرح الفارق بين المخزن الرئيسي (Store) وعهدة لجنة الأدبيات (Lit) والمخزون التالف (Damaged).</li>
          <li><strong>إدارة المخزن الرئيسي:</strong> لقطات شاشة توضيحية وشرح تفصيلي لاستلام الوارد، التحويل، الإرجاع، تسجيل التالف، وإلغاء الاستلام الخاطئ.</li>
          <li><strong>الحركات المجمعة واختصارات لوحة المفاتيح:</strong> كيفية تنفيذ عشرات الحركات في ثوانٍ باستخدام نافذة الحركة المجمعة أو أزرار لوحة المفاتيح.</li>
          <li><strong>الجرد الفعلي وقفل المخزن:</strong> خطوات بدء جلسة جرد، القفل التلقائي للمخزن، إدخال العد الفعلي، ومعادلات حساب الفروقات المالية.</li>
          <li><strong>مخزون لجنة الأدبيات، التسوية، والدفتر الشهري:</strong> متابعة المبيعات اللحظية، إرجاع الفائض للمخزن، وقراءة الدفتر المالي الموحد.</li>
          <li><strong>أذونات المخزون (Slips):</strong> دورة حياة أذونات التحويل (TR)، المرتجع (RT)، وصرف/مرتجع اللجان (IC / RC).</li>
          <li><strong>دورة طلبات المجموعات وأمناء الصندوق:</strong> من سلة مندوب المجموعة (GSR) قبل يوم 19، إلى اعتماد أمين الصندوق، وحتى تنفيذ لجنة الأدبيات.</li>
          <li><strong>مطبوعات اللجان الفرعية (بدون فواتير):</strong> كيفية طلب المطبوعات المجانية للجان (مثل H&I و PR) وإرجاع المتبقي.</li>
        </ul>
      </div>

      <div class="spiritual-quote">
        <p>«إن المحاسبة الدقيقة والشفافية في إدارة موارد الزمالة ومطبوعاتها هي أمانة روحانية تضمن وصول رسالة التعافي لكل مدمن يعاني، وتحافظ على أموال الإعانة الذاتية للمجموعات.»</p>
      </div>
    `
  },

  {
    id: "system-architecture",
    title: "هيكلة النظام، الأدوار، ومسارات الحركة",
    page: 2,
    category: "مقدمة وهيكلة النظام",
    tags: ["هيكلة", "أدوار", "صلاحيات", "مخزن", "أدبيات", "مجموعات", "أمين صندوق", "لجان"],
    content: `
      <h2>هيكلة النظام ومسارات حركة المخزون</h2>
      <p>يعتمد النظام على الفصل المحاسبي والرقابي الكامل بين ثلاثة أرصدة رئيسية لكل صنف من أصناف الأدبيات والعملات (التشيبس والميداليات)، مع ربطها آلياً عبر سجل حركات غير قابل للتلاعب (Audit Trail) وأذونات رسمية مرقمة.</p>

      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">مخطط مسار حركة المطبوعات والأرصدة الثلاثة في النظام</span>
          <span class="ui-window-url">NA-Egypt Inventory Lifecycle</span>
        </div>
        <div class="ui-canvas">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;">
            <div class="ui-kpi-card border-primary" style="flex-direction: column; align-items: flex-start;">
              <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                <span class="ui-badge blue">1. المخزن الرئيسي (Store)</span>
                <span class="ui-callout-pin">1</span>
              </div>
              <strong style="margin-top: 0.4rem; font-size: 0.92rem;">الرصيد الاحتياطي العام</strong>
              <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem; margin-bottom: 0;">يستقبل الشحنات الجديدة والطباعة (<span class="badge-code">+receive</span>)، ويغذي لجنة الأدبيات عبر أذونات تحويل (<span class="badge-code">TR</span>).</p>
            </div>

            <div class="ui-kpi-card border-info" style="flex-direction: column; align-items: flex-start;">
              <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                <span class="ui-badge cyan">2. لجنة الأدبيات (Lit)</span>
                <span class="ui-callout-pin">2</span>
              </div>
              <strong style="margin-top: 0.4rem; font-size: 0.92rem;">رصيد التوزيع والمبيعات</strong>
              <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem; margin-bottom: 0;">يُصرف منه فواتير المجموعات الشهرية وأذونات اللجان الفرعية (<span class="badge-code">IC</span>)، ويُرد الفائض للمخزن (<span class="badge-code">RT</span>).</p>
            </div>

            <div class="ui-kpi-card border-danger" style="flex-direction: column; align-items: flex-start;">
              <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                <span class="ui-badge red">3. المخزون التالف (Damaged)</span>
                <span class="ui-callout-pin">3</span>
              </div>
              <strong style="margin-top: 0.4rem; font-size: 0.92rem;">العزل والتوثيق الرقابي</strong>
              <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem; margin-bottom: 0;">أي نسخة بها عيب طباعة أو تلف رطوبة تُخصم من المخزن أو الأدبيات وتُسجل هنا مع سبب التلف.</p>
            </div>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 القواعد الذهبية للفصل بين الأرصدة:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> لا يتم البيع المباشر للمجموعات من رصيد المخزن الرئيسي إطلاقاً.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> طلبات المجموعات واللجان الفرعية تُخصم حصرياً من رصيد لجنة الأدبيات (lit_quantity).</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> كل حركة بين المخزن والأدبيات تُنشئ تلقائياً إذن مخزون رسمي قابل للطباعة.</div>
          </div>
        </div>
      </div>

      <h3>جدول الأدوار والمسؤوليات والصلاحيات</h3>
      <p>يوضح الجدول التالي كافة الأدوار المتعاملة مع منظومة المخزون، الصلاحيات البرمجية المرتبطة بها، والشاشات المتاحة لكل دور:</p>
      <div id="roles-matrix-table-container"></div>

      <h3>فئات الأصناف المعتمدة في النظام (9 فئات)</h3>
      <p>يُصنف كل عنصر في المخزون ضمن إحدى الفئات التسع التالية لضمان دقة الفرز والتقارير المالية:</p>
      <ul>
        <li><strong>كتب عربية (<span class="badge-code">Arabic Books</span>):</strong> مثل الكتاب الأساسي، خطواتنا الاثنتا عشرة، دليل العمل في الخطوات.</li>
        <li><strong>مطويات عربية (<span class="badge-code">Arabic IP</span>):</strong> المطويات التعريفية والكتيبات الصغيرة باللغة العربية.</li>
        <li><strong>كتب إنجليزية (<span class="badge-code">English Books</span>):</strong> الكتب والمراجع باللغة الإنجليزية (تُعرض بأسمائها الإنجليزية تلقائياً).</li>
        <li><strong>مطويات إنجليزية (<span class="badge-code">English IP</span>):</strong> المطويات الإنجليزية.</li>
        <li><strong>تشيبس / ميداليات بلاستيكية (<span class="badge-code">Chips</span>):</strong> ميداليات الاحتفال بمدد الامتناع المختلفة.</li>
        <li><strong>عملات معدنية (<span class="badge-code">Coins</span>)</strong> و <strong>ميداليات خاصة (<span class="badge-code">Medallions</span>)</strong>.</li>
        <li><strong>قراءات الاجتماعات (<span class="badge-code">Readings</span>):</strong> لوحات وبطاقات قراءات افتتاح الاجتماعات.</li>
        <li><strong>أخرى (<span class="badge-code">Others</span>):</strong> أي مطبوعات أو مستلزمات خدمية إضافية.</li>
      </ul>
    `
  },

  // =========================================================================
  // 2. إدارة المخزن الرئيسي (Store Management)
  // =========================================================================
  {
    id: "store-dashboard",
    title: "لوحة المخزن الرئيسي والمزامنة الحية",
    page: 4,
    category: "إدارة المخزن الرئيسي",
    tags: ["مخزن", "لوحة التحكم", "مزامنة حية", "نقص المخزون", "بطاقات", "فئات", "بحث"],
    content: `
      <h2>لوحة إدارة المخزن الرئيسي والأدبيات (/store)</h2>
      <p>تُعد شاشة المخزن الرئيسي (<span class="badge-code">/store</span>) مركز القيادة اليومي لمدير المخزن. تتميز هذه الشاشة بنظام <strong>المزامنة الحية والمباشرة (Live Sync)</strong>، حيث تتحدث كافة الأرقام والأرصدة لحظياً بمجرد تنفيذ أي عملية أو بيع في لجنة الأدبيات دون الحاجة لإعادة تحميل الصفحة.</p>

      <!-- UI Screenshot Mockup: Store Dashboard -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: لوحة المخزن الرئيسي والأدبيات</span>
          <span class="ui-window-url">GET /store</span>
        </div>
        <div class="ui-canvas">
          <!-- Top Bar -->
          <div class="ui-top-bar">
            <div class="ui-top-bar-title">
              <span>إدارة المخزن والأدبيات</span>
              <span class="ui-live-pill"><span class="ui-live-dot"></span> مزامنة حية ومباشرة <span class="ui-callout-pin">1</span></span>
            </div>
            <div class="ui-btn-group">
              <span class="ui-btn outline">⌨️ اختصارات لوحة المفاتيح <span class="kbd-key">?</span> <span class="ui-callout-pin">2</span></span>
              <span class="ui-btn warning">⇄ حركة مخزون مجمعة سريعة <span class="kbd-key">M</span> <span class="ui-callout-pin">3</span></span>
              <span class="ui-btn primary">+ إضافة صنف جديد <span class="kbd-key">N</span> <span class="ui-callout-pin">4</span></span>
            </div>
          </div>

          <!-- 4 KPI Cards -->
          <div class="ui-kpi-grid">
            <div class="ui-kpi-card border-primary">
              <div>
                <div class="ui-kpi-label">إجمالي رصيد المخزن <span class="ui-help-icon">؟</span> <span class="ui-callout-pin">5</span></div>
                <div class="ui-kpi-val" style="color: #2563eb;">4,820</div>
              </div>
              <span style="font-size: 1.4rem;">📦</span>
            </div>
            <div class="ui-kpi-card border-info">
              <div>
                <div class="ui-kpi-label">إجمالي رصيد الأدبيات <span class="ui-help-icon">؟</span></div>
                <div class="ui-kpi-val" style="color: #0284c7;">1,345</div>
              </div>
              <span style="font-size: 1.4rem;">📚</span>
            </div>
            <div class="ui-kpi-card border-danger">
              <div>
                <div class="ui-kpi-label">المخزون التالف <span class="ui-help-icon">؟</span></div>
                <div class="ui-kpi-val" style="color: #dc2626;">18</div>
              </div>
              <span style="font-size: 1.4rem;">💔</span>
            </div>
            <div class="ui-kpi-card border-warning">
              <div>
                <div class="ui-kpi-label">تنبيهات نقص المخزون (≤ 5) <span class="ui-callout-pin">6</span></div>
                <div class="ui-kpi-val">3 أصناف</div>
              </div>
              <span style="font-size: 1.4rem;">⚠️</span>
            </div>
          </div>

          <!-- Category Tabs & Search -->
          <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
            <span class="ui-badge blue" style="padding: 0.3rem 0.75rem;">الكل</span>
            <span class="ui-badge gray" style="padding: 0.3rem 0.75rem;">كتب عربية</span>
            <span class="ui-badge gray" style="padding: 0.3rem 0.75rem;">مطويات عربية</span>
            <span class="ui-badge gray" style="padding: 0.3rem 0.75rem;">كتب إنجليزية</span>
            <span class="ui-badge gray" style="padding: 0.3rem 0.75rem;">تشيبس</span>
            <span class="ui-badge gray" style="padding: 0.3rem 0.75rem;">ميداليات</span>
          </div>

          <!-- Table -->
          <div class="ui-table-card">
            <table class="ui-mock-table">
              <thead>
                <tr>
                  <th style="width: 32px;">☑️</th>
                  <th>اسم الصنف</th>
                  <th>الفئة</th>
                  <th>سعر البيع</th>
                  <th>رصيد المخزن</th>
                  <th>رصيد الأدبيات</th>
                  <th>التالف</th>
                  <th>الإجراءات السريعة <span class="ui-callout-pin">7</span></th>
                </tr>
              </thead>
              <tbody>
                <tr class="focused-row">
                  <td>☑️</td>
                  <td><strong>الكتاب الأساسي (Basic Text - Arabic)</strong></td>
                  <td><span class="ui-badge gray">كتب عربية</span></td>
                  <td><strong>150.00 ج.م</strong></td>
                  <td><span class="ui-badge blue">420</span></td>
                  <td><span class="ui-badge cyan">85</span></td>
                  <td><span class="ui-badge red">2 💬</span></td>
                  <td>
                    <div class="ui-btn-group">
                      <span class="ui-btn success" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">+ استلام (R)</span>
                      <span class="ui-btn info" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">→ تحويل (T)</span>
                      <span class="ui-btn warning" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">← إرجاع (U)</span>
                      <span class="ui-btn danger" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">تالف (D)</span>
                      <span class="ui-btn outline" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">إلغاء استلام (W)</span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>☐</td>
                  <td><strong>دليل العمل في الخطوات (Step Working Guides)</strong></td>
                  <td><span class="ui-badge gray">كتب عربية</span></td>
                  <td><strong>160.00 ج.م</strong></td>
                  <td><span class="ui-badge amber">4 ⚠️ منخفض</span></td>
                  <td><span class="ui-badge cyan">30</span></td>
                  <td><span class="ui-badge gray">0</span></td>
                  <td>
                    <div class="ui-btn-group">
                      <span class="ui-btn success" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">+ استلام</span>
                      <span class="ui-btn info" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">→ تحويل</span>
                      <span class="ui-btn warning" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">← إرجاع</span>
                      <span class="ui-btn danger" style="padding: 0.15rem 0.45rem; font-size: 0.68rem;">تالف</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">🔍 دليل العناصر المرقمة في شاشة المخزن الرئيسي:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> <strong>شارة المزامنة الحية:</strong> تؤكد أن الأرقام تتحدث تلقائياً مع وميض أخضر عند أي تغيير.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> <strong>دليل الاختصارات (?):</strong> يعرض كافة أزرار التحكم السريع بلوحة المفاتيح.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> <strong>حركة مخزون مجمعة (M):</strong> لتنفيذ استلام أو تحويل أو إرجاع لعدة أصناف في خطوة واحدة.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> <strong>إضافة صنف جديد (N):</strong> لتعريف كتاب أو ميدالية جديدة في دليل المخزون.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">5</span> <strong>أيقونة الشرح (؟):</strong> اضغط عليها لعرض معنى الرقم والمعادلة المحاسبية الخاصة به.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">6</span> <strong>بطاقة نقص المخزون:</strong> قابلة للنقر! اضغط عليها لتصفية الجدول وعرض الأصناف التي رصيدها 5 أو أقل.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">7</span> <strong>أزرار الإجراءات السريعة:</strong> لتنفيذ أي حركة فورية على الصنف أو التعديل المباشر (Inline Edit).</div>
          </div>
        </div>
      </div>

      <h3>معادلات البطاقات الإحصائية الأربع في أعلى الشاشة</h3>
      <div class="service-card">
        <h4>1. إجمالي رصيد المخزن الرئيسي (Total Store Stock)</h4>
        <p>يمثل مجموع الوحدات السليمة المتوفرة حالياً على أرفف المخزن الرئيسي لجميع الأصناف.</p>
        <div class="formula-box">🧮 المعادلة: الرصيد الافتتاحي + الوارد النشط + المرتجع من الأدبيات − المحول للأدبيات − تالف المخزن ± تسويات الجرد</div>

        <h4>2. إجمالي رصيد لجنة الأدبيات (Total Lit Stock)</h4>
        <p>يمثل مجموع الوحدات المتوفرة حالياً في عهدة لجنة الأدبيات والجاهزة للصرف للمجموعات أو اللجان الفرعية.</p>
        <div class="formula-box">🧮 المعادلة: المحول من المخزن − المباع للمجموعات/اللجان − الموزع على اللجان الفرعية − المرتجع للمخزن − تالف الأدبيات</div>

        <h4>3. إجمالي المخزون التالف (Total Damaged Stock)</h4>
        <p>يمثل مجموع الوحدات المعزولة كتالفة. يمكنك تمرير المؤشر أو الضغط على شارة التالف الحمراء بجانب أي صنف في الجدول لقراءة <strong>سجل ملاحظات التلف</strong> بالتاريخ واسم المستخدم.</p>

        <h4>4. تنبيهات نقص المخزون (Low Stock Alerts)</h4>
        <p>عدد الأصناف التي وصل رصيدها في المخزن الرئيسي إلى <strong>5 وحدات أو أقل (≤ 5)</strong>. عند الضغط على هذه البطاقة تتحول للون البرتقالي النشط وتقوم بتصفية الجدول لعرض الأصناف المنخفضة فقط لسرعة إعادة طلبها أو طباعتها.</p>
      </div>
    `
  },

  {
    id: "store-single-operations",
    title: "العمليات الفردية: الاستلام، التحويل، الإرجاع، التالف، وإلغاء الاستلام الخاطئ",
    page: 6,
    category: "إدارة المخزن الرئيسي",
    tags: ["استلام", "تحويل", "إرجاع", "تالف", "استلام خاطئ", "تعديل سريع", "عمليات المخزن"],
    content: `
      <h2>العمليات التشغيلية على الأصناف في المخزن الرئيسي</h2>
      <p>يوفر النظام خمس عمليات حركية أساسية لكل صنف في الجدول، بالإضافة إلى إمكانية التعديل المباشر لبيانات الصنف (الاسم، السعر، الفئة). جميع هذه العمليات تعمل بتقنية <span class="badge-code">AJAX</span> الفورية دون إعادة تحميل الصفحة.</p>

      <div class="workflow-steps">
        <div class="workflow-step">
          <div class="step-number">1</div>
          <div class="step-body">
            <h4>استلام مخزون جديد في المخزن (+ استلام / مفتاح <span class="kbd-key">R</span>)</h4>
            <p>يُستخدم عند ورود شحنة مطبوعات جديدة من المطبعة أو استلام ميداليات جديدة. يزيد هذا الإجراء من <strong>رصيد المخزن الرئيسي (<span class="badge-code">store_quantity</span>)</strong> فقط، ويسجل حركة من نوع <span class="badge-code">receive</span> في تقارير المستودع والدفتر الشهري.</p>
          </div>
        </div>

        <div class="workflow-step">
          <div class="step-number">2</div>
          <div class="step-body">
            <h4>تحويل مخزون إلى لجنة الأدبيات (→ تحويل / مفتاح <span class="kbd-key">T</span>)</h4>
            <p>يُستخدم لتغذية عهدة لجنة الأدبيات من المخزن الرئيسي. يقوم النظام فوراً بـ:</p>
            <ul>
              <li>خصم الكمية من <strong>رصيد المخزن الرئيسي</strong> وإضافتها إلى <strong>رصيد لجنة الأدبيات</strong>.</li>
              <li>إصدار <strong>إذن تحويل رسمي (<span class="badge-code">TR-YYYYMM-XXXX</span>)</strong> بحالة «تم التحويل» يظهر في شاشة الأذونات بانتظار تأكيد استلام مسؤول الأدبيات.</li>
            </ul>
          </div>
        </div>

        <div class="workflow-step">
          <div class="step-number">3</div>
          <div class="step-body">
            <h4>إرجاع مخزون من الأدبيات إلى المخزن (← إرجاع / مفتاح <span class="kbd-key">U</span>)</h4>
            <p>يُستخدم عند رد كميات فائضة من عهدة لجنة الأدبيات إلى المخزن الرئيسي. يقوم النظام بخصم الكمية من الأدبيات، وإضافتها للمخزن، وإصدار <strong>إذن مرتجع (<span class="badge-code">RT-YYYYMM-XXXX</span>)</strong> مكتمل تلقائياً.</p>
          </div>
        </div>

        <div class="workflow-step">
          <div class="step-number">4</div>
          <div class="step-body">
            <h4>تسجيل وحدات تالفة (تالف / مفتاح <span class="kbd-key">D</span>)</h4>
            <p>يُستخدم عند اكتشاف نسخ تالفة (بسبب الرطوبة، تمزق الغلاف، أو عيوب التصنيع). يطلب النظام تحديد:</p>
            <ul>
              <li><strong>جهة الخصم:</strong> هل التالف موجود في «مخزون المخزن الرئيسي (Store)» أم في «مخزون لجنة الأدبيات (Lit)»؟</li>
              <li><strong>الكمية التالفة</strong> و <strong>سبب التلف (إلزامي)</strong> لتوثيقه في سجل التالف.</li>
            </ul>
          </div>
        </div>

        <div class="workflow-step">
          <div class="step-number">5</div>
          <div class="step-body">
            <h4>إلغاء إدخال استلام خاطئ (إلغاء استلام / مفتاح <span class="kbd-key">W</span>)</h4>
            <p>ميزة رقابية ذكية لمعالجة أخطاء الإدخال البشري (مثال: أدخل الموظف استلام <strong>500</strong> نسخة بالخطأ بدلاً من <strong>50</strong>).</p>
          </div>
        </div>
      </div>

      <!-- UI Screenshot Mockup: Damaged & Wrong Receive Modals -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: نافذة تسجيل التالف ونافذة إلغاء الاستلام الخاطئ</span>
          <span class="ui-window-url">POST /store/{item}/damage &amp; /cancel-wrong-receive</span>
        </div>
        <div class="ui-canvas">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 1rem;">
            <!-- Modal 1: Mark Damaged -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden;">
              <div style="background: #fee2e2; color: #991b1b; padding: 0.65rem 0.9rem; font-weight: 800; display: flex; justify-content: space-between; align-items: center;">
                <span>💔 تسجيل وحدات تالفة: الكتاب الأساسي</span>
                <span class="ui-callout-pin">1</span>
              </div>
              <div style="padding: 0.9rem; font-size: 0.8rem;">
                <div style="margin-bottom: 0.6rem;">
                  <label style="font-weight: 700; display: block; margin-bottom: 0.25rem;">خصم الوحدات التالفة من: <span class="ui-callout-pin">2</span></label>
                  <div style="padding: 0.4rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; background: var(--bg-main);">🔘 مخزون المخزن الرئيسي (Store: 420) | ⚪ مخزون الأدبيات (Lit: 85)</div>
                </div>
                <div style="margin-bottom: 0.6rem;">
                  <label style="font-weight: 700; display: block; margin-bottom: 0.25rem;">عدد الوحدات التالفة:</label>
                  <div style="padding: 0.4rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-family: monospace;">2</div>
                </div>
                <div style="margin-bottom: 0.75rem;">
                  <label style="font-weight: 700; display: block; margin-bottom: 0.25rem;">سبب التلف / ملاحظة (مطلوب): <span class="ui-callout-pin">3</span></label>
                  <div style="padding: 0.45rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; color: var(--text-muted);">تلف الغلاف الخارجي أثناء النقل والشحن</div>
                </div>
                <div style="text-align: left;">
                  <span class="ui-btn danger">✓ تأكيد تسجيل التالف</span>
                </div>
              </div>
            </div>

            <!-- Modal 2: Cancel Wrong Receive -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden;">
              <div style="background: #fef3c7; color: #92400e; padding: 0.65rem 0.9rem; font-weight: 800; display: flex; justify-content: space-between; align-items: center;">
                <span>↺ إلغاء إدخال استلام خاطئ وبدء استلام جديد</span>
                <span class="ui-callout-pin">4</span>
              </div>
              <div style="padding: 0.9rem; font-size: 0.8rem;">
                <div style="background: var(--bg-main); border: 1px dashed #f59e0b; border-radius: 6px; padding: 0.55rem; margin-bottom: 0.65rem;">
                  <strong>تفاصيل آخر حركة استلام فعالة:</strong><br>
                  <span>الكمية المسجلة: <strong>500 وحدة</strong> | بواسطة: أحمد (مدير المخزن)</span>
                </div>
                <div style="margin-bottom: 0.75rem;">
                  <label style="font-weight: 700; display: block; margin-bottom: 0.25rem;">سبب الإلغاء / ملاحظة المراجعة: <span class="ui-callout-pin">5</span></label>
                  <div style="padding: 0.45rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; color: var(--text-muted);">تم إدخال 500 بدلاً من 50 بالخطأ</div>
                </div>
                <div style="text-align: left;">
                  <span class="ui-btn warning">↺ إلغاء الاستلام الخاطئ وبدء استلام جديد <span class="ui-callout-pin">6</span></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 كيف تعمل آلية التالف وإلغاء الاستلام الخاطئ؟</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> فتح نافذة التالف بالضغط على زر «تالف» أو مفتاح <span class="kbd-key">D</span>.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> تحديد مصدر الخصم بدقة (المخزن الرئيسي أو عهدة الأدبيات).</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> الملاحظة إجبارية وتظهر لاحقاً عند الضغط على شارة التالف بالجدول.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> نافذة الاستلام الخاطئ تعرض تلقائياً بيانات آخر حركة استلام نشطة للصنف.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">5</span> توثيق سبب الإلغاء لضمان الشفافية في سجل المراجعة (Audit Log).</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">6</span> يعكس الكمية الخاطئة، ويحول الحركة القديمة إلى «ملغى»، ويفتح نافذة استلام جديدة فوراً لإدخال الرقم الصحيح!</div>
          </div>
        </div>
      </div>

      <div class="alert-box alert-warning">
        <span class="alert-icon">⚠️</span>
        <div>
          <strong>شرط أمان عند إلغاء الاستلام الخاطئ:</strong><br>
          يتحقق النظام من أن الرصيد الحالي في المخزن الرئيسي يكفي لعكس كمية الاستلام الخاطئة. كما أن الحركات الملغاة (<span class="badge-code">status = cancelled</span>) تُستبعد تلقائياً من إجماليات الوارد في تقارير المستودع والدفتر الشهري حتى لا تتضخم الأرقام بالخطأ، مع بقائها مرئية بشارة <strong>«ملغى (إدخال خاطئ)»</strong> لأغراض المراجعة.
        </div>
      </div>
    `
  },

  {
    id: "store-batch-and-shortcuts",
    title: "الحركات المجمعة السريعة واختصارات لوحة المفاتيح",
    page: 8,
    category: "إدارة المخزن الرئيسي",
    tags: ["حركة مجمعة", "اختصارات", "لوحة المفاتيح", "تحديد متعدد", "إذن واحد", "سرعة"],
    content: `
      <h2>الحركات المجمعة السريعة (Batch Movements)</h2>
      <p>عند استلام شحنة كبيرة تضم 15 صنفاً مختلفاً، أو عند تحويل طلبية شهرية كاملة من المخزن الرئيسي إلى لجنة الأدبيات، لا حاجة لتنفيذ كل صنف على حدة! يوفر النظام طريقتين لتنفيذ الحركات المجمعة وإصدار <strong>إذن مخزون موحد</strong> بضغطة واحدة:</p>

      <div class="service-card">
        <h3>الطريقة الأولى: نافذة حركة المخزون المجمعة السريعة (زر <span class="kbd-key">M</span>)</h3>
        <ol>
          <li>اضغط على زر <strong>«⇄ حركة مخزون مجمعة سريعة»</strong> في أعلى شاشة المخزن (أو اضغط مفتاح <span class="kbd-key">M</span>).</li>
          <li>اختر <strong>نوع الحركة المجمعة</strong>:
            <ul>
              <li><strong>استلام مجمع في المخزن (+ Receive):</strong> لإضافة وارد جديد لعدة أصناف معاً.</li>
              <li><strong>تحويل مجمع إلى لجنة الأدبيات (→ Transfer to Lit):</strong> يُنقص المخزن ويزيد الأدبيات ويُصدر <strong>إذن تحويل واحد (<span class="badge-code">TR</span>)</strong> يجمع كل الأصناف!</li>
              <li><strong>إرجاع مجمع من الأدبيات للمخزن (← Return to Store):</strong> يُصدر <strong>إذن مرتجع واحد (<span class="badge-code">RT</span>)</strong>.</li>
            </ul>
          </li>
          <li>ابحث عن الأصناف أو تصفحها، وأدخل الكمية أمام كل صنف ترغب في تحريكه، ثم اضغط <strong>«تنفيذ الحركة المجمعة»</strong>.</li>
        </ol>
      </div>

      <div class="service-card">
        <h3>الطريقة الثانية: شريط التحديد المتعدد من الجدول (Bulk Action Bar)</h3>
        <p>حدد مربعات الاختيار (☑️) بجانب الأصناف المطلوبة في الجدول (أو تنقل بالأسهم واضغط <span class="kbd-key">Space</span>)، فيظهر لك شريط الإجراءات المجمعة لتنفيذ: <strong>استلام مجمع</strong>، <strong>تحويل مجمع</strong>، <strong>إرجاع مجمع</strong>، <strong>تعديل جماعي للأسعار/البيانات</strong>، أو <strong>حذف مجمع</strong>.</p>
      </div>

      <h2>دليل اختصارات لوحة المفاتيح الكامل (Keyboard Shortcuts)</h2>
      <p>صُممت واجهة المخزن والأدبيات لدعم العمل السريع بدون فأرة (Mouse-free Operation). اضغط <span class="kbd-key">?</span> في أي وقت داخل النظام لعرض هذا الدليل:</p>

      <div id="keyboard-shortcuts-table-container"></div>
    `
  },

  {
    id: "store-reports",
    title: "تقارير حركات المستودع والتحليلات المالية",
    page: 10,
    category: "إدارة المخزن الرئيسي",
    tags: ["تقارير", "سجل الحركات", "تصدير PDF", "تصدير CSV", "سرعة الدوران", "تقييم مالي"],
    content: `
      <h2>تقارير حركات المستودع (/store/reports)</h2>
      <p>توفر شاشة تقارير المستودع (<span class="badge-code">/store/reports</span>) سجلاً رقابياً وتحليلياً شاملاً لكل حركة تمت على المخزون، مع إمكانية التصفية المتقدمة وتصدير التقارير الرسمية بصيغتي <strong>PDF</strong> و <strong>Excel / CSV</strong>.</p>

      <!-- UI Screenshot Mockup: Store Reports -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: تقارير حركات المستودع والتحليلات</span>
          <span class="ui-window-url">GET /store/reports</span>
        </div>
        <div class="ui-canvas">
          <div class="ui-top-bar">
            <div class="ui-top-bar-title">📊 تقارير حركات المستودع</div>
            <div class="ui-btn-group">
              <span class="ui-btn danger">📄 تصدير PDF <span class="ui-callout-pin">1</span></span>
              <span class="ui-btn success">📊 تصدير CSV / Excel</span>
            </div>
          </div>

          <div class="ui-kpi-grid">
            <div class="ui-kpi-card border-primary">
              <div>
                <div class="ui-kpi-label">إجمالي الحركات <span class="ui-help-icon">؟</span></div>
                <div class="ui-kpi-val">142</div>
              </div>
              <span>🧾</span>
            </div>
            <div class="ui-kpi-card border-success">
              <div>
                <div class="ui-kpi-label">إجمالي الكميات المستلمة <span class="ui-callout-pin">2</span></div>
                <div class="ui-kpi-val" style="color: #16a34a;">+1,850</div>
              </div>
              <span>📥</span>
            </div>
            <div class="ui-kpi-card border-info">
              <div>
                <div class="ui-kpi-label">إجمالي المحول للأدبيات</div>
                <div class="ui-kpi-val" style="color: #0284c7;">920</div>
              </div>
              <span>📤</span>
            </div>
            <div class="ui-kpi-card border-warning">
              <div>
                <div class="ui-kpi-label">القيمة المالية للحركات <span class="ui-callout-pin">3</span></div>
                <div class="ui-kpi-val">124,500 ج.م</div>
              </div>
              <span>💰</span>
            </div>
          </div>

          <!-- Filter Bar Mock -->
          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.65rem; margin-bottom: 0.85rem; display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            <span class="ui-badge gray">الصنف: الكل ▾</span>
            <span class="ui-badge gray">الفئة: الكل ▾</span>
            <span class="ui-badge gray">نوع الحركة: الكل ▾</span>
            <span class="ui-badge gray">من تاريخ: 2026-10-01</span>
            <span class="ui-badge gray">إلى تاريخ: 2026-10-31</span>
            <span class="ui-btn primary" style="margin-right: auto;">تطبيق التصفية <span class="ui-callout-pin">4</span></span>
          </div>

          <div class="ui-table-card">
            <table class="ui-mock-table">
              <thead>
                <tr>
                  <th>التاريخ والوقت</th>
                  <th>الصنف</th>
                  <th>نوع الحركة</th>
                  <th>الكمية</th>
                  <th>القيمة المالية</th>
                  <th>بواسطة</th>
                  <th>الملاحظات / رقم الإذن</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2026-10-28 14:20</td>
                  <td><strong>الكتاب الأساسي</strong></td>
                  <td><span class="ui-badge cyan">تحويل للأدبيات</span></td>
                  <td><strong>50</strong></td>
                  <td>7,500.00 ج.م</td>
                  <td>أحمد (المخزن)</td>
                  <td>إذن تحويل #TR-202610-0012</td>
                </tr>
                <tr>
                  <td>2026-10-27 11:05</td>
                  <td><strong>ميدالية برونزية 1 سنة</strong></td>
                  <td><span class="ui-badge red">استلام ملغى (إدخال خاطئ)</span></td>
                  <td><del>500</del></td>
                  <td>0.00 ج.م</td>
                  <td>أحمد (المخزن)</td>
                  <td>إلغاء استلام خاطئ — تم إدخال 500 بدلاً من 50</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 مميزات شاشة تقارير المستودع:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> تصدير التقرير بنفس فلاتر البحث الحالية إلى ملف PDF رسمي أو شيت إكسل.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> إجمالي المستلم يستثني تلقائياً أي حركات استلام تم إلغاؤها كإدخال خاطئ.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> حساب القيمة المالية لكل حركة (كمية الحركة × سعر بيع الوحدة).</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> تصفية دقيقة حسب الصنف، الفئة، نوع الحركة (استلام، تحويل، مرتجع، تالف، مبيعات)، أو الفترة الزمنية.</div>
          </div>
        </div>
      </div>
    `
  },

  // =========================================================================
  // 3. الجرد الفعلي والمطابقة (Physical Stocktaking)
  // =========================================================================
  {
    id: "stocktaking-lifecycle",
    title: "الجرد الفعلي، قفل المخزن، وتسوية الفروقات",
    page: 11,
    category: "الجرد الفعلي والمطابقة",
    tags: ["جرد", "قفل المخزن", "عد فعلي", "فروقات", "تسوية", "عجز", "فائض", "مطابقة"],
    content: `
      <h2>نظام الجرد الفعلي والمطابقة (/store/stocktaking)</h2>
      <p>يوفر النظام دورة جرد فعلي محكمة تضمن مطابقة الأرصدة الدفترية المسجلة في النظام مع الأعداد الفعلية الموجودة على أرفف المخزن الرئيسي ولجنة الأدبيات، مع حساب الأثر المالي لأي عجز أو زيادة بدقة تامة.</p>

      <div class="alert-box alert-warning">
        <span class="alert-icon">🔒</span>
        <div>
          <strong>آلية القفل التلقائي للمخزن أثناء الجرد (CheckStoreLocked Middleware):</strong><br>
          بمجرد الضغط على <strong>«بدء جلسة جرد جديدة»</strong>، يلتقط النظام صورة لحظية (Snapshot) لأرصدة جميع الأصناف، ويقوم <strong>بقفل وتجميد كافة حركات المخزن الرئيسي تلقائياً</strong> (لا يمكن إضافة وارد، أو تحويل، أو إرجاع) حتى لا تتداخل الحركات اليومية مع عملية العد الفعلي. يظهر شريط تنبيه أصفر في أعلى شاشة المخزن يوضح رقم الجلسة النشطة واسم الموظف القائم بالجرد.
        </div>
      </div>

      <h3>المراحل الأربع لدورة الجرد الفعلي</h3>
      <div class="workflow-steps">
        <div class="workflow-step">
          <div class="step-number">1</div>
          <div class="step-body">
            <h4>بدء جلسة الجرد (Start Session)</h4>
            <p>من شاشة <span class="badge-code">/store/stocktaking</span>، أدخل ملاحظة الجلسة (مثال: «جرد نهاية الربع الثالث 2026») واضغط بدء الجلسة. يتم تجميد المخزن وتجهيز قائمة الجرد بجميع الأصناف.</p>
          </div>
        </div>

        <div class="workflow-step">
          <div class="step-number">2</div>
          <div class="step-body">
            <h4>إدخال العد الفعلي (Count Entry)</h4>
            <p>في شاشة العد (<span class="badge-code">/store/stocktaking/{session}/count</span>)، يقوم القائمون بالجرد بإدخال <strong>العدد الفعلي بالمخزن</strong> و <strong>العدد الفعلي بالأدبيات</strong> لكل صنف. يحسب النظام الفروقات لحظياً أثناء الكتابة.</p>
          </div>
        </div>

        <div class="workflow-step">
          <div class="step-number">3</div>
          <div class="step-body">
            <h4>إنهاء العد وإصدار تقرير المقارنة (Comparison Report)</h4>
            <p>عند اعتماد العد، تنتقل الجلسة إلى حالة «مكتمل» (<span class="badge-code">completed</span>) ويصدر تقرير المقارنة التفصيلي (<span class="badge-code">/store/stocktaking/{session}</span>) شاملاً قيمة الفروقات بالجنيه المصري.</p>
          </div>
        </div>

        <div class="workflow-step">
          <div class="step-number">4</div>
          <div class="step-body">
            <h4>تطبيق تسوية المخزون (Apply Adjustments)</h4>
            <p>بضغطة زر واحدة على <strong>«تطبيق التسويات وتحديث الأرصدة»</strong>، يقوم النظام بتحديث رصيد المخزن والأدبيات ليطابق العد الفعلي، ويسجل حركات تسوية جرد رسمية في السجل، ويفك قفل المخزن لاستئناف العمل الطبيعي.</p>
          </div>
        </div>
      </div>

      <!-- UI Screenshot Mockup: Stocktaking Count & Report -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: تقرير مقارنة الجرد الفعلي وتسوية الفروقات</span>
          <span class="ui-window-url">GET /store/stocktaking/{session}</span>
        </div>
        <div class="ui-canvas">
          <div class="ui-top-bar">
            <div class="ui-top-bar-title">
              <span>📋 تقرير مقارنة الجرد الفعلي #14</span>
              <span class="ui-badge green">تمت التسوية وتحديث الأرصدة</span>
            </div>
            <div class="ui-btn-group">
              <span class="ui-btn danger">📄 تصدير PDF <span class="ui-callout-pin">1</span></span>
              <span class="ui-btn success">📊 تصدير CSV</span>
              <span class="ui-btn primary">✓ تطبيق التسويات على المخزون <span class="ui-callout-pin">2</span></span>
            </div>
          </div>

          <div class="ui-kpi-grid">
            <div class="ui-kpi-card border-primary">
              <div>
                <div class="ui-kpi-label">إجمالي المتوقع بالنظام <span class="ui-callout-pin">3</span></div>
                <div class="ui-kpi-val">6,165</div>
              </div>
              <span>💻</span>
            </div>
            <div class="ui-kpi-card border-info">
              <div>
                <div class="ui-kpi-label">إجمالي العد الفعلي <span class="ui-callout-pin">4</span></div>
                <div class="ui-kpi-val" style="color: #0284c7;">6,162</div>
              </div>
              <span>✍️</span>
            </div>
            <div class="ui-kpi-card border-danger">
              <div>
                <div class="ui-kpi-label">صافي فرق الكمية <span class="ui-callout-pin">5</span></div>
                <div class="ui-kpi-val" style="color: #dc2626;">-3</div>
              </div>
              <span>⚖️</span>
            </div>
            <div class="ui-kpi-card border-warning">
              <div>
                <div class="ui-kpi-label">قيمة الفروقات المالية <span class="ui-callout-pin">6</span></div>
                <div class="ui-kpi-val" style="color: #dc2626;">-450.00 ج.م</div>
              </div>
              <span>💵</span>
            </div>
          </div>

          <div class="ui-table-card">
            <table class="ui-mock-table">
              <thead>
                <tr>
                  <th>الصنف</th>
                  <th>متوقع (مخزن)</th>
                  <th>معدود (مخزن)</th>
                  <th>فرق المخزن</th>
                  <th>متوقع (أدبيات)</th>
                  <th>معدود (أدبيات)</th>
                  <th>فرق الأدبيات</th>
                  <th>الأثر المالي (ج.م)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>الكتاب الأساسي</strong></td>
                  <td>420</td>
                  <td>418</td>
                  <td><span class="ui-badge red">-2 عجز</span></td>
                  <td>85</td>
                  <td>85</td>
                  <td><span class="ui-badge green">0 مطابق</span></td>
                  <td style="color: #dc2626; font-weight: 800;">-300.00 ج.م</td>
                </tr>
                <tr>
                  <td><strong>عيش صافياً (Living Clean)</strong></td>
                  <td>190</td>
                  <td>190</td>
                  <td><span class="ui-badge green">0 مطابق</span></td>
                  <td>40</td>
                  <td>39</td>
                  <td><span class="ui-badge red">-1 عجز</span></td>
                  <td style="color: #dc2626; font-weight: 800;">-150.00 ج.م</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">🧮 المعادلات المحاسبية لتقرير الجرد الفعلي:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> استخراج نسخة PDF رسمية من محضر الجرد للتوقيع والأرشفة.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> زر تطبيق التسويات يعتمد الأعداد الجديدة ويفتح المخزن للعمل.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> <strong>المتوقع بالنظام:</strong> رصيد المخزن الدفتري + رصيد الأدبيات الدفتري.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> <strong>العد الفعلي:</strong> المعدود بالمخزن + المعدود بالأدبيات.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">5</span> <strong>صافي الفرق:</strong> الكمية الفعلية المعدودة − الكمية المتوقعة بالنظام.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">6</span> <strong>قيمة الفروقات:</strong> مجموع (صافي فرق الصنف × سعر بيع الوحدة).</div>
          </div>
        </div>
      </div>
    `
  },

  // =========================================================================
  // 4. لجنة الأدبيات والدفتر الشهري والتسوية
  // =========================================================================
  {
    id: "lit-inventory-and-reconciliation",
    title: "مخزون لجنة الأدبيات وشاشة التسوية وإرجاع الفائض",
    page: 13,
    category: "لجنة الأدبيات والدفتر الشهري",
    tags: ["لجنة الأدبيات", "مخزون الأدبيات", "تسوية", "إرجاع الفائض", "مبيعات الشهر", "تالف الأدبيات"],
    content: `
      <h2>شاشة مخزون لجنة الأدبيات (/lit)</h2>
      <p>تُتيح شاشة مخزون المطبوعات (<span class="badge-code">/lit</span>) لأعضاء لجنة الأدبيات متابعة الرصيد المتاح في عهدتهم لحظياً، مع معرفة عدد النسخ المباعة من كل صنف خلال الشهر الجاري، وإمكانية تسجيل أي نسخ تالفة في عهدة اللجنة (بمفتاح <span class="kbd-key">D</span> أو زر التالف).</p>

      <h2>شاشة التسوية وإرجاع الفائض للمخزن (/lit/reconciliation)</h2>
      <p>في نهاية كل دورة بيع شهرية (أو بعد انتهاء اجتماع تسليم الأدبيات للمناطق)، تستخدم لجنة الأدبيات شاشة <strong>التسوية والإرجاع (<span class="badge-code">/lit/reconciliation</span>)</strong> لمطابقة ما استلمته من المخزن مع ما تم بيعه للمجموعات أو صرفه للجان الفرعية، وإرجاع الكميات المتبقية إلى المخزن الرئيسي بضغطة واحدة.</p>

      <!-- UI Screenshot Mockup: Lit Reconciliation -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: تسوية لجنة الأدبيات وإرجاع الفائض للمخزن</span>
          <span class="ui-window-url">GET /lit/reconciliation</span>
        </div>
        <div class="ui-canvas">
          <div class="ui-top-bar">
            <div class="ui-top-bar-title">
              <span>⇄ تسوية لجنة الأدبيات وإرجاع الفائض</span>
              <span class="ui-badge green">✓ تمت المزامنة مع الجرد الفعلي <span class="ui-callout-pin">1</span></span>
            </div>
            <div class="ui-btn-group">
              <span class="ui-btn outline">🧾 أذونات المخزون</span>
              <span class="ui-btn primary">📖 الدفتر الشهري</span>
            </div>
          </div>

          <div class="ui-kpi-grid">
            <div class="ui-kpi-card border-primary">
              <div>
                <div class="ui-kpi-label">المستلم من المخزن هذا الشهر <span class="ui-callout-pin">2</span></div>
                <div class="ui-kpi-val" style="color: #2563eb;">600</div>
              </div>
              <span>📥</span>
            </div>
            <div class="ui-kpi-card border-success">
              <div>
                <div class="ui-kpi-label">المباع للمجموعات هذا الشهر <span class="ui-callout-pin">3</span></div>
                <div class="ui-kpi-val" style="color: #16a34a;">480</div>
              </div>
              <span>🛒</span>
            </div>
            <div class="ui-kpi-card border-warning">
              <div>
                <div class="ui-kpi-label">المرتجع للمخزن هذا الشهر</div>
                <div class="ui-kpi-val" style="color: #d97706;">50</div>
              </div>
              <span>↩️</span>
            </div>
            <div class="ui-kpi-card border-info">
              <div>
                <div class="ui-kpi-label">الرصيد الحالي بالأدبيات</div>
                <div class="ui-kpi-val" style="color: #0284c7;">70</div>
              </div>
              <span>📚</span>
            </div>
          </div>

          <div class="ui-table-card">
            <table class="ui-mock-table">
              <thead>
                <tr>
                  <th>الصنف</th>
                  <th>مستلم من المخزن</th>
                  <th>مباع للمجموعات</th>
                  <th>موزع للجان</th>
                  <th>الرصيد الحالي</th>
                  <th>المقترح إرجاعه <span class="ui-callout-pin">4</span></th>
                  <th>الكمية المراد إرجاعها للمخزن <span class="ui-callout-pin">5</span></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>الكتاب الأساسي</strong></td>
                  <td><span class="ui-badge blue">+100</span></td>
                  <td><span class="ui-badge green">-75</span></td>
                  <td><span class="ui-badge cyan">-5</span></td>
                  <td><strong>20</strong></td>
                  <td><span class="ui-badge amber">20 مقترح</span></td>
                  <td>
                    <div style="padding: 0.25rem 0.5rem; border: 1px solid #2563eb; border-radius: 6px; width: 80px; font-weight: 800; text-align: center;">20</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div style="margin-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <span class="ui-btn outline">تعبئة تلقائية بالكميات المقترحة <span class="ui-callout-pin">6</span></span>
            <span class="ui-btn primary">↩️ تأكيد إرجاع الفائض للمخزن وإصدار إذن مرتجع (RT)</span>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 كيف تعمل شاشة التسوية الشهرية؟</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> يتكامل النظام تلقائياً مع آخر جلسة جرد فعلي تمت خلال الشهر.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> إجمالي ما استلمته لجنة الأدبيات عبر أذونات التحويل (TR).</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> إجمالي ما تم بيعه للمجموعات والهيئات الخدمية خلال الشهر المحدد.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> يحسب النظام تلقائياً الفائض المقترح إرجاعه = المعدود − المباع − الموزع للجان.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">5</span> إدخال الكمية المراد إرجاعها فعلياً للمخزن الرئيسي.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">6</span> عند الحفظ يتم خصم الكمية من الأدبيات وإضافتها للمخزن وإصدار إذن مرتجع رسمي (<span class="badge-code">RT</span>).</div>
          </div>
        </div>
      </div>
    `
  },

  {
    id: "monthly-ledger",
    title: "الدفتر الشهري الموحد للمخزون والمبيعات (Monthly Ledger)",
    page: 15,
    category: "لجنة الأدبيات والدفتر الشهري",
    tags: ["دفتر شهري", "ميزان المراجعة", "مبيعات", "تقييم المخزون", "مركز مالي", "تصدير PDF"],
    content: `
      <h2>الدفتر الشهري الموحد (/lit/ledger)</h2>
      <p>يُعد الدفتر الشهري (<span class="badge-code">/lit/ledger</span>) التقرير المحاسبي الأهم على مستوى الإقليم (متاح لمدير المخزن، لجنة الأدبيات، أمناء الصندوق/الإقليم RSC، والمشرف العام). يجمع هذا التقرير حركة <strong>المخزن الرئيسي (Litstore)</strong> وحركة <strong>لجنة الأدبيات (Lit)</strong> و <strong>المركز المجمع للزمالة</strong> في صفحة واحدة لكل شهر.</p>

      <!-- UI Screenshot Mockup: Monthly Ledger -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: الدفتر الشهري الموحد للمخزون والمبيعات</span>
          <span class="ui-window-url">GET /lit/ledger?month=2026-10</span>
        </div>
        <div class="ui-canvas">
          <div class="ui-top-bar">
            <div class="ui-top-bar-title">
              <span>📖 الدفتر الشهري للمخزون والأدبيات — أكتوبر 2026</span>
            </div>
            <div class="ui-btn-group">
              <span class="ui-btn outline">🧾 الأذونات</span>
              <span class="ui-btn danger">📄 تصدير الدفتر PDF <span class="ui-callout-pin">1</span></span>
              <span class="ui-btn success">📊 تصدير CSV</span>
            </div>
          </div>

          <!-- 3 Summary Pillar Cards -->
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0.85rem; margin-bottom: 1rem;">
            <!-- Pillar 1: Litstore -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-top: 4px solid #2563eb; border-radius: 10px; padding: 0.9rem;">
              <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong style="color: #2563eb;">📦 ملخص المخزن الرئيسي (Litstore)</strong>
                <span class="ui-callout-pin">2</span>
              </div>
              <div style="font-size: 0.78rem; display: flex; flex-direction: column; gap: 0.25rem;">
                <div style="display: flex; justify-content: space-between;"><span>الوارد المستلم (+):</span><strong style="color: #16a34a;">+1,200</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>المحول للأدبيات (−):</span><strong style="color: #2563eb;">-600</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>المرتجع من الأدبيات (+):</span><strong style="color: #d97706;">+50</strong></div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.25rem;"><span>الرصيد المتبقي بالمخزن:</span><strong>4,820 وحدة</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>قيمة مخزون المخزن:</span><strong style="color: #2563eb;">385,600 ج.م</strong></div>
              </div>
            </div>

            <!-- Pillar 2: Lit Committee -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-top: 4px solid #0284c7; border-radius: 10px; padding: 0.9rem;">
              <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong style="color: #0284c7;">📚 ملخص لجنة الأدبيات (Lit)</strong>
                <span class="ui-callout-pin">3</span>
              </div>
              <div style="font-size: 0.78rem; display: flex; flex-direction: column; gap: 0.25rem;">
                <div style="display: flex; justify-content: space-between;"><span>المستلم من المخزن (+):</span><strong style="color: #0284c7;">+600</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>المباع للمجموعات (−):</span><strong style="color: #16a34a;">-480</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>الموزع للجان الفرعية (−):</span><strong style="color: #7c3aed;">-35</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>المرتجع للمخزن (−):</span><strong style="color: #d97706;">-50</strong></div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.25rem;"><span>إيراد المبيعات الشهري:</span><strong style="color: #16a34a;">58,400 ج.م</strong></div>
              </div>
            </div>

            <!-- Pillar 3: Combined Position -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-top: 4px solid #16a34a; border-radius: 10px; padding: 0.9rem;">
              <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong style="color: #16a34a;">🏛️ المركز المالي المجمع للإقليم</strong>
                <span class="ui-callout-pin">4</span>
              </div>
              <div style="font-size: 0.78rem; display: flex; flex-direction: column; gap: 0.25rem;">
                <div style="display: flex; justify-content: space-between;"><span>إجمالي الوحدات (مخزن + أدبيات):</span><strong>6,165 وحدة</strong></div>
                <div style="display: flex; justify-content: space-between;"><span>إجمالي قيمة المخزون القائم:</span><strong>492,100 ج.م</strong></div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.25rem;"><span>صافي مبيعات الشهر:</span><strong style="color: #16a34a;">58,400 ج.م</strong></div>
              </div>
            </div>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 قراءة الأعمدة الثلاثة للدفتر الشهري:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> طباعة الدفتر الشهري بصيغة PDF لتقديمه في اجتماع لجنة خدمة الإقليم (RSC).</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> <strong>عمود المخزن الرئيسي:</strong> يوضح الوارد، المنصرف للأدبيات، المرتجع، والرصيد المتبقي وقيمته.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> <strong>عمود لجنة الأدبيات:</strong> يوضح المستلم، المباع للمجموعات، الموزع للجان مجاناً، وإجمالي إيراد المبيعات.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> <strong>عمود المركز المجمع:</strong> يجمع رصيد المخزن + رصيد الأدبيات لتوضيح إجمالي أصول المطبوعات بالإقليم.</div>
          </div>
        </div>
      </div>
    `
  },

  // =========================================================================
  // 5. أذونات المخزون والاستلام (Inventory Slips)
  // =========================================================================
  {
    id: "inventory-slips",
    title: "أذونات المخزون وتأكيد الاستلام (Inventory Slips)",
    page: 17,
    category: "الأذونات والاستلام",
    tags: ["أذونات", "إذن تحويل", "إذن مرتجع", "تأكيد الاستلام", "TR", "RT", "IC", "RC"],
    content: `
      <h2>منظومة أذونات المخزون الرسمية (/slips)</h2>
      <p>لا تتم أي حركة نقل للمطبوعات بين المخزن الرئيسي ولجنة الأدبيات أو بين لجنة الأدبيات واللجان الفرعية شفاهةً؛ بل يصدر النظام تلقائياً <strong>إذن مخزون رسمي مرقم تسلسلياً حسب السنة والشهر</strong>، ويمكن طباعته كـ PDF للتوقيع الفعلي أو تأكيد استلامه إلكترونياً عبر النظام.</p>

      <h3>أنواع أذونات المخزون الأربعة وبادئات الترقيم</h3>
      <div class="responsive-table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>كود البادئة</th>
              <th>مثال لرقم الإذن</th>
              <th>نوع الإذن</th>
              <th>الجهة المُصدِرة</th>
              <th>الجهة المستلمة</th>
              <th>هل يتضمن أسعار؟</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="badge-code">TR</span></td>
              <td><span class="badge-code">TR-202610-0001</span></td>
              <td><strong>إذن تحويل للأدبيات</strong> (<span class="badge-code">transfer_to_lit</span>)</td>
              <td>المخزن الرئيسي</td>
              <td>لجنة الأدبيات</td>
              <td>نعم (لتقييم العهدة)</td>
            </tr>
            <tr>
              <td><span class="badge-code">RT</span></td>
              <td><span class="badge-code">RT-202610-0001</span></td>
              <td><strong>إذن مرتجع للمخزن</strong> (<span class="badge-code">return_to_store</span>)</td>
              <td>لجنة الأدبيات</td>
              <td>المخزن الرئيسي</td>
              <td>نعم</td>
            </tr>
            <tr>
              <td><span class="badge-code">IC</span></td>
              <td><span class="badge-code">IC-202610-0001</span></td>
              <td><strong>إذن صرف للجنة فرعية</strong> (<span class="badge-code">issue_to_committee</span>)</td>
              <td>لجنة الأدبيات</td>
              <td>اللجنة الفرعية (مثل H&I / PR)</td>
              <td>لا (إذن كميات مجاني)</td>
            </tr>
            <tr>
              <td><span class="badge-code">RC</span></td>
              <td><span class="badge-code">RC-202610-0001</span></td>
              <td><strong>إذن مرتجع من لجنة فرعية</strong> (<span class="badge-code">return_from_committee</span>)</td>
              <td>اللجنة الفرعية</td>
              <td>لجنة الأدبيات</td>
              <td>لا (إذن كميات مجاني)</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- UI Screenshot Mockup: Slips & Acknowledge -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: سجل أذونات المخزون وتأكيد الاستلام الإلكتروني</span>
          <span class="ui-window-url">GET /slips &amp; /slips/{slip}</span>
        </div>
        <div class="ui-canvas">
          <div class="ui-kpi-grid">
            <div class="ui-kpi-card border-primary">
              <div>
                <div class="ui-kpi-label">إجمالي الأذونات</div>
                <div class="ui-kpi-val">68</div>
              </div>
              <span>🧾</span>
            </div>
            <div class="ui-kpi-card border-info">
              <div>
                <div class="ui-kpi-label">أذونات تحويل (TR)</div>
                <div class="ui-kpi-val">42</div>
              </div>
              <span>➡️</span>
            </div>
            <div class="ui-kpi-card border-success">
              <div>
                <div class="ui-kpi-label">أذونات مرتجع (RT)</div>
                <div class="ui-kpi-val">19</div>
              </div>
              <span>⬅️</span>
            </div>
            <div class="ui-kpi-card border-warning">
              <div>
                <div class="ui-kpi-label">بانتظار تأكيد الاستلام <span class="ui-callout-pin">1</span></div>
                <div class="ui-kpi-val">2</div>
              </div>
              <span>⏳</span>
            </div>
          </div>

          <div class="ui-table-card">
            <table class="ui-mock-table">
              <thead>
                <tr>
                  <th>رقم الإذن</th>
                  <th>النوع</th>
                  <th>الحالة</th>
                  <th>إجمالي الوحدات</th>
                  <th>القيمة التقديرية</th>
                  <th>صادر بواسطة</th>
                  <th>مستلم بواسطة</th>
                  <th>الإجراءات</th>
                </tr>
              </thead>
              <tbody>
                <tr class="focused-row">
                  <td><span class="badge-code">TR-202610-0012</span></td>
                  <td><span class="ui-badge cyan">صادر للأدبيات</span></td>
                  <td><span class="ui-badge amber">⏳ تم التحويل (بانتظار الاستلام)</span></td>
                  <td><strong>150 وحدة</strong></td>
                  <td>18,500.00 ج.م</td>
                  <td>أحمد (مدير المخزن)</td>
                  <td>—</td>
                  <td>
                    <div class="ui-btn-group">
                      <span class="ui-btn success" style="padding: 0.2rem 0.55rem; font-size: 0.7rem;">✓ تأكيد الاستلام <span class="ui-callout-pin">2</span></span>
                      <span class="ui-btn outline" style="padding: 0.2rem 0.55rem; font-size: 0.7rem;">👁️ عرض</span>
                      <span class="ui-btn danger" style="padding: 0.2rem 0.55rem; font-size: 0.7rem;">PDF <span class="ui-callout-pin">3</span></span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td><span class="badge-code">RT-202610-0004</span></td>
                  <td><span class="ui-badge green">مرتجع للمخزن</span></td>
                  <td><span class="ui-badge green">✓ مكتمل ومستلم</span></td>
                  <td><strong>45 وحدة</strong></td>
                  <td>5,400.00 ج.م</td>
                  <td>كريم (لجنة الأدبيات)</td>
                  <td>أحمد (2026-10-25 18:10)</td>
                  <td>
                    <div class="ui-btn-group">
                      <span class="ui-btn outline" style="padding: 0.2rem 0.55rem; font-size: 0.7rem;">👁️ عرض</span>
                      <span class="ui-btn danger" style="padding: 0.2rem 0.55rem; font-size: 0.7rem;">PDF</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 دورة اعتماد إذن المخزون:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> يبرز النظام عدد الأذونات التي حوّلها المخزن ولم تؤكد لجنة الأدبيات استلامها بعد.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> يضغط مسؤول لجنة الأدبيات على <strong>«تأكيد الاستلام»</strong> فيسجل النظام اسمه وتوقيت الاستلام وتتحول الحالة إلى «مستلم».</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> إمكانية تحميل الإذن كملف PDF منسق للطباعة والتوقيع اليدوي عند التسليم الفعلي.</div>
          </div>
        </div>
      </div>
    `
  },

  // =========================================================================
  // 6. طلبات المجموعات وأمناء الصندوق (Groups & Treasurers)
  // =========================================================================
  {
    id: "group-treasurer-requests",
    title: "دورة طلبات المجموعات، أمين الصندوق، وتنفيذ لجنة الأدبيات",
    page: 19,
    category: "طلبات المجموعات وأمناء الصندوق",
    tags: ["طلبات المجموعات", "سلة المطبوعات", "مندوب المجموعة", "GSR", "أمين الصندوق", "يوم 19", "فاتورة مجمعة"],
    content: `
      <h2>دورة طلبات الأدبيات الشهرية للمجموعات والمناطق</h2>
      <p>يدير النظام دورة شراء الأدبيات الشهرية للمجموعات عبر 3 مراحل مترابطة تبدأ من <strong>مندوب المجموعة (GSR)</strong>، وتمر عبر <strong>أمين صندوق الهيئة الخدمية (Treasurer)</strong>، وتنتهي بالتنفيذ والخصم من المخزون لدى <strong>لجنة الأدبيات</strong>.</p>

      <div class="alert-box alert-danger">
        <span class="alert-icon">📅</span>
        <div>
          <strong>قاعدة الإغلاق التلقائي يوم 19 في الشهر (Day 19 Cutoff Rule):</strong><br>
          <ul>
            <li><strong>من يوم 1 إلى يوم 19 في الشهر:</strong> يستطيع مندوب المجموعة (GSR) فتح سلة المطبوعات (<span class="badge-code">/literature-requests/cart</span>)، تعديل الكميات، وإرسال الطلب.</li>
            <li><strong>بعد يوم 19 في الشهر:</strong> تُغلق السلة تلقائياً أمام مندوبي المجموعات (GSR)، بينما <strong>يظل التعديل متاحاً لأمين صندوق المنطقة (Treasurer)</strong> لمراجعة الطلبات وتعديلها أو فتح طلب مجموعة معينة، وذلك طالما لم يتم إرسال الفاتورة المجمعة النهائية إلى لجنة الأدبيات.</li>
            <li><strong>بمجرد ضغط أمين الصندوق على «اعتماد وإرسال للجنة الأدبيات»:</strong> تُقفل طلبات الشهر بالكامل لتلك المنطقة وتنتقل لعهدة لجنة الأدبيات للتنفيذ.</li>
          </ul>
        </div>
      </div>

      <!-- UI Screenshot Mockup: GSR Cart & Treasurer Dashboard -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: (أ) سلة مندوب المجموعة GSR — (ب) لوحة أمين صندوق المنطقة</span>
          <span class="ui-window-url">/literature-requests/cart &amp; /treasurer</span>
        </div>
        <div class="ui-canvas">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <!-- Screen A: GSR Cart -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 0.9rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 0.65rem;">
                <strong style="color: var(--primary-color);">🛒 (أ) سلة طلبات المجموعة (GSR)</strong>
                <span class="ui-badge green">مفتوح حتى يوم 19 <span class="ui-callout-pin">1</span></span>
              </div>
              <table class="ui-mock-table" style="margin-bottom: 0.65rem;">
                <thead>
                  <tr>
                    <th>الصنف</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>الإجمالي</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>الكتاب الأساسي</td>
                    <td>150 ج.م</td>
                    <td><strong>5</strong></td>
                    <td>750 ج.م</td>
                  </tr>
                  <tr>
                    <td>ميدالية ترحيب (Just for Today)</td>
                    <td>25 ج.م</td>
                    <td><strong>20</strong></td>
                    <td>500 ج.م</td>
                  </tr>
                </tbody>
              </table>
              <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-main); padding: 0.5rem 0.75rem; border-radius: 8px;">
                <span>الإجمالي: <strong>1,250.00 ج.م</strong></span>
                <div class="ui-btn-group">
                  <span class="ui-btn outline">حفظ مسودة</span>
                  <span class="ui-btn primary">✓ إرسال لأمين الصندوق <span class="ui-callout-pin">2</span></span>
                </div>
              </div>
            </div>

            <!-- Screen B: Treasurer Dashboard -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 0.9rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 0.65rem;">
                <strong style="color: var(--primary-color);">💼 (ب) لوحة أمين الصندوق (Treasurer)</strong>
                <span class="ui-callout-pin">3</span>
              </div>
              <div style="font-size: 0.78rem; margin-bottom: 0.5rem; color: var(--text-muted);">طلبات المجموعات المرسلة هذا الشهر (تُجمع تلقائياً في فاتورة المنطقة):</div>
              <table class="ui-mock-table" style="margin-bottom: 0.65rem;">
                <thead>
                  <tr>
                    <th>المجموعة</th>
                    <th>الحالة</th>
                    <th>القيمة</th>
                    <th>تحكم</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>مجموعة الحرية</td>
                    <td><span class="ui-badge green">مُرسل</span></td>
                    <td>1,250 ج.م</td>
                    <td><span class="ui-badge blue">تعديل / إرجاع مسودة <span class="ui-callout-pin">4</span></span></td>
                  </tr>
                  <tr>
                    <td>مجموعة الأمل</td>
                    <td><span class="ui-badge green">مُرسل</span></td>
                    <td>2,100 ج.م</td>
                    <td><span class="ui-badge blue">تعديل / PDF</span></td>
                  </tr>
                </tbody>
              </table>
              <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 0.6rem 0.75rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.4rem;">
                <span style="font-size: 0.78rem; color: #1e40af;"><strong>الفاتورة المجمعة للمنطقة:</strong> 3,350.00 ج.م</span>
                <span class="ui-btn success">🚀 اعتماد وإرسال للجنة الأدبيات <span class="ui-callout-pin">5</span></span>
              </div>
            </div>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 خطوات العمل بين المجموعات وأمين الصندوق ولجنة الأدبيات:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> يملأ مندوب المجموعة (GSR) السلة ويحفظها كمسودة أو يرسلها قبل نهاية يوم 19.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> عند ضغط «إرسال لأمين الصندوق» تتحول حالة طلب المجموعة إلى <span class="badge-code">submitted</span> وتدخل في الفاتورة المجمعة.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> يرى أمين الصندوق كافة طلبات مجموعات منطقته مجمعة آلياً مع إجمالي الوحدات والمبلغ المطلوب تحصيله.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> يستطيع أمين الصندوق تعديل طلب أي مجموعة أو إعادته لحالة المسودة (<span class="badge-code">draft</span>) لتمكين المجموعة من تعديله.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">5</span> عند ضغط أمين الصندوق على «اعتماد وإرسال للجنة الأدبيات»، تنتقل الفاتورة المجمعة لشاشة لجنة الأدبيات (<span class="badge-code">/literature-requests/committee</span>).</div>
          </div>
        </div>
      </div>

      <h3>المرحلة الثالثة: مراجعة وتنفيذ الطلب لدى لجنة الأدبيات (/literature-requests/committee)</h3>
      <div class="service-card">
        <p>عند وصول الفاتورة المجمعة إلى شاشة لجنة الأدبيات (<span class="badge-code">/literature-requests/committee</span>):</p>
        <ol>
          <li>يضغط مسؤول لجنة الأدبيات على زر <strong>«مراجعة وتعديل الكميات المتاحة»</strong>.</li>
          <li>يقارن النظام تلقائياً الكمية المطلوبة من كل صنف مع <strong>الرصيد المتاح حالياً في عهدة لجنة الأدبيات (<span class="badge-code">lit_quantity</span>)</strong>، وينبه بشارة حمراء <strong>«يتجاوز الرصيد»</strong> إذا كان المطلوب أكبر من المتاح.</li>
          <li>عند اعتماد وصرف الطلب، يقوم النظام آلياً بـ <strong>خصم الكميات المنفذة من رصيد لجنة الأدبيات</strong>، وتسجيل المبيعات في الدفتر الشهري، وإصدار فاتورة PDF رسمية قابلة للطباعة والأرشفة في أرشيف الطلبات (<span class="badge-code">/literature-requests/archive</span>).</li>
        </ol>
      </div>
    `
  },

  // =========================================================================
  // 7. طلبات اللجان الفرعية (Committee Literature - Non-Invoice)
  // =========================================================================
  {
    id: "committee-literature",
    title: "مطبوعات اللجان الفرعية وأذونات الصرف والمرتجع (بدون فواتير)",
    page: 21,
    category: "مطبوعات اللجان الفرعية",
    tags: ["لجان فرعية", "مطبوعات اللجان", "H&I", "PR", "بدون فواتير", "إذن صرف", "إرجاع المتبقي", "IC", "RC"],
    content: `
      <h2>بوابة مطبوعات اللجان الفرعية (/committee-literature)</h2>
      <p>تحتاج اللجان الخدمية الفرعية (مثل لجنة المستشفيات والمؤسسات <strong>H&I</strong> أو لجنة العلاقات العامة <strong>PR</strong>) إلى مطبوعات ومطويات لتوزيعها مجاناً في الفعاليات والمؤسسات العلاجية. ولهذا الغرض، يوفر النظام مساراً خاصاً مستقلاً عن فواتير المبيعات النقدية يُسمى <strong>«مطبوعات اللجان الفرعية وأذونات الصرف غير النقدية»</strong>.</p>

      <div class="alert-box alert-success">
        <span class="alert-icon">🏛️</span>
        <div>
          <strong>الخصائص المحاسبية لطلبات اللجان الفرعية:</strong>
          <ul>
            <li><strong>أذونات كميات فقط (Quantity-Only Slips):</strong> لا تُنشئ فواتير مالية ولا تدخل ضمن إيرادات المبيعات النقدية، بل تظهر في بند مستقل بالدفتر الشهري باسم <strong>«الموزع على اللجان الفرعية»</strong>.</li>
            <li><strong>الخصم والإرجاع من رصيد الأدبيات حصرياً:</strong> يتم صرفها من رصيد لجنة الأدبيات (<span class="badge-code">lit_quantity</span>) بإذن صرف (<span class="badge-code">IC</span>)، وعند إرجاع الفائض بعد انتهاء الفعالية يعود الرصيد إلى لجنة الأدبيات بإذن مرتجع (<span class="badge-code">RC</span>).</li>
          </ul>
        </div>
      </div>

      <!-- UI Screenshot Mockup: Committee Literature Portal -->
      <div class="ui-screenshot-figure">
        <div class="ui-window-bar">
          <div class="ui-window-dots">
            <span class="ui-dot red"></span>
            <span class="ui-dot yellow"></span>
            <span class="ui-dot green"></span>
          </div>
          <span class="ui-window-title">لقطة شاشة: دورة طلب وصرف وإرجاع مطبوعات اللجان الفرعية</span>
          <span class="ui-window-url">GET /committee-literature/{id}</span>
        </div>
        <div class="ui-canvas">
          <div class="ui-top-bar">
            <div class="ui-top-bar-title">
              <span>📦 طلب مطبوعات لجنة المستشفيات والمؤسسات (H&I) — طلب #45</span>
              <span class="ui-badge cyan">إذن كميات (بدون فاتورة مالية)</span>
            </div>
            <div class="ui-btn-group">
              <span class="ui-btn primary">📤 إصدار إذن صرف للجنة (IC) <span class="ui-callout-pin">1</span></span>
              <span class="ui-btn success">✓ تأكيد استلام اللجنة <span class="ui-callout-pin">2</span></span>
              <span class="ui-btn warning">↩️ تسجيل مرتجع فائض من اللجنة (RC) <span class="ui-callout-pin">3</span></span>
            </div>
          </div>

          <div class="ui-table-card">
            <table class="ui-mock-table">
              <thead>
                <tr>
                  <th>الصنف المطلوب</th>
                  <th>الكمية المطلوبة</th>
                  <th>المنصرف الفعلي (إذن IC)</th>
                  <th>المرتجع بعد الفعالية (إذن RC)</th>
                  <th>صافي الموزع الفعلي <span class="ui-callout-pin">4</span></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>مطوية: من هو المدمن؟ وما هي زمالة المدمنين المجهولين؟</strong></td>
                  <td>100</td>
                  <td><span class="ui-badge blue">100 وحدة (IC-202610-0003)</span></td>
                  <td><span class="ui-badge amber">15 وحدة (RC-202610-0001)</span></td>
                  <td><strong style="color: #16a34a;">85 وحدة</strong></td>
                </tr>
                <tr>
                  <td><strong>الكتاب الأساسي (نسخ مكتبات المستشفيات)</strong></td>
                  <td>10</td>
                  <td><span class="ui-badge blue">10 وحدات (IC-202610-0003)</span></td>
                  <td><span class="ui-badge gray">0</span></td>
                  <td><strong style="color: #16a34a;">10 وحدات</strong></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="ui-caption-legend">
          <div class="ui-caption-title">📌 الخطوات الأربع لدورة مطبوعات اللجان الفرعية:</div>
          <div class="ui-callouts-grid">
            <div class="ui-callout-item"><span class="ui-callout-pin">1</span> بعد تقديم اللجنة لطلبها، يقوم مسؤول الأدبيات بإصدار <strong>إذن صرف (<span class="badge-code">IC</span>)</strong> فيُخصم العدد فوراً من رصيد الأدبيات.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">2</span> يضغط رئيس اللجنة الفرعية على <strong>«تأكيد الاستلام»</strong> لتوثيق استلامه الفعلي للمطبوعات.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">3</span> إذا تبقى فائض لدى اللجنة بعد النشاط، يتم فتح شاشة المرتجع وإصدار <strong>إذن مرتجع لجنة (<span class="badge-code">RC</span>)</strong> يعيد الكمية لرصيد الأدبيات.</div>
            <div class="ui-callout-item"><span class="ui-callout-pin">4</span> <strong>صافي الموزع للجان</strong> في الدفتر الشهري = إجمالي المنصرف بإذن (IC) − إجمالي المرتجع بإذن (RC).</div>
          </div>
        </div>
      </div>
    `
  },

  // =========================================================================
  // 8. قاموس المصطلحات والأسئلة الشائعة واستكشاف الأخطاء
  // =========================================================================
  {
    id: "glossary-and-faq",
    title: "قاموس المصطلحات، الأكواد، والأسئلة الشائعة (FAQ)",
    page: 23,
    category: "المراجع والأسئلة الشائعة",
    tags: ["قاموس", "مصطلحات", "أسئلة شائعة", "حل المشكلات", "أخطاء", "دعم فني"],
    content: `
      <h2>قاموس المصطلحات والأكواد المرجعية في النظام</h2>
      <p>يوضح الجدول التالي كافة المصطلحات الفنية والأكواد المختصرة المستخدمة في شاشات وتقارير نظام المخزون والأدبيات:</p>

      <div id="glossary-table-container"></div>

      <h2>الأسئلة الشائعة واستكشاف الأخطاء وإصلاحها (Troubleshooting & FAQ)</h2>

      <div class="rule-card">
        <h4>❓ س1: أزرار الاستلام والتحويل والحركة المجمعة في المخزن الرئيسي غير مفعلة (Disabled)، ما السبب؟</h4>
        <p><strong>الإجابة:</strong> السبب هو وجود <strong>جلسة جرد فعلي نشطة قيد التنفيذ</strong> (<span class="badge-code">in_progress</span>). يقوم النظام بقفل المخزن الرئيسي تلقائياً أثناء الجرد لمنع تضارب الأرصدة. توجه إلى شاشة الجرد الفعلي (<span class="badge-code">/store/stocktaking</span>)، أكمل إدخال العد واعتمد التسوية ليتم فتح المخزن فوراً.</p>
      </div>

      <div class="rule-card">
        <h4>❓ س2: أدخلت كمية استلام بالخطأ في المخزن الرئيسي (مثلاً 1000 بدلاً من 100)، كيف أصحح الخطأ بدون الإخلال بالتقارير؟</h4>
        <p><strong>الإجابة:</strong> لا تقم بتحويل الفرق وهمياً! اضغط على زر <strong>«إلغاء استلام»</strong> (أو مفتاح <span class="kbd-key">W</span>) أمام الصنف، واكتب سبب الإلغاء. سيقوم النظام بعكس الـ 1000 وحدة، ووضع علامة «ملغى (إدخال خاطئ)» على الحركة القديمة لاستبعادها من إجمالي الوارد، ويفتح لك نافذة جديدة لإدخال الـ 100 الصحيحة.</p>
      </div>

      <div class="rule-card">
        <h4>❓ س3: مندوب المجموعة (GSR) يشتكي من عدم قدرته على تعديل سلة المطبوعات، ماذا نفعل؟</h4>
        <p><strong>الإجابة:</strong> تحقق من سببين:
          <br>1. <strong>تجاوز يوم 19 في الشهر:</strong> بعد يوم 19 يُغلق التعديل أمام مندوبي المجموعات، ولكن يستطيع <strong>أمين صندوق المنطقة (Treasurer)</strong> الدخول من لوحته (<span class="badge-code">/literature-requests/treasurer</span>) وتعديل طلب المجموعة نيابة عنها.
          <br>2. <strong>حالة الطلب «مُرسل»:</strong> إذا كان المندوب قد ضغط «إرسال لأمين الصندوق» وأراد التعديل قبل يوم 19، يمكن لأمين الصندوق الضغط على زر <strong>«إرجاع لمسودة»</strong> ليتمكن المندوب من التعديل مرة أخرى.
        </p>
      </div>

      <div class="rule-card">
        <h4>❓ س4: كيف نميز بين الكتب التالفة الموجودة في المخزن الرئيسي والتالفة الموجودة لدى لجنة الأدبيات؟</h4>
        <p><strong>الإجابة:</strong> عند تسجيل التالف، يحدد المستخدم مصدر الخصم (<span class="badge-code">store</span> أو <span class="badge-code">lit</span>)، فيقوم النظام بخصم العدد من الرصيد المحدد بدقة، ويوثق المصدر والملاحظة واسم الموظف في نافذة سجل التالف التي تظهر عند النقر على شارة التالف الحمراء.</p>
      </div>

      <div class="study-box">
        <h3>🖨️ إرشادات الطباعة الورقية واستخراج ملف PDF</h3>
        <p>لطباعة هذا الدليل ورقياً أو حفظه كملف PDF كامل على جهازك أو هاتفك:</p>
        <ol>
          <li>اضغط على زر <strong>«📚 طباعة الدليل الكامل»</strong> في أعلى الصفحة لطباعة جميع الفصول متسلسلة مع صفحة الغلاف والفهرس وفواصل الصفحات الأوتوماتيكية.</li>
          <li>أو اضغط على زر <strong>«🖨️ طباعة القسم»</strong> لطباعة الفصل المعروض أمامك حالياً فقط.</li>
          <li>في نافذة الطباعة الخاصة بالمتصفح، اختر الوجهة <strong>Save as PDF (حفظ بتنسيق PDF)</strong>، وتأكد من ضبط حجم الورق على <strong>A4</strong> وتفعيل خيار <strong>Background graphics (رسومات الخلفية)</strong> للحصول على أفضل جودة للألوان والجداول واللقطات التوضيحية.</li>
        </ol>
      </div>
    `
  }
];

