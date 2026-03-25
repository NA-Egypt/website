<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\City;
use Illuminate\Http\Request;

class ForPublicController extends Controller
{
    public function index()
    {
        // بيانات حية فقط من قاعدة البيانات
        $data = [
            // إحصائيات حية من الداتابيز فقط
            'groupsCount' => Group::count(),
            'citiesCount' => City::count(),
            'meetingsCount' => Meeting::count(),

            // معلومات أول اجتماع (ثابتة من ملف الوورد)
            'firstMeeting' => [
                'date' => '26 نوفمبر 1989',
                'location' => 'مدرسة بمصر الجديدة',
                'members' => 4
            ],

            // بيانات الجرافات (شكل فقط - ستغيرها أنت)
            'graphs' => $this->getGraphsStructure(),

            // نشرات تعريفية (نصوص ثابتة)
            'arabicPamphlets' => $this->getArabicPamphlets(),
            'englishPamphlets' => $this->getEnglishPamphlets(),

            // أسئلة وأجوبة
            'faqs' => $this->getFaqs()
        ];

        return view('frontend.forpublic', $data);
    }

    private function getGraphsStructure()
    {
        // هذا مجرد شكل للجرافات - ستغير البيانات بنفسك
        return [
            'gender' => [
                'title' => 'النوع (Gender)',
                'type' => 'doughnut',
                'labels' => ['ذكور', 'إناث'],
                // سيتم ملئها من Excel لاحقاً
                'datasets' => [
                    [
                        'data' => [65, 35],
                        'backgroundColor' => ['#2196F3', '#E91E63']
                    ]
                ]
            ],

            'age' => [
                'title' => 'معدل الأعمار (Age)',
                'type' => 'bar',
                'labels' => ['تحت 20', '20-30', '30-40', '40-50', 'فوق 50'],
                'datasets' => [
                    [
                        'label' => 'النسبة %',
                        'data' => [15, 35, 30, 15, 5],
                        'backgroundColor' => '#4CAF50'
                    ]
                ]
            ],

            'abstinence' => [
                'title' => 'مدة الامتناع (Years Drug-Free)',
                'type' => 'pie',
                'labels' => ['أقل من سنة', '1-3 سنوات', '3-5 سنوات', '5-10 سنوات', 'أكثر من 10 سنوات'],
                'datasets' => [
                    [
                        'data' => [20, 30, 25, 15, 10],
                        'backgroundColor' => ['#FF9800', '#2196F3', '#4CAF50', '#9C27B0', '#F44336']
                    ]
                ]
            ],

            'drugs' => [
                'title' => 'المخدرات المستخدمة على أساس منتظم',
                'type' => 'horizontalBar',
                'labels' => ['الحشيش', 'الترامادول', 'الهيروين', 'الكوكايين', 'الأدوية المهدئة'],
                'datasets' => [
                    [
                        'label' => 'النسبة %',
                        'data' => [40, 35, 20, 3, 2],
                        'backgroundColor' => '#2196F3'
                    ]
                ]
            ],

            'education' => [
                'title' => 'مستوى التعليم (Educational Status)',
                'type' => 'horizontalBar',
                'labels' => ['أقل من الثانوية', 'ثانوية', 'دبلوم', 'بكالوريوس', 'دراسات عليا'],
                'datasets' => [
                    [
                        'label' => 'النسبة %',
                        'data' => [20, 30, 15, 25, 10],
                        'backgroundColor' => '#03A9F4'
                    ]
                ]
            ],

            'employment' => [
                'title' => 'الحالة الوظيفية (Employment Status)',
                'type' => 'doughnut',
                'labels' => ['موظف', 'عاطل', 'طالب', 'متقاعد', 'أخرى'],
                'datasets' => [
                    [
                        'data' => [45, 25, 15, 10, 5],
                        'backgroundColor' => ['#FF9800', '#F44336', '#2196F3', '#4CAF50', '#9C27B0']
                    ]
                ]
            ]
        ];
    }

