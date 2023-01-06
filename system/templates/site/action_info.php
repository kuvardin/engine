<?php

declare(strict_types=1);

/**
 * @var App\Sessions\Session $session
 * @var App\Sites\Input\SiteInput $input
 * @var App\Languages\Language $lang
 * @var App\Sites\TemplatesEngine\Page $page
 *
 * @var App\Actions\Action|null $action
 * @var int|null $date
 */

use App\Sites\Sections\ControlPanel\ControlPanelSectionController as Controller;


if (!isset($date)) {
    $date = null;
}

if (!isset($action)) {
    $action = null;
}

?>


<?php if ($action !== null): ?>
    <?php if ($action->getUser() !== null): ?>
        <a href="<?= Controller::getUrl($lang, "users/{$action->getUserId()}") ?>">
            <?= htmlspecialchars($action->getUser()->getFullName()) ?>
        </a>
    <?php else: ?>
        <a href="<?= Controller::getUrl($lang, "session/{$action->getSessionId()}") ?>">
            <?= $lang->require('session_with_number', [$action->getSessionId()]) ?>
        </a>
    <?php endif; ?>
    <br>

    <span class='badge bg-info' data-bs-toggle='tooltip' data-bs-placement='top'
          title="<?= $session->getDateTime($action->getCreationDate())->format('Y.m.d H:i:s') ?>">
        <?= $session->getDateTime($action->getCreationDate())->getShorten() ?>
    </span>

    <span class="badge bg-info">
        <?= $action->getIp() ?>
    </span>

    <?php if ($action->getUserAgent() !== null): ?>
        <span class="badge bg-info" data-bs-toggle="tooltip" data-bs-placement="top"
              title="<?= htmlspecialchars($action->getUserAgent()) ?>">
            <?= App\Sessions\UserAgent::searchOperationSystem($action->getUserAgent())->value ?>
        </span><br>
    <?php endif; ?>
<?php elseif ($date !== null): ?>
    <span class='badge bg-info' data-bs-toggle='tooltip' data-bs-placement='top'
          title="<?= $session->getDateTime($date)->format('Y.m.d H:i:s') ?>">
        <?= $session->getDateTime($date)->getShorten() ?>
    </span>
<?php else: ?>
    <?= $lang->require('unknown_actor') ?>
<?php endif; ?>
<br>
