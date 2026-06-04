<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

// ── Validate ID ───────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id < 1) {
    header('Location: ' . BASE_URL . '/news.php');
    exit;
}

// ── Fetch article + author ────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT n.id, n.title, n.body, n.image, n.created_at,
            u.firstname, u.lastname
     FROM   news n
     JOIN   users u ON u.id = n.user_id
     WHERE  n.id = :id
     LIMIT  1'
);
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    header('HTTP/1.1 404 Not Found');
    $page_title = 'Article Not Found';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="no-results">
            <h2>Article not found</h2>
            <p>The article you are looking for does not exist or has been removed.</p>
            <a href="' . BASE_URL . '/news.php" class="btn btn--primary" style="margin-top:1rem">Back to News</a>
          </div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ── Prev / Next navigation ────────────────────────────────────
$prev = $pdo->prepare(
    'SELECT id, title FROM news WHERE id < :id ORDER BY id DESC LIMIT 1'
);
$prev->execute([':id' => $id]);
$prev_article = $prev->fetch();

$next = $pdo->prepare(
    'SELECT id, title FROM news WHERE id > :id ORDER BY id ASC LIMIT 1'
);
$next->execute([':id' => $id]);
$next_article = $next->fetch();

// ── Related articles (3 most recent, excluding current) ───────
$related_stmt = $pdo->prepare(
    'SELECT id, title, image, created_at
     FROM   news
     WHERE  id != :id
     ORDER  BY created_at DESC
     LIMIT  3'
);
$related_stmt->execute([':id' => $id]);
$related = $related_stmt->fetchAll();

$page_title = $article['title'];
$author     = htmlspecialchars($article['firstname'] . ' ' . $article['lastname']);
$date_fmt   = date('d F Y', strtotime($article['created_at']));
$date_iso   = htmlspecialchars($article['created_at']);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Breadcrumb ─────────────────────────────────────────────── -->
<div style="background:var(--clr-surface);border-bottom:1px solid var(--clr-border)">
    <div class="container" style="padding-block:var(--sp-3)">
        <nav aria-label="Breadcrumb" style="font-size:.85rem;color:var(--clr-text-muted)">
            <a href="<?= BASE_URL ?>/index.php" style="color:var(--clr-text-muted)">Home</a>
            <span style="margin-inline:var(--sp-2)">/</span>
            <a href="<?= BASE_URL ?>/news.php" style="color:var(--clr-text-muted)">News</a>
            <span style="margin-inline:var(--sp-2)">/</span>
            <span style="color:var(--clr-accent)"><?= htmlspecialchars(mb_strimwidth($article['title'], 0, 60, '…')) ?></span>
        </nav>
    </div>
</div>

