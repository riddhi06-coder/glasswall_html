<?php
require_once __DIR__ . '/config/db.php';

// ---- Resolve category (default: residential) ----------------
$category_slug = isset($_GET['category']) ? trim($_GET['category']) : 'residential';

$catStmt = db()->prepare('SELECT id, name, slug, banner_image FROM categories WHERE slug = ? LIMIT 1');
$catStmt->execute([$category_slug]);
$category = $catStmt->fetch();

// Unknown category -> fall back to the first category.
if (!$category) {
    $category = db()->query('SELECT id, name, slug, banner_image FROM categories ORDER BY sort_order LIMIT 1')->fetch();
}

// ---- Fetch projects for this category -----------------------
$projects = [];
if ($category) {
    $projStmt = db()->prepare(
        'SELECT title, slug, thumb_image, thumb_height, location
           FROM projects
          WHERE category_id = ? AND is_active = 1
          ORDER BY sort_order, id'
    );
    $projStmt->execute([$category['id']]);
    $projects = $projStmt->fetchAll();
}

$page_title = ($category ? $category['name'] : 'Projects') . ' – Glass Wall Systems';

require __DIR__ . '/includes/header.php';
?>

                <!-- hero area start -->
<section class="tp-breadcrumb-area tp-bg tp-overlay p-relative" data-background="assets/images/banner/projects.webp">
        <div class="container">
            <div class="tp-breadcrumb pb-50">
                <div class="page-heading">
                <h1 class="tp-breadcrumb-title tp-text-white margin-0">Our Projects</h1>
                </div>
                <div class="tp-breadcrumb-menu tp-flex-center mb-15 pt-35">
                    <span><a href="index.html">Home</a></span>
                    <span class="tp-breadcrumb-dvdr">-</span>
                    <span>Our Projects</span>
                </div>
            </div>
        </div>
     </section>
                <!-- hero area end -->

<!-- projects-area (masonry) start -->
<section class="tp-services-area tp-services-spacing-4 fix">
    <div class="container">
        <div class="tp-services-heading">
            <div class="row">
                <div class="col-md-12 col-sm-12 text-center">
                    <div class="tp-section-title-wrap">
                        <span class="tp-section-sub-title mb-15 tp_fade_anim" data-duration=".9">Our Projects</span>
                        <h2 class="tp-section-title tp_fade_anim" data-duration=".9" data-delay=".2">Explore <?= e($category ? $category['name'] : 'Projects') ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Isotope grid wrapper -->
        <div class="row tp-project-masonry-grid">
            <div class="grid-sizer col-lg-4 col-md-6 col-sm-6"></div>

            <?php if (empty($projects)): ?>
            <div class="col-12 text-center py-5">
                <p>No projects found in this category yet.</p>
            </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
            <div class="grid-item col-lg-4 col-md-6 col-sm-6 mb-30">
                <div class="tpservices2 p-relative">
                    <?php
                        // Portrait (tall building) thumbs show the whole image at natural height;
                        // landscape thumbs keep their fixed masonry height.
                        $thumb_abs = __DIR__ . '/' . ltrim($project['thumb_image'], '/');
                        $thumb_portrait = false;
                        if (is_file($thumb_abs)) {
                            $td = @getimagesize($thumb_abs);
                            if ($td && !empty($td[1]) && ($td[0] / $td[1]) < 0.95) $thumb_portrait = true;
                        }
                    ?>
                    <div class="tpservices2__thumb br-15 mb-40">
                        <img src="<?= e($project['thumb_image']) ?>" alt="<?= e($project['title']) ?>" class="project-thumb-img"
                             style="<?= $thumb_portrait ? 'height:auto;' : 'height:' . (int) $project['thumb_height'] . 'px;' ?>">
                    </div>
                    <div class="tpservices2__main">
                        <div class="tpservices2__content">
                            <h3 class="tpservices2__title tp-fs-24 mb-10"><a href="project-details.php?slug=<?= e($project['slug']) ?>"><?= e($project['title']) ?></a></h3>
                            <?php if (!empty($project['location'])):
                                $location = $project['location'];
                                // Indian projects store only the city; append the country for display.
                                if ($category['slug'] !== 'international' && strpos($location, ',') === false) {
                                    $location .= ', India';
                                }
                            ?>
                            <span class="tpportfolio__date"><?= e($location) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tpservices2__link">
                        <a href="project-details.php?slug=<?= e($project['slug']) ?>" class="tpservices2__btn" aria-label="View <?= e($project['title']) ?>">
                            <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.000408173 7.39795H16.7596" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10"/>
                            <path d="M10.1596 0C10.1596 4.08932 13.6667 7.39831 18.0008 7.39831" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10"/>
                            <path d="M18.0008 7.39807C13.6667 7.39807 10.1596 10.7071 10.1596 14.7964" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>
<!-- projects-area (masonry) end -->

<?php require __DIR__ . '/includes/footer.php'; ?>
