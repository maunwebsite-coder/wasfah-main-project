<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function sendMessage(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'subject.required' => 'الموضوع مطلوب',
            'message.required' => 'الرسالة مطلوبة',
            'message.max' => 'الرسالة طويلة جداً (الحد الأقصى 2000 حرف)',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // إرسال الإيميل
            Mail::raw($this->formatMessage($request->all()), function ($message) use ($request) {
                $message->to('wasfah99@gmail.com')
                        ->subject('رسالة جديدة من موقع وصفة - ' . $request->subject)
                        ->from($request->email, $request->first_name . ' ' . $request->last_name);
            });

            return back()->with('success', 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.');
        }
    }

    private function formatMessage($data)
    {
        $message = "رسالة جديدة من موقع وصفة\n\n";
        $message .= "الاسم: " . $data['first_name'] . " " . $data['last_name'] . "\n";
        $message .= "البريد الإلكتروني: " . $data['email'] . "\n";
        
        if (!empty($data['phone'])) {
            $message .= "رقم الهاتف: " . $data['phone'] . "\n";
        }
        
        $message .= "الموضوع: " . $this->getSubjectText($data['subject']) . "\n\n";
        $message .= "الرسالة:\n" . $data['message'] . "\n\n";
        $message .= "---\n";
        $message .= "تم الإرسال في: " . now()->format('Y-m-d H:i:s');
        
        return $message;
    }

    private function getSubjectText($subject)
    {
        $subjects = [
            'general' => 'استفسار عام',
            'recipe' => 'مشكلة في وصفة',
            'workshop' => 'استفسار عن ورشة عمل',
            'technical' => 'مشكلة تقنية',
            'suggestion' => 'اقتراح',
            'other' => 'أخرى'
        ];

        return $subjects[$subject] ?? $subject;
    }
}

