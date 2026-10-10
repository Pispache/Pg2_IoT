(() => {
    const dialogs = document.querySelectorAll('dialog.edit-modal');
    const syncScroll = () => document.body.classList.toggle('modal-open', !!document.querySelector('dialog.edit-modal[open]'));
    const openModal = (dialog) => {
        if (!dialog || dialog.open) return;
        dialog.showModal();
        syncScroll();
        const firstField = dialog.querySelector('[aria-invalid="true"]') || dialog.querySelector('.edit-field input:not([type="hidden"]), .edit-field select, .edit-field textarea');
        firstField?.focus({ preventScroll: true });
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-modal-open]');
        if (trigger) openModal(document.getElementById(trigger.dataset.modalOpen));
        const close = event.target.closest('[data-modal-close]');
        if (close) close.closest('dialog')?.close();
    });

    dialogs.forEach((dialog) => {
        let startedOutside = false;
        const outside = (event) => {
            const rect = dialog.getBoundingClientRect();
            return event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
        };
        dialog.addEventListener('keydown', (event) => {
            if (event.key !== 'Tab') return;
            const fields = [...dialog.querySelectorAll('button, input:not([type="hidden"]), select, textarea, a[href], [tabindex]')]
                .filter(el => !el.disabled && el.tabIndex >= 0 && el.getClientRects().length);
            const first = fields[0];
            const last = fields[fields.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });
        dialog.addEventListener('pointerdown', (event) => {
            startedOutside = event.target === dialog && outside(event);
        });
        dialog.addEventListener('click', (event) => {
            if (startedOutside && event.target === dialog && outside(event)) dialog.close();
            startedOutside = false;
        });
        dialog.addEventListener('close', () => {
            dialog.querySelector('form').reset();
            syncScroll();
        });
        if (dialog.hasAttribute('data-open-on-load')) openModal(dialog);
    });
})();
