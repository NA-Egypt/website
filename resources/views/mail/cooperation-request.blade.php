<!DOCTYPE html>
<html>
<head>
    <title>Cooperation Request</title>
</head>
<body dir="rtl" style="text-align: right; font-family: Arial, sans-serif;">
    <h2>طلب تعاون جديد</h2>
    <p><strong>الاسم بالكامل:</strong> {{ $data['name'] }}</p>
    <p><strong>المهنة والتخصص:</strong> {{ $data['profession'] }}</p>
    <p><strong>اسم المؤسسة أو جهة العمل:</strong> {{ $data['organization'] }}</p>
    <p><strong>البريد الإلكتروني:</strong> {{ $data['email'] }}</p>
    <p><strong>رقم الهاتف:</strong> {{ $data['phone'] }}</p>
    <p><strong>المحافظة / المدينة:</strong> {{ $data['city'] }}</p>
    
    <h3>طبيعة التعاون المطلوب:</h3>
    <ul>
        @if(is_array($data['cooperationType']))
            @foreach($data['cooperationType'] as $type)
                <li>{{ $type }}</li>
            @endforeach
        @endif
        @if(!empty($data['cooperationTypeOther']))
            <li>أخرى: {{ $data['cooperationTypeOther'] }}</li>
        @endif
    </ul>
    
    <h3>أسئلة واستفسارات:</h3>
    <p>{{ $data['questions'] ?: 'لا يوجد' }}</p>

    <h3>تفضيلات التواصل:</h3>
    <p><strong>أفضل وسيلة للتواصل:</strong> {{ $data['contactMethod'] == 'أخرى' ? 'أخرى - ' . $data['contactMethodOther'] : $data['contactMethod'] }}</p>
    <p><strong>أفضل وقت للتواصل:</strong> {{ $data['contactTime'] }}</p>
</body>
</html>
