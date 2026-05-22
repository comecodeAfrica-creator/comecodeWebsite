<?php
require_once __DIR__ . '/config.php';
$data = getCommunityData();
$gallery = $data['gallery'];
$events = $data['events'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComeCode Community</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/community.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@400;500&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="grid-lines"></div>

    <script>
        AOS.init();
    </script>

    <header>
        <a href="../index.html">
            <div class="logo">C</div>
        </a>

        <nav id="navbar">
            <ul class="nav-links">
                <li><span class="dot"></span><a href="../index.html">ABOUT US</a></li>
                <li><span class="dot"></span><a href="../porfolio.html">WORKS</a></li>
                <li><span class="dot"></span><a href="community.php">COMMUNITY</a></li>
                <li class="mobile-only"><a href="../contact.html"><button class="btn-outline">CONTACT NOW</button></a>
                </li>
            </ul>
        </nav>

        <div class="header-actions">
            <a href="../contact.html"> <button class="btn-outline desktop-only">CONTACT NOW</button></a>
            <div class="menu-toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </header>

    <section class="community-hero">
        <div class="community-hero-content" data-aos="fade-up" data-aos-duration="1000">
            <span class="badge">COMMUNITY</span>
            <h1 class="section-title">ComeCode Community</h1>
            <p class="section-subtitle">Discover community stories, events, and creative uploads from our growing tech
                family.</p>
            <div class="hero-actions">
                <a href="admin.php"><button class="btn-purple">Admin Dashboard</button></a>
                <a href="#events"><button class="btn-outline">View Events</button></a>
            </div>
        </div>
    </section>

    <section class="community-section">
        <div class="community-container" data-aos="fade-up" data-aos-duration="1000">
            <h2 class="section-title">Community Highlights</h2>
            <p class="section-subtitle">Check out the latest snapshots from our events, member meetups and marketing
                collaborations.</p>

            <div class="image-grid">
                <?php foreach ($gallery as $item): ?>
                    <div class="image-item" data-aos="zoom-in" data-aos-delay="100">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="community-image"
                            alt="<?= htmlspecialchars($item['caption'] ?? 'Community image') ?>">
                        <?php if (!empty($item['caption'])): ?>
                            <div class="image-caption"><?= htmlspecialchars($item['caption']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="events-section" id="events">
        <div class="community-container" data-aos="fade-up" data-aos-duration="1000">
            <h2 class="section-title">Upcoming Community Events</h2>
            <p class="section-subtitle">Join the conversation and stay updated on what our team is creating next.</p>

            <div class="events-grid">
                <?php if (count($events) === 0): ?>
                    <p class="section-subtitle">No events uploaded yet. Check back soon for new announcements.</p>
                <?php endif; ?>

                <?php foreach ($events as $event): ?>
                    <article class="event-card" data-aos="fade-up" data-aos-delay="150">
                        <?php if (!empty($event['image'])): ?>
                            <div class="event-image-wrap">
                                <img src="<?= htmlspecialchars($event['image']) ?>"
                                    alt="<?= htmlspecialchars($event['title']) ?>" class="event-image">
                            </div>
                        <?php endif; ?>
                        <div class="event-content">
                            <span
                                class="event-date"><?= htmlspecialchars(date('F j, Y', strtotime($event['date']))) ?></span>
                            <h3><?= htmlspecialchars($event['title']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="connect-section">
        <div class="connect-container">
            <div class="connect-content" data-aos="slide-right" data-aos-duration="1000">
                <h2 class="connect-title">Become Part of the Community</h2>
                <p class="connect-text">Whether you're a novice or an expert, there's a seat for you at our table. Join
                    us and start contributing today.</p>
            </div>
            <div class="connect-actions" data-aos="slide-left" data-aos-duration="1000" data-aos-delay="200">
                <a href="../contact.html"><button class="btn-purple">Get in touch</button></a>
            </div>
        </div>
    </section>

    <footer class="edens-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col brand-col">
                    <img src="edens-logo.png" alt="ComeCode" class="footer-logo">
                    <p class="brand-bio">We are a data-driven, customer-centric digital marketing and web design agency
                        in Nigeria, serving clients all over the world.</p>
                    <div class="social-icons">
                        <a href="https://facebook.com/YOUR_PAGE" target="_blank" class="icon facebook"
                            aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/YOUR_HANDLE" target="_blank" class="icon twitter"
                            aria-label="Twitter / X">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <a href="https://linkedin.com/company/YOUR_COMPANY" target="_blank" class="icon linkedin"
                            aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="https://instagram.com/YOUR_HANDLE" target="_blank" class="icon instagram"
                            aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://youtube.com/@YOUR_CHANNEL" target="_blank" class="icon youtube"
                            aria-label="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>SERVICES</h4>
                    <ul class="footer-links">
                        <li><a href="#">CAC Registration</a></li>
                        <li><a href="#">Brand Identity</a></li>
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">App Development</a></li>
                        <li><a href="#">Website Management</a></li>
                        <li><a href="../contact.html">Get Started</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>ADDRESS</h4>
                    <address>Ugbowo, Benin City<br>Nigeria.</address>
                </div>

                <div class="footer-col">
                    <h4>CONTACT</h4>
                    <p>For more information contact us on</p>
                    <p class="contact-highlight">+2347070685345</p>
                    <p class="contact-email">admin@comecode.come</p>
                    <p class="contact-email">comecode31@gmail.com</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© 2026 - ComeCode. Top software company in Nigeria. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../main.js"></script>
</body>

</html>