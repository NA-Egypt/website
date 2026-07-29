<x-frontend.layout :title="app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy'" :description="app()->getLocale() === 'ar' ? 'سياسة الخصوصية الخاصة بموقع زمالة المدمنين المجهولين في مصر' : 'Privacy Policy for Narcotics Anonymous Egypt website'">
    <x-section-head>{{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</x-section-head>

    <div class="container my-5" style="max-width: 900px; line-height: 1.8;">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            @if(app()->getLocale() === 'ar')
                <h2 class="h4 text-primary mb-3">حماية الخصوصية والمعلومات</h2>
                <p>تلتزم زمالة المدمنين المجهولين في مصر بحماية خصوصية جميع زوار الموقع والخدمات التابعة لها. نحترم السرية والخصوصية والمجهولية كأحد أهم مبادئ الزمالة.</p>
                
                <h3 class="h5 mt-4 text-secondary">البيانات التي نجمعها</h3>
                <p>لا يقوم هذا الموقع بجمع بيانات شخصية تعبيرية أو تتبع هوية الزوار دون موافقتهم الصريحة. قد يتم استخدام كوكيز (Cookies) أساسية فقط لتوفير التصفح وسلاسة التجربة.</p>

                <h3 class="h5 mt-4 text-secondary">التواصل معنا</h3>
                <p>أي معلومات يتم تقديمها من خلال نماذج الاتصال أو الخط الساخن تعامل بسرية تامة ولا يتم مشاركتها مع أي جهة خارجية.</p>
            @else
                <h2 class="h4 text-primary mb-3">Privacy & Information Protection</h2>
                <p>Narcotics Anonymous Egypt is committed to protecting the privacy of all website visitors. Confidentiality and personal anonymity are core principles of our fellowship.</p>

                <h3 class="h5 mt-4 text-secondary">Information We Collect</h3>
                <p>This website does not collect personally identifiable information without explicit consent. Only essential cookies may be used to ensure technical site functionality and usability.</p>

                <h3 class="h5 mt-4 text-secondary">Contact & Communications</h3>
                <p>Any information submitted via contact forms or hotlines is treated with strict confidentiality and is never shared with third parties.</p>
            @endif
        </div>
    </div>
</x-frontend.layout>