<!-- ── Article ────────────────────────────────────────────────── -->
<div class="section">
    <div class="container" style="max-width:860px">

        <!-- Header -->
        <header class="article-header">
            <div class="card-meta" style="margin-bottom:var(--sp-4)">
                <span class="tag">News</span>
                <time datetime="<?= $date_iso ?>"><?= $date_fmt ?></time>
                <span>By <strong style="color:var(--clr-text)"><?= $author ?></strong></span>
            </div>

            <h1><?= htmlspecialchars($article['title']) ?></h1>
        </header>

        <!-- Hero image -->
        <?php if ($article['image']): ?>
            <figure style="margin-bottom:var(--sp-8)">
                <img
                    class="article-hero"
                    src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($article['image']) ?>"
                    alt="<?= htmlspecialchars($article['title']) ?>"
                >
                <figcaption><?= htmlspecialchars($article['title']) ?></figcaption>
            </figure>
        <?php endif; ?>

        <!-- Body -->
        <div class="article-body">
            <?php
                // Body is stored as plain text with \n\n paragraph breaks.
                // Convert to safe HTML paragraphs.
                $paragraphs = preg_split('/\r?\n\r?\n/', trim($article['body']));
                foreach ($paragraphs as $para):
                    $para = trim($para);
                    if ($para === '') continue;
            ?>
                <p><?= nl2br(htmlspecialchars($para)) ?></p>
            <?php endforeach; ?>
        </div>

        <!-- Share strip -->
        <div style="margin-top:var(--sp-10);padding-top:var(--sp-6);border-top:1px solid var(--clr-border);display:flex;align-items:center;gap:var(--sp-4);flex-wrap:wrap">
            <span style="font-size:.85rem;color:var(--clr-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:1px">Share</span>
            <?php
                $share_url   = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                $share_title = urlencode($article['title']);
            ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>"
               target="_blank" rel="noopener noreferrer"
               class="btn btn--outline btn--sm">Facebook</a>
            <a href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&text=<?= $share_title ?>"
               target="_blank" rel="noopener noreferrer"
               class="btn btn--outline btn--sm">X / Twitter</a>
        </div>

        <!-- Prev / Next navigation -->
        <?php if ($prev_article || $next_article): ?>
            <nav aria-label="Article navigation"
                 style="margin-top:var(--sp-10);display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-4)">

                <?php if ($prev_article): ?>
                    <a href="<?= BASE_URL ?>/news-single.php?id=<?= (int)$prev_article['id'] ?>"
                       style="background:var(--clr-surface);border:1px solid var(--clr-border);border-radius:var(--radius-md);padding:var(--sp-5);text-decoration:none;transition:border-color var(--transition)"
                       onmouseover="this.style.borderColor='var(--clr-accent)'"
                       onmouseout="this.style.borderColor='var(--clr-border)'">
                        <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:1px;color:var(--clr-text-muted);margin-bottom:var(--sp-2)">&larr; Previous</div>
                        <div style="color:var(--clr-white);font-weight:600;font-size:.95rem;line-height:1.4">
                            <?= htmlspecialchars(mb_strimwidth($prev_article['title'], 0, 70, '…')) ?>
                        </div>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <?php if ($next_article): ?>
                    <a href="<?= BASE_URL ?>/news-single.php?id=<?= (int)$next_article['id'] ?>"
                       style="background:var(--clr-surface);border:1px solid var(--clr-border);border-radius:var(--radius-md);padding:var(--sp-5);text-decoration:none;text-align:right;transition:border-color var(--transition)"
                       onmouseover="this.style.borderColor='var(--clr-accent)'"
                       onmouseout="this.style.borderColor='var(--clr-border)'">
                        <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:1px;color:var(--clr-text-muted);margin-bottom:var(--sp-2)">Next &rarr;</div>
                        <div style="color:var(--clr-white);font-weight:600;font-size:.95rem;line-height:1.4">
                            <?= htmlspecialchars(mb_strimwidth($next_article['title'], 0, 70, '…')) ?>
                        </div>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

            </nav>
        <?php endif; ?>

    </div><!-- /.container -->
</div>

<!-- ── Related articles ───────────────────────────────────────── -->
<?php if (!empty($related)): ?>
<section class="section section--sm" style="background:var(--clr-surface);border-top:1px solid var(--clr-border)" aria-labelledby="related-heading">
    <div class="container">
        <div class="section-header" style="margin-bottom:var(--sp-8)">
            <h2 id="related-heading" style="font-size:1.5rem">More Stories</h2>
        </div>

        <div class="news-grid">
            <?php foreach ($related as $rel): ?>
                <?php
                    $rel_date = date('d M Y', strtotime($rel['created_at']));
                    $rel_url  = BASE_URL . '/news-single.php?id=' . (int)$rel['id'];
                ?>
                <article class="card">
                    <?php if ($rel['image']): ?>
                        <a href="<?= $rel_url ?>" tabindex="-1" aria-hidden="true">
                            <img
                                class="card-img"
                                src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($rel['image']) ?>"
                                alt="<?= htmlspecialchars($rel['title']) ?>"
                                loading="lazy"
                            >
                        </a>
                    <?php else: ?>
                        <div class="card-img-placeholder" aria-hidden="true">🏎</div>
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="card-meta">
                            <span class="tag">News</span>
                            <time datetime="<?= htmlspecialchars($rel['created_at']) ?>"><?= $rel_date ?></time>
                        </div>
                        <h3 class="card-title" style="font-size:1rem">
                            <a href="<?= $rel_url ?>">
                                <?= htmlspecialchars($rel['title']) ?>
                            </a>
                        </h3>
                    </div>

                    <div class="card-footer">
                        <a href="<?= $rel_url ?>" class="btn btn--ghost btn--sm">Read More &rarr;</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
