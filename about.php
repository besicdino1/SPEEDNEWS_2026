<?php
require_once __DIR__ . '/includes/auth.php';

$page_title = 'About Us';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page hero ──────────────────────────────────────────────── -->
<div class="page-hero">
    <div class="container">
        <h1>About SpeedNews</h1>
        <p>The story behind the magazine — and the people who live it.</p>
    </div>
</div>

<!-- ── Mission section ────────────────────────────────────────── -->
<section class="section" aria-labelledby="mission-heading">
    <div class="container">
        <div class="about-grid">

            <!-- Text -->
            <div>
                <span class="hero-eyebrow" style="font-size:.7rem">Our Mission</span>
                <h2 id="mission-heading" style="margin-top:var(--sp-4);margin-bottom:var(--sp-6)">
                    Driven by a Passion<br>
                    <span style="color:var(--clr-accent)">for the Extraordinary</span>
                </h2>

                <p>
                    SpeedNews was founded in 2018 by a group of motoring journalists,
                    engineers, and racing enthusiasts who shared one conviction: that the
                    world deserved a publication willing to go deeper than press releases
                    and manufacturer talking points. We set out to tell the real story of
                    the automotive world — the engineering breakthroughs, the creative
                    decisions, the human drama behind the machines that capture our
                    collective imagination.
                </p>

                <p>
                    From our first issue — a behind-the-scenes investigation into the
                    development of a record-breaking hypercar — we established a
                    reputation for access, accuracy, and writing that respects the
                    intelligence of our readers. We do not simply report on cars; we
                    contextualise them within the broader currents of technology, culture,
                    motorsport, and design. A new Ferrari is not just a faster machine —
                    it is a statement about where human ambition is pointed at this
                    particular moment in history.
                </p>

                <p>
                    Today SpeedNews reaches over one million readers every month across
                    print and digital platforms. Our editorial team spans twelve countries,
                    giving us unmatched access to every major automotive hub — from
                    Silicon Valley's electric disruptors to the storied ateliers of
                    Modena. Whatever is happening at the sharp end of motoring, our
                    journalists are already there.
                </p>

                <p>
                    We believe that great automotive journalism serves a purpose beyond
                    entertainment. It documents a pivotal era — a moment when the internal
                    combustion engine, which shaped the entire twentieth century, is giving
                    way to something new, and when the very definition of what a car
                    can be is being rewritten in real time. That story deserves to be told
                    with the craft and rigour it commands, and that is the standard we
                    hold ourselves to every day.
                </p>
            </div>

            <!-- Featured video -->
            <div>
                <div class="about-video-wrap" role="region" aria-label="Featured video">
                    <!--
                        Car-related YouTube embed — Ferrari 296 GTB review.
                        Replace the video ID (dQw4w9WgXcQ is a placeholder) with
                        any public car video ID e.g. a supercar review or highlight.
                    -->
					<!--
                    <iframe
                        src="https://www.youtube.com/watch?v=MvVXL-vBQs0"
                        title="Ferrari SF90 Stradale — Full Review | SpeedNews"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
					-->
					<iframe 
						width="1335" height="751"
						src="https://www.youtube.com/embed/MvVXL-vBQs0"
						title="Ferrari SF90 Stradale - Official Video" frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						referrerpolicy="strict-origin-when-cross-origin"
						allowfullscreen>
					</iframe>
                </div>
                <p style="font-size:.82rem;color:var(--clr-text-muted);margin-top:var(--sp-3);text-align:center">
                    Watch: Ferrari SF90 Stradale — the hybrid hypercar that redefined the Prancing Horse.
                </p>
            </div>

        </div><!-- /.about-grid -->
    </div>
</section>

<!-- ── Stats strip ────────────────────────────────────────────── -->
<div class="about-stats container" style="margin-bottom:var(--sp-16)">
    <?php foreach ([
        ['2018',   'Year Founded'],
        ['1M+',    'Monthly Readers'],
        ['250+',   'Articles Published'],
        ['50+',    'Cars Tested'],
        ['12',     'Countries Covered'],
        ['3',      'Industry Awards'],
    ] as [$num, $label]): ?>
    <div class="stat-item">
        <span class="stat-number"><?= $num ?></span>
        <span class="stat-label"><?= $label ?></span>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Team section ───────────────────────────────────────────── -->
