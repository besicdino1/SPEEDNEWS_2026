<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'News';

// ── Pagination ────────────────────────────────────────────────
$per_page    = 6;
$current_page_num = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($current_page_num - 1) * $per_page;

// ── Total count ───────────────────────────────────────────────
$total = (int)$pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
$total_pages = (int)ceil($total / $per_page);

// ── Articles for this page ────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT n.id, n.title, n.body, n.image, n.created_at,
            u.firstname, u.lastname
     FROM   news n
     JOIN   users u ON u.id = n.user_id
     ORDER  BY n.created_at DESC
     LIMIT  :limit OFFSET :offset'
);
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page hero ──────────────────────────────────────────────── -->
<div class="page-hero">
    <div class="container">
        <h1>Latest News</h1>
        <p>In-depth coverage from the world of high-performance motoring.</p>
    </div>
</div>

<!-- ── News grid ─────────────────────────────────────────────── -->
<section class="section" aria-label="News articles">
    <div class="container">

        <?php if (empty($articles)): ?>
            <div class="no-results">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <h3>No articles yet</h3>
                <p>Check back soon — new stories are on their way.</p>
            </div>

        <?php else: ?>

            <!-- Article count -->
            <p style="color:var(--clr-text-muted);font-size:.9rem;margin-bottom:var(--sp-8)">
                Showing
                <?= $offset + 1 ?>–<?= min($offset + $per_page, $total) ?>
                of <strong><?= $total ?></strong> articles
            </p>

            <div class="news-grid">
                <?php foreach ($articles as $article): ?>
                    <?php
                        $excerpt = strip_tags($article['body']);
                        $excerpt = mb_strlen($excerpt) > 180
                            ? mb_substr($excerpt, 0, 180) . '…'
                            : $excerpt;
                        $date    = date('d M Y', strtotime($article['created_at']));
                        $author  = htmlspecialchars($article['firstname'] . ' ' . $article['lastname']);
                        $url     = BASE_URL . '/news-single.php?id=' . (int)$article['id'];
                    ?>
                    <article class="card">

                        <!-- Thumbnail -->
                        <?php if ($article['image']): ?>
                            <a href="<?= $url ?>" tabindex="-1" aria-hidden="true">
                                <img
                                    class="card-img"
                                    src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($article['image']) ?>"
                                    alt="<?= htmlspecialchars($article['title']) ?>"
                                    loading="lazy"
                                >
                            </a>
                        <?php else: ?>
                            <div class="card-img-placeholder" aria-hidden="true">🏎</div>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="card-meta">
                                <span class="tag">News</span>
                                <time datetime="<?= htmlspecialchars($article['created_at']) ?>">
                                    <?= $date ?>
                                </time>
                                <span>By <?= $author ?></span>
                            </div>

                            <h2 class="card-title" style="font-size:1.1rem">
                                <a href="<?= $url ?>">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h2>

                            <p class="card-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                        </div>

                        <div class="card-footer">
                            <a href="<?= $url ?>" class="btn btn--ghost btn--sm">
                                Read More &rarr;
                            </a>
                            <time style="font-size:.78rem;color:var(--clr-text-faint)"
                                  datetime="<?= htmlspecialchars($article['created_at']) ?>">
                                <?= $date ?>
                            </time>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>

            <!-- ── Pagination ─────────────────────────────────── -->
            <?php if ($total_pages > 1): ?>
                <nav class="pagination" aria-label="Article pagination">

                    <?php if ($current_page_num > 1): ?>
                        <a href="?page=<?= $current_page_num - 1 ?>"
                           aria-label="Previous page">&laquo;</a>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <?php if ($p === $current_page_num): ?>
                            <span class="current" aria-current="page"><?= $p ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $p ?>" aria-label="Page <?= $p ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($current_page_num < $total_pages): ?>
                        <a href="?page=<?= $current_page_num + 1 ?>"
                           aria-label="Next page">&raquo;</a>
                    <?php endif; ?>

                </nav>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
