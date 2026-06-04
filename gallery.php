<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Gallery';

// Fetch all gallery images ordered by newest first
$stmt = $pdo->query(
    'SELECT id, title, image, created_at
     FROM   gallery
     ORDER  BY created_at DESC'
);
$images = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page hero ──────────────────────────────────────────────── -->
<div class="page-hero">
    <div class="container">
        <h1>Gallery</h1>
        <p>A curated showcase of the world's most extraordinary automobiles.</p>
    </div>
</div>

<!-- ── Gallery grid ───────────────────────────────────────────── -->
<section class="section" aria-label="Photo gallery">
    <div class="container">

        <?php if (empty($images)): ?>
            <div class="no-results">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                <h3>No images yet</h3>
                <p>The gallery is being curated — check back soon.</p>
            </div>

        <?php else: ?>

            <p style="color:var(--clr-text-muted);font-size:.9rem;margin-bottom:var(--sp-8)">
                <?= count($images) ?> <?= count($images) === 1 ? 'photograph' : 'photographs' ?> in the collection.
                Click any image to enlarge.
            </p>

            <!-- Gallery grid — minimum 2 rows guaranteed by CSS auto-fill -->
            <div class="gallery-grid" role="list">
                <?php foreach ($images as $img): ?>
                    <?php
                        $src     = BASE_URL . '/assets/images/gallery/' . htmlspecialchars($img['image']);
                        $caption = htmlspecialchars($img['title']);
                        $year    = date('Y', strtotime($img['created_at']));
                    ?>
                    <div class="gallery-item"
                         role="listitem"
                         data-src="<?= $src ?>"
                         data-caption="<?= $caption ?>"
                         aria-label="<?= $caption ?>"
                         title="<?= $caption ?>">

                        <img
                            src="<?= $src ?>"
                            alt="<?= $caption ?>"
                            loading="lazy"
                        >

                        <div class="gallery-caption">
                            <span><?= $caption ?></span>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Admin upload shortcut (visible only to admins) -->
            <?php if (is_admin()): ?>
                <div style="text-align:center;margin-top:var(--sp-10)">
                    <a href="<?= BASE_URL ?>/admin/gallery-add.php" class="btn btn--ghost">
                        + Upload New Image
                    </a>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<!-- ── About the collection ───────────────────────────────────── -->
<section class="section section--sm"
         style="background:var(--clr-surface);border-top:1px solid var(--clr-border)"
         aria-labelledby="collection-heading">
    <div class="container" style="max-width:800px;text-align:center">
        <h2 id="collection-heading" style="margin-bottom:var(--sp-5)">
            About the Collection
        </h2>
        <p style="color:var(--clr-text-muted)">
            Every photograph in the SpeedNews gallery is selected for its ability to
            capture the soul of the machine — not merely its shape. From studio-lit
            detail shots that reveal the craftsmanship of a carbon-fibre panel, to
            action photographs that freeze a 300 km/h moment in time, our visual
            archive documents the golden age of performance motoring.
        </p>
        <p style="color:var(--clr-text-muted)">
            Brands represented span the full spectrum of the supercar world: Italian
            passion from Ferrari and Lamborghini, German precision from Porsche,
            French excess from Bugatti, British artistry from McLaren and Aston Martin.
            Each image is accompanied by a caption that places the car in its historical
            and engineering context.
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
