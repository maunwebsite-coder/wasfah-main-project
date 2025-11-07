document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('confirmationModal');
    const cancelButton = document.getElementById('cancelButton');
    const confirmButton = document.getElementById('confirmButton');
    const modalMessage = document.getElementById('confirmationModalMessage');

    if (!modal || !cancelButton || !confirmButton || !modalMessage) {
        return;
    }

    let formToSubmit = null;
    const defaultMessage = modalMessage.dataset.defaultMessage || modalMessage.textContent.trim();
    const defaultConfirmLabel = confirmButton.dataset.defaultLabel || confirmButton.textContent.trim();

    const openModal = (form, message, confirmLabel) => {
        formToSubmit = form;
        modalMessage.textContent = message || defaultMessage;
        confirmButton.textContent = confirmLabel || defaultConfirmLabel;
        modal.classList.remove('hidden');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        formToSubmit = null;
        modalMessage.textContent = defaultMessage;
        confirmButton.textContent = defaultConfirmLabel;
    };

    confirmButton.addEventListener('click', () => {
        if (formToSubmit) {
            formToSubmit.submit();
        }
        closeModal();
    });

    cancelButton.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            // Change button type to prevent direct submission
            submitButton.setAttribute('type', 'button');
            
            // Extract custom message from onsubmit attribute
            const onsubmitAttr = form.getAttribute('onsubmit');
            const match = onsubmitAttr.match(/confirm\('(.+?)'\)/);
            const message = match ? match[1] : 'هل أنت متأكد من أنك تريد حذف هذا العنصر؟';
            const confirmLabel = submitButton.getAttribute('data-confirm-button');

            // Remove the onsubmit attribute to prevent the old dialog
            form.removeAttribute('onsubmit');

            submitButton.addEventListener('click', (e) => {
                e.preventDefault();
                openModal(form, message, confirmLabel);
            });
        }
    });
});
