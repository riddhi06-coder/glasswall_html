<?php
/**
 * Shared page header (doctype -> opening <main>).
 * Self-contained: pulls in the DB connection so it can be included from any
 * page (.php or .html) without extra setup. Set $page_title before including.
 *
 * ── EDIT MENU LINKS HERE ────────────────────────────────────────────────
 * Change a value to point a menu item at a different page. '#' = no page yet.
 */
require_once __DIR__ . '/../config/db.php';

$menu = [
    // Overview submenu
    'about_us'            => 'vision-mission-values.html',
    'board_of_directors'  => 'board-of-directors.html',
    'innovation'          => 'innovation.html',
    'esg'                 => 'esg.html',
    'media'               => 'media.html',
    'awards'              => 'awards-recognition.html',
    // Products submenu
    'facade'              => 'facade-and-curtain-wall-systems.html',
    'louvers'             => 'louvers.html',
    'rain_screen'         => 'rain-screen-cladding.html',
    // Infrastructure submenu
    'design_engineering'  => 'design-and-engineering.html',
    'facility'            => 'facility.html',
    'project_management'  => 'project-management.html',
    // Investors Relations submenu
    'ipo'                 => 'ipo.html',
    'corporate_governance'=> 'corporate-governance.html',
    'annual_reports'      => 'annual-report.html',
    'investor_resources'  => '#',
    // Top-level
    'careers'             => '#',
    'contact'             => 'contact.html',
];

if (!isset($page_title)) {
    $page_title = 'Glass Wall Systems – Premium Glass and Facade Solutions';
}

