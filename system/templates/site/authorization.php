<?php

declare(strict_types=1);

/**
 * @var App\Sessions\Session $session
 * @var App\Sites\Input\SiteInput $input
 * @var App\Languages\Language $lang
 * @var App\Sites\TemplatesEngine\Page $page
 */

use App\Sites\Sections\Main\MainSectionController as Controller;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>
        <?= htmlspecialchars($page->title) ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if ($page->description !== null): ?>
        <meta content="<?= htmlspecialchars($page->description) ?>" name="description" />
    <?php endif; ?>

    <?php if ($page->no_indexing): ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>

    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/site/images/favicon.ico">

    <!-- Theme Config Js -->
    <script src="/assets/site/js/hyper-config.js"></script>

    <!-- App css -->
    <link href="/assets/site/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="/assets/site/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>

<body class="authentication-bg">
<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-4 col-lg-5">
                <div class="card">

                    <!-- Logo -->
                    <div class="card-header pt-4 pb-4 text-center bg-primary">
                        <a href="<?= Controller::getUrl($lang, '/') ?>">
                            <span><img src="/assets/site/images/logo.png" alt="logo" height="22"></span>
                        </a>
                    </div>

                    <div class="card-body p-4">
                        <div class="text-center w-75 m-auto">
                            <h4 class="text-dark-50 text-center pb-0 fw-bold">
                                <?= $lang->require('authorization') ?>
                            </h4>
                            <p class="text-muted mb-4">
                                <?= $lang->require('authorization_about') ?>
                            </p>
                        </div>

                        <?php foreach ($page->alerts as $alert): ?>
                            <div class="alert alert-<?= $alert->bootstrap_color?->value ?? 'info' ?> alert-dismissible bg-<?= $alert->bootstrap_color?->value ?? 'info' ?> text-white border-0 fade show" role="alert">
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                        aria-label="<?= $lang->require('close') ?>">
                                </button>
                                <?= htmlspecialchars($alert->text) ?>
                            </div>
                        <?php endforeach; ?>

                        <form action="?" method="post">

                            <div class="mb-3">
                                <label for="emailaddress" class="form-label">
                                    <?= $lang->require('your_email') ?>
                                </label>
                                <input class="form-control <?= (isset($page->errors['email']) ? 'is-invalid' : '') ?>"
                                       type="email" id="emailaddress" name="email" required
                                       value="<?= $input->getString('email', true) ?>"
                                       placeholder="<?= $lang->require('enter_your_email') ?>">
                                <?php if (isset($page->errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($page->errors['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <?= $lang->require('your_password') ?>
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" name="password" required
                                           class="form-control <?= (isset($page->errors['password']) ? 'is-invalid' : '') ?>"
                                           placeholder="<?= $lang->require('enter_your_password') ?>">
                                    <div class="input-group-text" data-password="false">
                                        <span class="password-eye"></span>
                                    </div>

                                    <?php if (isset($page->errors['password'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($page->errors['password']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <div class="pt-2">
                                <div class="row">
                                    <div class="col-4">
                                        <button class="btn btn-primary" type="submit">
                                            <?= $lang->require('log_in') ?>
                                        </button>
                                    </div>
                                    <div class="col-8 text-end">
                                        <div class="btn-group">
                                            <?php foreach (App::settings('languages') as $other_lang_code): ?>
                                                <?php if ($other_lang_code === $lang->getCode()): ?>
                                                    <button type="button" class="btn btn-secondary">
                                                        <?= strtoupper($other_lang_code) ?>
                                                    </button>
                                                <?php else: ?>
                                                    <a class="btn btn-light"
                                                       href="<?= Controller::getUrl($other_lang_code, $input->route, $input->getDataArray()) ?>">
                                                        <?= strtoupper($other_lang_code) ?>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted">
                            <?= $lang->require('dont_have_an_account') ?>
                            <a href="<?= Controller::getUrl($lang, 'registration') ?>" class="text-muted ms-1">
                                <b><?= $lang->require('sign_up') ?></b>
                            </a>
                        </p>
                    </div> <!-- end col -->
                </div>
            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->

<!-- Vendor js -->
<script src="/assets/site/js/vendor.min.js"></script>

<!-- App js -->
<script src="/assets/site/js/app.min.js"></script>

</body>
</html>
