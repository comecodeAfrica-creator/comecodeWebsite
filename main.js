// Wrap the initialization in DOMContentLoaded to avoid race conditions
(function () {
    function init() {
        //  MOBILE MENU
        const menu = document.querySelector('#mobile-menu');
        const nav = document.querySelector('#navbar');

        // Guarded: only attach mobile menu handlers if both elements exist
        if (menu && nav) {
            menu.addEventListener('click', function () {
                menu.classList.toggle('is-active');
                nav.classList.toggle('active');
            });
        }

        // helper: smooth scroll to connect section
        function scrollToConnect() {
            const el = document.querySelector('.connect-section');
            if (el) el.scrollIntoView({ behavior: 'smooth' });
        }

        // TRUST BADGE (optional)
        const badge = document.querySelector('.trust-badge');
        let mouseTimer;
        if (badge) {
            function showBadge() { badge.classList.add('show'); badge.classList.remove('hide'); }
            function hideBadge() { badge.classList.remove('show'); badge.classList.add('hide'); }
            document.addEventListener('mousemove', (e) => {
                if (!badge) return;
                badge.style.transform = `translate(${e.clientX + 20}px, ${e.clientY + 20}px)`;
                showBadge();
                if (mouseTimer) clearTimeout(mouseTimer);
                mouseTimer = setTimeout(() => { hideBadge(); }, 1000);
            });
            const buttons = document.querySelectorAll('button');
            buttons.forEach(btn => { btn.addEventListener('mouseenter', hideBadge); btn.addEventListener('mouseleave', showBadge); });
        }

        // SCROLL REVEAL
        try {
            const reveals = document.querySelectorAll('.reveal');
            if (reveals && reveals.length) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('active');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15 });
                reveals.forEach((reveal) => observer.observe(reveal));
            }
        } catch (err) {
            // IntersectionObserver might not be available or other issue — fail silently
            console.warn('Scroll reveal not initialized:', err && err.message);
        }

        // COPY EMAIL (optional)
        const copyBtn = document.getElementById('copyBtn');
        const emailText = document.getElementById('emailText');
        if (copyBtn && emailText) {
            copyBtn.addEventListener('click', () => {
                const email = emailText.textContent;
                navigator.clipboard.writeText(email).then(() => {
                    const originalText = copyBtn.textContent;
                    copyBtn.textContent = 'Copied!';
                    copyBtn.disabled = true;
                    setTimeout(() => { copyBtn.textContent = originalText; copyBtn.disabled = false; }, 5000);
                });
            });
        }

        // FLOATING MESSAGE TOGGLE + ACTIONS
        const container = document.getElementById('messageContainer');
        const toggleBtn = document.getElementById('messageToggle');
        if (toggleBtn && container) {
            const setExpanded = (val) => toggleBtn.setAttribute('aria-expanded', String(val));
            const toggle = (evt) => {
                const isActive = container.classList.toggle('active');
                setExpanded(isActive);
                if (evt && (evt.type === 'keydown')) evt.preventDefault();
            };
            toggleBtn.addEventListener('click', toggle);
            toggleBtn.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') toggle(e); });
            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!container.classList.contains('active')) return;
                if (!container.contains(e.target)) { container.classList.remove('active'); setExpanded(false); }
            });
        }

        // CALL BUTTON FUNCTIONALITY
        const callButton = document.querySelector('.email-message-container .call');
        if (callButton) {
            callButton.style.cursor = 'pointer';
            callButton.addEventListener('click', () => { window.location.href = 'tel:+2347070685345'; });
            callButton.setAttribute('tabindex', '0');
            callButton.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); window.location.href = 'tel:+2347070685345'; } });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
