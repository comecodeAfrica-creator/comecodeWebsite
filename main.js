 //  MOBILE MENU
        const menu = document.querySelector('#mobile-menu');
        const nav = document.querySelector('#navbar');

        menu.addEventListener('click', function () {
            menu.classList.toggle('is-active');
            nav.classList.toggle('active');
        });

        // About link — scroll AND close mobile menu
        document.getElementById('aboutNavLink').addEventListener('click', function (e) {
            e.preventDefault();
            // Close menu if open
            menu.classList.remove('is-active');
            nav.classList.remove('active');
            // Scroll to about section
            setTimeout(() => {
                const target = document.getElementById('about_target');
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        });

        //  HERO TITLE WORDS 
        let items = [
            { text: "Website", color: "#6ae3ff" },
            { text: "Apps", color: "#ffb46a" },
            { text: "Designs", color: "#6ae3ff" },
            { text: "Identities", color: "#ffb46a" }
        ];
        let index = 0;
        const title = document.querySelector(".hero-title");
        title.textContent = items[3].text;
        title.style.color = "#6ae3ff";

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

        // FLOATING MESSAGE TOGGLE
        const container = document.getElementById("messageContainer");
        const toggleBtn = document.getElementById("messageToggle");
        toggleBtn.addEventListener("click", () => {
            container.classList.toggle("active");
        });
