<x-frontend.layout>
    <x-section-head>{{ __('messages.fdcomm') }}</x-section-head>
    <style>
        body {
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        header p {
            font-size: 1.1em;
            color: #555;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border-right: 5px solid #0056b3;
        }
        .section h2 {
            color: #0056b3;
            margin-top: 0;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 10px;
            font-size: 1.5em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        input[type="text"], input[type="date"], select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
        }
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .checkbox-grid label {
            font-weight: normal;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.2s;
        }
        .checkbox-grid label:hover {
            background-color: #eef;
        }
        .checkbox-grid input[type="checkbox"] {
            margin-left: 10px;
            width: auto;
        }
        .note {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
        }
        button {
            background-color: #0056b3;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #003d7a;
        }
        .hidden {
            display: none;
            margin-top: 15px;
            padding: 10px;
            border: 1px dashed #0056b3;
            border-radius: 5px;
            background-color: #e6f0ff;
        }
        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>

<div class="container">
    <header>
        <h1>استبيان الأيام التعليمية</h1>
        <p>لجنة الدعم والتطوير - للسنة الخدمية 2025 - 2026</p>
        <div class="note">
            الهدف هو تحديد موضوعات الأيام التعليمية طبق لاحتياجات الأعضاء والمجموعات في كل منطقة على حدة. هذه الفعاليات تقوي مجموعاتنا وتُساعدنا على إيجاد حلول وأفكار جديدة.
        </div>
    </header>

    <form id="educationSurveyForm" action="https://form.taxi/s/noaxqol0" method="POST">
        <input type="hidden" name="_gotcha" style="display:none !important">
        <div class="section">
            <h2> بيانات المشارك</h2>
            <div class="note">زمالة المدمنين المجهولين لا تحتفظ بأي بيانات لأعضائها</div>
            <div class="form-group">
                <label for="firstName">الاسم الأول:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>

            <div class="form-group">
                <label for="sobrietyDate">تاريخ الامتناع (يوم - شهر - سنة):</label>
                <input type="date" id="sobrietyDate" name="sobrietyDate" placeholder="يوم - شهر - سنة" required>
            </div>

            <div class="form-group">
                <label for="groupNameSelect">اسم المجموعة:</label>
                <select class="select2" id="groupNameSelect" name="groupNameSelect" onchange="toggleGroupFields()" required>
                    <option value="">اختر من القائمة...</option>
                    <option value="other_group">مجموعة غير مدرجة على الجدول</option>
                    <option value="isolated_member">عضو منعزل</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->ar_name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="otherGroupField" class="hidden">
                <div class="form-group">
                    <label for="otherGroupName">اكتب اسم المجموعة:</label>
                    <input type="text" id="otherGroupName" name="otherGroupName">
                </div>
                <div class="form-group">
                    <label for="groupLocation">أين تقع المجموعة؟</label>
                    <select id="groupLocation" name="groupLocation">
                        <option value="">اختر المحافظة...</option>
                        <option value="القاهرة">القاهرة</option>
                        <option value="الإسكندرية">الإسكندرية</option>
                        <option value="الجيزة">الجيزة</option>
                        <option value="القاهرة">القاهرة</option>
                        <option value="الإسماعيلية">الإسماعيلية</option>
                        <option value="الأقصر">الأقصر</option>
                        <option value="البحر الأحمر">البحر الأحمر</option>
                        <option value="البحيرة">البحيرة</option>
                        <option value="الدقهلية">الدقهلية</option>
                        <option value="السويس">السويس</option>
                        <option value="الشرقية">الشرقية</option>
                        <option value="الغربية">الغربية</option>
                        <option value="الفيوم">الفيوم</option>
                        <option value="القليوبية">القليوبية</option>
                        <option value="المنوفية">المنوفية</option>
                        <option value="المنيا">المنيا</option>
                        <option value="الوادي الجديد">الوادي الجديد</option>
                        <option value="أسوان">أسوان</option>
                        <option value="أسيوط">أسيوط</option>
                        <option value="بني سويف">بني سويف</option>
                        <option value="بور سعيد">بور سعيد</option>
                        <option value="جنوب سيناء">جنوب سيناء</option>
                        <option value="دمياط">دمياط</option>
                        <option value="سوهاج">سوهاج</option>
                        <option value="شمال سيناء">شمال سيناء</option>
                        <option value="قنا">قنا</option>
                        <option value="كفر الشيخ">كفر الشيخ</option>
                        <option value="مطروح">مطروح</option>
                    </select>
                </div>
            </div>

            <div id="isolatedMemberField" class="hidden">
                <div class="form-group">
                    <label for="isolationReason">ما هو سبب الانعزال؟</label>
                    <select id="isolationReason" class="select2" name="isolationReason" onchange="toggleOtherReason()">
                        <option value="جغرافي">جغرافي</option>
                        <option value="صحي">صحي</option>
                        <option value="اجتماعي">اجتماعي</option>
                        <option value="other">أسباب أخرى</option>
                    </select>
                </div>
                <div id="otherReasonText" class="hidden">
                    <label for="otherReason">وضح سبب الانعزال الآخر:</label>
                    <input type="text" id="otherReason" name="otherReason">
                </div>
                <div class="form-group">
                    <label for="memberGovernorate">اسم المحافظة:</label>
                    <select id="memberGovernorate" class="select2" name="memberGovernorate">
                        <option value="">اختر المحافظة...</option>
                        <option value="القاهرة">القاهرة</option>
                        <option value="الإسكندرية">الإسكندرية</option>
                        <option value="الإسماعيلية">الإسماعيلية</option>
                        <option value="الأقصر">الأقصر</option>
                        <option value="البحر الأحمر">البحر الأحمر</option>
                        <option value="البحيرة">البحيرة</option>
                        <option value="الجيزة">الجيزة</option>
                        <option value="الدقهلية">الدقهلية</option>
                        <option value="السويس">السويس</option>
                        <option value="الشرقية">الشرقية</option>
                        <option value="الغربية">الغربية</option>
                        <option value="الفيوم">الفيوم</option>
                        <option value="القاهرة">القاهرة</option>
                        <option value="القليوبية">القليوبية</option>
                        <option value="المنوفية">المنوفية</option>
                        <option value="المنيا">المنيا</option>
                        <option value="الوادي الجديد">الوادي الجديد</option>
                        <option value="أسوان">أسوان</option>
                        <option value="أسيوط">أسيوط</option>
                        <option value="بني سويف">بني سويف</option>
                        <option value="بور سعيد">بور سعيد</option>
                        <option value="جنوب سيناء">جنوب سيناء</option>
                        <option value="دمياط">دمياط</option>
                        <option value="سوهاج">سوهاج</option>
                        <option value="شمال سيناء">شمال سيناء</option>
                        <option value="قنا">قنا</option>
                        <option value="كفر الشيخ">كفر الشيخ</option>
                        <option value="مطروح">مطروح</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>الخدمة الحالية</h2>
            <div class="form-group">
                <label for="serviceLevel">هل تقوم بالخدمة؟</label>
                <select id="serviceLevel" class="select2" name="serviceLevel" onchange="toggleServiceFields()">
                    <option value="لا">لا</option>
                    <option value="group">مجموعة</option>
                    <option value="forum">منتدى دعم مجموعات / لجنة خدمة منطقة</option>
                    <option value="committee">لجنة خدمية</option>
                    <option value="regional">لجنة خدمة الإقليم</option>
                </select>
            </div>

            <div id="serviceDetailsField" class="hidden">
                <label id="serviceDetailsLabel" for="serviceDetailsSelect">التفاصيل:</label>
                <select class="select2" id="serviceDetailsSelect" name="serviceDetailsSelect">
                    </select>
            </div>
        </div>

        <div class="section">
            <h2>اختيار الموضوعات للأيام التعليمية</h2>
            <div class="note">
                <p><strong>مهم:</strong> يحق لك اختيار **أربع موضوعات بحد أقصى**، على أن يكون الاختيار موزعاً على **الأربع محاور** (بواقع موضوع واحد من كل محور).</p>
                <p id="totalSelectionCount" class="error-message">الموضوعات المختارة: 0/4</p>
                <p id="axisError" class="error-message"></p>
            </div>

            <div class="section" id="axis1">
                <h2>المحور الأول: الروابط التي تربطنا</h2>
                <p>يهدف لتقوية جسور التواصل وتفعيل مبادئ الوحدة والمجهولية والخدمة والرسالة.</p>
                <div class="checkbox-grid">
                    <label><input type="checkbox" name="axis1_topic" value="الوحدة"> الوحدة</label>
                    <label><input type="checkbox" name="axis1_topic" value="المجهولية"> المجهولية </label>
                    <label><input type="checkbox" name="axis1_topic" value="الخدمة"> الخدمة</label>
                    <label><input type="checkbox" name="axis1_topic" value="التواصل"> التواصل</label>
                    <label><input type="checkbox" name="axis1_topic" value="جو التعافي"> جو التعافي </label>
                    <label><input type="checkbox" name="axis1_topic" value="الرسالة"> الرسالة </label>
                    <label><input type="checkbox" name="axis1_topic" value="العضو الجديد"> العضو الجديد </label>
                    <label><input type="checkbox" name="axis1_topic" value="لغة الزمالة"> لغة الزمالة </label>
                    <label><input type="checkbox" name="axis1_topic" value="التقاليد"> التقاليد </label>
                    <label><input type="checkbox" name="axis1_topic" value="المفاهيم"> المفاهيم </label>
                    <label><input type="checkbox" name="axis1_topic" value="اتخاذ القرار القائم على الاجماع"> اتخاذ القرار القائم على الإجماع </label>
                    <label><input type="checkbox" name="axis1_topic" value="الاحترام في البيئة الخدمية"> الاحترام في البيئة الخدمية </label>
                </div>
                <p class="error-message" id="error_axis1"></p>
            </div>

            <div class="section" id="axis2">
                <h2>المحور الثاني: المسافات التي تفصلنا</h2>
                <p>يُعالج تحديات الأعضاء والمجموعات في المناطق النائية أو المنعزلة أو حديثة النشأة.</p>
                <div class="checkbox-grid">
                    <label><input type="checkbox" name="axis2_topic" value="زمالة عالمية"> زمالة عالمية </label>
                    <label><input type="checkbox" name="axis2_topic" value="وضوح الرسالة"> وضوح الرسالة </label>
                    <label><input type="checkbox" name="axis2_topic" value="نشأة الزمالة"> نشأة الزمالة </label>
                    <label><input type="checkbox" name="axis2_topic" value="الهيكل الخدمي"> الهيكل الخدمي </label>
                    <label><input type="checkbox" name="axis2_topic" value="الاستقلال"> الاستقلالية </label>
                    <label><input type="checkbox" name="axis2_topic" value="الدعم الذاتي"> الدعم الذاتي </label>
                    <label><input type="checkbox" name="axis2_topic" value="التقليد السادس"> التقليد السادس </label>
                    <label><input type="checkbox" name="axis2_topic" value="انشاء مجموعات مستقرة"> إنشاء مجموعات مستقرة </label>
                    <label><input type="checkbox" name="axis2_topic" value="التعافي في العزلة"> التعافي في العزلة </label>
                    <label><input type="checkbox" name="axis2_topic" value="السلوكيات الغير لائقة"> السلوكيات الغير لائقة </label>
                    <label><input type="checkbox" name="axis2_topic" value="التقليد الثالث"> التقليد الثالث </label>
                    <label><input type="checkbox" name="axis2_topic" value="التشابهات والاختلافات"> التشابهات والاختلافات </label>
                </div>
                <p class="error-message" id="error_axis2"></p>
            </div>

            <div class="section" id="axis3">
                <h2>المحور الثالث: الإرشاد في خدماتنا</h2>
                <p>يهدف لترسيخ مفاهيم القيادة الفعالة وإعداد الصف الثاني والتوجيه الخدمي.</p>
                <div class="checkbox-grid">
                    <label><input type="checkbox" name="axis3_topic" value="القيادة الفعالة"> القيادة الفعالة</label>
                    <label><input type="checkbox" name="axis3_topic" value="تناوب القيادة"> تناوب القيادة</label>
                    <label><input type="checkbox" name="axis3_topic" value="تفويض السلطة"> تفويض السلطة </label>
                    <label><input type="checkbox" name="axis3_topic" value="جذب الأعضاء للخدمة واعداد صف ثاني"> جذب الأعضاء للخدمة وإعداد صف ثاني </label>
                    <label><input type="checkbox" name="axis3_topic" value="الاختيار المتأني للخدم"> الاختيار المتأني للخدم </label>
                    <label><input type="checkbox" name="axis3_topic" value="الإرشاد في الخدمة"> الإرشاد في الخدمة</label>
                    <label><input type="checkbox" name="axis3_topic" value="الإرشاد / التوجيه"> الإرشاد / التوجيه</label>
                </div>
                <p class="error-message" id="error_axis3"></p>
            </div>

            <div class="section" id="axis4">
                <h2>المحور الرابع: التوجيه هو حجر الأساس</h2>
                <p>يهدف لترسيخ مفاهيم التوجيه الشخصي والخدمي.</p>
                <div class="checkbox-grid">
                    <label><input type="checkbox" name="axis4_topic" value="التوجيه"> التوجيه</label>
                    <label><input type="checkbox" name="axis4_topic" value="المبادئ الروحانية"> المبادئ الروحانية </label>
                    <label><input type="checkbox" name="axis4_topic" value="الخطوات"> الخطوات </label>
                    <label><input type="checkbox" name="axis4_topic" value="ممارسة التقاليد في التوجيه"> ممارسة التقاليد في التوجيه </label>
                    <label><input type="checkbox" name="axis4_topic" value="التعافي الشخصي"> التعافي الشخصي </label>
                    <label><input type="checkbox" name="axis4_topic" value="أدبيات التعافي"> أدبيات التعافي </label>
                    <label><input type="checkbox" name="axis4_topic" value="المشاركة"> المشاركة </label>
                    <label><input type="checkbox" name="axis4_topic" value="القيمة العلاجية لمساعدة مدمن لمدمن آخر"> القيمة العلاجية لمساعدة مدمن لمدمن آخر </label>
                </div>
                <p class="error-message" id="error_axis4"></p>
            </div>
        </div>

        <div class="section">
            <h2>إضافة موضوعات أخرى</h2>
            <div class="form-group">
                <label for="addMoreTopics">6. هل تود إضافة موضوعات أخرى؟</label>
                <select id="addMoreTopics" class="select2" name="addMoreTopics" onchange="toggleOtherTopics()">
                    <option value="no">لا</option>
                    <option value="yes">نعم</option>
                </select>
            </div>
            <div id="otherTopicsField" class="hidden">
                <div class="note">
                    يمكن إضافة موضوعين بحد أقصى لكل عضو. مساهمتك بالآراء والأفكار تشجعنا على البحث عن المزيد من الأفكار الجديدة.
                </div>
                <label for="otherTopic1">الموضوع الإضافي الأول:</label>
                <input type="text" id="otherTopic1" name="otherTopic1" maxlength="100">
                <label for="otherTopic2" style="margin-top: 15px;">الموضوع الإضافي الثاني (اختياري):</label>
                <input type="text" id="otherTopic2" name="otherTopic2" maxlength="100">
            </div>
        </div>

        <button type="submit">إرسال الاستبيان</button>
        <p style="text-align: center; margin-top: 20px; font-style: italic; color: #666;">
            مع تحيات خدم لجنة الدعم والتطوير - نوفمبر 2025 
        </p>
    </form>
</div>

<script>
    // قائمة الخيارات المحتملة لحقول الخدمة
    const serviceOptions = {
        'group': @json($groups->pluck('ar_name')->toArray()),
        'forum': @json($serviceBody->pluck('ar_name')->toArray()), 
        'committee': @json($serviceCommittee->pluck('ar_name')->toArray()),
        'regional': ['إداريون','مفوضي الإقليم','المخزن','الجمعية','تقنية المعلومات','مجلس التخطيط']
    };

    // عناصر الـ DOM الرئيسية لتبديل العرض
    const otherGroupField = document.getElementById('otherGroupField');
    const isolatedMemberField = document.getElementById('isolatedMemberField');
    const otherReasonText = document.getElementById('otherReasonText');
    const serviceDetailsField = document.getElementById('serviceDetailsField');
    const serviceDetailsSelect = document.getElementById('serviceDetailsSelect');
    const serviceDetailsLabel = document.getElementById('serviceDetailsLabel');
    const otherTopicsField = document.getElementById('otherTopicsField');
    const totalSelectionCount = document.getElementById('totalSelectionCount');
    const axisError = document.getElementById('axisError');

    // لتبديل حقول (مجموعة غير مدرجة) أو (عضو منعزل)
    function toggleGroupFields() {
        const selectedValue = document.getElementById('groupNameSelect').value;
        // إخفاء الكل أولاً
        otherGroupField.classList.add('hidden');
        isolatedMemberField.classList.add('hidden');
        
        // إظهار الحقل المناسب
        if (selectedValue === 'other_group') {
            otherGroupField.classList.remove('hidden');
        } else if (selectedValue === 'isolated_member') {
            isolatedMemberField.classList.remove('hidden');
        }
    }

    // لتبديل حقل (وضح سبب الانعزال الآخر)
    function toggleOtherReason() {
        const reason = document.getElementById('isolationReason').value;
        if (reason === 'other') {
            otherReasonText.classList.remove('hidden');
        } else {
            otherReasonText.classList.add('hidden');
        }
    }

    // لتبديل حقول تفاصيل الخدمة (المجموعة/المنتدى/اللجنة)
    function toggleServiceFields() {
        const selectedService = document.getElementById('serviceLevel').value;
        serviceDetailsSelect.innerHTML = ''; // تفريغ القائمة

        if (selectedService !== 'no' && serviceOptions[selectedService]) {
            serviceDetailsField.classList.remove('hidden');
            let labelText = 'التفاصيل:';

            // تعيين نص التسمية حسب مستوى الخدمة
            if (selectedService === 'group') labelText = 'اختر اسم المجموعة:';
            else if (selectedService === 'forum') labelText = 'اختر اسم المنطقة/المنتدى:';
            else if (selectedService === 'committee') labelText = 'اختر اسم اللجنة الخدمية:';
            else if (selectedService === 'regional') labelText = 'اختر اسم لجنة الإقليم:';

            serviceDetailsLabel.textContent = labelText;

            serviceOptions[selectedService].forEach(optionText => {
                const option = document.createElement('option');
                option.value = optionText;
                option.textContent = optionText;
                serviceDetailsSelect.appendChild(option);
            });
        } else {
            serviceDetailsField.classList.add('hidden');
        }
    }

    // لتبديل حقل إضافة موضوعات أخرى
    function toggleOtherTopics() {
        const selected = document.getElementById('addMoreTopics').value;
        if (selected === 'yes') {
            otherTopicsField.classList.remove('hidden');
        } else {
            otherTopicsField.classList.add('hidden');
        }
    }

    // منطق التحقق من قيود الاختيار (أربع موضوعات، واحد من كل محور)
    const allCheckboxes = document.querySelectorAll('input[type="checkbox"][name$="_topic"]');
    allCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateSelections);
    });

    function validateSelections() {
        let totalSelected = 0;
        let axisCounts = {
            'axis1': 0,
            'axis2': 0,
            'axis3': 0,
            'axis4': 0
        };

        // عد الموضوعات لكل محور
        document.querySelectorAll('#axis1 input[type="checkbox"]').forEach(cb => { if(cb.checked) { totalSelected++; axisCounts.axis1++; } });
        document.querySelectorAll('#axis2 input[type="checkbox"]').forEach(cb => { if(cb.checked) { totalSelected++; axisCounts.axis2++; } });
        document.querySelectorAll('#axis3 input[type="checkbox"]').forEach(cb => { if(cb.checked) { totalSelected++; axisCounts.axis3++; } });
        document.querySelectorAll('#axis4 input[type="checkbox"]').forEach(cb => { if(cb.checked) { totalSelected++; axisCounts.axis4++; } });

        totalSelectionCount.textContent = `الموضوعات المختارة: ${totalSelected}/4`;
        
        let isValid = true;
        axisError.innerHTML = ''; // تفريغ رسالة الخطأ العامة
        
        // التحقق من أن مجموع الاختيارات لا يتجاوز 4
        if (totalSelected > 4) {
             axisError.innerHTML += 'الحد الأقصى للاختيار هو 4 موضوعات فقط.<br>';
             isValid = false;
        }

        // التحقق من أن كل محور لا يزيد عن اختيار واحد، وتطبيق التقييد الفوري
        for (const axis in axisCounts) {
            const errorElement = document.getElementById(`error_${axis}`);
            errorElement.textContent = ''; // مسح رسالة الخطأ الخاصة بالمحور

            if (axisCounts[axis] > 1) {
                errorElement.textContent = 'يُسمح باختيار موضوع واحد فقط من هذا المحور.';
                isValid = false;
                // تعطيل الاختيارات الزائدة في هذا المحور
                document.querySelectorAll(`#${axis} input[type="checkbox"]`).forEach(cb => {
                    if (!cb.checked) {
                        cb.disabled = true;
                    }
                });
            } else {
                // تفعيل الاختيارات إذا كان العدد صحيحًا
                document.querySelectorAll(`#${axis} input[type="checkbox"]`).forEach(cb => {
                    cb.disabled = false;
                });
            }
        }

        // إذا كانت المشكلة في العدد الكلي (مثلاً 4 من محورين فقط)، فسيتم إظهار رسالة الخطأ العامة (تم التعامل معها بالفعل)

        // تعطيل جميع التشيك بوكس إذا وصل المجموع إلى 4 وكان كل محور 1 أو 0
        if (totalSelected === 4 && isValid) {
            allCheckboxes.forEach(cb => {
                if (!cb.checked) {
                    cb.disabled = true;
                }
            });
        } else if (totalSelected < 4) {
            // إعادة تفعيل التشيك بوكس التي لم يتم تعطيلها بسبب تجاوز المحور
            allCheckboxes.forEach(cb => {
                const axisId = cb.closest('.section').id;
                if (axisCounts[axisId] <= 1) {
                    cb.disabled = false;
                }
            });
        }
        
        return isValid;
    }

    // إعداد التحقق من الإرسال النهائي
    document.getElementById('educationSurveyForm').addEventListener('submit', function(event) {
        if (!validateSelections()) {
            event.preventDefault(); // منع الإرسال
            alert('يُرجى مراجعة اختيارك للموضوعات. يجب اختيار 4 موضوعات بحد أقصى، واحد من كل محور كحد أقصى لكل محور.');
            document.getElementById('axisError').scrollIntoView();
        } else if (document.getElementById('addMoreTopics').value === 'yes' && 
                   (!document.getElementById('otherTopic1').value && !document.getElementById('otherTopic2').value)) {
            event.preventDefault();
            alert('لقد اخترت "نعم" لإضافة موضوعات أخرى، ولكن لم تقم بإدخال أي موضوع.');
        } else {
             // هنا يمكن إضافة منطق إرسال البيانات (AJAX أو Form Submit)
             alert('تم إرسال الاستبيان بنجاح. شكراً لمشاركتك!');
             // event.preventDefault(); // لإظهار الرسالة دون إرسال حقيقي في المثال
        }
    });

    // استدعاء الدوال عند تحميل الصفحة لتطبيق الإخفاء المبدئي
    document.addEventListener('DOMContentLoaded', () => {
        toggleGroupFields();
        toggleOtherReason();
        toggleServiceFields();
        toggleOtherTopics();
        validateSelections();
    });
</script>
</x-frontend.layout>
