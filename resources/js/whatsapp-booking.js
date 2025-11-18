const WhatsAppBooking = (() => {
    const defaultUser = {
        name: 'مستخدم',
        phone: 'غير محدد',
        email: 'غير محدد',
    };

    const state = {
        config: {
            isLoggedIn: false,
            whatsappNumber: '791567401816',
            bookingEndpoint: '/bookings',
            bookingNotes: 'حجز موحد - واتساب + قاعدة بيانات',
            loginUrl: '/login',
            registerUrl: '/register',
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

        if (typeof partial.loginUrl === 'string' && partial.loginUrl.trim()) {
            state.config.loginUrl = partial.loginUrl.trim();
        }

        if (typeof partial.registerUrl === 'string' && partial.registerUrl.trim()) {
            state.config.registerUrl = partial.registerUrl.trim();
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
            if (button.dataset.whatsappBookingBound === 'true') {
                return;
            }

            button.dataset.whatsappBookingBound = 'true';
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
            topics: button.dataset.topics || '',
            requirements: button.dataset.requirements || '',
            duration: button.dataset.duration || '',
            terms: button.dataset.terms || '',
        };
    }

    function getActiveBookingDetails() {
        return state.activeContext?.details ?? null;
    }

    function formatDetailValue(value, fallback = 'سيتم التحديد لاحقاً') {
        if (typeof value === 'string') {
            const trimmed = value.trim();
            if (trimmed.length) {
                return trimmed;
            }
        }

        if (typeof value === 'number' && Number.isFinite(value)) {
            return String(value);
        }

        return fallback;
    }

    function sanitizeUserField(value, fallback) {
        if (typeof value === 'string') {
            const trimmed = value.trim();
            if (trimmed.length && trimmed !== 'غير محدد') {
                return trimmed;
            }
        }

        if (typeof value === 'number' && Number.isFinite(value)) {
            return String(value);
        }

        return fallback;
    }

    function buildNormalizedUserProfile() {
        const defaults = {
            name: 'مشارك من وصفة',
            phone: 'غير متوفر',
            email: 'غير متوفر',
        };
        const profile = state.config.user || {};

        return {
            name: sanitizeUserField(profile.name, defaults.name),
            phone: sanitizeUserField(profile.phone, defaults.phone),
            email: sanitizeUserField(profile.email, defaults.email),
        };
    }

    function createWorkshopSummaryHTML(details) {
        const summaryItems = [
            {
                icon: 'fa-calendar-alt',
                label: 'موعد الورشة',
                value: formatDetailValue(details.date),
            },
            {
                icon: 'fa-user-tie',
                label: 'المدرب',
                value: formatDetailValue(details.instructor),
            },
            {
                icon: 'fa-map-marker-alt',
                label: 'المكان / النمط',
                value: formatDetailValue(details.location),
            },
            {
                icon: 'fa-hourglass-half',
                label: 'آخر موعد للتسجيل',
                value: formatDetailValue(details.deadline, 'حتى اكتمال المقاعد'),
            },
        ];

        const title = formatDetailValue(details.title, 'ورشة بدون عنوان');
        const priceLabel = formatDetailValue(details.price, 'سيتم مشاركة السعر لاحقاً');

        return `
            <div class="bg-gradient-to-b from-amber-50 via-white to-white border border-amber-100 rounded-3xl p-5 mb-6 shadow-lg">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="text-right">
                        <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest">تفاصيل مختصرة</p>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">${title}</h4>
                        <p class="text-sm text-gray-500">كل ما تحتاجه قبل الدفع عبر الواتساب مع احتساب رسوم خدمة إضافية بقيمة 1 USD</p>
                    </div>
                    <div class="text-left">
                        <p class="text-xs text-gray-500 mb-1">قيمة المشاركة</p>
                        <div class="bg-white text-amber-600 font-bold px-4 py-2 rounded-full shadow-sm whitespace-nowrap">
                            ${priceLabel}
                        </div>
                    </div>
                </div>
                <ul class="space-y-3">
                    ${summaryItems
                        .map(
                            (item) => `
                                <li class="flex items-center justify-between bg-white rounded-2xl px-4 py-3 border border-gray-100 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-base">
                                            <i class="fas ${item.icon}"></i>
                                        </span>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">${item.label}</p>
                                            <p class="text-sm font-semibold text-gray-900">${item.value}</p>
                                        </div>
                                    </div>
                                </li>
                            `,
                        )
                        .join('')}
                </ul>
            </div>
        `;
    }

    function showBookingConfirmation(details = getActiveBookingDetails()) {
        if (!details) {
            return;
        }

        const existingModal = document.getElementById('booking-confirmation-modal');
        if (existingModal) {
            existingModal.remove();
        }

        const summaryHTML = createWorkshopSummaryHTML(details);

        const modalHTML = `
            <div id="booking-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
                <div class="bg-white rounded-3xl p-8 w-full max-w-lg mx-auto shadow-2xl relative overflow-hidden">
                    <div class="absolute -top-20 -left-10 w-40 h-40 bg-amber-100 rounded-full opacity-40 pointer-events-none" aria-hidden="true"></div>
                    <div class="absolute -bottom-24 -right-6 w-56 h-56 bg-orange-100 rounded-full opacity-30 pointer-events-none" aria-hidden="true"></div>
                    <div class="relative text-right">
                        <div class="text-center mb-6">
                            <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-amber-50 text-amber-600 font-semibold text-xs">
                                <i class="fas fa-bolt text-sm"></i>
                                حجز سريع
                            </span>
                            <h3 class="text-2xl font-black text-gray-900 mt-4 mb-2">راجع التفاصيل قبل الدفع</h3>
                            <p class="text-gray-600 text-sm">اطّلع على تفاصيل الورشة المختصرة ثم اضغط زر الدفع لإكمال الحجز عبر الواتساب، مع ملاحظة أنه سيتم إضافة 1 USD على قيمة المشاركة كرسوم خدمة للحجز عبر الواتساب.</p>
                        </div>
                        ${summaryHTML}
                        <div class="space-y-3">
                            <button data-action="confirm-booking" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                <i class="fas fa-credit-card text-lg"></i>
                                إتمام الدفع
                            </button>
                            <button data-action="close-booking-modal" class="w-full bg-white border border-gray-200 text-gray-600 hover:text-gray-800 font-semibold py-3 px-6 rounded-2xl transition-all duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-right"></i>
                                تراجع
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
                markWorkshopAsBooked(context.details.id, data?.booking ?? null);
                sendMessage(context.details, whatsappBridgeWindow);
                const livewireUpdated = dispatchLivewireWhatsappEvent(
                    context.details.id,
                    data?.booking?.id ?? null
                );
                notify('تم حفظ الحجز في النظام وإرسال رسالة الواتساب!', 'success');

                if (!livewireUpdated) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }

                return;
            }

            if (data?.message && data.message.includes('حجز')) {
                markWorkshopAsBooked(context.details.id, data?.booking ?? null);
                notify('تم حجز هذه الورشة بالفعل.', 'success');
                const livewireUpdated = dispatchLivewireWhatsappEvent(
                    context.details.id,
                    data?.booking?.id ?? null
                );

                if (!livewireUpdated) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }

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

        const summaryHTML = createWorkshopSummaryHTML(details);

        const modalHTML = `
            <div id="login-required-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4" onclick="WhatsAppBooking.closeLoginRequiredModal(event)">
                <div class="bg-white rounded-3xl p-8 w-full max-w-lg mx-auto shadow-2xl relative overflow-hidden" onclick="event.stopPropagation()">
                    <button data-action="close-login-modal" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-full hover:bg-gray-100">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="absolute -top-16 -left-10 w-36 h-36 bg-amber-100 rounded-full opacity-40 pointer-events-none" aria-hidden="true"></div>
                    <div class="absolute -bottom-20 -right-6 w-48 h-48 bg-orange-100 rounded-full opacity-30 pointer-events-none" aria-hidden="true"></div>
                    <div class="relative text-right">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 text-2xl mb-4">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-2">سجّل دخولك لإكمال الدفع</h3>
                            <p class="text-gray-600 text-sm">نستخدم حسابك لحفظ بياناتك وتأكيد الحجز ثم نوجّهك مباشرة للدفع عبر الواتساب.</p>
                        </div>
                        ${summaryHTML}
                        <div class="space-y-3">
                            <a href="${state.config.loginUrl}" class="block w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 rounded-2xl transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2" data-login-link>
                                <i class="fas fa-sign-in-alt text-lg"></i>
                                تسجيل الدخول والمتابعة
                            </a>
                            <a href="${state.config.registerUrl}" class="block w-full border-2 border-amber-100 text-amber-600 font-bold py-3.5 rounded-2xl transition-all duration-200 hover:bg-amber-50 flex items-center justify-center gap-2" data-register-link>
                                <i class="fas fa-user-plus text-lg"></i>
                                إنشاء حساب جديد
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
            state.activeContext = null;
            return;
        }

        if (event && event.target?.id === 'login-required-modal') {
            document.getElementById('login-required-modal')?.remove();
            document.removeEventListener('keydown', escCloseHandler);
            state.activeContext = null;
            return;
        }

        if (!event) {
            document.getElementById('login-required-modal')?.remove();
            document.removeEventListener('keydown', escCloseHandler);
            state.activeContext = null;
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

    function markWorkshopAsBooked(workshopId, booking = null) {
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

        showInquirySection(workshopId, booking);
        showPendingAlert(workshopId);
    }

    function sendMessage(detailsOrTitle, price, date, instructor, location, deadline, maybeBridgeWindow = null) {
        const { whatsappNumber } = state.config;

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
                topics: detailsOrTitle.topics || '',
                requirements: detailsOrTitle.requirements || '',
                duration: detailsOrTitle.duration || '',
                terms: detailsOrTitle.terms || '',
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
                topics: '',
                requirements: '',
                duration: '',
                terms: '',
            };

            if (typeof maybeBridgeWindow === 'object' && maybeBridgeWindow !== null && 'closed' in maybeBridgeWindow) {
                bridgeWindow = maybeBridgeWindow;
            }
        }

        const normalizedDetails = {
            title: formatDetailValue(details.title, 'ورشة بدون عنوان'),
            price: formatDetailValue(details.price, 'سيتم مشاركة السعر لاحقاً'),
            date: formatDetailValue(details.date),
            instructor: formatDetailValue(details.instructor),
            location: formatDetailValue(details.location),
            deadline: formatDetailValue(details.deadline, 'حتى اكتمال المقاعد'),
            topics: formatDetailValue(details.topics),
            requirements: formatDetailValue(details.requirements),
            duration: formatDetailValue(details.duration),
            terms: formatDetailValue(details.terms),
        };

        const normalizedUser = buildNormalizedUserProfile();

        const whatsappMessage = `مرحباً،

أرغب في حجز ورشة *${normalizedDetails.title}* عبر موقع وصفة.

📅 التاريخ: ${normalizedDetails.date}
👩‍🏫 المدرب: ${normalizedDetails.instructor}
🌐 الموقع/النمط: ${normalizedDetails.location}
💵 السعر المعلن: ${normalizedDetails.price}
⏰ آخر موعد للتسجيل: ${normalizedDetails.deadline}

تفاصيل إضافية:

📘 موضوعات الورشة: ${normalizedDetails.topics}
🧰 متطلبات الورشة: ${normalizedDetails.requirements}
⏱️ مدة الورشة: ${normalizedDetails.duration}
📄 شروط وأحكام الحجز: ${normalizedDetails.terms}

📋 بياناتي:
👤 الاسم: ${normalizedUser.name}
📞 الهاتف: ${normalizedUser.phone}
📧 البريد الإلكتروني: ${normalizedUser.email}

💬 فضلاً تأكيد الحجز أو تزويدي بطريقة الدفع المناسبة.
💡 ملاحظة: أعلم أن الحجز داخل الورشة يضيف دولاراً إضافياً على السعر، لذلك أفضّل إتمامه الآن عبر واتساب.`;

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

    function initInquiryButtons(selector = '.js-whatsapp-inquiry-button') {
        const buttons = document.querySelectorAll(selector);
        buttons.forEach((button) => {
            if (button.dataset.whatsappInquiryBound === 'true') {
                return;
            }

            button.dataset.whatsappInquiryBound = 'true';
            button.addEventListener(
                'click',
                (event) => {
                    event.preventDefault();
                    startInquiryFromButton(button);
                },
                { passive: false },
            );
        });
    }

    function showInquirySection(workshopId, booking = null) {
        const bookingSection = document.querySelector(`.js-whatsapp-booking-section[data-workshop-id="${workshopId}"]`);
        const inquirySection = document.querySelector(`.js-whatsapp-inquiry-section[data-workshop-id="${workshopId}"]`);

        if (bookingSection) {
            bookingSection.classList.add('hidden');
        }

        if (!inquirySection) {
            return;
        }

        const inquiryButton = inquirySection.querySelector('.js-whatsapp-inquiry-button');
        if (inquiryButton && booking?.public_code) {
            inquiryButton.dataset.bookingCode = booking.public_code;
        }

        inquirySection.classList.remove('hidden');

        const label = inquirySection.parentElement?.querySelector('.js-whatsapp-section-label');
        if (label && label.dataset.followupLabel) {
            label.textContent = label.dataset.followupLabel;
        }
    }

    function showPendingAlert(workshopId) {
        const alert = document.querySelector(`.js-whatsapp-pending-alert[data-workshop-id="${workshopId}"]`);
        if (!alert) {
            return;
        }

        alert.classList.remove('hidden');
    }

    function dispatchLivewireWhatsappEvent(workshopId, bookingId = null) {
        dispatchStripeHideEvent(workshopId);

        if (!window.Livewire || typeof window.Livewire.dispatch !== 'function') {
            return false;
        }

        try {
            window.Livewire.dispatch('workshop-whatsapp-booked', {
                workshopId,
                bookingId,
            });
            return true;
        } catch (error) {
            console.warn('Failed to dispatch Livewire WhatsApp booking event', error);
            return false;
        }
    }

    function buildInquiryMessage(workshopTitle, bookingCode) {
        const normalizedUser = buildNormalizedUserProfile();
        const title = formatDetailValue(workshopTitle, 'ورشة بدون عنوان');
        const normalizedCode = typeof bookingCode === 'string' ? bookingCode.trim() : '';

        return `مرحباً فريق وصفة،

لدي استفسار حول حجز ورشة *${title}*${normalizedCode ? ` (رقم الحجز ${normalizedCode})` : ''}.

📋 بياناتي:
👤 الاسم: ${normalizedUser.name}
📞 الهاتف: ${normalizedUser.phone}
📧 البريد الإلكتروني: ${normalizedUser.email}

فضلاً أحتاج لتحديث حول طلبي. شكراً لكم!`;
    }

    function startInquiryFromButton(button) {
        if (!button) {
            return;
        }

        const workshopTitle = button.dataset.workshopTitle || '';
        const bookingCode = button.dataset.bookingCode || '';
        const whatsappNumber = (state.config.whatsappNumber || '').trim();

        if (!whatsappNumber) {
            notify('لا يمكن فتح محادثة الواتساب حالياً. يرجى تحديث الصفحة والمحاولة مرة أخرى.', 'error');
            return;
        }

        const inquiryMessage = buildInquiryMessage(workshopTitle, bookingCode);
        const encodedMessage = encodeURIComponent(inquiryMessage);
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

        window.open(whatsappUrl, '_blank');
    }

    function dispatchStripeHideEvent(workshopId) {
        try {
            window.dispatchEvent(
                new CustomEvent('workshop-hide-stripe', {
                    detail: {
                        workshopId,
                        elementId: 'stripe-checkout-card',
                    },
                }),
            );
        } catch (error) {
            console.warn('Failed to dispatch Stripe hide event', error);
        }
    }

    return {
        configure,
        initButtons,
        initInquiryButtons,
        startFlowFromButton,
        startInquiryFromButton,
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
window.startWhatsAppBookingInquiry = WhatsAppBooking.startInquiryFromButton;

if (Array.isArray(window.__WHATSAPP_BOOKING_PENDING__) && window.__WHATSAPP_BOOKING_PENDING__.length) {
    while (window.__WHATSAPP_BOOKING_PENDING__.length) {
        const bootstrap = window.__WHATSAPP_BOOKING_PENDING__.shift();
        if (typeof bootstrap === 'function') {
            try {
                bootstrap(WhatsAppBooking);
            } catch (error) {
                console.error('Failed to bootstrap WhatsApp booking module', error);
            }
        }
    }
    window.__WHATSAPP_BOOKING_PENDING__ = [];
}
