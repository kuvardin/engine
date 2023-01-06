<?php

declare(strict_types=1);

/**
 * @var App\Sessions\Session $session
 * @var App\Sites\Input\SiteInput $input
 * @var App\Languages\Language $lang
 * @var App\Sites\TemplatesEngine\Page $page
 *
 * @var int $error_code
 * @var string|null $error_message
 */

use App\Sites\Sections\Main\MainSectionController as Controller;

?>

<div class="card">
    <div class="card-body">
        <?php if ($error_message !== null): ?>
            <p><?= $error_message ?></p>
        <?php endif; ?>
        <div class="text-center">
            <a class="btn btn-info mt-3" href="<?= Controller::getUrl($lang, '/') ?>">
                <i class="mdi mdi-reply"></i>
                <?= $lang->require('go_to_home_page') ?>
            </a>
        </div>
    </div>
</div>
