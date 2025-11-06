const WhatsAppBooking = (() => {
    const defaultUser = {
        name: 'مستخدم',
        phone: 'غير محدد',
        email: 'غير محدد',
    };

    const state = {
        config: {
            isLoggedIn: false,
            whatsappNumber: '962790553680',
            bookingEndpoint: '/bookings',
            bookingNotes: 'حجز موحد - واتساب + قاعدة بيانات',
            user: { ...defaultUser },
        },
        activeContext: null,
    };

    function configure(partial = {}) {
        if (typeof partial.isLoggedIn === 'boolean') {
            state.config.isLoggedIn = partial.isLoggedIn;
        }

        if (typeof partial.whatsappNumber === 'string' && partial.whatsappNumber.trim()) {
            state.config.whatsappNumber = partial.whatsappNumber.trim();
        }

        if (typeof partial.bookingEndpoint === 'string' && partial.bookingEndpoint.trim()) {
            state.config.bookingEndpoint = partial.bookingEndpoint.trim();
        }

        if (typeof partial.bookingNotes === 'string') {
            state.config.bookingNotes = partial.bookingNotes;
        }

        if (partial.user && typeof partial.user === 'object') {
            state.config.user = {
                ...state.config.user,
                ...Object.fromEntries(
                    Object.entries(partial.user).map(([key, value]) => [
                        key,
                        value ?? defaultUser[key] ?? '',
                    ]),
                ),
            };
        }
    }

    function initButtons(selector = '.js-whatsapp-booking') {
        const buttons = document.querySelectorAll(selector);
        buttons.forEach((button) => {
            button.addEventListener(
                'click',
                (event) => {
                    event.preventDefault();

                    if (
                        button.disabled ||
                        button.dataset.isBooked === 'true' ||
                        button.dataset.loading === 'true'
                    ) {
                        return;
                    }

                    startFlowFromButton(button);
                },
                { passive: false },
            );
        });
    }

    function startFlowFromButton(button) {
        const details = extractDetails(button);

        state.activeContext = {
            triggerButton: button,
            details,
        };

        if (state.config.isLoggedIn) {
            showBookingConfirmation(details);
        } else {
            showLoginRequiredModal(details);
        }
    }

    function extractDetails(button) {
        return {
            id: Number(button.dataset.workshopId),
            title: button.dataset.title || '',
            price: button.dataset.price || '',
            date: button.dataset.date || '',
            instructor: button.dataset.instructor || '',
            location: button.dataset.location || '',
            deadline: button.dataset.deadline || '',
        };
    }

    function getActiveBookingDetails() {
        return state.activeContext?.details ?? null;
    }

    function showBookingConfirmation(details = getActiveBookingDetails()) {
        if (!details) {
            return;
        }

        const existingModal = document.getElementById('booking-confirmation-modal');
        if (existingModal) {
            existingModal.remove();
        }

        const modalHTML = `
            <div id="booking-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">تأكيد الحجز</h3>
                        <p class="text-gray-600.mb-6">هل أنت متأكد من حجز هذه الورشة؟</p>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-right">
                            <h4 class="font-semibold text-gray-900 mb-2">${details.title}</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>التاريخ:</span>
                                    <span class="font-medium">${details.date}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>المدرب:</span>
                                    <span class="font-medium">${details.instructor}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>المكان:</span>
                                    <span class="font-medium">${details.location}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>السعر:</span>
                                    <span class="font-medium text-green-600">${details.price}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button data-action="confirm-booking" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center justify-center">
                                <i class="fas fa-check ml-2"></i>
                                نعم، احجز الآن
                            </button>
                            <button data-action="close-booking-modal" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-xl transition-colors.flex items-center justify-center">
                                <i class="fas fa-times ml-2"></i>
                                إلغاء
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        document
            .querySelector('#booking-confirmation-modal [data-action="confirm-booking"]')
            ?.addEventListener('click', confirmBooking, { once: true });

        document
            .querySelector('#booking-confirmation-modal [data-action="close-booking-modal"]')
            ?.addEventListener('click', closeBookingConfirmation, { once: true });
    }

    async function confirmBooking() {
        const context = state.activeContext;
        if (!context) {
            return;
        }

        closeBookingConfirmation();

        const whatsappBridgeWindow = openWhatsAppBridgeWindow();
        setButtonLoadingState(context.triggerButton, true);

        try {
            const data = await persistBooking(context.details.id);

            if (data?.success) {
                markWorkshopAsBooked(context.details.id);
                sendMessage(context.details, whatsappBridgeWindow);
                notify('تم حفظ الحجز في النظام وإرسال رسالة الواتساب!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                return;
            }

            if (data?.message && data.message.includes('حجز')) {
                markWorkshopAsBooked(context.details.id);
                notify('تم حجز هذه الورشة بالفعل.', 'success');
                if (whatsappBridgeWindow && !whatsappBridgeWindow.closed) {
                    whatsappBridgeWindow.close();
                }
                return;
            }

            throw new Error(data?.message || 'booking_failed');
        } catch (error) {
            console.error('Booking error:', error);
            notify('حدث خطأ أثناء حفظ الحجز', 'error');
            if (whatsappBridgeWindow && !whatsappBridgeWindow.closed) {
                whatsappBridgeWindow.close();
            }
        } finally {
            setButtonLoadingState(context.triggerButton, false);
            state.activeContext = null;
        }
    }

    function closeBookingConfirmation() {
        document.getElementById('booking-confirmation-modal')?.remove();
    }

    function showLoginRequiredModal(details = getActiveBookingDetails()) {
        if (!details) {
            return;
        }

        const existingModal = document.getElementById('login-required-modal');
        if (existingModal) {
            existingModal.remove();
        }

        const modalHTML = `
            <div id="login-required-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="WhatsAppBooking.closeLoginRequiredModal(event)">
                <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 relative" onclick="event.stopPropagation()">
                    <button data-action="close-login-modal" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-full hover:bg-gray-100">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lock text-amber-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">تسجيل الدخول مطلوب</h3>
                        <p class="text-gray-600 mb-6">يجب تسجيل الدخول أولاً لحجز الورشة</p>
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-right">
                            <h4 class="font-semibold text-gray-900.mb-2">${details.title}</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>التاريخ:</span>
                                    <span class="font-medium">${details.date}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>المدرب:</span>
                                    <span class="font-medium">${details.instructor}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>المكان:</span>
                                    <span class="font-medium">${details.location}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>السعر:</span>
                                    <span class="font-medium text-green-600">${details.price}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <a href="/login" class="block w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl transition-all.duration-200 shadow-lg hover:shadow-xl" data-login-link>
                                <i class="fas fa-sign-in-alt ml-2"></i>
                                تسجيل الدخول
                            </a>
                            <a href="/register" class="block w-full border-2 border-amber-200 text-amber-600 font-bold py-3 rounded-xl.transition-all.duration-200 hover:bg-amber-50" data-register-link>
                                <i class="fas fa-user-plus ml-2"></i>
                                إنشاء حساب
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        document
            .querySelector('#login-required-modal [data-action="close-login-modal"]')
            ?.addEventListener('click', closeLoginRequiredModal, { once: true });

        document.addEventListener('keydown', escCloseHandler);
    }

    function closeLoginRequiredModal(event) {
        if (event && event.type === 'click' && event.currentTarget?.id !== 'login-required-modal') {
            // Click on close button
            document.getElementById('login-required-modal')?.remove();
            document.removeEventListener('keydown', escCloseHandler);
            return;
        }

        if (event && event.target?.id === 'login-required-modal') {
            document.getElementById('login-required-modal')?.remove();
            document.removeEventListener('keydown', escCloseHandler);
            return;
        }

        if (!event) {
            document.getElementById('login-required-modal')?.remove();
            document.removeEventListener('keydown', escCloseHandler);
        }
    }

    function escCloseHandler(event) {
        if (event.key === 'Escape') {
            closeLoginRequiredModal();
        }
    }

    function persistBooking(workshopId) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const headers = {
            'Content-Type': 'application/json',
        };

        if (csrf) {
            headers['X-CSRF-TOKEN'] = csrf;
        }

        return fetch(state.config.bookingEndpoint, {
            method: 'POST',
            headers,
            credentials: 'same-origin',
            body: JSON.stringify({
                workshop_id: workshopId,
                notes: state.config.bookingNotes,
            }),
        }).then((response) => response.json());
    }

    function openWhatsAppBridgeWindow() {
        try {
            return window.open('', '_blank');
        } catch (error) {
            console.warn('Unable to pre-open WhatsApp window:', error);
            return null;
        }
    }

    function setButtonLoadingState(button, isLoading) {
        if (!button) {
            return;
        }

        button.dataset.loading = isLoading ? 'true' : 'false';
        button.classList.toggle('opacity-60', isLoading);

        if (isLoading) {
            button.disabled = true;
        } else if (button.dataset.isBooked !== 'true') {
            button.disabled = false;
        }
    }

    function markWorkshopAsBooked(workshopId) {
        const selector = `.js-whatsapp-booking[data-workshop-id="${workshopId}"]`;
        const buttons = document.querySelectorAll(selector);

        buttons.forEach((button) => {
            const existingIcon = button.querySelector('.booking-button-icon');
            const hadTextXlIcon = existingIcon ? existingIcon.classList.contains('text-xl') : false;

            button.disabled = true;
            button.dataset.isBooked = 'true';
            button.classList.add('cursor-not-allowed', 'bg-green-500', 'text-white');
            button.classList.remove('hover:bg-green-600', 'hover:bg-green-50', 'bg-white', 'text-green-600', 'opacity-60');

            if (existingIcon) {
                existingIcon.className = 'fas fa-check ml-2 booking-button-icon';
                if (hadTextXlIcon) {
                    existingIcon.classList.add('text-xl');
                }
            }

            const label = button.querySelector('.booking-button-label');
            if (label) {
                label.textContent = 'تم الحجز بالفعل';
            } else {
                button.textContent = 'تم الحجز بالفعل';
            }
        });
    }

    function sendMessage(detailsOrTitle, price, date, instructor, location, deadline, maybeBridgeWindow = null) {
        const { user, whatsappNumber } = state.config;

        let details;
        let bridgeWindow = null;

        if (typeof detailsOrTitle === 'object' && detailsOrTitle !== null && !Array.isArray(detailsOrTitle)) {
            details = {
                title: detailsOrTitle.title || '',
                price: detailsOrTitle.price || '',
                date: detailsOrTitle.date || '',
                instructor: detailsOrTitle.instructor || '',
                location: detailsOrTitle.location || '',
                deadline: detailsOrTitle.deadline || '',
            };

            if (price && typeof price === 'object' && 'closed' in price) {
                bridgeWindow = price;
            }
        } else {
            details = {
                title: detailsOrTitle || '',
                price: price || '',
                date: date || '',
                instructor: instructor || '',
                location: location || '',
                deadline: deadline || '',
            };

            if (typeof maybeBridgeWindow === 'object' && maybeBridgeWindow !== null && 'closed' in maybeBridgeWindow) {
                bridgeWindow = maybeBridgeWindow;
            }
        }

        const whatsappMessage = `مرحباً! أريد حجز مقعد في الورشة التالية:

🏆 *${details.title}*

📅 التاريخ: ${details.date}
👨‍🏫 المدرب: ${details.instructor}
📍 المكان: ${details.location}
💰 السعر: ${details.price}
⏰ آخر موعد للتسجيل: ${details.deadline}

📋 *معلوماتي الشخصية:*
👤 الاسم: ${user.name}
📞 الهاتف: ${user.phone}
📧 البريد الإلكتروني: ${user.email}

يرجى تأكيد الحجز وتوضيح طريقة الدفع. شكراً!

💡 *ملاحظة:* تم حفظ الحجز في نظامنا تلقائياً.`;

        const encodedMessage = encodeURIComponent(whatsappMessage);
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

        if (bridgeWindow && !bridgeWindow.closed) {
            bridgeWindow.location.href = whatsappUrl;
            bridgeWindow.focus();
        } else {
            window.open(whatsappUrl, '_blank');
        }
    }

    function notify(message, type = 'info') {
        if (typeof window.showCustomAlert === 'function') {
            window.showCustomAlert(message, type);
            return;
        }

        console[type === 'error' ? 'error' : 'log'](message); // fallback
    }

    return {
        configure,
        initButtons,
        startFlowFromButton,
        showBookingConfirmation,
        confirmBooking,
        closeBookingConfirmation,
        showLoginRequiredModal,
        closeLoginRequiredModal,
        markWorkshopAsBooked,
        sendMessage,
        setButtonLoadingState,
    };
})();

window.WhatsAppBooking = WhatsAppBooking;
window.startWhatsAppBookingFlow = WhatsAppBooking.startFlowFromButton;
window.confirmBooking = WhatsAppBooking.confirmBooking;
window.closeBookingConfirmation = WhatsAppBooking.closeBookingConfirmation;
window.showBookingConfirmation = WhatsAppBooking.showBookingConfirmation;
window.showLoginRequiredModal = WhatsAppBooking.showLoginRequiredModal;
window.closeLoginRequiredModal = WhatsAppBooking.closeLoginRequiredModal;
window.markWorkshopAsBooked = WhatsAppBooking.markWorkshopAsBooked;
window.sendWhatsAppMessage = WhatsAppBooking.sendMessage;
