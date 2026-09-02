<?php
require_once __DIR__ . '/config/db.php';

// ---- Resolve project by slug --------------------------------
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$project = null;
if ($slug !== '') {
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.banner_image AS category_banner
           FROM projects p
           JOIN categories c ON c.id = p.category_id
          WHERE p.slug = ? AND p.is_active = 1
          LIMIT 1'
    );
    $stmt->execute([$slug]);
    $project = $stmt->fetch();
}

// Not found -> 404.
if (!$project) {
    http_response_code(404);
    $page_title = 'Project not found – Glass Wall Systems';
    require __DIR__ . '/includes/header.php';
    echo '<section class="pt-150 pb-150"><div class="container text-center">'
       . '<h2 class="tp-section-title mb-20">Project not found</h2>'
       . '<p class="mb-30">The project you are looking for does not exist.</p>'
       . '<a class="tp-btn" href="projects.php"><span class="tp-btn-text">Back to Projects</span></a>'
       . '</div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Scope of work items.
$scopeStmt = db()->prepare('SELECT scope_text FROM project_scope WHERE project_id = ? ORDER BY sort_order, id');
$scopeStmt->execute([$project['id']]);
$scope_items = $scopeStmt->fetchAll(PDO::FETCH_COLUMN);

// Detail hero image falls back to the listing thumbnail.
$detail_image = !empty($project['main_image']) ? $project['main_image'] : $project['thumb_image'];

// Choose the display fit by the image's own aspect ratio:
//  - portrait (tall building)  -> show the WHOLE image (contain) over a blurred backdrop
//  - landscape                 -> cover, biased down (trims sky)
$detail_is_portrait = false;
$detail_abs = __DIR__ . '/' . ltrim($detail_image, '/');
if (is_file($detail_abs)) {
    $dim = @getimagesize($detail_abs);
    if ($dim && !empty($dim[1]) && ($dim[0] / $dim[1]) < 0.95) {
        $detail_is_portrait = true;
    }
}

// Replace "&" with "and" in any displayed text.
function no_amp($text) {
    return str_replace(['&amp;', '&'], 'and', (string) $text);
}

// Info fields shown only when a value exists.
// Order: Location, Client, Architect, Consultant, Project Type, Façade Area, Year.
$info_fields = [
    'Location'     => $project['location'],      // City, Country (e.g. Mumbai, India)
    'Client'       => $project['client'],
    'Architect'    => $project['architect'],
    'Consultant'   => $project['consultant'],
    'Project Type' => $project['project_type'],
    'Façade Area'  => $project['project_area'],  // in sqm
    'Year'         => preg_match('/^\d{4}$/', (string) $project['completion_year'])
                        ? $project['completion_year'] : '-', // real year only; "Ongoing"/blank -> "-"
];

$page_title = $project['title'] . ' – Glass Wall Systems';

require __DIR__ . '/includes/header.php';
?>
<style>
  /* Uniform project image frame on the detail page */
  /* Landscape images: fill the banner, trim the sky */
  .tp-project-details-info img {
    width: 100%;
    height: clamp(260px, 34vw, 460px);
    object-fit: cover;
    object-position: center 62%;
    border-radius: 20px;
    display: block;
  }
  /* Portrait images (tall buildings): show the WHOLE building, tall and centered */
  .tp-project-details-info.is-portrait { text-align: center; }
  .tp-project-details-info.is-portrait img {
    width: auto;
    max-width: 100%;
    height: clamp(420px, 64vw, 720px);
    object-fit: contain;
    margin: 0 auto;
    border-radius: 20px;
    display: inline-block;
  }
  @media (max-width: 767px) {
    .tp-project-details-info img { height: 240px; }
    .tp-project-details-info.is-portrait img { height: 60vh; }
  }
</style>

                <!-- hero area start -->
<section class="tp-breadcrumb-area tp-bg tp-overlay p-relative" data-background="<?= e($project['category_banner']) ?>">
    <div class="container h-100">
        <div class="tp-breadcrumb pb-50" style="min-height:520px; position:relative;">

            <!-- Center Title -->
            <h1 class="tp-breadcrumb-title tp-text-white margin-0"
                style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:100%; text-align:center;">
                <?= e(no_amp($project['title'])) ?>
            </h1>

            <!-- Breadcrumb Bottom Left -->
            <div class="tp-breadcrumb-menu tp-flex-center"
                style="position:absolute; bottom:35px; left:0; margin:0;">
                <span><a href="index.html">Home</a></span>
                <span class="tp-breadcrumb-dvdr">-</span>
                <span>Our Projects</span>
                <span class="tp-breadcrumb-dvdr">-</span>
                <span><a href="projects.php?category=<?= e($project['category_slug']) ?>"><?= e($project['category_name']) ?></a></span>
                <span class="tp-breadcrumb-dvdr">-</span>
                <span><?= e(no_amp($project['title'])) ?></span>
            </div>

        </div>
    </div>
</section>
                <!-- hero area end -->

 <!-- project-details-area,start  -->
      <section class="tp-project-details-area pt-150 pb-100 tp-project-spacing fix">
        <div class="container">
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="tp-project-details-info br-20 mb-40 text-center<?= $detail_is_portrait ? ' is-portrait' : '' ?>">
            <img src="<?= e($detail_image) ?>" alt="<?= e($project['title']) ?>" class="w-100 br-20" />
        </div>
    </div>
</div>
            <div class="tp-project-des">
<div class="tp-project-about-main">
    <div class="row">
        <div class="col-xl-12">
            <div class="tp-project-about-wrap tp-flex-center  flex-wrap">

                <?php foreach ($info_fields as $label => $value): ?>
                    <?php if ($value !== null && $value !== ''): ?>
                <div class="tp-project-about">
                    <div class="tp-project-details-info-content">
                        <span class="d-inline-block fw-500 lh-1"><?= e($label) ?></span>
                        <h4 class="tp-project-details-info-title margin-0 lh-1">
                            <?= e(no_amp($value)) ?>
                        </h4>
                    </div>
                </div>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</div>
            <?php if (!empty($scope_items)): ?>
            <div class="tp-project-feature pt-40 border-top">
                <h3 class="tp-project-details-title mb-35 tp-text-center">Scope of Work</h3>
                <div class="tp-project-feature-main tp-bg-gray br-20">
                    <div class="tp-project-feature-wrap tp-flex-center justify-content-center tp_fade_anim " data-dure=".9">
                        <?php foreach ($scope_items as $scope): ?>
                        <div class="tp-project-feature-item">
                            <span class="d-inline-block tp-text-black"> <?= e(no_amp($scope)) ?></span>
                            <span class="tp-project-feature-dot"></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-10 text-center pt-40">
                 

                  <div class="tp-about-btn tp_fade_anim d-flex justify-content-center" data-delay=".3">
                    <a href="projects.php?category=<?= e($project['category_slug']) ?>" class="tp-btn">
                      <span class="tp-btn-text">Back to <?= e(no_amp($project['category_name'])) ?></span>

                      <span class="tp-btn-icon">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                          <path
                            d="M0.75 10.75L10.75 0.75"
                            stroke="currentcolor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          />
                          <path
                            d="M0.75 0.75H10.75V10.75"
                            stroke="currentcolor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          />
                        </svg>
                      </span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
      </section>
     <!-- project-details-area,end  -->
     
   

<?php require __DIR__ . '/includes/footer.php'; ?>