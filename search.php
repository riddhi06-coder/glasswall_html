<?php
/**
 * Live search endpoint — returns JSON of matching projects (DB) + products (static).
 * Called by assets/js/search.js as: search.php?q=<term>
 */
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$results = [];

// ---- Project categories -------------------------------------------------
try {
    $catStmt = db()->prepare('SELECT name, slug FROM categories WHERE name LIKE ? ORDER BY sort_order');
    $catStmt->execute(['%' . $q . '%']);
    foreach ($catStmt as $r) {
        $results[] = [
            'type'  => 'Category',
            'title' => $r['name'],
            'sub'   => 'Project Category',
            'url'   => 'projects.php?category=' . rawurlencode($r['slug']),
        ];
    }
} catch (Throwable $e) {
    // ignore
}

// ---- Products (static pages) + their sub-products (card titles) ----------
$productPages = [
    'facade-and-curtain-wall-systems.html' => 'Facade & Curtain Wall Systems',
    'louvers.html'                          => 'Louvers',
    'rain-screen-cladding.html'             => 'Rain Screen Cladding',
];

$items = [];
foreach ($productPages as $url => $name) {
    // The product page itself.
    $items[] = ['title' => $name, 'sub' => 'Product', 'url' => $url];

    // Sub-products = the <h3> card titles inside .card-content on that page.
    $file = __DIR__ . '/' . $url;
    if (is_file($file)) {
        $html = file_get_contents($file);
        if (preg_match_all('/<div class="card-content">\s*<h3[^>]*>(.*?)<\/h3>/is', $html, $m)) {
            foreach ($m[1] as $t) {
                $t = trim(preg_replace('/\s+/', ' ', strip_tags($t)));
                if ($t !== '') {
                    $items[] = ['title' => $t, 'sub' => $name, 'url' => $url];
                }
            }
        }
    }
}

$needle = mb_strtolower($q);
foreach ($items as $it) {
    if (mb_strpos(mb_strtolower($it['title']), $needle) !== false) {
        $results[] = ['type' => 'Product', 'title' => $it['title'], 'sub' => $it['sub'], 'url' => $it['url']];
    }
}

// ---- Projects (from DB) — match title, location or client ----
try {
    $stmt = db()->prepare(
        'SELECT p.title, p.slug, p.location, c.name AS category
           FROM projects p
           JOIN categories c ON c.id = p.category_id
          WHERE p.is_active = 1
            AND (p.title LIKE :a OR p.location LIKE :b OR p.client LIKE :c)
          ORDER BY (p.title LIKE :d) DESC, p.title
          LIMIT 10'
    );
    $like = '%' . $q . '%';
    $stmt->bindValue(':a', $like);
    $stmt->bindValue(':b', $like);
    $stmt->bindValue(':c', $like);
    $stmt->bindValue(':d', $q . '%');
    $stmt->execute();

    foreach ($stmt as $r) {
        $sub = $r['category'];
        if (!empty($r['location'])) {
            $sub .= ' · ' . $r['location'];
        }
        $results[] = [
            'type'  => 'Project',
            'title' => $r['title'],
            'sub'   => $sub,
            'url'   => 'project-details.php?slug=' . rawurlencode($r['slug']),
        ];
    }
} catch (Throwable $e) {
    // On DB error just return whatever products matched.
}

echo json_encode($results);
