<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Workshop;
use Carbon\Carbon;

class WorkshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workshops = [
            [
                'title' => 'ورشة تعلم صنع الحلويات العربية التقليدية',
                'description' => 'تعلم كيفية صنع أشهى الحلويات العربية مثل البقلاوة والكنافة والقطايف',
                'content' => 'في هذه الورشة سوف تتعلم أسرار صنع الحلويات العربية التقليدية من الصفر. سنبدأ بالتعرف على المكونات الأساسية وطرق التحضير الصحيحة، ثم ننتقل لصنع البقلاوة والكنافة والقطايف خطوة بخطوة.',
                'instructor' => 'الشيف فاطمة أحمد',
                'instructor_avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face',
                'instructor_bio' => 'شيف متخصصة في الحلويات العربية مع 15 عام من الخبرة',
                'category' => 'حلويات',
                'level' => 'مبتدئ',
                'duration' => 180, // 3 ساعات
                'max_participants' => 15,
                'price' => 150.00,
                'currency' => 'USD',
                'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop',
                    'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&h=300&fit=crop',
                    'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=300&fit=crop'
                ],
                'location' => 'مطبخ وصفة - الرياض',
                'address' => 'شارع الملك فهد، حي العليا، الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'start_date' => Carbon::now()->addDays(7)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(7)->setTime(13, 0),
                'registration_deadline' => Carbon::now()->addDays(5),
                'is_online' => false,
                'requirements' => 'لا توجد متطلبات خاصة، جميع المكونات متوفرة',
                'what_you_will_learn' => 'تعلم صنع البقلاوة، الكنافة، القطايف، وأسرار الحلويات العربية',
                'materials_needed' => 'جميع الأدوات والمكونات متوفرة في الورشة',
                'is_active' => true,
                'is_featured' => false,
                'rating' => 4.8,
                'reviews_count' => 25
            ],
            [
                'title' => 'ورشة الطبخ الإيطالي الأصيل',
                'description' => 'اكتشف أسرار المطبخ الإيطالي الأصيل مع شيف إيطالي محترف',
                'content' => 'ورشة شاملة لتعلم الطبخ الإيطالي الأصيل. سنتعلم صنع المعكرونة الطازجة، البيتزا، والريزوتو، بالإضافة إلى الصلصات التقليدية الإيطالية.',
                'instructor' => 'الشيف ماركو روسي',
                'instructor_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face',
                'instructor_bio' => 'شيف إيطالي محترف مع 20 عام من الخبرة في المطاعم الإيطالية',
                'category' => 'مطبخ عالمي',
                'level' => 'متوسط',
                'duration' => 180, // 3 ساعات (الحد الأقصى)
                'max_participants' => 12,
                'price' => 200.00,
                'currency' => 'USD',
                'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop',
                    'https://images.unsplash.com/photo-1572441713132-51c75654db73?w=400&h=300&fit=crop'
                ],
                'location' => 'مطبخ وصفة - جدة',
                'address' => 'كورنيش جدة، حي الزهراء',
                'latitude' => 21.4858,
                'longitude' => 39.1925,
                'start_date' => Carbon::now()->addDays(10)->setTime(14, 0),
                'end_date' => Carbon::now()->addDays(10)->setTime(18, 0),
                'registration_deadline' => Carbon::now()->addDays(8),
                'is_online' => false,
                'requirements' => 'معرفة أساسية بالطبخ',
                'what_you_will_learn' => 'صنع المعكرونة الطازجة، البيتزا، الريزوتو، والصلصات الإيطالية',
                'materials_needed' => 'جميع المكونات متوفرة',
                'is_active' => true,
                'is_featured' => false,
                'rating' => 4.9,
                'reviews_count' => 18
            ],
            [
                'title' => 'ورشة الطبخ الصحي - أونلاين',
                'description' => 'تعلم الطبخ الصحي والمتوازن من منزلك',
                'content' => 'ورشة أونلاين لتعلم الطبخ الصحي والمتوازن. سنتعلم كيفية تحضير وجبات صحية ولذيذة باستخدام مكونات طبيعية ومغذية.',
                'instructor' => 'د. سارة محمد - أخصائية التغذية',
                'instructor_avatar' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150&h=150&fit=crop&crop=face',
                'instructor_bio' => 'أخصائية تغذية مع 10 أعوام من الخبرة في الطبخ الصحي',
                'category' => 'طبخ صحي',
                'level' => 'مبتدئ',
                'duration' => 120, // ساعتان
                'max_participants' => 30,
                'price' => 80.00,
                'currency' => 'USD',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop',
                    'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=400&h=300&fit=crop'
                ],
                'location' => 'أونلاين',
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'start_date' => Carbon::now()->addDays(5)->setTime(19, 0),
                'end_date' => Carbon::now()->addDays(5)->setTime(21, 0),
                'registration_deadline' => Carbon::now()->addDays(3),
                'is_online' => true,
                'meeting_link' => 'https://meet.google.com/abc-defg-hij',
                'requirements' => 'مطبخ مجهز، اتصال إنترنت جيد',
                'what_you_will_learn' => 'مبادئ الطبخ الصحي، تحضير وجبات متوازنة، اختيار المكونات الصحية',
                'materials_needed' => 'قائمة المكونات ستُرسل قبل الورشة',
                'is_active' => true,
                'is_featured' => false,
                'rating' => 4.6,
                'reviews_count' => 32
            ],
            [
                'title' => 'ورشة صنع الخبز والمعجنات',
                'description' => 'تعلم صنع الخبز والمعجنات الطازجة من الصفر',
                'content' => 'ورشة شاملة لتعلم صنع الخبز والمعجنات الطازجة. سنتعلم صنع الخبز العربي، الفرنسي، والكرواسان، بالإضافة إلى المعجنات الحلوة والمالحة.',
                'instructor' => 'الشيف خالد السعيد',
                'instructor_avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
                'instructor_bio' => 'شيف متخصص في الخبز والمعجنات مع 12 عام من الخبرة',
                'category' => 'خبز ومعجنات',
                'level' => 'متوسط',
                'duration' => 180, // 3 ساعات (الحد الأقصى)
                'max_participants' => 10,
                'price' => 180.00,
                'currency' => 'USD',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=300&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=300&fit=crop',
                    'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&h=300&fit=crop'
                ],
                'location' => 'مطبخ وصفة - الدمام',
                'address' => 'شارع الملك عبدالعزيز، حي الفيصلية، الدمام',
                'latitude' => 26.4207,
                'longitude' => 50.0888,
                'start_date' => Carbon::now()->addDays(14)->setTime(9, 0),
                'end_date' => Carbon::now()->addDays(14)->setTime(14, 0),
                'registration_deadline' => Carbon::now()->addDays(12),
                'is_online' => false,
                'requirements' => 'لا توجد متطلبات خاصة',
                'what_you_will_learn' => 'صنع الخبز العربي والفرنسي، الكرواسان، والمعجنات المختلفة',
                'materials_needed' => 'جميع المكونات والأدوات متوفرة',
                'is_active' => true,
                'is_featured' => false,
                'rating' => 4.7,
                'reviews_count' => 20
            ],
            [
                'title' => 'ورشة الحلويات الفرنسية المتقدمة',
                'description' => 'تعلم صنع أشهر الحلويات الفرنسية مع تقنيات متقدمة',
                'content' => 'ورشة متقدمة لتعلم صنع الحلويات الفرنسية الكلاسيكية مثل الماكارون، الإكلير، والتارت. سنتعلم التقنيات المتقدمة والدقيقة المطلوبة لهذه الحلويات.',
                'instructor' => 'الشيف ماري كلير',
                'instructor_avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face',
                'instructor_bio' => 'شيف فرنسية متخصصة في الحلويات مع 18 عام من الخبرة',
                'category' => 'حلويات',
                'level' => 'متقدم',
                'duration' => 180, // 3 ساعات (الحد الأقصى)
                'max_participants' => 8,
                'price' => 300.00,
                'currency' => 'USD',
                'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=300&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=300&fit=crop',
                    'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop'
                ],
                'location' => 'مطبخ وصفة - الرياض',
                'address' => 'شارع التحلية، حي العليا، الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'start_date' => Carbon::now()->addDays(21)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(21)->setTime(16, 0),
                'registration_deadline' => Carbon::now()->addDays(19),
                'is_online' => false,
                'requirements' => 'خبرة في الحلويات، دقة في العمل',
                'what_you_will_learn' => 'صنع الماكارون، الإكلير، التارت، وتقنيات الحلويات الفرنسية المتقدمة',
                'materials_needed' => 'قائمة مفصلة ستُرسل قبل الورشة',
                'is_active' => true,
                'is_featured' => true,
                'rating' => 4.9,
                'reviews_count' => 15
            ]
        ];

        foreach ($workshops as $workshop) {
            Workshop::create($workshop);
        }
    }
}