<section class="section section--sm"
         style="background:var(--clr-surface);border-top:1px solid var(--clr-border)"
         aria-labelledby="team-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="team-heading">Meet the Team</h2>
            <p>The writers, photographers, and engineers who make SpeedNews possible.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:var(--sp-6)">
            <?php
            $team = [
                [
                    'name'  => 'Alex Turner',
                    'role'  => 'Editor-in-Chief',
                    'bio'   => 'Former racing driver turned journalist. Alex has tested over 200 cars across five continents and holds a lap record at the Nürburgring Nordschleife.',
                    'emoji' => '🏆',
                ],
                [
                    'name'  => 'Maria Santos',
                    'role'  => 'Senior Editor',
                    'bio'   => 'Mechanical engineering graduate and road-test specialist. Maria\'s technical reviews are required reading for every manufacturer\'s development team.',
                    'emoji' => '⚙️',
                ],
                [
                    'name'  => 'James Okafor',
                    'role'  => 'Motorsport Correspondent',
                    'bio'   => 'Trackside at every major race series. James brings a driver\'s perspective to Formula 1, WEC, and the growing world of electric motorsport.',
                    'emoji' => '🏁',
                ],
                [
                    'name'  => 'Sophia Müller',
                    'role'  => 'Photography Director',
                    'bio'   => 'Award-winning automotive photographer whose work has appeared in the world\'s most prestigious motoring publications for over fifteen years.',
                    'emoji' => '📷',
                ],
            ];
            foreach ($team as $member): ?>
            <div style="background:var(--clr-surface-2);border:1px solid var(--clr-border);border-radius:var(--radius-md);padding:var(--sp-6);text-align:center;transition:border-color var(--transition)"
                 onmouseover="this.style.borderColor='var(--clr-accent)'"
                 onmouseout="this.style.borderColor='var(--clr-border)'">
                <div style="font-size:2.5rem;margin-bottom:var(--sp-4)"><?= $member['emoji'] ?></div>
                <h3 style="font-size:1.1rem;margin-bottom:var(--sp-1)"><?= htmlspecialchars($member['name']) ?></h3>
                <div style="font-size:.8rem;color:var(--clr-accent);text-transform:uppercase;letter-spacing:1px;margin-bottom:var(--sp-4)">
                    <?= htmlspecialchars($member['role']) ?>
                </div>
                <p style="font-size:.88rem;color:var(--clr-text-muted);margin:0;line-height:1.6">
                    <?= htmlspecialchars($member['bio']) ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Values ─────────────────────────────────────────────────── -->
<section class="section" aria-labelledby="values-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="values-heading">What We Stand For</h2>
            <p>The principles that guide every article, photograph, and review we publish.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--sp-6)">
            <?php
            $values = [
                ['🎯', 'Accuracy First',       'Every specification, lap time, and technical claim is verified before publication. We correct errors promptly and transparently.'],
                ['🔍', 'Independent Voices',   'We accept no manufacturer advertising in our editorial content. Our reviews reflect what we actually experience behind the wheel, nothing more.'],
                ['📖', 'Depth Over Speed',      'We would rather publish one thorough analysis than ten shallow takes. The cars we cover deserve writing that matches their own ambition.'],
                ['🌍', 'Global Perspective',    'Motoring culture looks different from Tokyo, São Paulo, Munich, and Detroit. We reflect that diversity in our coverage and our team.'],
            ];
            foreach ($values as [$icon, $title, $text]): ?>
            <div style="background:var(--clr-surface);border:1px solid var(--clr-border);border-left:4px solid var(--clr-accent);border-radius:0 var(--radius-md) var(--radius-md) 0;padding:var(--sp-6)">
                <div style="font-size:1.8rem;margin-bottom:var(--sp-3)"><?= $icon ?></div>
                <h3 style="font-size:1rem;margin-bottom:var(--sp-3)"><?= htmlspecialchars($title) ?></h3>
                <p style="font-size:.88rem;color:var(--clr-text-muted);margin:0;line-height:1.7">
                    <?= htmlspecialchars($text) ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── CTA ────────────────────────────────────────────────────── -->
<section class="section section--sm"
         style="background:var(--clr-accent)"
         aria-labelledby="cta-heading">
    <div class="container" style="text-align:center">
        <h2 id="cta-heading" style="color:#fff;margin-bottom:var(--sp-4)">
            Ready to Join the Conversation?
        </h2>
        <p style="color:rgba(255,255,255,.85);margin-bottom:var(--sp-6)">
            Create a free account to save articles, leave comments, and stay up to
            date with the SpeedNews newsletter.
        </p>
        <div style="display:flex;gap:var(--sp-4);justify-content:center;flex-wrap:wrap">
            <a href="<?= BASE_URL ?>/register.php" class="btn btn--outline">Create Account</a>
            <a href="<?= BASE_URL ?>/contact.php"
               style="background:rgba(0,0,0,.25);color:#fff;padding:var(--sp-3) var(--sp-6);border-radius:var(--radius-sm);font-weight:600;text-decoration:none;border:2px solid rgba(255,255,255,.3);transition:background var(--transition)"
               onmouseover="this.style.background='rgba(0,0,0,.4)'"
               onmouseout="this.style.background='rgba(0,0,0,.25)'">
                Get in Touch
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
