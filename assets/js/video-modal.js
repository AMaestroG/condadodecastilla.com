document.addEventListener('DOMContentLoaded', () => {
    const videoModal = document.getElementById('video-modal');

    // Proceed only if the modal exists on the current page
    if (!videoModal) {
        return;
    }

    const openModalButtons = document.querySelectorAll('.open-video-modal');
    const closeModalButtons = videoModal.querySelectorAll('.close-video-modal'); // Query within the modal
    const iframe = videoModal.querySelector('iframe');

    if (!iframe) {
        // console.warn('Video modal iframe not found. Video modal functionality may be impaired.');
        return;
    }
    const videoSrc = iframe.src; // Store original src to reset it (and potentially enable autoplay)

    openModalButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            iframe.src = videoSrc; // Reset src, useful if autoplay is part of the src or to restart
            videoModal.classList.remove('hidden');
            videoModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            // Optional: focus on the modal or an element within it for accessibility
            // videoModal.focus(); or iframe.focus(); (check iframe focusability)
        });
    });

    const closeTheModal = () => {
        videoModal.classList.add('hidden');
        videoModal.setAttribute('aria-hidden', 'true');
        iframe.src = ''; // Stop video by removing src. Important for performance & UX.
        document.body.style.overflow = ''; // Restore scrolling
    };

    closeModalButtons.forEach(button => {
        button.addEventListener('click', closeTheModal);
    });

    // Close modal if backdrop (the modal itself, acting as overlay) is clicked
    videoModal.addEventListener('click', (event) => {
        if (event.target === videoModal) {
            closeTheModal();
        }
    });

    // Keyboard accessibility: Close with Escape key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !videoModal.classList.contains('hidden')) {
            closeTheModal();
        }
    });
});
