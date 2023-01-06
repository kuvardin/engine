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

<div class="card">
    <div class="card-body">
        <b><?= $lang->require('last_name') ?>:</b>
        <?= htmlspecialchars($session->requireUser()->getLastName()) ?><br>

        <b><?= $lang->require('first_name') ?>:</b>
        <?= htmlspecialchars($session->requireUser()->getFirstName()) ?><br>

        <b><?= $lang->require('middle_name') ?>:</b>
        <?= htmlspecialchars($session->requireUser()->getMiddleName()) ?><br>



    </div>

</div>