    private function getArabicPamphlets()
    {
        return [
            ['title' => 'من، ماذا، كيف ولماذا', 'desc' => 'توضح أن المدمن هو شخص تسيطر المخدرات على حياته'],
            ['title' => 'مرحبًا في زمالة المدمنين المجهولين', 'desc' => 'نشرة ترحيبية توضح أساسيات الزمالة'],
            ['title' => 'مقدمة عن اجتماعات زمالة المدمنين المجهولين', 'desc' => 'تعطي مفهوم عن ما نقوم به عندما نجتمع'],
            ['title' => 'نظرة أخرى', 'desc' => 'تشجع الأعضاء على إعادة تقييم حياتهم وسلوكياتهم'],
            ['title' => 'من المدمنين الشباب للمدمنين الشباب', 'desc' => 'تشارك تجارب الشباب الذين وجدوا التعافي'],
            ['title' => 'البقاء ممتنعًا في الخارج', 'desc' => 'إرشادات للأفراد الذين ينتقلون من المستشفيات إلى الحياة اليومية'],
            ['title' => 'للآباء وأولياء أمور المدمنين الشباب', 'desc' => 'إرشادات للآباء الذين يعانون مع إدمان أطفالهم'],
            ['title' => 'إمكانية الوصول لذوي الاحتياجات الإضافية', 'desc' => 'تؤكد على أهمية جعل الاجتماعات متاحة للجميع']
        ];
    }

    private function getEnglishPamphlets()
    {
        return [
            ['title' => 'Membership Survey 2024', 'desc' => 'Our 2024 survey of 32,398 NA members'],
            ['title' => 'Information about NA', 'desc' => 'Includes facts about the history of NA, organizational philosophy, and membership demographics'],
            ['title' => 'NA: A Resource In Your Community', 'desc' => 'Provides information about local NA services'],
            ['title' => 'In Times of Illness', 'desc' => 'Revised in 2010 to reflect members\' experiences with challenges'],
            ['title' => 'By Young Addicts, For Young Addicts', 'desc' => 'Developed by young members of Narcotics Anonymous']
        ];
    }

    private function getFaqs()
    {
        return [
            [
                'question' => 'من هم أعضاء زمالة المدمنين المجهولين؟',
                'answer' => 'أي شخص لديه الرغبة في الامتناع عن تعاطي المخدرات يمكنه أن يكون عضوًا...'
            ],
            [
                'question' => 'كيف يمكننا إيجاد اجتماعات زمالة المدمنين المجهولين؟',
                'answer' => 'لمعرفة أماكن ومواعيد اجتماعات التعافي...'
            ],
            [
                'question' => 'ماذا يحدث في اجتماعات زمالة المدمنين المجهولين؟',
                'answer' => 'اجتماعات زمالة المدمنين المجهولين ليست فصًلا دراسيًا...'
            ],
            [
                'question' => 'ما هي الاجتماعات المفتوحة والمغلقة؟',
                'answer' => 'الاجتماعات المغلقة هي للمدمنين فقط...'
            ],
            [
                'question' => 'ما هي رسوم العضوية في زمالة المدمنين المجهولين؟',
                'answer' => 'إنها لا تكلف شيئا، فلا يوجد رسوم لحضور الاجتماعات...'
            ],
            [
                'question' => 'كيف يمكنني أن أجعل شخصًا ما يتوقف عن تعاطى المخدرات؟',
                'answer' => 'لقد تعلمنا من خلال تجاربنا الشخصية...'
            ],
            [
                'question' => 'هل تدير زمالة المدمنين المجهولين أي مراكز للعلاج؟',
                'answer' => 'لا. زمالة المدمنين المجهولين لا تدير مراكز العلاج...'
            ],
            [
                'question' => 'أنا لا اتعاطى المخدرات، ولكن أرغب في المساعدة. ماذا يمكنني أن أفعل؟',
                'answer' => 'نشكرك قبل أي شيء على اهتمامك...'
            ],
            [
                'question' => 'ما الفرق بين المدمنين المجهولين ومراكز علاج الإدمان؟',
                'answer' => 'زمالة المدمنين المجهولين لا تهدف إلى الربح...'
            ]
        ];
    }
}