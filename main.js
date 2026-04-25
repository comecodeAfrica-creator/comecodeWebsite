 //  MOBILE MENU
        const menu = document.querySelector('#mobile-menu');
        const nav = document.querySelector('#navbar');

        menu.addEventListener('click', function () {
            menu.classList.toggle('is-active');
            nav.classList.toggle('active');
        });

       //let's build button

       function scrollToConnect() {
    document.querySelector('.connect-section').scrollIntoView({
        behavior: 'smooth'
    });
}
        

        // TRUST BADGE
        const badge = document.querySelector('.trust-badge');
        let mouseTimer;
        function showBadge() {
            badge.classList.add('show');
            badge.classList.remove('hide');
        }
        function hideBadge() {
            badge.classList.remove('show');
            badge.classList.add('hide');
        }
        document.addEventListener('mousemove', (e) => {
            badge.style.transform = `translate(${e.clientX + 20}px, ${e.clientY + 20}px)`;
            showBadge();
            if (mouseTimer) clearTimeout(mouseTimer);
            mouseTimer = setTimeout(() => { hideBadge(); }, 1000);
        });
        const buttons = document.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', hideBadge);
            btn.addEventListener('mouseleave', () => { showBadge(); });
        });

        // SCROLL REVEAL 
        const reveals = document.querySelectorAll(".reveal");
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("active");
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.15 }
        );
        reveals.forEach((reveal) => { observer.observe(reveal); });

        // COPY EMAIL 
        const copyBtn = document.getElementById("copyBtn");
        const emailText = document.getElementById("emailText");
        copyBtn.addEventListener("click", () => {
            const email = emailText.textContent;
            navigator.clipboard.writeText(email).then(() => {
                const originalText = copyBtn.textContent;
                copyBtn.textContent = "Copied!";
                copyBtn.disabled = true;
                setTimeout(() => {
                    copyBtn.textContent = originalText;
                    copyBtn.disabled = false;
                }, 5000);
            });
        });

        // FLOATING MESSAGE TOGGLE + ACTIONS
        const container = document.getElementById("messageContainer");
        const toggleBtn = document.getElementById("messageToggle");
        if (toggleBtn && container) {
            const setExpanded = (val) => toggleBtn.setAttribute('aria-expanded', String(val));

            const toggle = (evt) => {
                const isActive = container.classList.toggle('active');
                setExpanded(isActive);
                // If event was a keyboard event, prevent default scrolling
                if (evt && (evt.type === 'keydown')) evt.preventDefault();
            };

            toggleBtn.addEventListener('click', toggle);
            // keyboard support: Enter / Space
            toggleBtn.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') toggle(e);
            });

            // Close the container when clicking outside
            document.addEventListener('click', (e) => {
                if (!container.classList.contains('active')) return;
                if (!container.contains(e.target)) {
                    container.classList.remove('active');
                    setExpanded(false);
                }
            });
        }

        // CALL BUTTON FUNCTIONALITY
        // Uses tel: protocol to open the dialer on supported devices
        const callButton = document.querySelector('.email-message-container .call');
        if (callButton) {
            callButton.style.cursor = 'pointer';
            callButton.addEventListener('click', () => {
                window.location.href = 'tel:+2347070685345';
            });
            // keyboard accessibility
            callButton.setAttribute('tabindex', '0');
            callButton.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    window.location.href = 'tel:+2347070685345';
                }
            });
        }