// Categories for the Projects nav menu (dynamic from DB).
$nav_categories = [];
try {
    $nav_categories = db()->query('SELECT name, slug FROM categories ORDER BY sort_order')->fetchAll();
} catch (Throwable $ex) {
    $nav_categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($page_title) ?></title>
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.png" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/spacing.css" />
    <link rel="stylesheet" href="assets/css/odometer-min.css" />
    <link rel="stylesheet" href="assets/css/font-awesome-pro.css">
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/media.css" />
    <style>
      /* --- Header search widget --- */
      .gws-search { position: relative; }
      .gws-search-dropdown {
        position: absolute; top: 100%; right: 0; margin-top: 14px;
        width: 340px; max-width: 90vw; background: #fff; border-radius: 12px;
        box-shadow: 0 12px 34px rgba(0,0,0,.18); padding: 14px; z-index: 9999;
      }
      .gws-search-dropdown[hidden] { display: none; }
      #gwsSearchInput {
        width: 100%; padding: 11px 14px; border: 1px solid #dcdcdc; border-radius: 8px;
        font-size: 14px; outline: none; color: #111; background: #fff;
      }
      #gwsSearchInput:focus { border-color: #0a4bb3; }
      #gwsSearchInput::placeholder { color: #9aa0a6 !important; opacity: 1; }
      #gwsSearchInput::-webkit-input-placeholder { color: #9aa0a6 !important; }
      #gwsSearchInput::-moz-placeholder { color: #9aa0a6 !important; opacity: 1; }
      #gwsSearchInput:-ms-input-placeholder { color: #9aa0a6 !important; }
      #gwsSearchResults { list-style: none; margin: 10px 0 0; padding: 0; max-height: 340px; overflow-y: auto; }
      #gwsSearchResults li { margin: 0; }
      #gwsSearchResults li a {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        padding: 10px 11px; border-radius: 7px; text-decoration: none; color: #111; line-height: 1.3;
      }
      #gwsSearchResults li a:hover { background: #f3f4f6; }
      #gwsSearchResults small { color: #888; font-size: 12px; }
      #gwsSearchResults .gws-tag {
        flex: none; font-size: 11px; color: #fff; background: #0a4bb3;
        border-radius: 20px; padding: 3px 9px; white-space: nowrap;
      }
      #gwsSearchResults .gws-tag.product { background: #0a8a5f; }
      #gwsSearchResults .gws-tag.category { background: #b45309; }
      .gws-search-empty { padding: 10px 4px; color: #777; font-size: 13px; }
    </style>
</head>
<body class="">

    <!-- header start -->
    <header>
        <div class="tp-header-area tp-header-transparent sticky-black" id="header-sticky">
            <div class="container">
                <div class="tp-header-border-white tp-header-spacing">
                    <div class="tp-header-wrap">
                        <div class="row gx-0 tp-align-center">
                            <div class="col-xl-3 col-lg-3 col-md-8 col-5">
                                <div class="tp-header-logo">
                                    <a href="index.html">
                                        <img data-width="275" src="assets/images/gws.png" alt="Glass Wall Systems Logo" />
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-8 col-md-8 col-2 tp-text-center">
                                <div class="tp-header-menu tp-header-menu-white tp-bluer-bg tp-text-center d-none d-xl-inline-block">
                                    <nav class="tp-mobile-menu-active">
                                        <ul>
                                            <li><a href="#">Overview</a>
                                                <ul class="sub-menu">
                                                    <li><a href="<?= e($menu['about_us']) ?>"><span>About Us</span></a></li>
                                                    <li><a href="<?= e($menu['board_of_directors']) ?>"><span>Board of Directors</span></a></li>
                                                    <li><a href="<?= e($menu['innovation']) ?>"><span>Innovation</span></a></li>
                                                    <li><a href="<?= e($menu['esg']) ?>"><span>ESG</span></a></li>
                                                    <li><a href="<?= e($menu['media']) ?>"><span>Media</span></a></li>
                                                    <li><a href="<?= e($menu['awards']) ?>"><span>Awards and Certificates</span></a></li>
                                                </ul>
                                            </li>
                                            <li><a href="#">Products</a>
                                                <ul class="sub-menu">
                                                    <li><a href="<?= e($menu['facade']) ?>"><span>Facade and Curtain Wall Systems</span></a></li>
                                                    <li><a href="<?= e($menu['louvers']) ?>"><span>Louvers</span></a></li>
                                                    <li><a href="<?= e($menu['rain_screen']) ?>"><span>Rain Screen Cladding</span></a></li>
                                                </ul>
                                            </li>
                                            <li><a href="#">Projects</a>
                                                <ul class="sub-menu">
                                                    <?php foreach ($nav_categories as $nav_cat): ?>
                                                    <li><a href="projects.php?category=<?= e($nav_cat['slug']) ?>"><span><?= e(str_replace(['&amp;', '&'], 'and', $nav_cat['name'])) ?></span></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                            <li><a href="#">Infrastructure</a>
                                                <ul class="sub-menu">
                                                    <li><a href="<?= e($menu['design_engineering']) ?>"><span>Design and Engineering</span></a></li>
                                                    <li><a href="<?= e($menu['facility']) ?>"><span>Facility</span></a></li>
                                                    <li><a href="<?= e($menu['project_management']) ?>"><span>Project Management</span></a></li>
                                                </ul>
                                            </li>
                                            <li><a href="#">Investors Relations</a>
                                                <ul class="sub-menu">
                                                    <li><a href="<?= e($menu['ipo']) ?>"><span>IPO</span></a></li>
                                                    <li><a href="<?= e($menu['corporate_governance']) ?>"><span>Corporate Governance</span></a></li>
                                                    <li><a href="<?= e($menu['annual_reports']) ?>"><span>Annual Reports</span></a></li>
                                                    <li><a href="<?= e($menu['investor_resources']) ?>"><span>Investor Resources</span></a></li>
                                                </ul>
                                            </li>
                                            <li><a href="<?= e($menu['careers']) ?>">Careers</a></li>
                                            <li><a href="<?= e($menu['contact']) ?>">Contact Us</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-xl-1 col-lg-1 col-md-1 col-5">
                                <div class="tp-header-cta tp-flex-center tp-justify-end">
                                    <div class="tp-cta-phone tp-header-cta-phone mr-30 d-none d-xl-inline-block gws-search" id="gwsSearch">
                                        <a class="tp-flex-center gws-search-toggle" href="#" aria-label="Search">
                                            <span class="tp-cta-phone-icon mr-10">
                                                <img src="assets/images/icons/search.svg"/>
                                            </span>
                                        </a>
                                        <div class="gws-search-dropdown" hidden>
                                            <input type="text" id="gwsSearchInput" placeholder="Search here.." autocomplete="off">
                                            <ul id="gwsSearchResults"></ul>
                                        </div>
                                    </div>
                                    <div class="tp-header-bar d-xl-none">
                                        <button class="header-sidebar-btn tp-offcanvas-toogle ml-10" aria-label="Open menu">
                                            <span></span>
                                            <span></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header end -->
    <div id="loading">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="object" id="object_four"></div>
                <div class="object" id="object_three"></div>
                <div class="object" id="object_two"></div>
                <div class="object" id="object_one"></div>
            </div>
        </div>
    </div>
    <!-- magic cursor start -->
    <div id="magic-cursor" class="cursor-secoundery-bg">
        <div id="ball"></div>
    </div>
    <!-- magic cursor end -->

    <!-- back to top start -->
    <div class="back-to-top-wrapper">
        <button id="back_to_top" type="button" class="back-to-top-btn" aria-label="Back to top">
            <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 6L6 1L1 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    <!-- back to top end -->

    <!-- offcanvas start -->
    <aside class="tp-offcanvas">
        <div class="tp-offcanvas-wrapper">
            <div class="tp-offcanvas-header d-flex align-items-center justify-content-between mb-40">
                <div class="tp-offcanvas-logo">
                    <a href="index.html">
                        <img data-width="183" src="assets/images/logo.webp" alt="Glass Wall Systems" />
                    </a>
                </div>
                <div class="tp-offcanvas-button">
                    <button class="tp-offcanvas-button-close tp-offcanvas-close-toggle" aria-label="Close menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="tp-offcanvas-menu mb-30">
                <nav></nav>
            </div>

        </div>
    </aside>
    <div class="tp-offcanvas-overlay"></div>
    <!-- offcanvas end -->

    <div id="smooth-wrapper">
        <div id="smooth-content">
            <main>