const menu = document.querySelector('#mobile-menu');
        const nav = document.querySelector('#navbar');

        menu.addEventListener('click', function () {
            menu.classList.toggle('is-active'); // Animates hamburger
            nav.classList.toggle('active');     // Slides in the menu
        });


        let items = [
            { text: "Website", color: "#6ae3ff" },
            { text: "Apps", color: "#ffb46a" },
            { text: "Designs", color: "#6ae3ff" },
            { text: "Identities", color: "#ffb46a" }
        ];

        let index = 0;
        const title = document.querySelector(".hero-title");

        const titleInitialText = title.textContent;
        const originalColor = "#6ae3ff";
        title.textContent = items[3].text;
        title.style.color = originalColor;
        //items.push({ text: titleInitialText, color: originalColor });

        // initial color
        //title.style.color = items[0].color;

        /*setInterval(() => {
            title.classList.add("fade-out");

            setTimeout(() => {
                index = (index + 1) % items.length;
                title.textContent = items[index].text;
                title.style.color = items[index].color;

                title.classList.remove("fade-out");
                title.classList.add("fade-in");
            }, 500);

        }, 3000);*/

        const badge = document.querySelector('.trust-badge');
        let mouseTimer;

        // Function to show badge
        function showBadge() {
            badge.classList.add('show');
            badge.classList.remove('hide');
        }

        // Function to hide badge
        function hideBadge() {
            badge.classList.remove('show');
            badge.classList.add('hide');
        }

        // Track mouse movement
        document.addEventListener('mousemove', (e) => {
            // Move the badge with cursor
            badge.style.transform = `translate(${e.clientX + 20}px, ${e.clientY + 20}px)`;
            showBadge();

            // Clear previous timer
            if (mouseTimer) clearTimeout(mouseTimer);

            // Hide badge if mouse stops for 1s
            mouseTimer = setTimeout(() => {
                hideBadge();
            }, 1000);
        });

        // Hide badge when hovering buttons
        const buttons = document.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', hideBadge);
            btn.addEventListener('mouseleave', () => {
                // Show again when leaving button
                showBadge();
            });
        });


        const reveals = document.querySelectorAll(".reveal");

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("active");
        observer.unobserve(entry.target); // animate once
      }
    });
  },
  {
    threshold: 0.15, // trigger when 15% visible
  }
);

reveals.forEach((reveal) => {
  observer.observe(reveal);
});


// copied email js code

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


    // message icon butttons

    const container = document.getElementById("messageContainer");
    const toggleBtn = document.getElementById("messageToggle");

    toggleBtn.addEventListener("click", () => {
        container.classList.toggle("active");
    });

