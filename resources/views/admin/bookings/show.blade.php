@extends('layouts.app')

@section('title', 'تفاصيل الحجز')

@push('styles')
<style>
/* Custom Modal Styles */
#confirmationModal, #alertModal {
    transition: opacity 0.3s ease-in-out;
}

#confirmationModal.show, #alertModal.show {
    opacity: 1;
}

.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-backdrop {
    backdrop-filter: blur(2px);
}

/* Enhanced button hover effects */
.modal-button {
    transition: all 0.2s ease-in-out;
    position: relative;
    overflow: hidden;
}

.modal-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.modal-button:active {
    transform: translateY(0);
}

/* RTL Support for modals */
.modal-content {
    direction: rtl;
    text-align: right;
}

.modal-content .text-center {
    text-align: center;
}

/* Focus styles for accessibility */
.modal-button:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Loading spinner animation */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-calendar-check text-blue-600 ml-2"></i>
                        تفاصيل الحجز #{{ $booking->id }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">عرض تفاصيل الحجز وإدارته</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة لقائمة الحجوزات
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- تفاصيل الحجز -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">معلومات الحجز</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">تفاصيل الحجز الأساسية</p>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">رقم الحجز</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ $booking->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">تاريخ الحجز</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->created_at->format('Y-m-d H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">حالة الحجز</dt>
                                <dd class="mt-1">
                                    @if($booking->status === 'confirmed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle ml-1"></i>
                                            مؤكدة
                                        </span>
                                    @elseif($booking->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock ml-1"></i>
                                            في الانتظار
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle ml-1"></i>
                                            ملغية
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">حالة الدفع</dt>
                                <dd class="mt-1">
                                    @if($booking->payment_status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check ml-1"></i>
                                            مدفوعة
                                        </span>
                                    @elseif($booking->payment_status === 'refunded')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-undo ml-1"></i>
                                            مستردة
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock ml-1"></i>
                                            في الانتظار
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">المبلغ</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($booking->payment_amount, 2) }} {{ $booking->workshop->currency }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">طريقة الدفع</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->payment_method ?? 'غير محدد' }}</dd>
                            </div>
                            @if($booking->confirmed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">تاريخ التأكيد</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->confirmed_at->format('Y-m-d H:i') }}</dd>
                            </div>
                            @endif
                            @if($booking->cancelled_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">تاريخ الإلغاء</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->cancelled_at->format('Y-m-d H:i') }}</dd>
                            </div>
                            @endif
                        </dl>
                        
                        @if($booking->notes)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">ملاحظات</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $booking->notes }}</dd>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- معلومات الورشة -->
                <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">معلومات الورشة</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">عنوان الورشة</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->workshop->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">المدرب</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->workshop->instructor }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">تاريخ الورشة</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->workshop->start_date->format('Y-m-d H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">المكان</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $booking->workshop->is_online ? 'ورشة أونلاين' : ($booking->workshop->location ?? 'ورشة حضورية') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">السعر</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->workshop->formatted_price }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">المدة</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->workshop->formatted_duration }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- معلومات المستخدم والإجراءات -->
            <div class="space-y-6">
                <!-- معلومات المستخدم -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">معلومات المستخدم</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            </div>
                            <div class="mr-4">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                @if($booking->user->phone)
                                <div class="text-sm text-gray-500">{{ $booking->user->phone }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإجراءات -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">الإجراءات</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        @if($booking->status === 'pending')
                            <button onclick="confirmBooking({{ $booking->id }})" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-check ml-2"></i>
                                تأكيد الحجز
                            </button>
                            <button onclick="cancelBooking({{ $booking->id }})" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-times ml-2"></i>
                                إلغاء الحجز
                            </button>
                        @endif

                        <button onclick="updatePayment({{ $booking->id }})" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-credit-card ml-2"></i>
                            تحديث حالة الدفع
                        </button>

                        <a href="{{ route('admin.workshops.show', $booking->workshop) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-eye ml-2"></i>
                            عرض الورشة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden modal-backdrop">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content">
        <div class="mt-3 text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <i class="fas fa-question-circle text-blue-600 text-xl"></i>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="modalTitle">
                تأكيد العملية
            </h3>
            
            <!-- Message -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modalMessage">
                    هل أنت متأكد من تأكيد هذا الحجز؟
                </p>
            </div>
            
            <!-- Buttons -->
            <div class="items-center px-4 py-3">
                <div class="flex justify-center space-x-4 space-x-reverse">
                    <button id="modalCancel" class="modal-button px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </button>
                    <button id="modalConfirm" class="modal-button px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                        <i class="fas fa-check ml-2"></i>
                        تأكيد
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Alert Modal -->
<div id="alertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden modal-backdrop">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content">
        <div class="mt-3 text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                تنبيه
            </h3>
            
            <!-- Message -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="alertMessage">
                    حدث خطأ أثناء تنفيذ العملية
                </p>
            </div>
            
            <!-- Button -->
            <div class="items-center px-4 py-3">
                <div class="flex justify-center">
                    <button id="alertOk" class="modal-button px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-check ml-2"></i>
                        موافق
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Modal Functions
function showConfirmationModal(title, message, onConfirm) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
    
    // Clear previous event listeners
    const confirmBtn = document.getElementById('modalConfirm');
    const cancelBtn = document.getElementById('modalCancel');
    
    // Remove existing listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
    
    // Add new listeners
    document.getElementById('modalConfirm').addEventListener('click', () => {
        hideConfirmationModal();
        onConfirm();
    });
    
    document.getElementById('modalCancel').addEventListener('click', hideConfirmationModal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideConfirmationModal();
        }
    });
}

function hideConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function showAlertModal(message) {
    document.getElementById('alertMessage').textContent = message;
    const modal = document.getElementById('alertModal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
    
    // Clear previous event listeners
    const okBtn = document.getElementById('alertOk');
    const newOkBtn = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOkBtn, okBtn);
    
    // Add new listener
    document.getElementById('alertOk').addEventListener('click', hideAlertModal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideAlertModal();
        }
    });
}

function hideAlertModal() {
    const modal = document.getElementById('alertModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function showCancellationModal(bookingId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content">
            <div class="mt-3 text-center">
                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    إلغاء الحجز
                </h3>
                
                <!-- Message -->
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        يرجى إدخال سبب الإلغاء:
                    </p>
                    <textarea id="cancellationReason" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                        rows="3" 
                        placeholder="أدخل سبب الإلغاء هنا..."
                        required></textarea>
                    <div id="cancellationError" class="hidden mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle ml-1"></i>
                        يرجى إدخال سبب الإلغاء
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="items-center px-4 py-3">
                    <div class="flex justify-center space-x-4 space-x-reverse">
                        <button id="cancelCancel" class="modal-button px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                        <button id="cancelConfirm" class="modal-button px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                            <i class="fas fa-check ml-2"></i>
                            تأكيد الإلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Event listeners
    document.getElementById('cancelCancel').addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
    // Hide error when user starts typing
    document.getElementById('cancellationReason').addEventListener('input', () => {
        document.getElementById('cancellationError').classList.add('hidden');
    });
    
    document.getElementById('cancelConfirm').addEventListener('click', () => {
        const reason = document.getElementById('cancellationReason').value.trim();
        const errorDiv = document.getElementById('cancellationError');
        
        if (reason === '') {
            errorDiv.classList.remove('hidden');
            return;
        } else {
            errorDiv.classList.add('hidden');
        }
        
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch(`/admin/bookings/${bookingId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cancellation_reason: reason
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlertModal(data.message);
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlertModal('حدث خطأ أثناء إلغاء الحجز');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
        
        document.body.removeChild(modal);
    });
}

function confirmBooking(bookingId) {
    showConfirmationModal(
        'تأكيد الحجز',
        'هل أنت متأكد من تأكيد هذا الحجز؟',
        () => {
            fetch(`/admin/bookings/${bookingId}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showAlertModal(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlertModal('حدث خطأ أثناء تأكيد الحجز');
            });
        }
    );
}

function cancelBooking(bookingId) {
    showCancellationModal(bookingId);
}

function updatePayment(bookingId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content">
            <div class="mt-3 text-center">
                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    تحديث حالة الدفع
                </h3>
                
                <!-- Form -->
                <div class="mt-2 px-7 py-3">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الدفع</label>
                        <select id="paymentStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending">في الانتظار</option>
                            <option value="paid">مدفوعة</option>
                            <option value="refunded">مستردة</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع (اختياري)</label>
                        <input type="text" id="paymentMethod" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="مثال: فيزا، ماستركارد، تحويل بنكي">
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="items-center px-4 py-3">
                    <div class="flex justify-center space-x-4 space-x-reverse">
                        <button id="paymentCancel" class="modal-button px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                        <button id="paymentConfirm" class="modal-button px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-check ml-2"></i>
                            تحديث
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Event listeners
    document.getElementById('paymentCancel').addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
    document.getElementById('paymentConfirm').addEventListener('click', () => {
        const status = document.getElementById('paymentStatus').value;
        const method = document.getElementById('paymentMethod').value.trim();
        
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch(`/admin/bookings/${bookingId}/update-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                payment_status: status,
                payment_method: method || null
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlertModal(data.message);
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlertModal('حدث خطأ أثناء تحديث حالة الدفع');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
        
        document.body.removeChild(modal);
    });
}
</script>
@endsection
