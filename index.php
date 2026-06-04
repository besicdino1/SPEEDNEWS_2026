<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Home';

// Fetch 3 latest news articles
$stmt = $pdo->query(
    'SELECT id, title, body, image, created_at
     FROM news
     ORDER BY created_at DESC
     LIMIT 3'
);
$latest_news = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero" aria-label="Homepage hero">
    <div class="hero-bg" role="img" aria-label="High-performance sports car on a racing track"></div>
    <div class="hero-overlay"></div>

    <div class="container hero-content">
        <span class="hero-eyebrow">The Automotive World Awaits</span>

        <h1>Where <span>Speed</span> Meets<br>Storytelling</h1>

        <p>
            SpeedNews is your premier destination for in-depth coverage of the
            world's most extraordinary automobiles — from screaming hypercars to
            the latest electric revolution reshaping the road.
        </p>

        <div class="hero-actions">
            <a href="<?= BASE_URL ?>/news.php" class="btn btn--primary">Latest News</a>
            <a href="<?= BASE_URL ?>/gallery.php" class="btn btn--outline">View Gallery</a>
        </div>
    </div>
</section>

<!-- ============================================================
     INTRO — Editorial  (1 heading + 3 paragraphs)
     ============================================================ -->
<section class="section section--sm" aria-labelledby="intro-heading">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-12);align-items:center">

            <div>
                <span class="hero-eyebrow" style="font-size:.7rem">About SpeedNews</span>
                <h2 id="intro-heading" style="margin-top:var(--sp-4);margin-bottom:var(--sp-6)">
                    Passion for the<br><span style="color:var(--clr-accent)">Open Road</span>
                </h2>

                <p>
                    The automotive world has never moved faster. From the sun-baked
                    asphalt of the Nürburgring to the digital proving grounds of electric
                    vehicle manufacturers, the race to define the future of mobility is
                    being run on every front simultaneously. SpeedNews puts you in the
                    driving seat — delivering expert analysis, breathtaking photography,
                    and the stories behind the machines that define an era.
                </p>

                <p>
                    We cover every segment of the industry with equal depth: the
                    hand-built exotica emerging from Maranello and Sant'Agata Bolognese;
                    the precision-engineered sports cars rolling off lines in Stuttgart
                    and Zuffenhausen; the boundary-pushing electrics redefining what a
                    car can be. Whether you are a lifelong petrolhead or a newcomer
                    discovering the joy of driving, SpeedNews speaks your language.
                </p>

                <p>
                    Our team of journalists, engineers, and racing enthusiasts brings
                    decades of combined experience to every article, review, and
                    investigation. We believe that great automotive writing captures
                    not just the performance figures, but the feeling — the way a car
                    makes you feel alive the moment you press the throttle and hear the
                    engine respond. That feeling is what we chase, every single day.
                </p>

                <a href="<?= BASE_URL ?>/about.php" class="btn btn--ghost" style="margin-top:var(--sp-4)">
                    About Us
                </a>
            </div>

            <!-- Featured image with caption -->
            <figure>
                <img
                    src="<?= BASE_URL ?>/assets/images/editorial.jpg"
                    alt="Close-up of a Ferrari steering wheel and carbon-fibre dashboard"
                    style="border-radius:var(--radius-md);width:100%;aspect-ratio:4/3;object-fit:cover;box-shadow:var(--shadow-lg)"
                >
                <figcaption>
                    The cockpit of a modern hypercar — where art and engineering collide.
                    Carbon fibre, Alcantara, and digital displays replace analogue gauges,
                    yet the thrill remains timeless.
                </figcaption>
            </figure>

        </div>
    </div>
</section>

<!-- ============================================================
     STATS STRIP
     ============================================================ -->
<div style="background:var(--clr-accent);padding:var(--sp-8) 0">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--sp-6);text-align:center">
            <?php foreach ([
                ['250+', 'Articles Published'],
                ['50+',  'Cars Tested'],
                ['12',   'Countries Covered'],
                ['1M+',  'Monthly Readers'],
            ] as [$num, $label]): ?>
            <div>
                <div style="font-size:2.2rem;font-weight:900;color:#fff;line-height:1"><?= $num ?></div>
                <div style="font-size:.8rem;color:rgba(255,255,255,.75);text-transform:uppercase;letter-spacing:1px;margin-top:var(--sp-2)"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ============================================================
     LATEST NEWS
     ============================================================ -->
