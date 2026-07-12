/**
 * LYNVAII Hotel Booking System — Admin App JS
 */

document.addEventListener('DOMContentLoaded', () => {
    // Handle Modals (Close on backdrop click)
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                backdrop.classList.remove('active');
            }
        });
    });

    // Prevent modal close when clicking inside modal
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Handle Escape key to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal-backdrop.active');
            if (activeModal) {
                activeModal.classList.remove('active');
            }
        }
    });
});
