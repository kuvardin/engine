<?php

declare(strict_types=1);

/**
 * @var App\Sessions\Session $session
 * @var App\Sites\Input\SiteInput $input
 * @var App\Languages\Language $lang
 * @var App\Sites\TemplatesEngine\Page $page
 *
 * @var string $route
 * @var string[] $route_parts
 * @var App\Sites\TemplatesEngine\Sidebar\Block[] $sidebar_blocks
 */

use App\Sites\Sections\Main\MainSectionController as Controller;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?= htmlspecialchars($page->title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if ($page->description !== null): ?>
        <meta content="<?= htmlspecialchars($page->description) ?>" name="description"/>
    <?php endif; ?>

    <?php if ($page->no_indexing): ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>

    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/site/images/favicon.ico">

    <link href="/assets/site/vendor/jstree/themes/default/style.min.css" rel="stylesheet" type="text/css">

    <!-- Select2 css -->
    <link href="/assets/site/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="/assets/site/vendor/daterangepicker/daterangepicker.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <!-- Vector Map css -->
    <link rel="stylesheet"
          href="/assets/site/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css">

    <!-- Theme Config Js -->
    <script src="/assets/site/js/hyper-config.js"></script>

    <!-- App css -->
    <link href="/assets/site/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style"/>

    <!-- Icons css -->
    <link href="/assets/site/css/icons.min.css" rel="stylesheet" type="text/css"/>

    <script src="https://kit.fontawesome.com/6e49ef54c7.js" crossorigin="anonymous"></script>

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6835532484624938"
            crossorigin="anonymous"></script>
</head>
<body>
<!-- Begin page -->
<div class="wrapper">
    <!-- ========== Topbar Start ========== -->
    <div class="navbar-custom topnav-navbar">
        <div class="container-fluid detached-nav">
            <!-- Topbar Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="<?= Controller::getUrl($lang, '/') ?>" class="logo-light">
                    <span class="logo-lg">
                        <img src="/assets/site/images/logo.png" alt="logo" height="22">
                    </span>
                    <span class="logo-sm">
                        <img src="/assets/site/images/logo-sm.png" alt="small logo" height="22">
                    </span>
                </a>
                <!-- Logo Dark -->
                <a href="<?= Controller::getUrl($lang, '/') ?>" class="logo-dark">
                    <span class="logo-lg">
                        <img src="/assets/site/images/logo-dark.png" alt="dark logo" height="22">
                    </span>
                    <span class="logo-sm">
                        <img src="/assets/site/images/logo-dark-sm.png" alt="small logo" height="22">
                    </span>
                </a>
            </div>
            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="mdi mdi-menu"></i>
            </button>
            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
            <ul class="list-unstyled topbar-menu float-end mb-0">
                <li class="dropdown notification-list d-lg-none">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <i class="ri-search-line noti-icon"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                        <form class="p-3">
                            <input type="search" class="form-control" placeholder="Search ..."
                                   aria-label="Recipient's username">
                        </form>
                    </div>
                </li>
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <span class="align-middle d-none d-lg-inline-block">
                            <?= mb_strtoupper($lang->getCode()) ?>
                        </span>
                        <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">
                        <?php foreach (App::settings('languages') as $one_lang_code): ?>
                            <?php if ($lang->getCode() !== $one_lang_code): ?>
                                <a href="<?= Controller::getUrl($one_lang_code, '/') ?>"
                                   class="dropdown-item notify-item">
                                    <span class="align-middle">
                                        <?= mb_strtoupper($one_lang_code) ?>
                                    </span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <i class="ri-notification-3-line noti-icon"></i>
                        <span class="noti-icon-badge"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                        <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 font-16 fw-semibold"> Notification</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                        <small>Clear All</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="px-3" style="max-height: 300px;" data-simplebar>
                            <h5 class="text-muted font-13 fw-normal mt-2">Today</h5>
                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                                <div class="card-body">
                      <span class="float-end noti-close-btn text-muted">
                        <i class="mdi mdi-close"></i>
                      </span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon bg-primary">
                                                <i class="mdi mdi-comment-account-outline"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Datacorp <small
                                                        class="fw-normal text-muted ms-1">1 min ago</small>
                                            </h5>
                                            <small class="noti-item-subtitle text-muted">Caleb Flakelar commented on
                                                Admin</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                      <span class="float-end noti-close-btn text-muted">
                        <i class="mdi mdi-close"></i>
                      </span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon bg-info">
                                                <i class="mdi mdi-account-plus"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Admin <small
                                                        class="fw-normal text-muted ms-1">1 hours ago</small>
                                            </h5>
                                            <small class="noti-item-subtitle text-muted">New user registered</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <h5 class="text-muted font-13 fw-normal mt-0">Yesterday</h5>
                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                      <span class="float-end noti-close-btn text-muted">
                        <i class="mdi mdi-close"></i>
                      </span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon">
                                                <img src="/assets/site/images/users/avatar-2.jpg"
                                                     class="img-fluid rounded-circle" alt=""/>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Cristina Pride <small
                                                        class="fw-normal text-muted ms-1">1 day ago</small>
                                            </h5>
                                            <small class="noti-item-subtitle text-muted">Hi, How are you? What about our
                                                next meeting</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <h5 class="text-muted font-13 fw-normal mt-0">30 Dec 2021</h5>
                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                      <span class="float-end noti-close-btn text-muted">
                        <i class="mdi mdi-close"></i>
                      </span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon bg-primary">
                                                <i class="mdi mdi-comment-account-outline"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Datacorp</h5>
                                            <small class="noti-item-subtitle text-muted">Caleb Flakelar commented on
                                                Admin</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item p-0 notify-item card read-noti shadow-none mb-2">
                                <div class="card-body">
                      <span class="float-end noti-close-btn text-muted">
                        <i class="mdi mdi-close"></i>
                      </span>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon">
                                                <img src="/assets/site/images/users/avatar-4.jpg"
                                                     class="img-fluid rounded-circle" alt=""/>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14">Karen Robinson</h5>
                                            <small class="noti-item-subtitle text-muted">Wow ! this admin looks good and
                                                awesome design</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="text-center">
                                <i class="mdi mdi-dots-circle mdi-spin text-muted h3 mt-0"></i>
                            </div>
                        </div>
                        <!-- All-->
                        <a href="javascript:void(0);"
                           class="dropdown-item text-center text-primary notify-item border-top border-light py-2"> View
                            All </a>
                    </div>
                </li>
                <li class="dropdown notification-list d-none d-sm-inline-block">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <i class="ri-apps-2-line noti-icon"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg p-0">
                        <div class="p-2">
                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="/assets/site/images/brands/slack.png" alt="slack">
                                        <span>Slack</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="/assets/site/images/brands/github.png" alt="Github">
                                        <span>GitHub</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="/assets/site/images/brands/dribbble.png" alt="dribbble">
                                        <span>Dribbble</span>
                                    </a>
                                </div>
                            </div>
                            <div class="row g-0">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="/assets/site/images/brands/bitbucket.png" alt="bitbucket">
                                        <span>Bitbucket</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="/assets/site/images/brands/dropbox.png" alt="dropbox">
                                        <span>Dropbox</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="/assets/site/images/brands/g-suite.png" alt="G Suite">
                                        <span>G Suite</span>
                                    </a>
                                </div>
                            </div>
                            <!-- end row-->
                        </div>
                    </div>
                </li>
                <li class="notification-list d-none d-sm-inline-block">
                    <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                        <i class="ri-settings-3-line noti-icon"></i>
                    </a>
                </li>
                <li class="notification-list d-none d-sm-inline-block">
                    <a class="nav-link" href="javascript:void(0)" id="light-dark-mode">
                        <i class="ri-moon-line noti-icon"></i>
                    </a>
                </li>
                <li class="notification-list d-none d-md-inline-block">
                    <a class="nav-link" href="" data-toggle="fullscreen">
                        <i class="ri-fullscreen-line noti-icon"></i>
                    </a>
                </li>
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#"
                       role="button" aria-haspopup="false" aria-expanded="false">
                <span class="account-user-avatar">
                  <img src="/assets/site/images/users/avatar-1.jpg" alt="user-image" class="rounded-circle">
                </span>
                        <span>
                  <span class="account-user-name">
                      <?= htmlspecialchars($session->requireUser()->getFirstName()) ?>
                  </span>
                  <span class="account-position">Founder</span>
                </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                        <!-- item-->
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle me-1"></i>
                            <span>My Account</span>
                        </a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-edit me-1"></i>
                            <span>Settings</span>
                        </a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-lifebuoy me-1"></i>
                            <span>Support</span>
                        </a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-lock-outline me-1"></i>
                            <span>Lock Screen</span>
                        </a>
                        <!-- item-->
                        <a href="<?= Controller::getUrl($lang, 'quit') ?>" class="dropdown-item notify-item">
                            <i class="mdi mdi-logout me-1"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </li>
            </ul>
            <!-- Topbar Search Form -->
            <div class="app-search dropdown">
                <form>
                    <div class="input-group">
                        <input type="search" class="form-control dropdown-toggle" placeholder="Search..."
                               id="top-search">
                        <span class="mdi mdi-magnify search-icon"></span>
                        <button class="input-group-text btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
                <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h5 class="text-overflow mb-2">Found <span class="text-danger">17</span> results </h5>
                    </div>
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="uil-notes font-16 me-1"></i>
                        <span>Analytics Report</span>
                    </a>
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="uil-life-ring font-16 me-1"></i>
                        <span>How can I help you?</span>
                    </a>
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="uil-cog font-16 me-1"></i>
                        <span>User profile settings</span>
                    </a>
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow mb-2 text-uppercase">Users</h6>
                    </div>
                    <div class="notification-list">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="d-flex">
                                <img class="d-flex me-2 rounded-circle"
                                     src="/assets/site/images/users/avatar-2.jpg"
                                     alt="Generic placeholder image" height="32">
                                <div class="w-100">
                                    <h5 class="m-0 font-14">Erwin Brown</h5>
                                    <span class="font-12 mb-0">UI Designer</span>
                                </div>
                            </div>
                        </a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="d-flex">
                                <img class="d-flex me-2 rounded-circle"
                                     src="/assets/site/images/users/avatar-5.jpg"
                                     alt="Generic placeholder image" height="32">
                                <div class="w-100">
                                    <h5 class="m-0 font-14">Jacob Deo</h5>
                                    <span class="font-12 mb-0">Developer</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="leftside-menu">
        <!-- Logo Light -->
        <a href="<?= Controller::getUrl($lang, '/') ?>" class="logo logo-light">
          <span class="logo-lg">
            <img src="/assets/site/images/logo.png" alt="logo" height="22">
          </span>
            <span class="logo-sm">
            <img src="/assets/site/images/logo-sm.png" alt="small logo" height="22">
          </span>
        </a>
        <!-- Logo Dark -->
        <a href="<?= Controller::getUrl($lang, '/') ?>" class="logo logo-dark">
          <span class="logo-lg">
            <img src="/assets/site/images/logo-dark.png" alt="dark logo" height="22">
          </span>
            <span class="logo-sm">
            <img src="/assets/site/images/logo-dark-sm.png" alt="small logo" height="22">
          </span>
        </a>
        <!-- Sidebar Hover Menu Toggle Button -->
        <button type="button" class="btn button-sm-hover p-0" data-bs-toggle="tooltip" data-bs-placement="right"
                title="Show Full Sidebar">
            <i class="ri-checkbox-blank-circle-line align-middle"></i>
        </button>
        <!-- Sidebar -left -->
        <div class="h-100" id="leftside-menu-container" data-simplebar>
            <!-- Leftbar User -->
            <div class="leftbar-user">
                <a href="pages-profile.html">
                    <img src="/assets/site/images/users/avatar-1.jpg" alt="user-image" height="42"
                         class="rounded-circle shadow-sm">
                    <span class="leftbar-user-name">Dominic Keller</span>
                </a>
            </div>
            <!--- Sidemenu -->
            <ul class="side-nav">
                <?php foreach ($sidebar_blocks as $sidebar_block): ?>
                    <li class="side-nav-title side-nav-item">
                        <?= htmlspecialchars($sidebar_block->name) ?>
                    </li>
                    <?php foreach ($sidebar_block->items as $sidebar_item): ?>
                        <li class="side-nav-item">
                            <?php if ($sidebar_item->path === null): ?>
                                <a data-bs-toggle="collapse" href="#sidebar_<?= $sidebar_item->code ?>"
                                   aria-expanded="false" aria-controls="sidebar_<?= $sidebar_item->code ?>"
                                   class="side-nav-link">
                                    <i class="<?= $sidebar_item->icon ?>"></i>
                                    <span><?= htmlspecialchars($sidebar_item->name) ?></span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar_<?= $sidebar_item->code ?>">
                                    <ul class="side-nav-second-level">
                                        <?php foreach ($sidebar_item->subitems as $sidebar_subitem): ?>
                                            <li>
                                                <a href="<?= Controller::getUrl($lang, $sidebar_subitem->path) ?>">
                                                    <?= htmlspecialchars($sidebar_subitem->name) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <a href="<?= Controller::getUrl($lang, $sidebar_item->path) ?>" class="side-nav-link">
                                    <i class="<?= $sidebar_item->icon ?>"></i>
                                    <span><?= htmlspecialchars($sidebar_item->name) ?></span>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
            <!--- End Sidemenu -->
            <div class="clearfix"></div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">

                            <?php if ($page->breadcrumbs !== [] || $page->header_links !== []): ?>
                                <div class="page-title-right">
                                    <?php if ($page->breadcrumbs !== []): ?>
                                        <ol class="breadcrumb m-0">
                                            <?php foreach ($page->breadcrumbs as $breadcrumb): ?>
                                                <li class="breadcrumb-item">
                                                    <a href="<?= Controller::getUrl($lang, $breadcrumb->path, $breadcrumb->get) ?>">
                                                        <?= htmlspecialchars($breadcrumb->name) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                            <li class="breadcrumb-item active">
                                                <?= htmlspecialchars(App::shortenString($page->title, 30)) ?>
                                            </li>
                                        </ol>
                                    <?php endif; ?>

                                    <?php if ($page->header_links !== []): ?>
                                        <?php foreach ($page->header_links as $header_link): ?>
                                            <a class="btn btn-sm btn-<?= $header_link->bootstrap_color?->value ?? 'primary' ?>"
                                               href="<?= Controller::getUrl($lang, $header_link->path) ?>">
                                                <?php if ($header_link->icon !== null): ?>
                                                    <i class="<?= $header_link->icon ?>"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($header_link->title) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <h4 class="page-title">
                                <?= htmlspecialchars($page->title) ?>
                                <?php if ($page->counter !== null): ?>
                                    <span class="badge bg-primary">
                                        <?= $page->counter ?>
                                    </span>
                                <?php endif; ?>
                            </h4>
                        </div>
                    </div>
                </div>

                <?php if ($page->alerts !== []): ?>
                    <?php foreach ($page->alerts as $alert): ?>
                        <div class="alert alert-<?= ($alert->bootstrap_color?->value ?? 'info') ?>
                        alert-dismissible bg-<?= ($alert->bootstrap_color?->value ?? 'info') ?> text-white border-0
                        fade show" role="alert">
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                    aria-label="<?= $lang->require('close') ?>"></button>
                            <?= htmlspecialchars($alert->text) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?= $page->content ?>
            </div>
            <!-- container -->
        </div>
        <!-- content -->
        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        © <?= App::settings('site.copyright') ?>
                    </div>
                    <div class="col-md-6">
                        <div class="text-md-end footer-links d-none d-md-block">
                            <?= sprintf('%0.2f', App::getGenerationTime()) ?> ms. |
                            <a href="https://t.me/kuvardin" target="_blank">Telegram</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
</div>
<!-- END wrapper -->

<!-- Theme Settings -->
<div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas">
    <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
        <h5 class="text-white m-0">Theme Settings</h5>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div data-simplebar class="h-100">
            <div class="card mb-0 p-3">
                <h5 class="mt-0 font-16 fw-bold mb-3">Choose Layout</h5>
                <div class="row">
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input id="customizer-layout01" name="data-layout" type="radio" value="vertical"
                                   class="form-check-input">
                            <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout01">
                                <span class="d-flex h-100">
                                    <span class="flex-shrink-0">
                                        <span class="bg-light d-flex h-100 border-end flex-column p-1 px-2">
                                            <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                            <span class="d-block border border-3 w-100 mb-1"></span>
                                            <span class="d-block border border-3 w-100 mb-1"></span>
                                            <span class="d-block border border-3 w-100 mb-1"></span>
                                            <span class="d-block border border-3 w-100 mb-1"></span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="bg-light d-block p-1"></span>
                                        </span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <h5 class="font-14 text-center text-muted mt-2">Vertical</h5>
                    </div>
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input id="customizer-layout02" name="data-layout" type="radio" value="horizontal"
                                   class="form-check-input">
                            <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout02">
                            <span class="d-flex h-100 flex-column">
                            <span class="bg-light d-flex p-1 align-items-center border-bottom">
                            <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                            <span class="d-block border border-3 ms-auto"></span>
                            <span class="d-block border border-3 ms-1"></span>
                            <span class="d-block border border-3 ms-1"></span>
                            <span class="d-block border border-3 ms-1"></span>
                            </span>
                            <span class="bg-light d-block p-1"></span>
                            </span>
                            </label>
                        </div>
                        <h5 class="font-14 text-center text-muted mt-2">Horizontal</h5>
                    </div>
                </div>
                <h5 class="my-3 font-16 fw-bold">Color Scheme</h5>
                <div class="colorscheme-cardradio">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-theme" id="layout-color-light"
                                       value="light">
                                <label class="form-check-label p-0 avatar-md w-100" for="layout-color-light">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end flex-column p-1 px-2">
                                <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column bg-white">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Light</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-theme" id="layout-color-dark"
                                       value="dark">
                                <label class="form-check-label p-0 avatar-md w-100 bg-black" for="layout-color-dark">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light-lighten d-flex h-100 flex-column p-1 px-2">
                                <span class="d-block p-1 bg-light-lighten rounded mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light-lighten d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Dark</h5>
                        </div>
                    </div>
                </div>
                <div id="layout-width">
                    <h5 class="my-3 font-16 fw-bold">Layout Mode</h5>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-mode"
                                       id="layout-mode-fluid" value="fluid">
                                <label class="form-check-label p-0 avatar-md w-100" for="layout-mode-fluid">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Fluid</h5>
                        </div>
                        <div class="col-4" id="layout-boxed">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-mode"
                                       id="layout-mode-boxed" value="boxed">
                                <label class="form-check-label p-0 avatar-md w-100 px-2" for="layout-mode-boxed">
                                <span class="d-flex h-100 border-start border-end">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end flex-column p-1">
                                <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Boxed</h5>
                        </div>
                        <div class="col-4" id="layout-detached">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-layout-mode"
                                       id="data-layout-detached" value="detached">
                                <label class="form-check-label p-0 avatar-md w-100" for="data-layout-detached">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-flex p-1 align-items-center border-bottom">
                                <span class="d-block p-1 bg-dark-lighten rounded me-1"></span>
                                <span class="d-block border border-3 ms-auto"></span>
                                <span class="d-block border border-3 ms-1"></span>
                                <span class="d-block border border-3 ms-1"></span>
                                <span class="d-block border border-3 ms-1"></span>
                                </span>
                                <span class="d-flex h-100 p-1 px-2">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 flex-column p-1 px-2">
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100"></span>
                                </span>
                                </span>
                                </span>
                                <span class="bg-light d-block p-1 mt-auto px-2"></span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Detached</h5>
                        </div>
                    </div>
                </div>
                <h5 class="my-3 font-16 fw-bold">Topbar Color</h5>
                <div class="row">
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color"
                                   id="topbar-color-light" value="light">
                            <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-light">
                            <span class="d-flex h-100">
                            <span class="flex-shrink-0">
                            <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                            <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            </span>
                            </span>
                            <span class="flex-grow-1">
                            <span class="d-flex h-100 flex-column">
                            <span class="bg-light d-block p-1"></span>
                            </span>
                            </span>
                            </span>
                            </label>
                        </div>
                        <h5 class="font-14 text-center text-muted mt-2">Light</h5>
                    </div>
                    <div class="col-4">
                        <div class="form-check card-radio">
                            <input class="form-check-input" type="radio" name="data-topbar-color" id="topbar-color-dark"
                                   value="dark">
                            <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-dark">
                            <span class="d-flex h-100">
                            <span class="flex-shrink-0">
                            <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                            <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            <span class="d-block border border-3 w-100 mb-1"></span>
                            </span>
                            </span>
                            <span class="flex-grow-1">
                            <span class="d-flex h-100 flex-column">
                            <span class="bg-dark d-block p-1"></span>
                            </span>
                            </span>
                            </span>
                            </label>
                        </div>
                        <h5 class="font-14 text-center text-muted mt-2">Dark</h5>
                    </div>
                </div>
                <div id="sidebar-color">
                    <h5 class="my-3 font-16 fw-bold">Sidebar Color</h5>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-color"
                                       id="leftbar-color-light" value="light">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-color-light">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Light</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-color"
                                       id="leftbar-color-dark" value="dark">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-color-dark">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-dark d-flex h-100 flex-column p-1 px-2">
                                <span class="d-block p-1 bg-light-lighten rounded mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Dark</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-color"
                                       id="leftbar-color-default" value="default">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-color-default">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-primary bg-gradient d-flex h-100 flex-column p-1 px-2">
                                <span class="d-block p-1 bg-light-lighten rounded mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                <span class="d-block border opacity-25 border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Brand</h5>
                        </div>
                    </div>
                </div>
                <div id="sidebar-size">
                    <h5 class="my-3 font-16 fw-bold">Sidebar Size</h5>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size"
                                       id="leftbar-size-default" value="default">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-default">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end  flex-column p-1 px-2">
                                <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Default</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size"
                                       id="leftbar-size-compact" value="compact">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-compact">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end  flex-column p-1">
                                <span class="d-block p-1 bg-dark-lighten rounded mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Compact</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size"
                                       id="leftbar-size-small" value="condensed">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-small">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end  flex-column">
                                <span class="d-block p-1 bg-dark-lighten mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Condensed</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size"
                                       id="leftbar-size-small-hover" value="sm-hover">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-small-hover">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="bg-light d-flex h-100 border-end  flex-column">
                                <span class="d-block p-1 bg-dark-lighten mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                <span class="d-block border border-3 w-100 mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Hover View</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidenav-size"
                                       id="leftbar-size-full" value="full">
                                <label class="form-check-label p-0 avatar-md w-100" for="leftbar-size-full">
                                <span class="d-flex h-100">
                                <span class="flex-shrink-0">
                                <span class="d-flex h-100 border-end  flex-column">
                                <span class="d-block p-1 bg-dark-lighten mb-1"></span>
                                </span>
                                </span>
                                <span class="flex-grow-1">
                                <span class="d-flex h-100 flex-column">
                                <span class="bg-light d-block p-1"></span>
                                </span>
                                </span>
                                </span>
                                </label>
                            </div>
                            <h5 class="font-14 text-center text-muted mt-2">Full Layout</h5>
                        </div>
                    </div>
                </div>
                <div id="layout-position">
                    <h5 class="my-3 font-16 fw-bold">Layout Position</h5>
                    <div class="btn-group radio" role="group">
                        <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-fixed"
                               value="fixed">
                        <label class="btn btn-light w-sm" for="layout-position-fixed">Fixed</label>
                        <input type="radio" class="btn-check" name="data-layout-position"
                               id="layout-position-scrollable" value="scrollable">
                        <label class="btn btn-light w-sm ms-0" for="layout-position-scrollable">Scrollable</label>
                    </div>
                </div>
                <div id="sidebar-user">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <label class="font-16 fw-bold m-0" for="sidebaruser-check">Sidebar User Info</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="sidebar-user" id="sidebaruser-check">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer border-top p-3 text-center">
        <div class="row">
            <div class="col-6">
                <button type="button" class="btn btn-light w-100" id="reset-layout">Reset</button>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-primary w-100">Buy Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Vendor js -->
<script src="/assets/site/js/vendor.min.js"></script>
<!--  Select2 Plugin Js -->
<script src="/assets/site/vendor/select2/js/select2.min.js"></script>
<!-- Daterangepicker js -->
<script src="/assets/site/vendor/daterangepicker/moment.min.js"></script>
<script src="/assets/site/vendor/daterangepicker/daterangepicker.js"></script>
<!-- Apex Charts js -->
<script src="/assets/site/vendor/apexcharts/apexcharts.min.js"></script>
<!-- Vector Map js -->
<script src="/assets/site/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/assets/site/vendor/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js"></script>
<!-- Dashboard App js -->
<script src="/assets/site/js/pages/demo.dashboard.js"></script>
<!-- jstree -->
<script src="/assets/site/vendor/jstree/jstree.min.js"></script>
<!-- App js -->
<script src="/assets/site/js/app.js"></script>

<?php foreach ($page->chartjs_charts as $chartjs_chart_index => $chartjs_chart): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new Chart(document.getElementById("chartjs-<?= $chartjs_chart_index ?>"), {
                type: '<?= $chartjs_chart::getType() ?>',
                data: <?= json_encode($chartjs_chart->data, JSON_THROW_ON_ERROR) ?>,
                options: <?= json_encode($chartjs_chart->options, JSON_THROW_ON_ERROR) ?>,
            });
        });

    </script>
<?php endforeach; ?>

</body>
</html>