<section class="section" aria-labelledby="news-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="news-heading">Latest from the Pit Lane</h2>
            <p>Fresh off the press — the stories driving the automotive conversation right now.</p>
        </div>

        <?php if (empty($latest_news)): ?>
            <div class="no-results">
                <p>No articles published yet. Check back soon.</p>
            </div>
        <?php else: ?>
            <div class="news-grid">
                <?php foreach ($latest_news as $article): ?>
                    <?php
                        $excerpt = strip_tags($article['body']);
                        $excerpt = mb_strlen($excerpt) > 160
                            ? mb_substr($excerpt, 0, 160) . '…'
                            : $excerpt;
                        $date = date('d M Y', strtotime($article['created_at']));
                    ?>
                    <article class="card">
                        <?php if ($article['image']): ?>
                            <img
                                class="card-img"
                                src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($article['image']) ?>"
                                alt="<?= htmlspecialchars($article['title']) ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="card-img-placeholder">🏎</div>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="card-meta">
                                <span class="tag">News</span>
                                <time datetime="<?= htmlspecialchars($article['created_at']) ?>"><?= $date ?></time>
                            </div>
                            <h3 class="card-title">
                                <a href="<?= BASE_URL ?>/news-single.php?id=<?= (int)$article['id'] ?>">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h3>
                            <p class="card-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                        </div>

                        <div class="card-footer">
                            <a href="<?= BASE_URL ?>/news-single.php?id=<?= (int)$article['id'] ?>"
                               class="btn btn--ghost btn--sm">
                                Read More &rarr;
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div style="text-align:center;margin-top:var(--sp-10)">
                <a href="<?= BASE_URL ?>/news.php" class="btn btn--outline">
                    View All Articles
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================
     SOCIAL MEDIA STRIP
     ============================================================ -->
<section class="section section--sm" style="background:var(--clr-surface);border-top:1px solid var(--clr-border);border-bottom:1px solid var(--clr-border)" aria-label="Follow us">
    <div class="container" style="text-align:center">
        <h3 style="margin-bottom:var(--sp-3)">Follow the Speed</h3>
        <p style="color:var(--clr-text-muted);margin-bottom:var(--sp-6)">
            Stay connected for breaking news, exclusive photography, and behind-the-scenes access.
        </p>

        <div class="social-icons" style="justify-content:center;gap:var(--sp-4)">

            <a href="#" class="social-icon" aria-label="Facebook" rel="noopener noreferrer"
               style="width:52px;height:52px">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                </svg>
            </a>

            <a href="#" class="social-icon" aria-label="X (Twitter)" rel="noopener noreferrer"
               style="width:52px;height:52px">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 4l16 16M20 4 4 20"/>
                </svg>
            </a>

            <a href="#" class="social-icon" aria-label="Instagram" rel="noopener noreferrer"
               style="width:52px;height:52px">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                    <circle cx="12" cy="12" r="4"/>
                    <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                </svg>
            </a>

            <a href="#" class="social-icon" aria-label="YouTube" rel="noopener noreferrer"
               style="width:52px;height:52px">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/>
                    <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="currentColor" stroke="none"/>
                </svg>
            </a>

            <a href="#" class="social-icon" aria-label="TikTok" rel="noopener noreferrer"
               style="width:52px;height:52px">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>
                </svg>
            </a>

        </div>
    </div>
</section>

<!-- ============================================================
     NEWSLETTER TEASER
     ============================================================ -->
<section class="section section--sm" aria-labelledby="newsletter-heading">
    <div class="container" style="max-width:640px;text-align:center">
        <span class="hero-eyebrow" style="font-size:.7rem">Newsletter</span>
        <h2 id="newsletter-heading" style="margin-top:var(--sp-4);margin-bottom:var(--sp-4)">
            Never Miss a Story
        </h2>
        <p style="color:var(--clr-text-muted);margin-bottom:var(--sp-6)">
            Subscribe via our contact page and receive weekly highlights, exclusive
            reviews, and early access to gallery drops — straight to your inbox.
        </p>
        <a href="<?= BASE_URL ?>/contact.php" class="btn btn--primary">Subscribe Now</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
